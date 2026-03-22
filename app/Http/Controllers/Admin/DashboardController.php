<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        // Display the QR attendance monitor dashboard
        return view('admin.index');
    }
}
