<?php

namespace App\Services;

use App\Models\Leave;
use App\Models\Attendance;
use App\Models\EmployeeLeaveBalance;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * ============================================================
 *  LEAVE SERVICE - Core Leave Approval & Processing
 * ============================================================
 * 
 * LEAVE WITHOUT PAY (LWOP) SYSTEM:
 * --------------------------------
 * When an employee takes leave:
 * 1. If available credits >= requested days:
 *    - All days are "paid leave" (covered by leave balance)
 *    - paid_days = requested_days
 *    - unpaid_days = 0
 * 
 * 2. If available credits < requested days:
 *    - Only available credits are paid
 *    - Excess days are Leave Without Pay (LWOP)
 *    - paid_days = available_credits
 *    - unpaid_days = requested_days - available_credits
 * 
 * 3. If no available credits (available credits = 0):
 *    - All days are LWOP
 *    - paid_days = 0
 *    - unpaid_days = requested_days
 * 
 * PAYROLL INTEGRATION:
 * -------------------
 * When calculating leave pay during payroll generation:
 * 
 * 1. Find all approved/paid leaves for the period
 * 2. For each leave:
 *    a) Calculate leave pay = daily_rate × paid_days
 *    b) Add as allowance: "Leave Pay"
 *    c) LWOP days (unpaid_days) receive NO pay
 * 
 * Example Calculation:
 *    Daily rate: ₱500
 *    Leave request: 5 days
 *    Available credits: 2 days
 *    
 *    paid_days = 2 → Leave Pay = 500 × 2 = ₱1,000 ✓ (added to allowances)
 *    unpaid_days = 3 → No pay for these days (LWOP)
 * 
 * ATTENDANCE IMPACT:
 * -----------------
 * All leave days (both paid and unpaid) are marked as "on leave" in attendance.
 * This prevents normal time-in/time-out for those days.
 * The distinction between paid/unpaid is handled only in payroll.
 * 
 * ============================================================
 */
class LeaveService
{
    /**
     * Approve a leave request and create attendance records.
     * Calculates paid days (covered by credits) and unpaid days (LWOP).
     * 
     * IMPORTANT: This is called only when HR CHOOSES to approve the leave.
     * HR is NOT required to approve. HR can still REJECT the leave for any reason,
     * regardless of available credits or LWOP considerations.
     * 
     * @param Leave $leave
     * @param int $approvedBy User ID of the approver
     * @return array ['success' => bool, 'message' => string, 'paid_days' => int, 'unpaid_days' => int]
     */
    public function approveLeave(Leave $leave, int $approvedBy): array
    {
        try {
            DB::beginTransaction();

            // Step 1: Check if leave is still pending
            if ($leave->status !== 'pending') {
                return [
                    'success' => false,
                    'message' => "Leave request is already " . $leave->status . ".",
                    'paid_days' => 0,
                    'unpaid_days' => 0,
                ];
            }

            // Step 2: Calculate total leave days
            $totalLeaveDays = $leave->start_date->diffInDays($leave->end_date) + 1;

            // Step 3: Check leave credits
            $leaveTypeId = $leave->leave_type_id;
            $year = $leave->start_date->year;

            $leaveBalance = EmployeeLeaveBalance::where('user_id', $leave->user_id)
                ->where('leave_type_id', $leaveTypeId)
                ->where('year', $year)
                ->first();

            if (!$leaveBalance) {
                return [
                    'success' => false,
                    'message' => "No leave balance found for this leave type and year.",
                    'paid_days' => 0,
                    'unpaid_days' => 0,
                ];
            }

            // Step 4: Calculate paid vs unpaid days
            $availableCredits = $leaveBalance->remaining_days;
            $paidDays = min($availableCredits, $totalLeaveDays);
            $unpaidDays = max(0, $totalLeaveDays - $paidDays);

            // Step 5: Update leave status to approved with paid/unpaid breakdown
            $leave->update([
                'status' => 'approved',
                'approved_by' => $approvedBy,
                'paid_days' => $paidDays,
                'unpaid_days' => $unpaidDays,
            ]);

            // Step 6: Create attendance records with 'on leave' status for each day
            // Both paid and unpaid days are marked as 'on leave' in attendance
            $currentDate = $leave->start_date->clone();
            while ($currentDate->lte($leave->end_date)) {
                // Skip weekends if needed (configure based on business rules)
                if ($this->isWorkingDay($currentDate)) {
                    Attendance::updateOrCreate(
                        [
                            'user_id' => $leave->user_id,
                            'date' => $currentDate->format('Y-m-d'),
                        ],
                        [
                            'status' => 'on leave',
                            'is_manual' => true,
                        ]
                    );
                }
                $currentDate->addDay();
            }

            DB::commit();

            // Build result message
            $message = "Leave approved successfully. ";
            if ($paidDays > 0) {
                $message .= "{$paidDays} day(s) covered by leave credits. ";
            }
            if ($unpaidDays > 0) {
                $message .= "{$unpaidDays} day(s) as Leave Without Pay (LWOP).";
            } else {
                $message .= "{$totalLeaveDays} day(s) marked as 'on leave' in attendance.";
            }

            return [
                'success' => true,
                'message' => $message,
                'paid_days' => $paidDays,
                'unpaid_days' => $unpaidDays,
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'success' => false,
                'message' => "Error approving leave: " . $e->getMessage(),
                'paid_days' => 0,
                'unpaid_days' => 0,
            ];
        }
    }

    /**
     * Check if a date is a working day (Mon-Fri, excluding holidays)
     * You can extend this to check against the holiday table
     */
    private function isWorkingDay(Carbon $date): bool
    {
        // Monday = 1, Sunday = 7
        $dayOfWeek = $date->dayOfWeek;
        
        // Check if it's not a weekend (Saturday or Sunday)
        if ($dayOfWeek == 0 || $dayOfWeek == 6) {
            return false;
        }

        // You can extend this to check against Holiday table if needed
        // $holiday = Holiday::where('date', $date->format('Y-m-d'))->exists();
        // return !$holiday;

        return true;
    }

    /**
     * Mark approved leaves as 'paid' after payroll generation.
     * Called from PayrollService or PayrollController.
     * 
     * @param int $userId
     * @param Carbon $start
     * @param Carbon $end
     */
    public function markLeavesAsPaid(int $userId, Carbon $start, Carbon $end): void
    {
        Leave::where('user_id', $userId)
            ->where('status', 'approved')
            ->where(function ($query) use ($start, $end) {
                $query->whereDate('start_date', '<=', $end->toDateString())
                      ->whereDate('end_date', '>=', $start->toDateString());
            })
            ->update(['status' => 'paid']);
    }

    /**
     * Get approved leaves for a specific employee and payroll period.
     * Calculates the leave pay for each approved leave.
     * 
     * PAYROLL INTEGRATION:
     * Returns allowance items that should be added to the manualAllowances array
     * during payroll generation. Each approved leave with paid_days > 0 becomes
     * a "Leave Pay" allowance: daily_rate × paid_days
     * 
     * @param int $userId User ID
     * @param Carbon $start Period start date
     * @param Carbon $end Period end date
     * @param float $dailyRate Daily salary rate of the employee
     * @return array Array of allowance items: [['name' => 'Leave Pay', 'amount' => X.XX], ...]
     */
    public function getApprovedLeavesAllowances(int $userId, Carbon $start, Carbon $end, float $dailyRate): array
    {
        $approvedLeaves = Leave::where('user_id', $userId)
            ->where('status', 'approved')
            ->where(function ($query) use ($start, $end) {
                // Leave overlaps with the period if:
                // - start_date <= period_end AND end_date >= period_start
                $query->whereDate('start_date', '<=', $end->toDateString())
                      ->whereDate('end_date', '>=', $start->toDateString());
            })
            ->get();

        $allowances = [];

        foreach ($approvedLeaves as $leave) {
            // If paid_days is null (leaves approved before LWOP system), default to total days
            $paidDays = $leave->paid_days ?? $leave->days;
            if ($paidDays > 0) {
                $leavePayAmount = $dailyRate * $paidDays;
                $allowances[] = [
                    'name' => 'Leave Pay',
                    'amount' => $leavePayAmount,
                    'paid_days' => $paidDays,
                ];
            }
        }

        return $allowances;
    }


    /**
     * Reject a leave request and optionally remove attendance records.
     * 
     * @param Leave $leave
     * @param int $rejectedBy User ID of the rejector
     * @param string $reason Rejection reason
     * @return array ['success' => bool, 'message' => string]
     */
    public function rejectLeave(Leave $leave, int $rejectedBy, string $reason = ''): array
    {
        try {
            DB::beginTransaction();

            if ($leave->status !== 'pending') {
                return [
                    'success' => false,
                    'message' => "Cannot reject a leave that is " . $leave->status . "."
                ];
            }

            $leave->update([
                'status' => 'rejected',
                'approved_by' => $rejectedBy,
                'rejection_reason' => $reason,
            ]);

            DB::commit();

            return [
                'success' => true,
                'message' => "Leave rejected successfully."
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'success' => false,
                'message' => "Error rejecting leave: " . $e->getMessage()
            ];
        }
    }
}
