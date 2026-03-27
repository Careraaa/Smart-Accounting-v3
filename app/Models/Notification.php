<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'data',
        'read_at',
    ];

    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
    ];

    /**
     * Get the user that this notification belongs to
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead()
    {
        if ($this->read_at === null) {
            $this->update(['read_at' => Carbon::now()]);
        }
        return $this;
    }

    /**
     * Mark notification as unread
     */
    public function markAsUnread()
    {
        $this->update(['read_at' => null]);
        return $this;
    }

    /**
     * Check if notification is read
     */
    public function isRead()
    {
        return $this->read_at !== null;
    }

    /**
     * Check if notification is unread
     */
    public function isUnread()
    {
        return $this->read_at === null;
    }

    /**
     * Scope to get unread notifications
     */
    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    /**
     * Scope to get read notifications
     */
    public function scopeRead($query)
    {
        return $query->whereNotNull('read_at');
    }

    /**
     * Scope to filter by type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Get recent notifications
     */
    public function scopeRecent($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Get the action URL for this notification based on its type
     */
    public function getActionUrl()
    {
        $type = $this->type;
        $data = $this->data ?? [];
        $user = Auth::user();
        $isAccountant = $user && $user->role === 'accountant';

        switch ($type) {
            // Payroll related notifications
            case 'payroll_processed':
            case 'payroll_released':
            case 'payroll_created':
            case 'payroll_updated':
            case 'payroll_deleted':
            case 'payroll_recalculated':
                // Route accountants to payroll-approval
                if ($isAccountant) {
                    return route('payroll-approval.index');
                }
                if ($data['payroll_id'] ?? null) {
                    return route('payroll.salary-computation.show', ['payroll' => $data['payroll_id']]);
                }
                return route('payroll.index');

            case 'payroll_generated':
            case 'payroll_ready_review':
                // Route accountants to payroll-approval
                if ($isAccountant) {
                    return route('payroll-approval.index');
                }
                return route('payroll.index');

            // Leave related notifications
            case 'leave_submitted':
            case 'leave_approved':
            case 'leave_rejected':
            case 'leave_updated':
            case 'leave_deleted':
                if ($data['leave_id'] ?? null) {
                    return route('leave.show', ['leave' => $data['leave_id']]);
                }
                return route('leave.index');

            case 'leave_pending_approval':
                return route('leave.pending'); // or leave.index with filter

            // Attendance related notifications
            case 'attendance_issue':
            case 'employee_absent':
            case 'employee_late':
            case 'attendance_recorded':
                if ($data['employee_id'] ?? null) {
                    return route('employees.show', ['employee' => $data['employee_id']]);
                }
                return route('employees.index');

            // Overtime related notifications
            case 'overtime_submitted':
            case 'overtime_approved':
            case 'overtime_rejected':
            case 'overtime_updated':
            case 'overtime_deleted':
                if ($data['record_id'] ?? null) {
                    return route('overtime.show', ['overtime' => $data['record_id']]);
                }
                return route('overtime.index');

            case 'overtime_pending_approval':
                return route('overtime.pending'); // or overtime.index with filter

            // Remittance related notifications
            case 'remittance_created':
            case 'remittance_updated':
            case 'remittance_deleted':
            case 'remittance_approved':
            case 'remittance_rejected':
                // Route accountants to remittance approval
                if ($isAccountant) {
                    return route('remittance-approval.index');
                }
                if ($data['remittance_id'] ?? null) {
                    return route('remittances.show', ['remittance' => $data['remittance_id']]);
                }
                return route('remittances.index');

            // Default: no action
            default:
                return null;
        }
    }
}
