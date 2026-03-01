<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Attendance;
use App\Models\Leave;
use App\Models\OvertimeUndertime;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Employee Statistics (exclude superadmin)
        $totalEmployees = Employee::where('role', '!=', 'superadmin')->count();
        $activeEmployees = Employee::where('role', '!=', 'superadmin')->where('status', 'active')->count();
        $inactiveEmployees = Employee::where('role', '!=', 'superadmin')->where('status', 'inactive')->count();
        $onLeaveEmployees = Leave::where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->distinct('user_id')
            ->count();
        
        // Attendance Statistics (Today)
        $today = now()->startOfDay();
        $presentToday = Attendance::whereDate('date', $today)
            ->where('status', 'present')
            ->count();
        $absentToday = Attendance::whereDate('date', $today)
            ->where('status', 'absent')
            ->count();
        $lateToday = Attendance::whereDate('date', $today)
            ->where('status', 'late')
            ->count();
        
        $attendanceRate = $totalEmployees > 0 
            ? (($presentToday + $lateToday) / $totalEmployees) * 100 
            : 0;
        
        // Leave Statistics
        $totalLeaves = Leave::count();
        $approvedLeaves = Leave::where('status', 'approved')->count();
        $pendingLeaves = Leave::where('status', 'pending')->count();
        $rejectedLeaves = Leave::where('status', 'rejected')->count();
        
        // Overtime/Undertime Statistics
        $totalOvertimeHours = OvertimeUndertime::where('type', 'overtime')
            ->sum('hours') ?? 0;
        $totalUndertimeHours = OvertimeUndertime::where('type', 'undertime')
            ->sum('hours') ?? 0;
        $totalOvertimeRecords = OvertimeUndertime::where('type', 'overtime')->count();
        
        // Attendance trend (last 7 days)
        $attendanceTrend = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->startOfDay();
            $present = Attendance::whereDate('date', $date)
                ->where('status', 'present')
                ->count();
            $absent = Attendance::whereDate('date', $date)
                ->where('status', 'absent')
                ->count();
            $late = Attendance::whereDate('date', $date)
                ->where('status', 'late')
                ->count();
            
            $attendanceTrend[] = [
                'date' => $date->format('m-d'),
                'present' => $present,
                'absent' => $absent,
                'late' => $late
            ];
        }
        
        // Recent leaves
        $recentLeaves = Leave::with('employee')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        // Recent attendance records
        $recentAttendance = Attendance::with('employee')
            ->orderBy('date', 'desc')
            ->limit(10)
            ->get();
        
        return view('hr.index', compact(
            'totalEmployees',
            'activeEmployees',
            'inactiveEmployees',
            'onLeaveEmployees',
            'presentToday',
            'absentToday',
            'lateToday',
            'attendanceRate',
            'totalLeaves',
            'approvedLeaves',
            'pendingLeaves',
            'rejectedLeaves',
            'totalOvertimeHours',
            'totalUndertimeHours',
            'totalOvertimeRecords',
            'attendanceTrend',
            'recentLeaves',
            'recentAttendance'
        ));
    }
}
