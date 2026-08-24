<?php

namespace App\Http\Controllers\Accountant;

use App\Http\Controllers\Controller;
use App\Models\DailyRemittance;
use App\Notifications\RemittanceNotification;
use App\Traits\LogsUserActivity;
use Illuminate\Http\Request;

class RemittanceApprovalController extends Controller
{
    use LogsUserActivity;
    public function index()
    {
        $this->logActivity('viewed', 'Remittance Approval List', request()->url());

        $remittances = DailyRemittance::with('driver', 'pao', 'route', 'vehicle')
            ->orderByDesc('remittance_date')
            ->get();
        return view('accountant.remittances-approval.index', compact('remittances'));
    }

    public function show(DailyRemittance $remittance)
    {
        $this->logActivity('viewed', "Remittance #{$remittance->id}", request()->url(), 'daily_remittance', $remittance->id);

        $remittance->load('driver', 'pao', 'route', 'vehicle');
        return view('accountant.remittances-approval.show', compact('remittance'));
    }

    public function approve(DailyRemittance $remittance)
    {
        $this->logActivity('approved', "Remittance #{$remittance->id}", request()->url(), 'daily_remittance', $remittance->id);

        $remittance->update(['status' => 'approved']);
        $remittance->load('driver', 'pao', 'vehicle');

        RemittanceNotification::remittanceApproved($remittance);

        return redirect()->back()->with('success', 'Remittance approved successfully.');
    }

    public function reject(Request $request, DailyRemittance $remittance)
    {
        $this->logActivity('rejected', "Remittance #{$remittance->id}", request()->url(), 'daily_remittance', $remittance->id);

        $remittance->update(['status' => 'rejected']);

        return redirect()->back()->with('success', 'Remittance rejected successfully.');
    }
}
