<?php

namespace App\Http\Controllers\Accountant;

use App\Http\Controllers\Controller;
use App\Models\DailyRemittance;
use App\Notifications\RemittanceNotification;
use Illuminate\Http\Request;

class RemittanceApprovalController extends Controller
{
    public function index()
    {
        $remittances = DailyRemittance::with('driver', 'pao', 'route', 'vehicle')->get();
        return view('accountant.remittances-approval.index', compact('remittances'));
    }

    public function approve(DailyRemittance $remittance)
    {
        $remittance->update(['status' => 'approved']);
        $remittance->load('driver', 'pao', 'vehicle');

        RemittanceNotification::remittanceApproved($remittance);

        return redirect()->back()->with('success', 'Remittance approved successfully.');
    }

    public function reject(Request $request, DailyRemittance $remittance)
    {
        $request->validate([
            'rejection_reason' => 'nullable|string|max:255',
        ]);

        $remittance->update(['status' => 'rejected']);
        $remittance->load('driver', 'pao', 'vehicle');

        RemittanceNotification::remittanceRejected($remittance);

        return redirect()->back()->with('success', 'Remittance rejected successfully.');
    }
}
