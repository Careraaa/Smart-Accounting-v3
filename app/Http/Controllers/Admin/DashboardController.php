<?php

namespace App\Http\Controllers\Admin;

use App\Traits\LogsUserActivity;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    use LogsUserActivity;
    public function index()
    {
        $this->logActivity('viewed', 'dashboard');

        // Display the QR attendance monitor dashboard
        return view('admin.index');
    }
}
