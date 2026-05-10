@extends('layouts.layout')

@push('styles')
    @include('employee.profile._styles')
@endpush

@section('content')
<div class="pf-page pf-wrap">
    <div class="pf-backdrop"><div class="pf-grid"></div></div>
    <div class="pf-content">

    @if (session('success'))
        <div class="prl-flash success" style="margin-bottom:18px;">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
            </svg>
            {{ session('success') }}
        </div>
    @endif

    @php
        $u = auth()->user();
        $initials = strtoupper(substr($u->first_name ?? $u->name ?? 'U', 0, 1) . substr($u->last_name ?? '', 0, 1));
        $dept = $u->department ?? '—';
        $role = ucfirst(str_replace('_', ' ', $u->role ?? 'user'));
    @endphp

    <div class="pf-hero">
        <div class="pf-hero-left">
            <div class="pf-avatar">{{ $initials }}</div>
            <div>
                <p class="pf-hero-name">{{ $u->first_name ? ($u->first_name . ' ' . $u->last_name) : $u->name }}</p>
                <p class="pf-hero-meta">
                    <span class="pf-mono">{{ $u->email ?? '—' }}</span>
                    <span style="margin:0 8px;color:#37415122;">•</span>
                    <span>{{ $dept }}</span>
                </p>
                <div class="pf-chip-row">
                    <span class="pf-chip">
                        <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3M5 11h14M5 7h14a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2V9a2 2 0 012-2z"/>
                        </svg>
                        Joined {{ $u->created_at?->format('M Y') ?? '—' }}
                    </span>
                    <span class="pf-chip">
                        <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 11c1.657 0 3-1.343 3-3S13.657 5 12 5s-3 1.343-3 3 1.343 3 3 3z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21a7 7 0 10-14 0"/>
                        </svg>
                        {{ $role }}
                    </span>
                    @if($u->status ?? null)
                        <span class="pf-chip">
                            <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                            </svg>
                            {{ ucfirst($u->status) }}
                        </span>
                    @endif
                </div>
            </div>
        </div>
        <div class="pf-hero-right">
            <a href="{{ route('settings.account') }}" class="pf-btn-primary">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Settings
            </a>
        </div>
    </div>

    <div class="pf-stats">
        <div class="pf-stat s-blue">
            <div class="pf-stat-icon">
                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 21a8 8 0 10-16 0" />
                </svg>
            </div>
            <div>
                <div class="pf-stat-label">Role</div>
                <div class="pf-stat-value">{{ $role }}</div>
            </div>
        </div>
        <div class="pf-stat s-amber">
            <div class="pf-stat-icon">
                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 7h18M3 12h18M3 17h18" />
                </svg>
            </div>
            <div>
                <div class="pf-stat-label">Department</div>
                <div class="pf-stat-value">{{ $dept }}</div>
            </div>
        </div>
        <div class="pf-stat s-green">
            <div class="pf-stat-icon">
                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2 8.5C2 6.567 3.567 5 5.5 5h13C20.433 5 22 6.567 22 8.5v7c0 1.933-1.567 3.5-3.5 3.5h-13C3.567 19 2 17.433 2 15.5v-7z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 12h.01M10 12h.01M14 12h.01M18 12h.01" />
                </svg>
            </div>
            <div>
                <div class="pf-stat-label">Phone</div>
                <div class="pf-stat-value pf-mono" style="font-size:1.05rem;">{{ $u->phone ?? '—' }}</div>
            </div>
        </div>
        <div class="pf-stat s-red">
            <div class="pf-stat-icon">
                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="4" width="18" height="18" rx="2" />
                    <path stroke-linecap="round" d="M16 2v4M8 2v4M3 10h18" />
                </svg>
            </div>
            <div>
                <div class="pf-stat-label">Member Since</div>
                <div class="pf-stat-value" style="font-size:1.0rem;">{{ $u->created_at?->format('M d, Y') ?? '—' }}</div>
            </div>
        </div>
    </div>

    <div class="pf-two-col">
        <div class="pf-card">
            <div class="pf-card-head">
                <p class="pf-card-title"><span class="pf-dot"></span> Personal Information</p>
                <button onclick="openPersonalInfoModal()" class="pf-btn-sec" style="padding:7px 12px;border-radius:9px;">
                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                    </svg>
                    View All
                </button>
            </div>
            <div class="pf-card-body">
                <div style="display:grid;gap:12px;">
                    <div>
                        <p style="font-size:0.7rem;font-weight:700;text-transform:uppercase;color:#9ca3af;margin:0 0 4px;letter-spacing:0.05em;">First Name</p>
                        <p style="font-size:0.95rem;font-weight:600;color:#111827;margin:0;">{{ $u->first_name ?? '—' }}</p>
                    </div>
                    <div>
                        <p style="font-size:0.7rem;font-weight:700;text-transform:uppercase;color:#9ca3af;margin:0 0 4px;letter-spacing:0.05em;">Last Name</p>
                        <p style="font-size:0.95rem;font-weight:600;color:#111827;margin:0;">{{ $u->last_name ?? '—' }}</p>
                    </div>
                    <div>
                        <p style="font-size:0.7rem;font-weight:700;text-transform:uppercase;color:#9ca3af;margin:0 0 4px;letter-spacing:0.05em;">Email</p>
                        <p style="font-size:0.95rem;font-weight:600;color:#111827;margin:0;word-break:break-all;">{{ $u->email ?? '—' }}</p>
                    </div>
                    <div>
                        <p style="font-size:0.7rem;font-weight:700;text-transform:uppercase;color:#9ca3af;margin:0 0 4px;letter-spacing:0.05em;">Phone</p>
                        <p style="font-size:0.95rem;font-weight:600;color:#111827;margin:0;">{{ $u->phone ?? '—' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="pf-card">
            <div class="pf-card-head">
                <p class="pf-card-title"><span class="pf-dot"></span> My Documents</p>
                <a href="{{ route('employee.attachments.index') }}" class="pf-btn-sec" style="padding:7px 12px;border-radius:9px;">
                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                    </svg>
                    View All
                </a>
            </div>
            <div class="pf-card-body">
                @php
                    use App\Models\EmployeeAttachment;
                    $attachmentTypes = EmployeeAttachment::attachmentTypes();
                    $existingByKey   = EmployeeAttachment::where('user_id', $u->id)
                        ->orderByDesc('created_at')
                        ->get()
                        ->groupBy('attachment_key')
                        ->map(fn($g) => $g->first());
                    $flat = $existingByKey->flatten();
                    $approvedCount = $flat->where('status','approved')->count();
                    $pendingCount  = $flat->where('status','pending')->count();
                    $rejectedCount = $flat->where('status','rejected')->count();
                    $uploadedKeys  = $existingByKey->keys()->count();
                    $totalTypes    = count($attachmentTypes);
                    $missingCount  = $totalTypes - $uploadedKeys;
                @endphp
                
                <div style="display:grid;gap:12px;">
                    <div style="display:flex;align-items:center;gap:10px;padding:10px;background:#f0fdf4;border-radius:10px;border:1px solid #bbf7d0;">
                        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" style="color:#16a34a;flex-shrink:0;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                        <div style="flex:1;">
                            <div style="font-size:0.75rem;font-weight:700;color:#16a34a;text-transform:uppercase;">Approved</div>
                            <div style="font-size:1rem;font-weight:800;color:#111827;">{{ $approvedCount }}</div>
                        </div>
                    </div>

                    <div style="display:flex;align-items:center;gap:10px;padding:10px;background:#fffbeb;border-radius:10px;border:1px solid #fde68a;">
                        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" style="color:#d97706;flex-shrink:0;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div style="flex:1;">
                            <div style="font-size:0.75rem;font-weight:700;color:#d97706;text-transform:uppercase;">Under Review</div>
                            <div style="font-size:1rem;font-weight:800;color:#111827;">{{ $pendingCount }}</div>
                        </div>
                    </div>

                    <div style="display:flex;align-items:center;gap:10px;padding:10px;background:#fff0f0;border-radius:10px;border:1px solid #fecaca;">
                        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" style="color:#c8292a;flex-shrink:0;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4v2m0-10a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div style="flex:1;">
                            <div style="font-size:0.75rem;font-weight:700;color:#c8292a;text-transform:uppercase;">Action Needed</div>
                            <div style="font-size:1rem;font-weight:800;color:#111827;">{{ $rejectedCount + $missingCount }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div></div>

<!-- Personal Information Modal -->
<div id="personalInfoModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.6);backdrop-filter:blur(4px);z-index:9999;align-items:center;justify-content:center;padding:16px;">
    <div style="background:#fff;border-radius:16px;width:100%;max-width:800px;max-height:85vh;overflow-y:auto;box-shadow:0 20px 60px rgba(0,0,0,0.3);animation:slideUp 0.3s ease-out;position:relative;z-index:10000;">
        <div style="padding:20px;border-bottom:1px solid #f3f4f6;display:flex;align-items:center;justify-content:space-between;">
            <h2 style="font-size:1.2rem;font-weight:800;color:#111827;margin:0;">Personal Information</h2>
            <button onclick="closePersonalInfoModal()" style="background:none;border:none;cursor:pointer;padding:4px;">
                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        
        <div style="padding:20px;display:grid;gap:16px;">
            <!-- Full Name Section -->
            <div>
                <p style="font-size:0.7rem;font-weight:700;text-transform:uppercase;color:#9ca3af;margin:0 0 8px;letter-spacing:0.05em;">Full Name</p>
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px;">
                    <div>
                        <p style="font-size:0.75rem;color:#9ca3af;margin:0 0 4px;">First Name</p>
                        <p style="font-size:0.95rem;font-weight:600;color:#111827;margin:0;">{{ $u->first_name ?? '—' }}</p>
                    </div>
                    <div>
                        <p style="font-size:0.75rem;color:#9ca3af;margin:0 0 4px;">Middle Name</p>
                        <p style="font-size:0.95rem;font-weight:600;color:#111827;margin:0;">{{ $u->middle_name ?? '—' }}</p>
                    </div>
                    <div>
                        <p style="font-size:0.75rem;color:#9ca3af;margin:0 0 4px;">Last Name</p>
                        <p style="font-size:0.95rem;font-weight:600;color:#111827;margin:0;">{{ $u->last_name ?? '—' }}</p>
                    </div>
                </div>
            </div>

            <!-- Contact Information Section -->
            <div>
                <p style="font-size:0.7rem;font-weight:700;text-transform:uppercase;color:#9ca3af;margin:0 0 8px;letter-spacing:0.05em;">Contact Information</p>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                    <div>
                        <p style="font-size:0.75rem;color:#9ca3af;margin:0 0 4px;">Email</p>
                        <p style="font-size:0.95rem;font-weight:600;color:#111827;margin:0;word-break:break-all;">{{ $u->email ?? '—' }}</p>
                    </div>
                    <div>
                        <p style="font-size:0.75rem;color:#9ca3af;margin:0 0 4px;">Phone</p>
                        <p style="font-size:0.95rem;font-weight:600;color:#111827;margin:0;">{{ $u->phone ?? '—' }}</p>
                    </div>
                </div>
            </div>

            <!-- Personal Details Section -->
            <div>
                <p style="font-size:0.7rem;font-weight:700;text-transform:uppercase;color:#9ca3af;margin:0 0 8px;letter-spacing:0.05em;">Personal Details</p>
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px;">
                    <div>
                        <p style="font-size:0.75rem;color:#9ca3af;margin:0 0 4px;">Gender</p>
                        <p style="font-size:0.95rem;font-weight:600;color:#111827;margin:0;">{{ ucfirst($u->gender ?? '—') }}</p>
                    </div>
                    <div>
                        <p style="font-size:0.75rem;color:#9ca3af;margin:0 0 4px;">Civil Status</p>
                        <p style="font-size:0.95rem;font-weight:600;color:#111827;margin:0;">{{ ucfirst($u->civil_status ?? '—') }}</p>
                    </div>
                    <div>
                        <p style="font-size:0.75rem;color:#9ca3af;margin:0 0 4px;">Date of Birth</p>
                        <p style="font-size:0.95rem;font-weight:600;color:#111827;margin:0;">{{ $u->date_of_birth?->format('M d, Y') ?? '—' }}</p>
                    </div>
                </div>
                @if($u->civil_status === 'married' && $u->spouse_name)
                    <div style="margin-top:12px;">
                        <p style="font-size:0.75rem;color:#9ca3af;margin:0 0 4px;">Spouse Name</p>
                        <p style="font-size:0.95rem;font-weight:600;color:#111827;margin:0;">{{ $u->spouse_name }}</p>
                    </div>
                @endif
            </div>

            <!-- Birth & Education Section -->
            <div>
                <p style="font-size:0.7rem;font-weight:700;text-transform:uppercase;color:#9ca3af;margin:0 0 8px;letter-spacing:0.05em;">Birth & Education</p>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                    <div>
                        <p style="font-size:0.75rem;color:#9ca3af;margin:0 0 4px;">Place of Birth</p>
                        <p style="font-size:0.95rem;font-weight:600;color:#111827;margin:0;">{{ $u->place_of_birth ?? '—' }}</p>
                    </div>
                    <div>
                        <p style="font-size:0.75rem;color:#9ca3af;margin:0 0 4px;">Educational Attainment</p>
                        <p style="font-size:0.95rem;font-weight:600;color:#111827;margin:0;">{{ $u->educational_attainment ?? '—' }}</p>
                    </div>
                </div>
            </div>

            <!-- Address Section -->
            <div>
                <p style="font-size:0.7rem;font-weight:700;text-transform:uppercase;color:#9ca3af;margin:0 0 8px;letter-spacing:0.05em;">Address</p>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:12px;">
                    <div>
                        <p style="font-size:0.75rem;color:#9ca3af;margin:0 0 4px;">Street / House No.</p>
                        <p style="font-size:0.95rem;font-weight:600;color:#111827;margin:0;">{{ $u->address_street ?? '—' }}</p>
                    </div>
                    <div>
                        <p style="font-size:0.75rem;color:#9ca3af;margin:0 0 4px;">Barangay</p>
                        <p style="font-size:0.95rem;font-weight:600;color:#111827;margin:0;">{{ $u->address_barangay ?? '—' }}</p>
                    </div>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px;">
                    <div>
                        <p style="font-size:0.75rem;color:#9ca3af;margin:0 0 4px;">City</p>
                        <p style="font-size:0.95rem;font-weight:600;color:#111827;margin:0;">{{ $u->address_city ?? '—' }}</p>
                    </div>
                    <div>
                        <p style="font-size:0.75rem;color:#9ca3af;margin:0 0 4px;">Province</p>
                        <p style="font-size:0.95rem;font-weight:600;color:#111827;margin:0;">{{ $u->address_province ?? '—' }}</p>
                    </div>
                    <div>
                        <p style="font-size:0.75rem;color:#9ca3af;margin:0 0 4px;">Postal Code</p>
                        <p style="font-size:0.95rem;font-weight:600;color:#111827;margin:0;">{{ $u->address_postal_code ?? '—' }}</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div style="padding:16px;border-top:1px solid #f3f4f6;display:flex;gap:10px;justify-content:flex-end;">
            <a href="{{ route('employee.profile.edit') }}" style="display:inline-flex;align-items:center;justify-content:center;padding:10px 16px;background:#c8292a;color:#fff;border:none;border-radius:10px;font-weight:600;font-size:0.82rem;cursor:pointer;transition:all 0.15s;text-decoration:none;" onmouseover="this.style.background='#a81f20'" onmouseout="this.style.background='#c8292a'">
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" style="margin-right:6px;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z" />
                </svg>
                Edit
            </a>
        </div>
    </div>
</div>

<style>
    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>

<script>
    function openPersonalInfoModal() {
        const modal = document.getElementById('personalInfoModal');
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    function closePersonalInfoModal() {
        const modal = document.getElementById('personalInfoModal');
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }

    // Close modal when clicking outside
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('personalInfoModal');
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                closePersonalInfoModal();
            }
        });

        // Close modal on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closePersonalInfoModal();
            }
        });
    });
</script>

@endsection