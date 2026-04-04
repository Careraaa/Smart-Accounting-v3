@extends('layouts.layout')

@push('styles')
<style>
@import url('https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Sora:wght@400;600;700;800&display=swap');
.prl-page{font-family:'Sora',sans-serif}
.prl-topbar{display:flex;align-items:flex-start;justify-content:space-between;gap:16px;margin-bottom:24px;flex-wrap:wrap}
.prl-topbar-title{font-size:1.35rem;font-weight:800;color:#111827;letter-spacing:-0.02em;margin:0 0 2px}
.prl-topbar-sub{font-size:.78rem;color:#9ca3af;margin:0}
.prl-topbar-actions{display:flex;gap:8px;flex-wrap:wrap;align-items:center}
.prl-btn-sec{display:inline-flex;align-items:center;gap:7px;padding:9px 16px;background:#fff;color:#374151;border:1px solid #e5e7eb;border-radius:10px;font-family:'Sora',sans-serif;font-size:.82rem;font-weight:600;text-decoration:none;cursor:pointer;transition:all .15s;white-space:nowrap}
.prl-btn-sec:hover{border-color:#c8292a;color:#c8292a;background:#fff5f5}
.prl-form-card{background:#fff;border:1px solid #e5e7eb;border-radius:14px;overflow:hidden;max-width:760px;margin:0 auto}
.prl-form-head{padding:18px 24px;border-bottom:1px solid #f3f4f6;font-size:.88rem;font-weight:700;color:#111827;display:flex;align-items:center;gap:8px}
.prl-dot{width:8px;height:8px;border-radius:50%;background:#c8292a;display:inline-block}
.prl-form-body{padding:24px}
.prl-form-row{display:grid;grid-template-columns:1fr 1fr;gap:18px;margin-bottom:18px}
@media(max-width:640px){.prl-form-row{grid-template-columns:1fr}}
.prl-form-group{margin-bottom:18px}
.prl-form-label{font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#9ca3af;display:block;margin-bottom:6px}
.prl-form-label .req{color:#c8292a}
.prl-form-control{width:100%;border:1px solid #e5e7eb;border-radius:8px;padding:9px 12px;font-size:.845rem;font-family:'Sora',sans-serif;color:#111827;background:#fff;outline:none;transition:border-color .15s,box-shadow .15s}
.prl-form-control:focus{border-color:#c8292a;box-shadow:0 0 0 3px rgba(200,41,42,.08)}
.prl-form-control.is-invalid{border-color:#ef4444}
.prl-form-hint{font-size:.75rem;color:#9ca3af;margin-top:4px}
.prl-invalid-feedback{font-size:.75rem;color:#c8292a;margin-top:4px}
.prl-form-footer{padding:16px 24px;border-top:1px solid #f3f4f6;display:flex;gap:10px;align-items:center}
.prl-btn-submit{display:inline-flex;align-items:center;gap:7px;padding:10px 22px;background:#111827;color:#fff;border:none;border-radius:10px;font-family:'Sora',sans-serif;font-size:.845rem;font-weight:700;cursor:pointer;transition:background .15s}
.prl-btn-submit:hover{background:#000}
</style>
@endpush

@section('content')
<div class="prl-page">

    <div class="prl-topbar">
        <div>
            <h1 class="prl-topbar-title">Edit OT / UT Record</h1>
            <p class="prl-topbar-sub">Update overtime or undertime details</p>
        </div>
        <div class="prl-topbar-actions">
            <a href="{{ route('overtime.show', $overtime) }}" class="prl-btn-sec">
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                Cancel
            </a>
        </div>
    </div>

    <div class="prl-form-card">
        <div class="prl-form-head"><span class="prl-dot"></span> Edit Record</div>
        <form action="{{ route('overtime.update', $overtime) }}" method="POST" id="overtimeForm">
            @csrf
            @method('PUT')
            <div class="prl-form-body">

                <div class="prl-form-row">
                    <div>
                        <label class="prl-form-label">Employee <span class="req">*</span></label>
                        <select name="user_id" class="prl-form-control @error('user_id') is-invalid @enderror" required>
                            <option value="">Select Employee</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}" {{ $overtime->user_id == $employee->id ? 'selected' : '' }}>
                                    {{ $employee->first_name }} {{ $employee->last_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('user_id')<div class="prl-invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div>
                        <label class="prl-form-label">Type <span class="req">*</span></label>
                        <select name="type" class="prl-form-control @error('type') is-invalid @enderror" required>
                            <option value="">Select Type</option>
                            <option value="overtime"  {{ $overtime->type === 'overtime'  ? 'selected' : '' }}>Overtime</option>
                            <option value="undertime" {{ $overtime->type === 'undertime' ? 'selected' : '' }}>Undertime</option>
                        </select>
                        @error('type')<div class="prl-invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="prl-form-row">
                    <div>
                        <label class="prl-form-label">Date <span class="req">*</span></label>
                        <input type="date" name="date" class="prl-form-control @error('date') is-invalid @enderror"
                            value="{{ $overtime->date->format('Y-m-d') }}" required>
                        @error('date')<div class="prl-invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div>
                        <label class="prl-form-label">Hours <span class="req">*</span></label>
                        <input type="number" name="hours" class="prl-form-control @error('hours') is-invalid @enderror"
                            value="{{ $overtime->hours }}" placeholder="0.5" step="0.5" min="0.5" max="24" required>
                        <div class="prl-form-hint">Between 0.5 and 24 hours</div>
                        @error('hours')<div class="prl-invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="prl-form-group">
                    <label class="prl-form-label">Reason <span class="req">*</span></label>
                    <textarea name="reason" class="prl-form-control @error('reason') is-invalid @enderror"
                        rows="4" required>{{ $overtime->reason }}</textarea>
                    @error('reason')<div class="prl-invalid-feedback">{{ $message }}</div>@enderror
                </div>

            </div>
            <div class="prl-form-footer">
                <button type="submit" class="prl-btn-submit">
                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    Update Record
                </button>
                <a href="{{ route('overtime.show', $overtime) }}" class="prl-btn-sec">Cancel</a>
            </div>
        </form>
    </div>

</div>
@endsection