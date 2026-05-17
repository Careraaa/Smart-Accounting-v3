@php
    $breakdown = $breakdown ?? [];
    $computed = ($breakdown['total_basic_salary'] ?? 0) / 12;
@endphp
<div class="tmp-breakdown">
    <h4>Computation Breakdown</h4>
    <div class="tmp-formula-box">{{ $breakdown['formula'] ?? '(total_basic_salary / 12)' }}</div>
    <div class="tmp-stats" style="margin-top:12px;">
        <div class="tmp-stat">
            <div class="tmp-stat-label">Total Basic Salary</div>
            <div class="tmp-stat-value">₱{{ number_format($breakdown['total_basic_salary'] ?? 0, 2) }}</div>
        </div>
        <div class="tmp-stat">
            <div class="tmp-stat-label">Months Worked</div>
            <div class="tmp-stat-value">{{ number_format($breakdown['months_worked'] ?? 0, 2) }}</div>
        </div>
        <div class="tmp-stat">
            <div class="tmp-stat-label">13th Month Pay</div>
            <div class="tmp-stat-value">₱{{ number_format($computed, 2) }}</div>
        </div>
    </div>
    @if(!empty($breakdown['payroll_lines']))
    <table class="tmp-line-table" style="margin-top:14px;">
        <thead>
            <tr>
                <th>Pay Period</th>
                <th>Basic Salary</th>
                <th>Excluded Items</th>
            </tr>
        </thead>
        <tbody>
            @foreach($breakdown['payroll_lines'] as $line)
            <tr>
                <td>{{ $line['period_start'] }} – {{ $line['period_end'] }}</td>
                <td>₱{{ number_format($line['basic_salary'], 2) }}</td>
                <td style="color:#9ca3af;font-size:0.72rem;">OT, allowances, holiday pay, etc.</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
    @if(!empty($breakdown['notes']))
    <p style="font-size:0.75rem;color:#6b7280;margin:12px 0 0;">{{ $breakdown['notes'] }}</p>
    @endif
</div>

