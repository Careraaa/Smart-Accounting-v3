<?php

namespace App\Notifications;

use App\Models\DailyRemittance;
use App\Services\NotificationService;

class RemittanceNotification
{

    /**
     * Notify remittance clerk that remittance was approved
     */
    public static function remittanceApproved(DailyRemittance $remittance)
    {
        $message = "Remittance from driver {$remittance->driver->name} for {$remittance->remittance_date->format('M d, Y')} amounting to ₱" . number_format($remittance->net_remittance, 2) . " has been approved.";
        $data = ['remittance_id' => $remittance->id, 'status' => 'approved', 'driver_id' => $remittance->driver_id];
        
        app(NotificationService::class)->sendToRole('remittance_clerk', 'remittance_approved', 'Remittance Approved', $message, $data);
    }

    /**
     * Notify remittance clerk that remittance was rejected
     */
    public static function remittanceRejected(DailyRemittance $remittance)
    {
        $message = "Remittance from driver {$remittance->driver->name} for {$remittance->remittance_date->format('M d, Y')} amounting to ₱" . number_format($remittance->net_remittance, 2) . " has been rejected and requires resubmission.";
        $data = ['remittance_id' => $remittance->id, 'status' => 'rejected', 'driver_id' => $remittance->driver_id];
        
        app(NotificationService::class)->sendToRole('remittance_clerk', 'remittance_rejected', 'Remittance Rejected', $message, $data);
    }

    /**
     * Notify accountant that remittance has been submitted
     */
    public static function remittanceSubmitted(DailyRemittance $remittance)
    {
        $remittance->load('driver', 'pao', 'vehicle');

        $message = "{$remittance->driver->name} submitted a remittance for {$remittance->remittance_date->format('M d, Y')} amounting to ₱" . number_format($remittance->net_remittance, 2) . ".";
        $data = [
            'remittance_id' => $remittance->id,
            'driver_id' => $remittance->driver_id,
            'driver_name' => $remittance->driver->name,
            'total_collection' => $remittance->total_collection,
            'total_expenses' => $remittance->total_expenses,
            'net_remittance' => $remittance->net_remittance,
            'pao_name' => $remittance->pao->name,
            'vehicle_name' => $remittance->vehicle->name,
            'remittance_date' => $remittance->remittance_date->format('M d, Y'),
            'status' => $remittance->status,
        ];
        
        app(NotificationService::class)->sendToRole('accountant', 'remittance_submitted', 'New Remittance Submitted', $message, $data);
    }

    /**
     * Notify accountant that remittance has been updated
     */
    public static function remittanceUpdated(DailyRemittance $remittance)
    {
        $remittance->load('driver', 'pao', 'vehicle');

        $message = "{$remittance->driver->name}'s remittance has been updated. Net amount: ₱" . number_format($remittance->net_remittance, 2) . ".";
        $data = [
            'remittance_id' => $remittance->id,
            'driver_id' => $remittance->driver_id,
            'driver_name' => $remittance->driver->name,
            'total_collection' => $remittance->total_collection,
            'total_expenses' => $remittance->total_expenses,
            'net_remittance' => $remittance->net_remittance,
            'pao_name' => $remittance->pao->name,
            'vehicle_name' => $remittance->vehicle->name,
            'remittance_date' => $remittance->remittance_date->format('M d, Y'),
            'status' => $remittance->status,
        ];
        
        app(NotificationService::class)->sendToRole('accountant', 'remittance_updated', 'Remittance Updated', $message, $data);
    }
}
