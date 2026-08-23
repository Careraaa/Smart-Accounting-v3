<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daily Time Record - {{ $employee->last_name }}, {{ $employee->first_name }}</title>
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('images/knights-icon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        :root { --black: #0a0a0a; --gray-dark: #3a3a3a; --gray-mid: #707070; --gray-rule: #b8b8b8; --gray-bg: #f7f7f7; --page-w: 3.2in; --page-h: 8.5in; --font-body: 'Sora', sans-serif; }
        body { margin: 0; font-family: var(--font-body); font-size: 10px; color: var(--black); background: #efefef; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        .controls { max-width: var(--page-w); margin: 28px auto 12px; display: flex; justify-content: flex-end; gap: 8px; }
        .btn { display: inline-flex; align-items: center; gap: 6px; padding: 8px 18px; border-radius: 4px; font: 600 12px var(--font-body); cursor: pointer; text-decoration: none; border: 1.5px solid transparent; }
        .btn-primary { background: var(--black); color: white; }
        .btn-ghost { background: white; color: var(--gray-dark); border-color: #e0e0e0; }
        .page { width: var(--page-w); min-height: var(--page-h); margin: 0 auto 40px; padding: .22in .2in; background: white; box-shadow: 0 2px 24px rgba(0,0,0,.08); }
        .form-heading { position: relative; text-align: center; margin-bottom: 9px; }
        .form-code { position: absolute; top: 0; left: 0; font: 600 10px Georgia, serif; }
        h1 { margin: 0; font: 700 15px Georgia, serif; letter-spacing: .3px; }
        .employee-meta { margin-bottom: 10px; font: 11px Georgia, serif; }
        .meta-row { display: flex; align-items: baseline; gap: 5px; height: 19px; }
        .meta-label { white-space: nowrap; }
        .meta-line { flex: 1; border-bottom: 1px solid #888; min-width: 0; }
        .meta-value { font-weight: 600; min-width: 150px; border-bottom: 1px solid #888; padding: 0 4px 1px; }
        table { width: 100%; border-collapse: collapse; table-layout: fixed; font: 10px Georgia, serif; }
        th, td { border: 1px solid #777; text-align: center; padding: 0; height: 16px; }
        thead th { font-weight: 600; height: 19px; }
        thead tr:first-child th { height: 17px; }
        .day-col { width: 8%; }
        .time-col { width: 15%; }
        .hours-col { width: 13%; }
        .min-col { width: 12%; }
        .total-row td { border: 0; border-top: 1px solid #777; height: 20px; text-align: right; padding-right: 4px; }
        .certification { margin-top: 17px; font: 10px Georgia, serif; line-height: 1.2; text-align: justify; }
        .signature { margin-top: 22px; text-align: center; font: 10px Georgia, serif; }
        .signature-line { border-top: 1px solid #333; padding-top: 3px; }
        .signature + .signature { margin-top: 22px; }
        .muted { color: #777; }
        @media print { body { background: white; } .controls { display: none !important; } .page { width: var(--page-w); min-height: var(--page-h); margin: 0; box-shadow: none; padding: .22in .2in; } }
        @page { size: 3.2in 8.5in; margin: 0; }
    </style>
</head>
<body>
    <div class="controls">
        <button class="btn btn-primary" onclick="window.print()">Print DTR</button>
    </div>

    <main class="page">
        <div class="form-heading">
            <h1>DAILY TIME RECORD</h1>
        </div>

        <div class="employee-meta">
            <div class="meta-row"><span class="meta-label">Name</span><span class="meta-value">{{ $employee->last_name }}, {{ $employee->first_name }}</span></div>
            <div class="meta-row"><span class="meta-label">For the month of</span><span class="meta-value">{{ $month->format('F Y') }}</span></div>
            <div class="meta-row"><span class="meta-label">Office Hours (regular days)</span><span class="meta-value">{{ $officeHours }}</span></div>
            <div class="meta-row"><span class="meta-label">Arrival &amp; Departure</span><span class="meta-line"></span></div>
            <div class="meta-row"><span class="meta-label">Saturdays</span><span class="meta-line"></span></div>
        </div>

        @php $totalHours = 0; $totalMinutes = 0; @endphp
        <table>
            <thead>
                <tr><th rowspan="2" class="day-col"></th><th colspan="2">A M</th><th colspan="2">P M</th><th rowspan="2" class="hours-col">Hours</th><th rowspan="2" class="min-col">Min.</th></tr>
                <tr><th class="time-col">Arri<br>val</th><th class="time-col">Depar<br>ture</th><th class="time-col">Arri<br>val</th><th class="time-col">Depar<br>ture</th></tr>
            </thead>
            <tbody>
                @for($dayNumber = 1; $dayNumber <= $month->daysInMonth; $dayNumber++)
                    @php
                        $dateKey = $month->copy()->setDay($dayNumber)->format('Y-m-d');
                        $attendance = $attendances[$dateKey] ?? null;
                        $timeIn = $attendance && $attendance->time_in ? Carbon\Carbon::parse($attendance->time_in)->format('g:i') : '';
                        $timeOut = $attendance && $attendance->time_out ? Carbon\Carbon::parse($attendance->time_out)->format('g:i') : '';
                        $worked = $attendance ? $attendance->hours_worked : null;
                        $hours = $worked !== null ? (int) floor($worked) : '';
                        $minutes = $worked !== null ? (int) round(($worked - floor($worked)) * 60) : '';
                        if ($worked !== null) { $totalHours += (int) $hours; $totalMinutes += (int) $minutes; }
                    @endphp
                    <tr>
                        <td>{{ $dayNumber }}</td><td>{{ $timeIn }}</td><td></td><td></td><td>{{ $timeOut }}</td><td>{{ $hours }}</td><td>{{ $minutes }}</td>
                    </tr>
                @endfor
                @php $totalHours += intdiv($totalMinutes, 60); $totalMinutes %= 60; @endphp
                <tr class="total-row"><td colspan="5"><strong>Total</strong></td><td><strong>{{ $totalHours }}</strong></td><td><strong>{{ $totalMinutes }}</strong></td></tr>
            </tbody>
        </table>

        <div class="certification">I certify on my honor that the above is true and correct record of the hours of work performed, record of which was made daily at the time of arrival and departure from the office.</div>
        <div class="signature"><div class="signature-line">(Signature)</div><div class="muted" style="margin-top: 14px;">Verified as to the prescribed office hours</div></div>
        <div class="signature"><div class="signature-line">(In-charge)</div></div>
    </main>
</body>
</html>