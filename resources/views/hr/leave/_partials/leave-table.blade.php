@php
$showEmployee = in_array('employee', $columns);
$showDepartment = in_array('department', $columns);
$showLeaveType = in_array('leave_type', $columns);
$showDates = in_array('dates', $columns);
$showDays = in_array('days', $columns);
$showApplied = in_array('applied', $columns);
$showPayStatus = in_array('pay_status', $columns);
$showApprovedDate = in_array('approved_date', $columns);
$showRejectedDate = in_array('rejected_date', $columns);
@endphp

<div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200">
                    @if($showEmployee)<th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500">Employee</th>@endif
                    @if($showDepartment)<th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500">Department</th>@endif
                    @if($showLeaveType)<th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500">Leave Type</th>@endif
                    @if($showDates)<th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500">Dates</th>@endif
                    @if($showDays)<th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500">Days</th>@endif
                    @if($showPayStatus)<th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500">Pay Status</th>@endif
                    @if($showApplied)<th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500">Applied</th>@endif
                    @if($showApprovedDate)<th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500">Approved Date</th>@endif
                    @if($showRejectedDate)<th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500">Rejected Date</th>@endif
                </tr>
            </thead>
            <tbody>
                @forelse($leaves as $leave)
                @php
                    $days = $leave->start_date->diffInDays($leave->end_date) + 1;
                @endphp
                <tr class="border-b border-gray-100 hover:bg-gray-50/50 transition-colors cursor-pointer"
                    data-name="{{ strtolower(($leave->employee->first_name ?? '') . ' ' . ($leave->employee->last_name ?? '')) }}"
                    data-dept="{{ strtolower($leave->employee->department ?? '') }}"
                    data-type="{{ strtolower($leave->leave_type ?? '') }}"
                    onclick="window.location='{{ route('leave.show', $leave) }}'">
                    @if($showEmployee)
                    <td class="px-4 py-3.5">
                        <div class="font-semibold text-gray-900 text-sm">{{ $leave->employee->first_name ?? 'N/A' }} {{ $leave->employee->last_name ?? '' }}</div>
                    </td>
                    @endif
                    @if($showDepartment)
                    <td class="px-4 py-3.5">
                        <span class="text-xs text-gray-400 font-mono">{{ $leave->employee->department ?? 'N/A' }}</span>
                    </td>
                    @endif
                    @if($showLeaveType)
                    <td class="px-4 py-3.5">
                        @if($status === 'pending')
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">{{ $leave->leave_type }}</span>
                        @elseif($status === 'approved')
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">{{ $leave->leave_type }}</span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-red-50 text-red-700 border border-red-200">{{ $leave->leave_type }}</span>
                        @endif
                    </td>
                    @endif
                    @if($showDates)
                    <td class="px-4 py-3.5">
                        <span class="text-sm text-gray-700 font-mono">{{ $leave->start_date->format('M d') }} – {{ $leave->end_date->format('M d, Y') }}</span>
                    </td>
                    @endif
                    @if($showDays)
                    <td class="px-4 py-3.5">
                        <span class="font-bold text-gray-900">{{ $days }}<span class="text-gray-400 font-normal text-xs">d</span></span>
                    </td>
                    @endif
                    @if($showPayStatus)
                    <td class="px-4 py-3.5">
                        @if($leave->status === 'paid')
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">Paid</span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">Approved</span>
                        @endif
                    </td>
                    @endif
                    @if($showApplied)
                    <td class="px-4 py-3.5">
                        <span class="text-xs text-gray-400 font-mono">{{ $leave->created_at->format('M d, Y') }}</span>
                    </td>
                    @endif
                    @if($showApprovedDate)
                    <td class="px-4 py-3.5">
                        <span class="text-xs text-gray-400 font-mono">{{ $leave->updated_at->format('M d, Y') }}</span>
                    </td>
                    @endif
                    @if($showRejectedDate)
                    <td class="px-4 py-3.5">
                        <span class="text-xs text-gray-400 font-mono">{{ $leave->updated_at->format('M d, Y') }}</span>
                    </td>
                    @endif
                </tr>
                @empty
                <tr>
                    <td colspan="{{ count($columns) }}">
                        <div class="flex flex-col items-center justify-center py-16 text-center">
                            <div class="w-14 h-14 rounded-xl bg-gray-100 flex items-center justify-center text-gray-300 mb-4">
                                <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                            </div>
                            <p class="text-sm font-semibold text-gray-700">{{ $emptyTitle }}</p>
                            <p class="text-xs text-gray-400 mt-1">{{ $emptySub }}</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- No results message --}}
    <div class="no-results hidden">
        <div class="flex flex-col items-center justify-center py-12 text-center">
            <div class="w-14 h-14 rounded-xl bg-gray-100 flex items-center justify-center text-gray-300 mb-4">
                <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
            </div>
            <p class="text-sm font-semibold text-gray-700">No results found</p>
            <p class="text-xs text-gray-400 mt-1">Try a different search or filter.</p>
        </div>
    </div>

    {{-- Pagination --}}
    @if(method_exists($leaves, 'hasPages') && $leaves->hasPages())
    <div class="flex items-center justify-end px-4 py-3 border-t border-gray-100 bg-gray-50/50">
        {{ $leaves->appends(['tab' => $status])->links('pagination::tailwind') }}
    </div>
    @endif
</div>
