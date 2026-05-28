@extends('layouts.layout')

@push('styles')
<style>
@keyframes fadeSlideUp {
    0% { opacity: 0; transform: translateY(12px); }
    100% { opacity: 1; transform: translateY(0); }
}
@keyframes scaleIn {
    0% { opacity: 0; transform: scale(0.92); }
    100% { opacity: 1; transform: scale(1); }
}
@keyframes slideInRight {
    0% { opacity: 0; transform: translateX(-10px); }
    100% { opacity: 1; transform: translateX(0); }
}
.stat-card { animation: scaleIn 0.4s cubic-bezier(0.16, 1, 0.3, 1) both; }
.stat-card:nth-child(1) { animation-delay: 0.05s; }
.stat-card:nth-child(2) { animation-delay: 0.1s; }
.fade-up { animation: fadeSlideUp 0.45s cubic-bezier(0.16, 1, 0.3, 1) both; }
.filter-bar { animation: slideInRight 0.4s cubic-bezier(0.16, 1, 0.3, 1) 0.1s both; }
.table-wrap { animation: fadeSlideUp 0.5s cubic-bezier(0.16, 1, 0.3, 1) 0.15s both; }
</style>
@endpush

@section('content')
<div class="max-w-full" data-ls>

    <div class="flex items-start justify-between gap-4 mb-6 flex-wrap fade-up">
        <div>
            <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight m-0">Leave Request Details</h1>
            <p class="text-xs text-gray-400 m-0 mt-0.5">{{ $leave->employee->first_name }} {{ $leave->employee->last_name }} · {{ $leave->leave_type }}</p>
        </div>
        <a href="{{ url()->previous() }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-white text-gray-600 border border-gray-200 rounded-xl text-xs font-semibold no-underline hover:border-gray-600 hover:text-gray-600 hover:bg-gray-50 transition-colors whitespace-nowrap cursor-pointer">
            <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back
        </a>
    </div>

    <div class="grid grid-cols-1 gap-4">

        {{-- Details card --}}
        <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden table-wrap">
            <div class="px-6 py-5 border-b border-gray-50 flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-gray-100 text-gray-400 flex items-center justify-center shrink-0">
                    <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <div>
                    <p class="text-sm font-extrabold text-gray-900 m-0 leading-tight">Leave Details</p>
                    <p class="text-xs text-gray-400 m-0">Submitted {{ $leave->created_at->format('M d, Y') }}</p>
                </div>
                <div class="ml-auto">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider
                        {{ $leave->status === 'approved' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : '' }}
                        {{ $leave->status === 'rejected' ? 'bg-red-50 text-red-700 border border-red-200' : '' }}
                        {{ $leave->status === 'pending' ? 'bg-amber-50 text-amber-700 border border-amber-200' : '' }}">
                        <span class="w-1.5 h-1.5 rounded-full
                            {{ $leave->status === 'approved' ? 'bg-emerald-600' : '' }}
                            {{ $leave->status === 'rejected' ? 'bg-red-600' : '' }}
                            {{ $leave->status === 'pending' ? 'bg-amber-500' : '' }}">
                        </span>
                        {{ ucfirst($leave->status) }}
                    </span>
                </div>
            </div>
            <div class="p-6">

                <div class="text-[0.68rem] font-bold uppercase tracking-[0.12em] text-gray-400 border-b border-gray-50 pb-2.5 mb-5">Employee</div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-5">
                    <div>
                        <span class="block text-[0.7rem] font-bold uppercase tracking-wide text-gray-400 mb-1.5">Full Name</span>
                        <div class="bg-gray-50/50 border border-gray-200 rounded-lg px-3.5 py-2.5"><span class="text-sm text-gray-900 font-medium">{{ $leave->employee->first_name }} {{ $leave->employee->last_name }}</span></div>
                    </div>
                    <div>
                        <span class="block text-[0.7rem] font-bold uppercase tracking-wide text-gray-400 mb-1.5">Department</span>
                        <div class="bg-gray-50/50 border border-gray-200 rounded-lg px-3.5 py-2.5"><span class="text-sm text-gray-900 font-medium">{{ $leave->employee->department ?? 'N/A' }}</span></div>
                    </div>
                </div>

                <div class="text-[0.68rem] font-bold uppercase tracking-[0.12em] text-gray-400 border-b border-gray-50 pb-2.5 mb-5">Leave Info</div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-5">
                    <div>
                        <span class="block text-[0.7rem] font-bold uppercase tracking-wide text-gray-400 mb-1.5">Leave Type</span>
                        <div class="bg-gray-50/50 border border-gray-200 rounded-lg px-3.5 py-2.5"><span class="text-sm text-gray-900 font-medium">{{ $leave->leave_type }}</span></div>
                    </div>
                    <div>
                        <span class="block text-[0.7rem] font-bold uppercase tracking-wide text-gray-400 mb-1.5">Number of Days</span>
                        <div class="bg-gray-50/50 border border-gray-200 rounded-lg px-3.5 py-2.5 font-mono text-xs"><span class="text-sm text-gray-900 font-medium">{{ $leave->start_date->diffInDays($leave->end_date) + 1 }} day(s)</span></div>
                    </div>
                    <div>
                        <span class="block text-[0.7rem] font-bold uppercase tracking-wide text-gray-400 mb-1.5">Start Date</span>
                        <div class="bg-gray-50/50 border border-gray-200 rounded-lg px-3.5 py-2.5 font-mono text-xs"><span class="text-sm text-gray-900 font-medium">{{ $leave->start_date->format('F d, Y') }}</span></div>
                    </div>
                    <div>
                        <span class="block text-[0.7rem] font-bold uppercase tracking-wide text-gray-400 mb-1.5">End Date</span>
                        <div class="bg-gray-50/50 border border-gray-200 rounded-lg px-3.5 py-2.5 font-mono text-xs"><span class="text-sm text-gray-900 font-medium">{{ $leave->end_date->format('F d, Y') }}</span></div>
                    </div>
                </div>

                <div class="text-[0.68rem] font-bold uppercase tracking-[0.12em] text-gray-400 border-b border-gray-50 pb-2.5 mb-5">Reason</div>
                <div class="mb-6">
                    <span class="block text-[0.7rem] font-bold uppercase tracking-wide text-gray-400 mb-1.5">Remarks / Reason</span>
                    <div class="bg-gray-50/50 border border-gray-200 rounded-lg p-3 px-3.5 py-3 text-sm text-gray-600 leading-relaxed whitespace-pre-wrap min-h-[80px]">{{ $leave->reason }}</div>
                </div>

                {{-- Status Info --}}
                @if($leave->status === 'approved')
                <div class="rounded-xl p-4 mb-3 bg-emerald-50 border border-emerald-200">
                    <div class="text-sm font-bold mb-2 text-emerald-700">
                        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" class="inline mr-1"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        Approved
                    </div>
                    <div class="flex justify-between items-center text-xs mb-1"><span class="text-gray-400 font-semibold">By</span><span class="text-gray-900 font-semibold">{{ $leave->approvedBy->first_name ?? 'Admin' }}</span></div>
                    <div class="flex justify-between items-center text-xs"><span class="text-gray-400 font-semibold">Date</span><span class="text-gray-900 font-semibold">{{ $leave->updated_at->format('M d, Y') }}</span></div>
                </div>
                @elseif($leave->status === 'rejected')
                <div class="rounded-xl p-4 mb-3 bg-red-50 border border-red-200">
                    <div class="text-sm font-bold mb-2 text-red-700">
                        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" class="inline mr-1"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        Rejected
                    </div>
                    <div class="flex justify-between items-center text-xs mb-1"><span class="text-gray-400 font-semibold">By</span><span class="text-gray-900 font-semibold">{{ $leave->approvedBy->first_name ?? 'Admin' }}</span></div>
                    <div class="flex justify-between items-center text-xs"><span class="text-gray-400 font-semibold">Date</span><span class="text-gray-900 font-semibold">{{ $leave->updated_at->format('M d, Y') }}</span></div>
                    @if($leave->rejection_reason)
                    <div class="text-xs text-gray-600 mt-2 pt-2 border-t border-red-200/50 leading-relaxed"><strong>Reason:</strong> {{ $leave->rejection_reason }}</div>
                    @endif
                </div>
                @endif

                {{-- Action Buttons --}}
                @if($leave->status === 'pending')
                <div class="mt-6 pt-6 border-t border-gray-50">
                    <div class="grid grid-cols-2 gap-2.5 max-w-[400px] ml-auto max-sm:grid-cols-1">
                        {{-- Reject --}}
                        <button type="button" id="rejectBtn"
                            class="flex items-center justify-center gap-2 px-4 py-2.5 bg-red-600/80 text-white border-none rounded-xl text-xs font-bold cursor-pointer hover:bg-red-700/80 transition-colors whitespace-nowrap">
                            <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            Reject Leave
                        </button>

                        {{-- Approve --}}
                        <button type="button" id="approveBtn"
                            class="flex items-center justify-center gap-2 px-4 py-2.5 bg-emerald-600 text-white border-none rounded-xl text-xs font-bold cursor-pointer hover:bg-emerald-700 transition-colors whitespace-nowrap">
                            <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            Approve Leave
                        </button>
                    </div>
                </div>
                @endif

            </div>
        </div>
    </div>

    {{-- Approval Modal --}}
    <div class="fixed inset-0 bg-black/50 hidden items-center justify-center z-[9999] p-5" id="approvalModal">
        <div class="bg-white rounded-2xl shadow-xl max-w-lg w-full p-7">
            <div class="mb-5">
                <h3 class="text-lg font-extrabold text-gray-900 m-0 mb-1">Approve Leave Request</h3>
                <p class="text-xs text-gray-400 m-0">Are you sure you want to approve this leave request?</p>
            </div>
            <form action="{{ route('leave.approve', $leave) }}" method="POST" id="approvalForm">
                @csrf
                <div class="mb-6">
                    <p class="text-sm text-gray-600 leading-relaxed m-0">
                        <strong>Employee:</strong> {{ $leave->employee->first_name }} {{ $leave->employee->last_name }}<br>
                        <strong>Leave Type:</strong> {{ $leave->leave_type }}<br>
                        <strong>Period:</strong> {{ $leave->start_date->format('M d, Y') }} to {{ $leave->end_date->format('M d, Y') }}
                    </p>
                </div>
                <div class="flex gap-2.5 justify-end">
                    <button type="button" id="approvalCancelBtn" class="px-4 py-2.5 border-none rounded-xl text-xs font-bold cursor-pointer transition-colors bg-gray-100 text-gray-600 hover:bg-gray-200">Cancel</button>
                    <button type="submit" class="px-4 py-2.5 border-none rounded-xl text-xs font-bold cursor-pointer transition-colors text-white bg-emerald-600 hover:bg-emerald-700">Approve Leave</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Rejection Modal --}}
    <div class="fixed inset-0 bg-black/50 hidden items-center justify-center z-[9999] p-5" id="rejectionModal">
        <div class="bg-white rounded-2xl shadow-xl max-w-lg w-full p-7">
            <div class="mb-5">
                <h3 class="text-lg font-extrabold text-gray-900 m-0 mb-1">Reject Leave Request</h3>
                <p class="text-xs text-gray-400 m-0">Please provide a reason for rejecting this leave request</p>
            </div>
            <form action="{{ route('leave.reject', $leave) }}" method="POST" id="rejectionForm">
                @csrf
                <div class="mb-6">
                    <label for="rejection_reason" class="block text-xs font-bold uppercase tracking-wide text-gray-500 mb-1.5">Rejection Reason <span class="text-amber-600">*</span></label>
                    <textarea name="rejection_reason" id="rejection_reason"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-xs text-gray-900 bg-gray-50/50 outline-none resize-y min-h-[80px] focus:border-gray-400 focus:ring-2 focus:ring-gray-200/50 focus:bg-white transition-colors @error('rejection_reason') border-amber-500 @enderror"
                        placeholder="Enter reason for rejection…" required></textarea>
                    @error('rejection_reason')
                        <span class="text-xs text-amber-600 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>
                <div class="flex gap-2.5 justify-end">
                    <button type="button" id="cancelBtn" class="px-4 py-2.5 border-none rounded-xl text-xs font-bold cursor-pointer transition-colors bg-gray-100 text-gray-600 hover:bg-gray-200">Cancel</button>
                    <button type="submit" class="px-4 py-2.5 border-none rounded-xl text-xs font-bold cursor-pointer transition-colors bg-red-600/80 text-white hover:bg-red-700/80">Reject Leave</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    // Approve button and modal
    const approveBtn = document.getElementById('approveBtn');
    const approvalModal = document.getElementById('approvalModal');
    const approvalCancelBtn = document.getElementById('approvalCancelBtn');

    if (approveBtn) {
        approveBtn.addEventListener('click', function() {
            approvalModal.classList.remove('hidden');
            approvalModal.classList.add('flex');
        });
    }

    if (approvalCancelBtn) {
        approvalCancelBtn.addEventListener('click', function() {
            approvalModal.classList.add('hidden');
            approvalModal.classList.remove('flex');
        });
    }

    // Reject button and modal
    const rejectBtn = document.getElementById('rejectBtn');
    const cancelBtn = document.getElementById('cancelBtn');
    const rejectionModal = document.getElementById('rejectionModal');

    if (rejectBtn) {
        rejectBtn.addEventListener('click', function() {
            rejectionModal.classList.remove('hidden');
            rejectionModal.classList.add('flex');
            document.querySelector('#rejection_reason').focus();
        });
    }

    if (cancelBtn) {
        cancelBtn.addEventListener('click', function() {
            rejectionModal.classList.add('hidden');
            rejectionModal.classList.remove('flex');
            document.querySelector('#rejection_reason').value = '';
        });
    }

    // Close modals on overlay click
    const approvalOverlay = document.getElementById('approvalModal');
    const rejectionOverlay = document.getElementById('rejectionModal');

    if (approvalOverlay) {
        approvalOverlay.addEventListener('click', function(e) {
            if (e.target === approvalOverlay) {
                approvalModal.classList.add('hidden');
                approvalModal.classList.remove('flex');
            }
        });
    }

    if (rejectionOverlay) {
        rejectionOverlay.addEventListener('click', function(e) {
            if (e.target === rejectionOverlay) {
                rejectionModal.classList.add('hidden');
                rejectionModal.classList.remove('flex');
                document.querySelector('#rejection_reason').value = '';
            }
        });
    }

    // Close modals on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            approvalModal.classList.add('hidden');
            approvalModal.classList.remove('flex');
            rejectionModal.classList.add('hidden');
            rejectionModal.classList.remove('flex');
            document.querySelector('#rejection_reason').value = '';
        }
    });
</script>
@endpush
