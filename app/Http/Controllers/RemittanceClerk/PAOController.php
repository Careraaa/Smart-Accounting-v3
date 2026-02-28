<?php

namespace App\Http\Controllers\RemittanceClerk;

use App\Http\Controllers\Controller;
use App\Models\PAO;
use Illuminate\Http\Request;

class PAOController extends Controller
{
    public function index(Request $request)
    {
        $sortBy = $request->get('sort_by', 'name');
        $sortOrder = $request->get('sort_order', 'asc');
        
        // Whitelist allowed columns to prevent SQL injection
        $allowedColumns = ['name', 'contact_number', 'status'];
        if (!in_array($sortBy, $allowedColumns)) {
            $sortBy = 'name';
        }
        
        // Validate sort order
        if (!in_array($sortOrder, ['asc', 'desc'])) {
            $sortOrder = 'asc';
        }
        
        $paos = PAO::orderBy($sortBy, $sortOrder)->get();
        return view('remittance-clerk.paos.index', compact('paos', 'sortBy', 'sortOrder'));
    }

    public function create()
    {
        return view('remittance-clerk.paos.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'contact_number' => 'required',
            'email' => 'required|email|unique:paos',
            'address' => 'nullable|string',
            'date_of_hire' => 'required|date',
        ]);

        PAO::create($validated);

        return redirect()->route('paos.index')->with('success', 'PAO/Conductor created successfully.');
    }

    public function show(PAO $pao)
    {
        return view('remittance-clerk.paos.show', compact('pao'));
    }

    public function edit(PAO $pao)
    {
        return view('remittance-clerk.paos.edit', compact('pao'));
    }

    public function update(Request $request, PAO $pao)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'contact_number' => 'required',
            'email' => 'required|email|unique:paos,email,' . $pao->id,
            'address' => 'nullable|string',
            'date_of_hire' => 'required|date',
        ]);

        $pao->update($validated);

        return redirect()->route('paos.index')->with('success', 'PAO/Conductor updated successfully.');
    }

    public function destroy(PAO $pao)
    {
        $pao->delete();
        return redirect()->route('paos.index')->with('success', 'PAO/Conductor deleted successfully.');
    }
}
