<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Holiday;
use Illuminate\Http\Request;

class HolidayController extends Controller
{
    public function index(Request $request)
    {
        $year = $request->get('year', now()->year);

        $holidays = Holiday::whereYear('date', $year)
            ->get()
            ->map(function ($holiday) {
                return [
                    'date' => $holiday->date->format('Y-m-d'),
                    'name' => $holiday->name,
                ];
            });

        return response()->json($holidays);
    }
}
