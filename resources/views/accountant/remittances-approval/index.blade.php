@extends('layouts.layout')

@push('styles')
    @include('remittance-clerk._ui-styles')
@endpush

@section('content')
<div class="col-12">
    <div class="remui-page">
        <div class="remui-backdrop"><div class="remui-grid"></div></div>

        <div class="remui-hero mb-3">
            <div>
                <h5 class="remui-title">Remittance Approval</h5>
                <p class="remui-subtitle mb-0">Approve or reject remittances submitted by the remittance clerk.</p>
            </div>
        </div>

        <div class="card remui-card">
            <div class="card-header">
                <span class="card-title mb-0">Pending for Approval</span>
            </div>
            <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover w-100 mb-0 remui-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Route</th>
                            <th>Vehicle</th>
                            <th>Collection</th>
                            <th>Expenses</th>
                            <th>Net Remittance</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($remittances->where('status', 'pending') as $remittance)
                            <tr>
                                <td class="text-muted" style="font-size:.82rem;">{{ $remittance->remittance_date?->format('M d, Y') }}</td>
                                <td>{{ $remittance->route->route_name }}</td>
                                <td>{{ $remittance->vehicle->plate_number }}</td>
                                <td>₱{{ number_format($remittance->total_collection, 2) }}</td>
                                <td class="text-muted">₱{{ number_format($remittance->total_expenses, 2) }}</td>
                                <td><strong>₱{{ number_format($remittance->net_remittance, 2) }}</strong></td>
                                <td class="text-center">
                                    @php
                                        $statusMap = [
                                            'pending'  => ['bg' => '#fffbeb', 'color' => '#d97706'],
                                            'approved' => ['bg' => '#f0fdf4', 'color' => '#16a34a'],
                                            'rejected' => ['bg' => '#fff1f2', 'color' => '#e11d48'],
                                        ];
                                        $st = $statusMap[$remittance->status] ?? ['bg' => '#f4f5f7', 'color' => '#9898a8'];
                                    @endphp
                                    <span style="display:inline-block; background:{{ $st['bg'] }}; color:{{ $st['color'] }}; font-size:0.7rem; font-weight:700; padding:3px 10px; border-radius:20px; letter-spacing:0.4px; text-transform:uppercase;">
                                        {{ ucfirst($remittance->status) }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    @if($remittance->status === 'pending')
                                        <div class="d-flex justify-content-center gap-1">
                                            <form action="{{ route('remittance-approval.approve', $remittance) }}" method="POST" class="d-inline"
                                                data-sa-confirm="Approve remittance of ₱{{ number_format($remittance->net_remittance, 2) }} for {{ $remittance->route->route_name }} on {{ $remittance->remittance_date?->format('M d, Y') }}? This action will mark it as completed.">
                                                @csrf
                                                <button type="submit" class="emp-action-btn emp-action-approve" title="Approve">
                                                    <i class="feather-check"></i>
                                                </button>
                                            </form>
                                            <button type="button" class="emp-action-btn emp-action-danger" title="Reject"
                                                    onclick="openRemittanceRejectModal({{ $remittance->id }}, '{{ addslashes($remittance->route->route_name) }}', '{{ $remittance->remittance_date?->format('M d, Y') }}', '{{ number_format($remittance->net_remittance, 2) }}')">
                                                <i class="feather-x"></i>
                                            </button>
                                        </div>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</div>
@endsection

{{-- ── Remittance Reject Modal ── --}}
<div class="modal fade" id="remittanceRejectModal" tabindex="-1" aria-hidden="true"
     style="font-family:'Sora',system-ui,sans-serif;">
    <div class="modal-dialog modal-dialog-centered" style="max-width:440px;">
        <div class="modal-content" style="border-radius:16px;border:none;box-shadow:0 20px 60px rgba(0,0,0,0.15);">
            <form id="remittanceRejectForm" method="POST">
                @csrf
                <div class="modal-header" style="border-bottom:1px solid #f3f4f6;padding:18px 22px;">
                    <div style="display:flex;align-items:center;gap:10px;">
                        <div style="width:36px;height:36px;border-radius:10px;background:#fff1f2;border:1px solid #fecaca;display:flex;align-items:center;justify-content:center;color:#c8292a;flex-shrink:0;">
                            <i class="feather-x-circle" style="font-size:16px;"></i>
                        </div>
                        <div>
                            <h6 class="modal-title" style="font-size:0.92rem;font-weight:800;color:#111827;margin:0;">Reject Remittance</h6>
                            <p id="remittanceRejectDesc" style="font-size:0.78rem;color:#9ca3af;margin:0;"></p>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="padding:20px 22px;">
                    <label style="font-size:0.72rem;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;color:#9ca3af;display:block;margin-bottom:6px;">
                        Reason for Rejection <span style="color:#c8292a;">*</span>
                    </label>
                    <textarea name="rejection_reason" id="remittanceRejectReason" class="form-control" rows="3" required
                              placeholder="Explain why this remittance is being rejected…"
                              style="border:1px solid #e5e7eb;border-radius:8px;font-size:0.845rem;font-family:'Sora',sans-serif;color:#111827;padding:9px 12px;resize:vertical;"></textarea>
                    <p style="font-size:0.75rem;color:#9ca3af;margin:6px 0 0;">The remittance clerk will be notified and can resubmit.</p>
                </div>
                <div class="modal-footer" style="border-top:1px solid #f3f4f6;padding:14px 22px;gap:8px;">
                    <button type="button" data-bs-dismiss="modal"
                            style="display:inline-flex;align-items:center;gap:6px;padding:8px 18px;border-radius:9px;font-family:'Sora',sans-serif;font-size:0.82rem;font-weight:700;border:1px solid #e5e7eb;background:#f3f4f6;color:#374151;cursor:pointer;">
                        Cancel
                    </button>
                    <button type="submit"
                            style="display:inline-flex;align-items:center;gap:6px;padding:8px 18px;border-radius:9px;font-family:'Sora',sans-serif;font-size:0.82rem;font-weight:700;border:none;background:#c8292a;color:#fff;box-shadow:0 2px 8px rgba(200,41,42,0.3);cursor:pointer;">
                        <i class="feather-x-circle"></i> Reject
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function openRemittanceRejectModal(id, route, date, amount) {
    const baseUrl = '{{ url('/remittance-approval') }}';
    document.getElementById('remittanceRejectForm').action = baseUrl + '/' + id + '/reject';
    document.getElementById('remittanceRejectDesc').textContent = `₱${amount} · ${route} · ${date}`;
    document.getElementById('remittanceRejectReason').value = '';
    new bootstrap.Modal(document.getElementById('remittanceRejectModal')).show();
}
</script>
@endpush