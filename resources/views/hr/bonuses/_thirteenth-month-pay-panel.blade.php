@include('hr.bonuses.thirteenth-month-pay._computation-styles')

<div class="bn-card" style="margin-top:24px;">
    <div class="bn-card-header" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
        <p class="bn-card-title">13th Month Pay — {{ $calendarYear }}</p>
        <form method="POST" action="{{ route('payroll.thirteenth-month-pay.compute') }}" style="display:flex;gap:8px;align-items:center;">
            @csrf
            <input type="hidden" name="year" value="{{ $calendarYear }}">
            <input type="hidden" name="return_bonus_edit" value="1">
            <input type="hidden" name="bonus_id" value="{{ $bonus->id }}">
            <button type="submit" class="bn-btn-submit" style="padding:8px 14px;font-size:0.78rem;">Compute All</button>
        </form>
    </div>
    <div class="bn-card-body">
        <form method="GET" action="{{ route('bonuses.edit', $bonus) }}" style="display:flex;gap:8px;margin-bottom:16px;align-items:center;">
            <label style="font-size:0.78rem;font-weight:600;color:#6b7280;">Year</label>
            <input type="number" name="year" value="{{ $calendarYear }}" min="2000" max="2100" class="bn-input" style="width:120px;">
            <button type="submit" class="bn-btn-sec" style="padding:8px 14px;">Load</button>
            <a href="{{ route('payroll.thirteenth-month-pay.index', ['year' => $calendarYear]) }}" class="bn-btn-sec" style="padding:8px 14px;">Full Management Page</a>
        </form>

        @if($editingRecord)
            @php $breakdown = $editingRecord->computation_breakdown ?? []; @endphp
            @include('hr.bonuses.thirteenth-month-pay._computation-breakdown', ['breakdown' => $breakdown])
            <form method="POST" action="{{ route('payroll.thirteenth-month-pay.update', $editingRecord) }}" style="margin-top:16px;">
                @csrf @method('PUT')
                <input type="hidden" name="return_bonus_edit" value="1">
                <input type="hidden" name="bonus_id" value="{{ $bonus->id }}">
                <div class="bn-field">
                    <label class="bn-label">Notes</label>
                    <textarea name="notes" class="bn-textarea">{{ old('notes', $editingRecord->notes) }}</textarea>
                </div>
                <label style="display:flex;align-items:center;gap:8px;font-size:0.82rem;">
                    <input type="checkbox" name="is_eligible" value="1" @checked($editingRecord->is_eligible)> Eligible
                </label>
                <div style="margin-top:12px;display:flex;gap:8px;">
                    <button type="submit" class="bn-btn-submit" style="padding:8px 16px;">Save Record</button>
                    <a href="{{ route('bonuses.edit', ['bonus' => $bonus, 'year' => $calendarYear]) }}" class="bn-btn-cancel">Close</a>
                </div>
            </form>
        @endif

        <table class="bn-table" style="width:100%;border-collapse:collapse;font-size:0.82rem;">
            <thead>
                <tr style="background:#f8f9fb;">
                    <th style="padding:10px 12px;text-align:left;">Employee</th>
                    <th style="padding:10px 12px;text-align:right;">Basic Earned</th>
                    <th style="padding:10px 12px;text-align:right;">13th Month</th>
                    <th style="padding:10px 12px;">Status</th>
                </tr>
            </thead>
            <tbody id="tmpPanelRows">
                @forelse($records as $record)
                <tr class="bn-row-clickable"
                    style="border-top:1px solid #f3f4f6;cursor:pointer;"
                    data-href="{{ route('bonuses.edit', ['bonus' => $bonus, 'year' => $calendarYear, 'record' => $record->id]) }}"
                    tabindex="0" role="link">
                    <td style="padding:10px 12px;">{{ $record->user?->name }}</td>
                    <td style="padding:10px 12px;text-align:right;font-family:'DM Mono',monospace;">₱{{ number_format($record->total_basic_salary_earned, 2) }}</td>
                    <td style="padding:10px 12px;text-align:right;font-family:'DM Mono',monospace;">₱{{ number_format($record->thirteenth_month_pay, 2) }}</td>
                    <td style="padding:10px 12px;"><span class="tmp-badge {{ $record->status }}">{{ ucfirst($record->status) }}</span></td>
                </tr>
                @empty
                <tr><td colspan="4" style="padding:24px;text-align:center;color:#9ca3af;">No records yet. Run Compute All to generate.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
(function () {
    document.querySelectorAll('#tmpPanelRows tr.bn-row-clickable').forEach(function (row) {
        row.addEventListener('click', function () {
            window.location.href = row.dataset.href;
        });
        row.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                window.location.href = row.dataset.href;
            }
        });
    });
})();
</script>

