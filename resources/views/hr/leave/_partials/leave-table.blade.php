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
            <tbody id="lvTbody_{{ $status }}"></tbody>
        </table>
    </div>

    <div id="lvNoRes_{{ $status }}" class="hidden">
        <div class="flex flex-col items-center justify-center py-12 text-center">
            <div class="w-14 h-14 rounded-xl bg-gray-100 flex items-center justify-center text-gray-300 mb-4">
                <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
            </div>
            <p class="text-sm font-semibold text-gray-500" id="lvEmptyTitle_{{ $status }}">{{ $emptyTitle }}</p>
            <p class="text-xs text-gray-400 mt-1" id="lvEmptySub_{{ $status }}">{{ $emptySub }}</p>
        </div>
    </div>

    <div class="flex items-center justify-between px-4 py-3 border-t border-gray-100 bg-gray-50/50 flex-wrap gap-3">
        <div class="text-xs text-gray-400" id="lvInfo_{{ $status }}">Showing <strong class="text-gray-700">0</strong> records</div>
        <nav id="lvNav_{{ $status }}" class="flex items-center gap-1"></nav>
    </div>
</div>

<script>
window.leaveData_{{ $status }} = {!! json_encode($leaves->map(fn($l) => [
    'id' => $l->id,
    'name' => ($l->employee->first_name ?? '') . ' ' . ($l->employee->last_name ?? ''),
    'name_lower' => strtolower(($l->employee->first_name ?? '') . ' ' . ($l->employee->last_name ?? '')),
    'dept' => strtolower($l->employee->department ?? ''),
    'type' => strtolower($l->leave_type ?? ''),
    'type_display' => $l->leave_type ?? '',
    'department_display' => $l->employee->department ?? 'N/A',
    'start_date' => $l->start_date->format('M d'),
    'end_date' => $l->end_date->format('M d, Y'),
    'days' => $l->days,
    'created_at' => $l->created_at->format('M d, Y'),
    'updated_at' => $l->updated_at->format('M d, Y'),
    'status' => $l->status ?? 'pending',
    'show_columns' => $columns,
    'url' => route('leave.show', $l),
])) !!};
</script>
