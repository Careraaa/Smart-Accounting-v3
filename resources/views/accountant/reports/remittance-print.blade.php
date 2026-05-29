<!DOCTYPE html>
<html><head>
<meta charset="utf-8">
<title>Remittance Report — {{ date('F', mktime(0,0,0,$month,1)) }} {{ $year }}</title>
<style>
body{font-family:'Segoe UI',Arial,sans-serif;font-size:10px;color:#1f2937;padding:30px;margin:0}
h1{font-size:18px;margin:0 0 4px;color:#111827}
.sub{font-size:10px;color:#6b7280;margin:0 0 20px}
table{width:100%;border-collapse:collapse}
th{background:#f3f4f6;text-align:left;font-size:7px;text-transform:uppercase;letter-spacing:0.5px;color:#6b7280;padding:6px 8px;border-bottom:1px solid #e5e7eb}
td{padding:6px 8px;border-bottom:1px solid #f3f4f6;font-size:10px}
tr.total td{font-weight:700;border-top:2px solid #374151;background:#f9fafb}
.txt-right{text-align:right}
.tabular{font-variant-numeric:tabular-nums}
.mono{font-family:'Courier New',monospace}
</style>
</head><body>
<h1>Remittance Report</h1>
<p class="sub">{{ date('F', mktime(0,0,0,$month,1)) }} {{ $year }} &middot; {{ $remittances->count() }} approved {{ Str::plural('record', $remittances->count()) }}</p>
<table>
<thead>
<tr>
<th>Date</th>
<th>Driver</th>
<th>Route</th>
<th class="txt-right">Collection</th>
<th class="txt-right">Expenses</th>
<th class="txt-right">Net Remittance</th>
</tr>
</thead>
<tbody>
@php $tCol=0;$tExp=0;$tNet=0; @endphp
@forelse($remittances as $r)
@php $tCol+=$r->total_collection;$tExp+=$r->total_expenses;$tNet+=$r->net_remittance; @endphp
<tr>
<td>{{ $r->remittance_date?->format('M d, Y') ?? '—' }}</td>
<td>{{ $r->driver?->name ?? '—' }}</td>
<td>{{ $r->route?->route_name ?? '—' }}</td>
<td class="txt-right tabular mono">₱{{ number_format($r->total_collection,2) }}</td>
<td class="txt-right tabular mono">₱{{ number_format($r->total_expenses,2) }}</td>
<td class="txt-right tabular mono">₱{{ number_format($r->net_remittance,2) }}</td>
</tr>
@empty
<tr><td colspan="6" style="text-align:center;padding:30px;color:#9ca3af">No approved remittance records.</td></tr>
@endforelse
<tr class="total">
<td colspan="3">TOTALS ({{ $remittances->count() }} records)</td>
<td class="txt-right tabular mono">₱{{ number_format($tCol,2) }}</td>
<td class="txt-right tabular mono">₱{{ number_format($tExp,2) }}</td>
<td class="txt-right tabular mono">₱{{ number_format($tNet,2) }}</td>
</tr>
</tbody>
</table>
<script>window.print();</script>
</body></html>