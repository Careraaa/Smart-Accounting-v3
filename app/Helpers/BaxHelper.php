<?php

namespace App\Helpers;

class BaxHelper
{
    /**
     * Get badge color class based on status
     * Used for journal entries, payroll, remittances, etc.
     */
    public static function statusColor($status)
    {
        return match($status) {
            'Draft', 'Pending', 'In Progress' => 'warning',
            'Approved', 'Completed', 'Posted', 'Active', 'Reconciled' => 'success',
            'Rejected', 'Declined', 'Failed', 'Inactive', 'Expired' => 'danger',
            'Pending Approval' => 'info',
            'Verified' => 'success',
            'Partial' => 'warning',
            'Unpaid' => 'danger',
            'Paid' => 'success',
            'Balanced' => 'success',
            'Not Balanced' => 'warning',
            default => 'secondary',
        };
    }

    /**
     * Get badge text for status
     */
    public static function statusBadge($status)
    {
        return '<span class="badge bg-' . self::statusColor($status) . '">' . $status . '</span>';
    }
}
