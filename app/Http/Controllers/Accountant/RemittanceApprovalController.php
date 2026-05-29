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
        $remittances = DailyRemittance::with('driver', 'pao', 'route', 'vehicle')
            ->orderByDesc('remittance_date')
            ->get();
        return view('accountant.remittances-approval.index', compact('remittances'));
    }

    public function show(DailyRemittance $remittance)
    {
        $remittance->load('driver', 'pao', 'route', 'vehicle');
        return view('accountant.remittances-approval.show', compact('remittance'));
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
        $remittance->update(['status' => 'rejected']);

        return redirect()->back()->with('success', 'Remittance rejected successfully.');
    }
}
