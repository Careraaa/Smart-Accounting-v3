@extends('layouts.layout')

@push('styles')
<style>
@import url('https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700&display=swap');
.hld-page { font-family: 'Sora', sans-serif; }

.hld-topbar { display:flex;align-items:flex-start;justify-content:space-between;gap:16px;margin-bottom:24px;flex-wrap:wrap; }
.hld-topbar-title { font-size:1.35rem;font-weight:800;color:#111827;letter-spacing:-0.02em;margin:0 0 2px; }
.hld-topbar-sub   { font-size:0.78rem;color:#9ca3af;margin:0; }

.hld-btn-sec {
    display:inline-flex;align-items:center;gap:7px;padding:9px 16px;background:#fff;color:#374151;
    border:1px solid #e5e7eb;border-radius:10px;font-family:'Sora',sans-serif;font-size:0.82rem;
    font-weight:600;text-decoration:none;cursor:pointer;transition:all 0.15s;white-space:nowrap;
}
.hld-btn-sec:hover { border-color:#c8292a;color:#c8292a;background:#fff5f5; }

.hld-flash.error { display:flex;align-items:center;gap:10px;padding:12px 16px;border-radius:10px;font-size:0.82rem;font-weight:500;margin-bottom:20px;background:#fff0f0;border:1px solid #fecaca;color:#c8292a;animation:hldFlashIn 0.3s ease; }
@keyframes hldFlashIn { from{opacity:0;transform:translateY(-6px)}to{opacity:1;transform:translateY(0)} }

.hld-form-wrap { max-width:600px;margin:0 auto; }
.hld-card { background:#fff;border:1px solid #e5e7eb;border-radius:14px;overflow:hidden; }
.hld-card-header { padding:18px 20px;border-bottom:1px solid #f3f4f6;display:flex;align-items:flex-start;gap:14px; }
.hld-card-icon { width:40px;height:40px;border-radius:10px;background:#fff5f5;display:flex;align-items:center;justify-content:center;color:#c8292a;flex-shrink:0; }
.hld-card-title { font-size:0.88rem;font-weight:700;color:#111827;margin:0 0 2px; }
.hld-card-sub { font-size:0.72rem;color:#9ca3af;margin:0; }
.hld-card-body { padding:24px 20px; }

.hld-field { margin-bottom:20px; }
.hld-label { display:block;font-size:0.82rem;font-weight:600;color:#111827;margin-bottom:8px; }
.hld-label .req { color:#c8292a; }

.hld-input { width:100%;border:1px solid #e5e7eb;border-radius:8px;padding:10px 12px;font-size:0.84rem;font-family:'Sora',sans-serif;color:#111827;background:#f9fafb;outline:none;transition:border-color 0.15s,background 0.15s; }
.hld-input:focus { border-color:#c8292a;background:#fff;box-shadow:0 0 0 3px rgba(200,41,42,0.08); }
.hld-input.is-invalid { border-color:#ef4444;background:#fff5f5; }

.hld-select-wrap { position:relative; }
.hld-select-wrap svg { position:absolute;right:12px;top:50%;transform:translateY(-50%);pointer-events:none;color:#9ca3af; }
.hld-select { width:100%;border:1px solid #e5e7eb;border-radius:8px;padding:10px 36px 10px 12px;font-size:0.84rem;font-family:'Sora',sans-serif;color:#111827;background:#f9fafb;outline:none;cursor:pointer;appearance:none;transition:border-color 0.15s; }
.hld-select:focus { border-color:#c8292a; }

.hld-textarea { width:100%;border:1px solid #e5e7eb;border-radius:8px;padding:10px 12px;font-size:0.84rem;font-family:'Sora',sans-serif;color:#111827;background:#f9fafb;outline:none;resize:vertical;min-height:100px;transition:border-color 0.15s; }
.hld-textarea:focus { border-color:#c8292a;background:#fff; }

.hld-invalid-feedback { display:block;font-size:0.75rem;color:#ef4444;margin-top:6px; }

.hld-divider { font-size:0.72rem;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;color:#9ca3af;margin:24px 0 16px;padding-bottom:8px;border-bottom:1px solid #f3f4f6; }

.hld-form-actions { display:flex;gap:10px;justify-content:flex-end;padding-top:16px;border-top:1px solid #f3f4f6;margin-top:24px; }
.hld-btn-cancel { display:inline-flex;align-items:center;gap:7px;padding:9px 18px;background:#fff;color:#374151;border:1px solid #e5e7eb;border-radius:10px;font-family:'Sora',sans-serif;font-size:0.82rem;font-weight:600;text-decoration:none;cursor:pointer;transition:all 0.15s; }
.hld-btn-cancel:hover { border-color:#d1d5db;color:#111827;background:#f9fafb; }
.hld-btn-submit { display:inline-flex;align-items:center;gap:7px;padding:9px 18px;background:#c8292a;color:#fff;border:none;border-radius:10px;font-family:'Sora',sans-serif;font-size:0.82rem;font-weight:600;cursor:pointer;transition:background 0.15s; }
.hld-btn-submit:hover { background:#a81f20; }

.hld-btn-danger {
    display:inline-flex;align-items:center;gap:7px;padding:9px 18px;background:#ef4444;color:#fff;border:none;border-radius:10px;font-family:'Sora',sans-serif;font-size:0.82rem;font-weight:600;cursor:pointer;transition:background 0.15s;
}
.hld-btn-danger:hover { background:#dc2626; }

/* Modal */
.hld-modal-overlay { position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.5);display:flex;align-items:center;justify-content:center;z-index:999;opacity:0;pointer-events:none;transition:opacity 0.2s; }
.hld-modal-overlay.active { opacity:1;pointer-events:all; }

/* Employee modal styles */
.emp-modal-overlay { position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.5);display:flex;align-items:center;justify-content:center;z-index:999;opacity:0;pointer-events:none;transition:opacity 0.2s; }
.emp-modal-overlay.active { opacity:1;pointer-events:all; }
.emp-modal { background:#fff;border-radius:12px;padding:24px;max-width:400px;width:90%;box-shadow:0 20px 25px -5px rgba(0,0,0,0.15); }
.emp-modal-title { font-size:1rem;font-weight:700;color:#111827;margin:0 0 12px; }
.emp-modal-message { font-size:0.84rem;color:#6b7280;margin:0 0 20px;line-height:1.5; }
.emp-modal-actions { display:flex;gap:10px;justify-content:flex-end; }
.emp-modal-btn { display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border-radius:8px;font-family:'Sora',sans-serif;font-size:0.82rem;font-weight:600;cursor:pointer;transition:all 0.15s;border:none; }
.emp-modal-btn-cancel { background:#fff;color:#374151;border:1px solid #e5e7eb; }
.emp-modal-btn-cancel:hover { border-color:#d1d5db;background:#f9fafb; }
.emp-modal-btn-confirm { background:#ef4444;color:#fff; }
.emp-modal-btn-confirm:hover { background:#dc2626; }
</style>
@endpush

@section('content')
<div class="hld-page">

    {{-- Error flash --}}
    @if($errors->any())
    <div class="hld-flash error">
        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="flex-shrink:0;margin-top:2px;"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 16v-4m0-4h.01"/></svg>
        <div>
            <strong>Please fix the errors:</strong>
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif

    {{-- Topbar --}}
    <div class="hld-topbar">
        <div>
            <h1 class="hld-topbar-title">Edit Holiday</h1>
            <p class="hld-topbar-sub">Update details for <strong>{{ $holiday->name }}</strong></p>
        </div>
        <div style="display:flex;gap:10px;">
            <a href="{{ url()->previous() }}" class="hld-btn-sec">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back
            </a>
            <button type="button" class="hld-btn-danger" onclick="document.getElementById('deleteModal').classList.add('active');">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                Delete
            </button>
        </div>
    </div>

    {{-- Form card --}}
    <div class="hld-form-wrap">
        <div class="hld-card">

            <div class="hld-card-header">
                <div class="hld-card-icon">
                    <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </div>
                <div>
                    <p class="hld-card-title">Holiday Details</p>
                    <p class="hld-card-sub">All fields marked <span style="color:#c8292a;">*</span> are required</p>
                </div>
            </div>

            <div class="hld-card-body">
                <form method="POST" action="{{ route('holiday.update', $holiday) }}" id="hldForm" novalidate>
                    @csrf
                    @method('PUT')

                    {{-- Holiday Name --}}
                    <div class="hld-field">
                        <label for="name" class="hld-label">Holiday Name <span class="req">*</span></label>
                        <input type="text" name="name" id="name"
                            class="hld-input @error('name') is-invalid @enderror"
                            placeholder="e.g., Christmas Day"
                            value="{{ old('name', $holiday->name) }}" required>
                        @error('name')
                            <span class="hld-invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Holiday Date --}}
                    <div class="hld-field">
                        <label for="date" class="hld-label">Holiday Date <span class="req">*</span></label>
                        <input type="date" name="date" id="date"
                            class="hld-input @error('date') is-invalid @enderror"
                            value="{{ old('date', $holiday->date->format('Y-m-d')) }}" required>
                        @error('date')
                            <span class="hld-invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Holiday Type --}}
                    <div class="hld-divider">Classification</div>
                    <div class="hld-field">
                        <label for="type" class="hld-label">Holiday Type <span class="req">*</span></label>
                        <div class="hld-select-wrap">
                            <select name="type" id="type"
                                class="hld-select @error('type') is-invalid @enderror"
                                required>
                                <option value="">— Select Type —</option>
                                <option value="regular"   @selected(old('type', $holiday->type) === 'regular')>Regular Holiday (Full pay when worked)</option>
                                <option value="special" @selected(old('type', $holiday->type) === 'special')>Special Non-Working (No work, no pay)</option>
                            </select>
                            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                        </div>
                        @error('type')
                            <span class="hld-invalid-feedback">{{ $message }}</span>
                        @enderror
                        <div style="font-size:0.72rem;color:#9ca3af;margin-top:8px;line-height:1.4;">
                            <strong>Regular:</strong> Employees receive full pay even if not worked (if worked previous day).<br>
                            <strong>Special:</strong> Only paid if worked on that day.
                        </div>
                    </div>

                    {{-- Form actions --}}
                    <div class="hld-form-actions">
                        <button type="submit" class="hld-btn-submit">
                            <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection

{{-- Delete Confirmation Modal --}}
<div id="deleteModal" class="emp-modal-overlay">
    <div class="emp-modal">
        <h3 class="emp-modal-title">Delete Holiday</h3>
        <p class="emp-modal-message">Are you sure you want to delete <strong>{{ $holiday->name }}</strong>? This action cannot be undone.</p>
        <div class="emp-modal-actions">
            <button type="button" class="emp-modal-btn emp-modal-btn-cancel" onclick="closeDeleteModal()">Cancel</button>
            <button type="button" class="emp-modal-btn emp-modal-btn-confirm" onclick="confirmDelete()">Delete</button>
        </div>
    </div>
</div>

<script>
function closeDeleteModal() {
    document.getElementById('deleteModal').classList.remove('active');
}

function confirmDelete() {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route('holiday.destroy', $holiday) }}';
    form.innerHTML = '@csrf @method('DELETE')';
    document.body.appendChild(form);
    form.submit();
}

document.getElementById('deleteModal').addEventListener('click', function(e) {
    if (e.target === this) {
        this.classList.remove('active');
    }
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeDeleteModal();
    }
});
</script>
