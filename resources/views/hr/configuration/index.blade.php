@extends('layouts.layout')

@push('styles')
@include('superadmin.partials.prl-theme')
<style>
/* Layout fixes for HR Configuration */
.prl-page {
    padding: 20px 16px !important;
    max-width: 1200px !important;
    margin: 0 auto !important;
}

.prl-topbar {
    margin-bottom: 28px !important;
    padding-bottom: 16px !important;
    border-bottom: 1px solid #f3f4f6 !important;
}

.prl-topbar-title {
    font-size: 1.75rem !important;
    font-weight: 800 !important;
    color: #111827 !important;
    margin-bottom: 4px !important;
}

.prl-topbar-sub {
    font-size: 0.85rem !important;
    color: #6b7280 !important;
}

/* Override Bootstrap to avoid conflicts */
.prl-page * { box-sizing: border-box; }
.prl-page .form-control { background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; }
.prl-page .form-control:focus { border-color: #c8292a; background: #fff; box-shadow: 0 0 0 3px rgba(200,41,42,0.08); }
.prl-page .modal-content { border: none; border-radius: 12px; }
.prl-page .is-invalid { border-color: #ef4444 !important; }
</style>
@endpush

@section('content')
<div class="col-12">
    <div class="prl-page">
        {{-- Topbar --}}
        <div class="prl-topbar">
            <div>
                <h1 class="prl-topbar-title">HR Configuration</h1>
                <p class="prl-topbar-sub">Manage shifts, payroll cutoffs, and attendance settings</p>
            </div>
        </div>

        {{-- Flash Messages --}}
        @if ($errors->any())
            <div class="prl-flash error">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        @if (session('success'))
            <div class="prl-flash success">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        {{-- Tabs --}}
        <div class="prl-tabs-wrap">
            <div class="prl-tabs">
                <button type="button" class="prl-tab-btn active" onclick="switchTab('shifts', this)">Shifts</button>
                <button type="button" class="prl-tab-btn" onclick="switchTab('payroll', this)">Payroll Cutoff</button>
                <button type="button" class="prl-tab-btn" onclick="switchTab('attendance', this)">Attendance Rules</button>
            </div>
        </div>

        {{-- ════════════════════════════════════════ --}}
        {{-- SHIFTS TAB --}}
        {{-- ════════════════════════════════════════ --}}
        <div id="shifts" class="prl-tab-panel active">
            <div class="prl-cfg-block">
                <h3 class="prl-cfg-block-title">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="1"></circle>
                        <path d="M12 1v6m0 6v6"></path>
                        <path d="M4.22 4.22l4.24 4.24m2.12 2.12l4.24 4.24"></path>
                        <path d="M1 12h6m6 0h6"></path>
                        <path d="M4.22 19.78l4.24-4.24m2.12-2.12l4.24-4.24"></path>
                    </svg>
                    Manage Shifts
                </h3>

                <div style="display:flex;justify-content:flex-end;margin-bottom:20px;">
                    <button class="prl-btn-generate" data-bs-toggle="modal" data-bs-target="#addShiftModal">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="12" y1="5" x2="12" y2="19"></line>
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                        </svg>
                        Add New Shift
                    </button>
                </div>

                @if($shifts->isEmpty())
                    <div class="prl-empty">
                        <div class="prl-empty-icon">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polyline points="12 6 12 12 16 14"></polyline>
                            </svg>
                        </div>
                        <p class="prl-empty-title">No shifts configured</p>
                        <p class="prl-empty-sub">Click "Add New Shift" to create your first shift</p>
                    </div>
                @else
                    <div class="prl-table-card">
                        <div class="prl-table-scroll">
                            <table class="prl-table">
                                <thead>
                                    <tr>
                                        <th>Shift Name</th>
                                        <th>Start Time</th>
                                        <th>End Time</th>
                                        <th>Break Time</th>
                                        <th>Work Hours</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($shifts as $shift)
                                        <tr>
                                            <td><strong>{{ $shift->name }}</strong></td>
                                            <td>{{ \Carbon\Carbon::createFromFormat('H:i:s', $shift->start_time)->format('h:i A') }}</td>
                                            <td>{{ \Carbon\Carbon::createFromFormat('H:i:s', $shift->end_time)->format('h:i A') }}</td>
                                            <td>
                                                @if($shift->break_start && $shift->break_end)
                                                    {{ \Carbon\Carbon::createFromFormat('H:i:s', $shift->break_start)->format('h:i A') }} - {{ \Carbon\Carbon::createFromFormat('H:i:s', $shift->break_end)->format('h:i A') }}
                                                @else
                                                    <span style="color:#9ca3af;">-</span>
                                                @endif
                                            </td>
                                            <td><span style="display:inline-flex;align-items:center;gap:5px;padding:4px 10px;border-radius:6px;background:#eff6ff;color:#1d4ed8;font-size:0.75rem;font-weight:600;">{{ $shift->duration }} hrs</span></td>
                                            <td>
                                                @if($shift->is_active)
                                                    <span style="display:inline-flex;align-items:center;gap:5px;padding:4px 10px;border-radius:6px;background:#f0fdf4;color:#16a34a;font-size:0.75rem;font-weight:600;">Active</span>
                                                @else
                                                    <span style="display:inline-flex;align-items:center;gap:5px;padding:4px 10px;border-radius:6px;background:#f3f4f6;color:#6b7280;font-size:0.75rem;font-weight:600;">Inactive</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="prl-actions">
                                                    <button type="button" class="prl-action-btn" data-bs-toggle="modal" data-bs-target="#editShiftModal{{ $shift->id }}" title="Edit">
                                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                                        </svg>
                                                    </button>
                                                    <form action="{{ route('settings.shift.destroy', $shift) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="prl-action-btn" title="Delete" style="color:#c8292a;">
                                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                                <polyline points="3 6 5 6 21 6"></polyline>
                                                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                            </svg>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>

                                        <!-- Edit Shift Modal -->
                                        <div class="modal fade" id="editShiftModal{{ $shift->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Edit Shift: {{ $shift->name }}</h5>
                                                    </div>
                                                    <form action="{{ route('settings.shift.update', $shift) }}" method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="modal-body">
                                                            <div style="margin-bottom:16px;">
                                                                <label class="prl-cfg-label">Shift Name</label>
                                                                <input type="text" class="prl-ctrl" name="name" value="{{ $shift->name }}" required>
                                                            </div>
                                                            <div style="margin-bottom:16px;">
                                                                <label class="prl-cfg-label">Start Time</label>
                                                                <p style="font-size:0.75rem;color:#9ca3af;margin:0 0 6px;">Current: {{ \Carbon\Carbon::createFromFormat('H:i:s', $shift->start_time)->format('h:i A') }}</p>
                                                                <input type="time" class="prl-ctrl" name="start_time" value="{{ substr($shift->start_time, 0, 5) }}" required>
                                                            </div>
                                                            <div style="margin-bottom:16px;">
                                                                <label class="prl-cfg-label">End Time</label>
                                                                <p style="font-size:0.75rem;color:#9ca3af;margin:0 0 6px;">Current: {{ \Carbon\Carbon::createFromFormat('H:i:s', $shift->end_time)->format('h:i A') }}</p>
                                                                <input type="time" class="prl-ctrl" name="end_time" value="{{ substr($shift->end_time, 0, 5) }}" required>
                                                            </div>
                                                            <div style="margin-bottom:16px;">
                                                            <label class="prl-cfg-label">Break Start Time (Optional)</label>
                                                            @if($shift->break_start)
                                                                <p style="font-size:0.75rem;color:#9ca3af;margin:0 0 6px;">Current: {{ \Carbon\Carbon::createFromFormat('H:i:s', $shift->break_start)->format('h:i A') }}</p>
                                                            @endif
                                                            <input type="time" class="prl-ctrl" name="break_start" value="{{ $shift->break_start ? substr($shift->break_start, 0, 5) : '' }}">
                                                        </div>
                                                        <div style="margin-bottom:16px;">
                                                            <label class="prl-cfg-label">Break End Time (Optional)</label>
                                                            @if($shift->break_end)
                                                                <p style="font-size:0.75rem;color:#9ca3af;margin:0 0 6px;">Current: {{ \Carbon\Carbon::createFromFormat('H:i:s', $shift->break_end)->format('h:i A') }}</p>
                                                            @endif
                                                            <input type="time" class="prl-ctrl" name="break_end" value="{{ $shift->break_end ? substr($shift->break_end, 0, 5) : '' }}">
                                                            </div>
                                                            <div style="display:flex;align-items:center;gap:8px;">
                                                                <input type="checkbox" id="is_active{{ $shift->id }}" name="is_active" value="1" {{ $shift->is_active ? 'checked' : '' }} style="width:16px;height:16px;cursor:pointer;border-radius:4px;border:1px solid #e5e7eb;">
                                                                <label for="is_active{{ $shift->id }}" style="font-size:0.82rem;color:#374151;cursor:pointer;margin:0;">Active</label>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="submit" class="prl-btn-generate">Save Changes</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- ════════════════════════════════════════ --}}
        {{-- PAYROLL CUTOFF TAB --}}
        {{-- ════════════════════════════════════════ --}}
        <div id="payroll" class="prl-tab-panel">
            <div class="prl-cfg-block">
                <h3 class="prl-cfg-block-title">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="16" y1="2" x2="16" y2="6"></line>
                        <line x1="8" y1="2" x2="8" y2="6"></line>
                        <line x1="3" y1="10" x2="21" y2="10"></line>
                    </svg>
                    Payroll Cutoff Settings
                </h3>

                @if($activeCutoff)
                    <div class="prl-info-panel">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0;margin-top:2px;">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="16" x2="12" y2="12"></line>
                            <line x1="12" y1="8" x2="12.01" y2="8"></line>
                        </svg>
                        <div>
                            <strong>Current Setting:</strong>
                            @if((int)$activeCutoff->cutoff_day === 15)
                                Bi-monthly (15th of each month)
                            @elseif((int)$activeCutoff->cutoff_day === 30)
                                Monthly (30th of each month)
                            @elseif((int)$activeCutoff->cutoff_day <= 7)
                                Weekly (every 7 days)
                            @else
                                Cutoff on the {{ $activeCutoff->cutoff_day }}th of each month
                            @endif
                            <br>
                            <small>Next cutoff: {{ \App\Models\PayrollCutoffSchedule::getNextCutoffDate()?->format('F d, Y') ?? 'Not calculated' }}</small>
                        </div>
                    </div>
                @endif

                <form action="{{ route('settings.payroll-cutoff.update') }}" method="POST">
                    @csrf

                    <p style="font-size:0.82rem;color:#6b7280;margin-bottom:16px;font-weight:500;">Select Payroll Cutoff Frequency</p>

                    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:12px;margin-bottom:20px;">
                        <div class="prl-card" id="weekly-card" style="cursor:pointer;transition:all 0.15s;border:2px solid #e5e7eb;" onclick="selectFrequency('weekly')">
                            <div class="prl-card-body" style="text-align:center;padding:16px;">
                                <input type="radio" name="frequency" value="weekly" style="margin-right:8px;" {{ ($activeCutoff && (int)$activeCutoff->cutoff_day <= 7) ? 'checked' : '' }}>
                                <label style="font-weight:600;color:#111827;cursor:pointer;display:inline;">Weekly</label>
                                <p style="font-size:0.75rem;color:#9ca3af;margin:6px 0 0;margin-bottom:0;">Every 7 days</p>
                            </div>
                        </div>

                        <div class="prl-card" id="bi-monthly-card" style="cursor:pointer;transition:all 0.15s;border:2px solid #e5e7eb;" onclick="selectFrequency('bi-monthly')">
                            <div class="prl-card-body" style="text-align:center;padding:16px;">
                                <input type="radio" name="frequency" value="bi-monthly" style="margin-right:8px;" {{ ($activeCutoff && (int)$activeCutoff->cutoff_day === 15) ? 'checked' : '' }}>
                                <label style="font-weight:600;color:#111827;cursor:pointer;display:inline;">Bi-monthly</label>
                                <p style="font-size:0.75rem;color:#9ca3af;margin:6px 0 0;margin-bottom:0;">15th of each month</p>
                            </div>
                        </div>

                        <div class="prl-card" id="monthly-card" style="cursor:pointer;transition:all 0.15s;border:2px solid #e5e7eb;" onclick="selectFrequency('monthly')">
                            <div class="prl-card-body" style="text-align:center;padding:16px;">
                                <input type="radio" name="frequency" value="monthly" style="margin-right:8px;" {{ ($activeCutoff && (int)$activeCutoff->cutoff_day === 30) ? 'checked' : '' }}>
                                <label style="font-weight:600;color:#111827;cursor:pointer;display:inline;">Monthly</label>
                                <p style="font-size:0.75rem;color:#9ca3af;margin:6px 0 0;margin-bottom:0;">30th of each month</p>
                            </div>
                        </div>

                        <div class="prl-card" id="custom-card" style="cursor:pointer;transition:all 0.15s;border:2px solid #e5e7eb;" onclick="selectFrequency('custom')">
                            <div class="prl-card-body" style="text-align:center;padding:16px;">
                                <input type="radio" name="frequency" value="custom" style="margin-right:8px;">
                                <label style="font-weight:600;color:#111827;cursor:pointer;display:inline;">Custom</label>
                                <p style="font-size:0.75rem;color:#9ca3af;margin:6px 0 0;margin-bottom:0;">Specify cutoff day</p>
                            </div>
                        </div>
                    </div>

                    {{-- Hidden input to always include cutoff_day in form submission --}}
                    <input type="hidden" name="cutoff_day" id="cutoff_day_hidden" value="{{ old('cutoff_day', $activeCutoff?->cutoff_day ?? 15) }}">

                    {{-- Custom cutoff day input (only shown when custom frequency is selected) --}}
                    <div id="custom-cutoff-day" style="display:none;margin-bottom:16px;">
                        <label class="prl-cfg-label">Cutoff Day (1-31) <span class="req">*</span></label>
                        <input type="number" class="prl-ctrl" id="cutoff_day_visible" min="1" max="31" value="{{ old('cutoff_day', $activeCutoff?->cutoff_day ?? 15) }}">
                    </div>

                    <div style="margin-bottom:16px;">
                        <label class="prl-cfg-label">Label (Optional)</label>
                        <input type="text" class="prl-ctrl" name="label" placeholder="e.g., Monthly Payroll" value="{{ old('label', $activeCutoff?->label ?? '') }}">
                    </div>

                    <button type="submit" class="prl-btn-generate">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                            <polyline points="17 21 17 13 7 13 7 21"></polyline>
                            <polyline points="7 3 7 8 15 8"></polyline>
                        </svg>
                        Save Payroll Cutoff Settings
                    </button>
                </form>
            </div>
        </div>

        {{-- ════════════════════════════════════════ --}}
        {{-- ATTENDANCE RULES TAB --}}
        {{-- ════════════════════════════════════════ --}}
        <div id="attendance" class="prl-tab-panel">
            <div class="prl-cfg-block">
                <h3 class="prl-cfg-block-title">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 11l3 3L22 4"></path>
                        <path d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Attendance Rules
                </h3>

                <form action="{{ route('settings.attendance-settings.update') }}" method="POST">
                    @csrf

                    <h4 style="font-size:0.82rem;font-weight:700;color:#374151;margin:0 0 12px;">Grace Period</h4>

                    <div class="prl-cfg-field">
                        <label class="prl-cfg-label">Grace Period (Minutes) <span class="req">*</span></label>
                        <input type="number" class="prl-ctrl @error('grace_period') is-invalid @enderror" name="grace_period" min="0" max="30" value="{{ old('grace_period', $gracePeriod) }}" required>
                        <p class="prl-cfg-desc">Additional time before marking as late</p>
                        @error('grace_period')
                            <span class="prl-err">{{ $message }}</span>
                        @enderror
                    </div>

                    <div style="margin-top:20px;">
                        <button type="submit" class="prl-btn-generate">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                                <polyline points="17 21 17 13 7 13 7 21"></polyline>
                                <polyline points="7 3 7 8 15 8"></polyline>
                            </svg>
                            Save Attendance Settings
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Add Shift Modal -->
<div class="modal fade" id="addShiftModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="border:none;border-radius:12px;">
            <div class="modal-header" style="border-bottom:1px solid #f3f4f6;padding:16px 20px;">
                <h5 class="modal-title" style="font-size:0.9rem;font-weight:700;color:#111827;margin:0;">Add New Shift</h5>
            </div>
            <form action="{{ route('settings.shift.store') }}" method="POST">
                @csrf
                <div class="modal-body" style="padding:20px;">
                    <div style="margin-bottom:16px;">
                        <label class="prl-cfg-label">Shift Name</label>
                        <input type="text" class="prl-ctrl" name="name" placeholder="e.g., Morning Shift" required>
                    </div>
                    <div style="margin-bottom:16px;">
                        <label class="prl-cfg-label">Start Time</label>
                        <p style="font-size:0.75rem;color:#9ca3af;margin:0 0 6px;">Format: 12-hour (e.g., 09:00 AM)</p>
                        <input type="time" class="prl-ctrl" name="start_time" required>
                    </div>
                    <div style="margin-bottom:16px;">
                        <label class="prl-cfg-label">End Time</label>
                        <p style="font-size:0.75rem;color:#9ca3af;margin:0 0 6px;">Format: 12-hour (e.g., 05:00 PM)</p>
                        <input type="time" class="prl-ctrl" name="end_time" required>
                    </div>
                    <div style="margin-bottom:16px;">
                        <label class="prl-cfg-label">Break Start Time (Optional)</label>
                        <p style="font-size:0.75rem;color:#9ca3af;margin:0 0 6px;">Format: 12-hour (e.g., 12:00 PM)</p>
                        <input type="time" class="prl-ctrl" name="break_start">
                    </div>
                    <div style="margin-bottom:16px;">
                        <label class="prl-cfg-label">Break End Time (Optional)</label>
                        <p style="font-size:0.75rem;color:#9ca3af;margin:0 0 6px;">Format: 12-hour (e.g., 01:00 PM)</p>
                        <input type="time" class="prl-ctrl" name="break_end">
                    </div>
                    <div style="display:flex;align-items:center;gap:8px;">
                        <input type="checkbox" id="new_is_active" name="is_active" value="1" checked style="width:16px;height:16px;cursor:pointer;border-radius:4px;border:1px solid #e5e7eb;">
                        <label for="new_is_active" style="font-size:0.82rem;color:#374151;cursor:pointer;margin:0;">Active</label>
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid #f3f4f6;padding:12px 20px;display:flex;gap:10px;justify-content:flex-end;">
                    <button type="submit" class="prl-btn-generate">Add Shift</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function switchTab(tabName, element) {
    // Hide all tab panels
    document.querySelectorAll('.prl-tab-panel').forEach(panel => {
        panel.classList.remove('active');
    });
    
    // Remove active class from all tab buttons
    document.querySelectorAll('.prl-tab-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    
    // Show the selected tab panel
    document.getElementById(tabName).classList.add('active');
    
    // Add active class to the clicked button
    element.classList.add('active');
}

function selectFrequency(frequency) {
    document.querySelector(`input[value="${frequency}"]`).checked = true;
    const hiddenInput = document.getElementById('cutoff_day_hidden');
    const visibleInput = document.getElementById('cutoff_day_visible');
    
    // Set the cutoff_day value based on frequency
    if (frequency === 'weekly') {
        hiddenInput.value = 7;
        if (visibleInput) visibleInput.value = 7;
    } else if (frequency === 'bi-monthly') {
        hiddenInput.value = 15;
        if (visibleInput) visibleInput.value = 15;
    } else if (frequency === 'monthly') {
        hiddenInput.value = 30;
        if (visibleInput) visibleInput.value = 30;
    }
    
    // Show/hide custom input
    if (frequency === 'custom') {
        document.getElementById('custom-cutoff-day').style.display = 'block';
        if (visibleInput) {
            visibleInput.focus();
            // Sync visible input changes to hidden input
            visibleInput.oninput = function() {
                hiddenInput.value = this.value;
            };
        }
    } else {
        document.getElementById('custom-cutoff-day').style.display = 'none';
    }
}

// Initialize custom cutoff display on page load
document.addEventListener('DOMContentLoaded', function() {
    const selectedFrequency = document.querySelector('input[name="frequency"]:checked')?.value;
    if (selectedFrequency === 'custom') {
        document.getElementById('custom-cutoff-day').style.display = 'block';
    }
});
</script>
@endsection
