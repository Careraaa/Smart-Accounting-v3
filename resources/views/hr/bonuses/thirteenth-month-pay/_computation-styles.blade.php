<style>
.tmp-stats { display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-bottom:20px; }
@media (max-width:720px) { .tmp-stats { grid-template-columns:1fr; } }
.tmp-stat { background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:16px; }
.tmp-stat-label { font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;color:#9ca3af;margin-bottom:4px; }
.tmp-stat-value { font-size:1.1rem;font-weight:800;color:#111827;font-family:'DM Mono',monospace; }
.tmp-breakdown { background:#f8f9fb;border:1px solid #e5e7eb;border-radius:12px;padding:16px;margin-top:12px; }
.tmp-breakdown h4 { font-size:0.78rem;font-weight:700;text-transform:uppercase;color:#6b7280;margin:0 0 12px; }
.tmp-line-table { width:100%;border-collapse:collapse;font-size:0.78rem; }
.tmp-line-table th, .tmp-line-table td { padding:8px 10px;border-bottom:1px solid #e5e7eb;text-align:left; }
.tmp-formula-box { font-family:'DM Mono',monospace;background:#111827;color:#f9fafb;padding:12px 16px;border-radius:10px;font-size:0.85rem;margin:10px 0; }
.tmp-badge { display:inline-block;padding:3px 10px;border-radius:20px;font-size:0.72rem;font-weight:700; }
.tmp-badge.pending { background:#fffbeb;color:#d97706;border:1px solid #fde68a; }
.tmp-badge.partial { background:#eff6ff;color:#2563eb;border:1px solid #bfdbfe; }
.tmp-badge.paid { background:#f0fdf4;color:#15803d;border:1px solid #bbf7d0; }
</style>

