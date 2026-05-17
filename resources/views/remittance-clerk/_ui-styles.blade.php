<style>
@import url('https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Sora:wght@400;600;700;800&display=swap');
*, *::before, *::after { box-sizing: border-box; }

:root {
    --rem-accent: #c8292a;
    --rem-ink: #111827;
    --rem-muted: #6b7280;
    --rem-border: #e5e7eb;
    --rem-bg: #f8f9fb;
}

/* ── Page shell ─────────────────────────────────────────────── */
.remui-page { font-family: 'Sora', sans-serif; }

/* ── Flash messages ─────────────────────────────────────────── */
.prl-flash { display:flex;align-items:center;gap:10px;padding:12px 16px;border-radius:10px;font-size:0.82rem;font-weight:500;margin-bottom:20px;animation:flashIn 0.3s ease; }
.prl-flash.success { background:#f0fdf4;border:1px solid #bbf7d0;color:#15803d; }
.prl-flash.error   { background:#fff0f0;border:1px solid #fecaca;color:#c8292a; }
.prl-flash.info    { background:#eff6ff;border:1px solid #bfdbfe;color:#1d4ed8; }
@keyframes flashIn { from{opacity:0;transform:translateY(-6px)}to{opacity:1;transform:translateY(0)} }

/* ── Topbar ─────────────────────────────────────────────────── */
.prl-topbar { display:flex;align-items:flex-start;justify-content:space-between;gap:16px;margin-bottom:24px;flex-wrap:wrap; }
.prl-topbar-title { font-size:1.35rem;font-weight:800;color:#111827;letter-spacing:-0.02em;margin:0 0 2px; }
.prl-topbar-sub   { font-size:0.78rem;color:#9ca3af;margin:0; }
.prl-topbar-actions { display:flex;gap:8px;flex-wrap:wrap;align-items:center; }

/* ── Action hero card (dark) ────────────────────────────────── */
.prl-generate-card {
    background:#111827;border-radius:16px;padding:24px 28px;
    display:flex;align-items:center;justify-content:space-between;gap:20px;
    margin-bottom:24px;flex-wrap:wrap;position:relative;overflow:hidden;
}
.prl-generate-card::before { content:'';position:absolute;top:-60px;right:-60px;width:200px;height:200px;border-radius:50%;background:rgba(200,41,42,0.15);pointer-events:none; }
.prl-generate-left { position:relative;z-index:1; }
.prl-generate-eyebrow { font-size:0.67rem;font-weight:700;text-transform:uppercase;letter-spacing:0.12em;color:#c8292a;margin-bottom:6px; }
.prl-generate-title  { font-size:1.1rem;font-weight:800;color:#fff;margin:0 0 4px;letter-spacing:-0.02em; }
.prl-generate-sub    { font-size:0.78rem;color:#6b7280; }
.prl-generate-right  { position:relative;z-index:1; }

/* ── Stats grid ─────────────────────────────────────────────── */
.prl-stats { display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:24px; }
@media(max-width:1100px) { .prl-stats { grid-template-columns:repeat(2,1fr); } }
@media(max-width:600px)  { .prl-stats { grid-template-columns:1fr; } }
.prl-stats-3 { grid-template-columns:repeat(3,1fr); }
@media(max-width:900px)  { .prl-stats-3 { grid-template-columns:repeat(2,1fr); } }

.prl-stat { background:#fff;border:1px solid #e5e7eb;border-radius:14px;padding:18px 20px;display:flex;align-items:flex-start;gap:14px;position:relative;overflow:hidden;transition:box-shadow 0.15s; }
.prl-stat:hover { box-shadow:0 4px 20px rgba(0,0,0,0.07); }
.prl-stat::after { content:'';position:absolute;bottom:0;left:0;right:0;height:3px;border-radius:0 0 14px 14px; }
.prl-stat.s-red::after    { background:#c8292a; }
.prl-stat.s-green::after  { background:#16a34a; }
.prl-stat.s-amber::after  { background:#d97706; }
.prl-stat.s-blue::after   { background:#0284c7; }
.prl-stat.s-slate::after  { background:#64748b; }
.prl-stat.s-purple::after { background:#7c3aed; }
.prl-stat-icon { width:40px;height:40px;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0; }
.prl-stat.s-red    .prl-stat-icon { background:#fff0f0;color:#c8292a; }
.prl-stat.s-green  .prl-stat-icon { background:#f0fdf4;color:#16a34a; }
.prl-stat.s-amber  .prl-stat-icon { background:#fffbeb;color:#d97706; }
.prl-stat.s-blue   .prl-stat-icon { background:#f0f9ff;color:#0284c7; }
.prl-stat.s-slate  .prl-stat-icon { background:#f3f4f6;color:#64748b; }
.prl-stat.s-purple .prl-stat-icon { background:#f5f3ff;color:#7c3aed; }
.prl-stat-label { font-size:0.67rem;font-weight:700;text-transform:uppercase;letter-spacing:0.09em;color:#9ca3af;margin-bottom:4px; }
.prl-stat-value { font-size:1.6rem;font-weight:800;color:#111827;line-height:1;font-variant-numeric:tabular-nums;font-family:'DM Mono',monospace; }
.prl-stat-sub   { font-size:0.73rem;color:#9ca3af;margin-top:4px; }

/* ── Filter bar ─────────────────────────────────────────────── */
.prl-filter-bar { background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:10px 14px;display:flex;align-items:center;gap:10px;margin-bottom:14px;flex-wrap:wrap; }
.prl-search-wrap { position:relative;flex:1;min-width:180px; }
.prl-search-wrap svg { position:absolute;left:10px;top:50%;transform:translateY(-50%);color:#9ca3af;pointer-events:none; }
.prl-search-input { width:100%;border:1px solid #e5e7eb;border-radius:8px;padding:7px 12px 7px 32px;font-size:0.82rem;font-family:'Sora',sans-serif;color:#111827;background:#f9fafb;outline:none;transition:border-color 0.15s; }
.prl-search-input:focus { border-color:#c8292a;background:#fff;box-shadow:0 0 0 3px rgba(200,41,42,0.08); }
.prl-filter-select { border:1px solid #e5e7eb;border-radius:8px;padding:7px 12px;font-size:0.82rem;font-family:'Sora',sans-serif;color:#374151;background:#f9fafb;outline:none;cursor:pointer; }
.prl-filter-select:focus { border-color:#c8292a; }

/* ── Table card ─────────────────────────────────────────────── */
.prl-table-card { background:#fff;border:1px solid #e5e7eb;border-radius:14px;overflow:hidden; }
.prl-table-scroll { overflow-x:auto; }
.prl-table { width:100%;border-collapse:collapse;font-size:0.835rem; }
.prl-table thead tr { background:#f8f9fb;border-bottom:1px solid #e5e7eb; }
.prl-table thead th { padding:11px 16px;font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:0.09em;color:#6b7280;white-space:nowrap;font-family:'Sora',sans-serif; }
.prl-table tbody tr { border-bottom:1px solid #f3f4f6;transition:background 0.12s; }
.prl-table tbody tr:last-child { border-bottom:none; }
.prl-table tbody tr.clickable { cursor:pointer; }
.prl-table tbody tr.clickable:hover { background:#f0f4ff; }
.prl-table tbody tr:not(.clickable):hover { background:#fafafa; }
.prl-table tbody td { padding:12px 16px;color:#374151;vertical-align:middle; }

/* ── Mono values ────────────────────────────────────────────── */
.prl-mono { font-family:'DM Mono',monospace;font-size:0.82rem;font-variant-numeric:tabular-nums; }
.prl-mono.bold  { color:#111827;font-weight:700; }
.prl-mono.green { color:#16a34a;font-weight:600; }
.prl-mono.red   { color:#c8292a;font-weight:600; }
.prl-mono.muted { color:#9ca3af; }

/* ── Status badges ──────────────────────────────────────────── */
.prl-status { display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border-radius:20px;font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;white-space:nowrap; }
.prl-status::before { content:'';width:5px;height:5px;border-radius:50%; }
.prl-status.s-active   { background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0; }
.prl-status.s-active::before { background:#16a34a; }
.prl-status.s-approved { background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0; }
.prl-status.s-approved::before { background:#16a34a; }
.prl-status.s-pending  { background:#fffbeb;color:#d97706;border:1px solid #fde68a; }
.prl-status.s-pending::before { background:#d97706; }
.prl-status.s-partial  { background:#fff7ed;color:#ea580c;border:1px solid #fed7aa; }
.prl-status.s-partial::before { background:#ea580c; }
.prl-status.s-inactive { background:#fff0f0;color:#c8292a;border:1px solid #fecaca; }
.prl-status.s-inactive::before { background:#ef4444; }
.prl-status.s-rejected { background:#fff0f0;color:#c8292a;border:1px solid #fecaca; }
.prl-status.s-rejected::before { background:#ef4444; }
.prl-status.s-maintenance { background:#f5f3ff;color:#7c3aed;border:1px solid #ddd6fe; }
.prl-status.s-maintenance::before { background:#7c3aed; }

/* ── Action buttons ─────────────────────────────────────────── */
.prl-actions { display:flex;align-items:center;gap:5px;justify-content:flex-end; }
.prl-action-btn {
    display:inline-flex;align-items:center;gap:5px;padding:6px 12px;border-radius:8px;
    font-family:'Sora',sans-serif;font-size:0.72rem;font-weight:700;
    text-decoration:none;border:none;cursor:pointer;transition:all 0.12s;white-space:nowrap;
}
.prl-action-btn.edit    { background:#fffbeb;color:#d97706;border:1px solid #fde68a; }
.prl-action-btn.edit:hover { background:#d97706;color:#fff;border-color:#d97706; }
.prl-action-btn.danger  { background:#fff0f0;color:#c8292a;border:1px solid #fecaca; }
.prl-action-btn.danger:hover { background:#c8292a;color:#fff;border-color:#c8292a; }
.prl-action-btn.primary { background:#111827;color:#fff;border:1px solid #111827; }
.prl-action-btn.primary:hover { background:#000;color:#fff; }

/* ── Topbar buttons ─────────────────────────────────────────── */
.prl-btn-add {
    display:inline-flex;align-items:center;gap:8px;padding:10px 18px;background:#c8292a;color:#fff;
    border:none;border-radius:10px;font-family:'Sora',sans-serif;font-size:0.84rem;font-weight:700;
    cursor:pointer;transition:background 0.15s;text-decoration:none;white-space:nowrap;
}
.prl-btn-add:hover { background:#a81f20;color:#fff; }
.prl-btn-ghost {
    display:inline-flex;align-items:center;gap:7px;padding:9px 14px;background:#fff;color:#374151;
    border:1px solid #e5e7eb;border-radius:10px;font-family:'Sora',sans-serif;font-size:0.82rem;font-weight:600;
    cursor:pointer;transition:all 0.12s;text-decoration:none;white-space:nowrap;
}
.prl-btn-ghost:hover { border-color:#c8292a;color:#c8292a;background:#fff5f5; }

/* ── Empty state ────────────────────────────────────────────── */
.prl-empty { display:flex;flex-direction:column;align-items:center;justify-content:center;padding:56px 24px;text-align:center; }
.prl-empty-icon  { width:56px;height:56px;background:#f3f4f6;border-radius:16px;display:flex;align-items:center;justify-content:center;margin-bottom:14px;color:#d1d5db; }
.prl-empty-title { font-size:0.9rem;font-weight:700;color:#374151;margin:0 0 6px; }
.prl-empty-sub   { font-size:0.78rem;color:#9ca3af;margin:0; }

/* ── Detail cards (show pages) ──────────────────────────────── */
.prl-detail-card { background:#fff;border:1px solid #e5e7eb;border-radius:14px;overflow:hidden;margin-bottom:16px; }
.prl-detail-head { padding:14px 18px;border-bottom:1px solid #f3f4f6;display:flex;align-items:center;justify-content:space-between; }
.prl-detail-title { font-size:0.88rem;font-weight:700;color:#111827;margin:0; }
.prl-detail-body { padding:20px 18px; }
.prl-field-label { font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;color:#9ca3af;display:block;margin-bottom:4px; }
.prl-field-value { font-size:0.875rem;font-weight:600;color:#111827; }

/* ── Form section divider ───────────────────────────────────── */
.prl-section-divider { margin:10px 0 16px;font-size:0.72rem;font-weight:900;text-transform:uppercase;letter-spacing:0.10em;color:rgba(17,24,39,0.60); }

/* ── Sort links ─────────────────────────────────────────────── */
.sort-link { display:inline-flex;align-items:center;gap:6px;color:inherit;text-decoration:none;font-weight:700; }
.sort-link:hover { color:#c8292a;text-decoration:none; }

/* ── Legacy aliases (keep existing pages working) ───────────── */
.remui-page { font-family:'Sora',sans-serif; }
.emp-badge { display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border-radius:20px;font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;white-space:nowrap;border:1px solid transparent; }
.emp-badge-active, .emp-badge-approved { background:#f0fdf4;color:#16a34a;border-color:rgba(22,163,74,0.20); }
.emp-badge-pending { background:#fffbeb;color:#d97706;border-color:rgba(217,119,6,0.20); }
.emp-badge-inactive { background:#fff1f2;color:#e11d48;border-color:rgba(225,29,72,0.18); }
.emp-field-label { font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;color:#9ca3af;display:block;margin-bottom:4px; }
.emp-field-value { font-size:0.875rem;font-weight:600;color:#111827; }

/* ── Confirmation modal ─────────────────────────────────────── */
.prl-modal-overlay { position:fixed;inset:0;background:rgba(0,0,0,0.45);z-index:9999;display:flex;align-items:center;justify-content:center;padding:20px;opacity:0;pointer-events:none;transition:opacity 0.2s; }
.prl-modal-overlay.open { opacity:1;pointer-events:all; }
.prl-modal { background:#fff;border-radius:18px;padding:32px;max-width:420px;width:100%;box-shadow:0 20px 60px rgba(0,0,0,0.2);transform:translateY(12px);transition:transform 0.2s; }
.prl-modal-overlay.open .prl-modal { transform:translateY(0); }
.prl-modal-icon { width:52px;height:52px;border-radius:14px;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;background:#fff0f0;color:#c8292a; }
.prl-modal-title { font-size:1rem;font-weight:800;color:#111827;text-align:center;margin:0 0 8px; }
.prl-modal-body  { font-size:0.82rem;color:#6b7280;text-align:center;margin:0 0 24px;line-height:1.6; }
.prl-modal-actions { display:flex;gap:10px; }
.prl-modal-cancel { flex:1;padding:11px;background:#fff;color:#374151;border:1px solid #e5e7eb;border-radius:10px;font-family:'Sora',sans-serif;font-size:0.845rem;font-weight:600;cursor:pointer;transition:all 0.12s; }
.prl-modal-cancel:hover { border-color:#c8292a;color:#c8292a; }
.prl-modal-confirm { flex:1;padding:11px;background:#c8292a;color:#fff;border:none;border-radius:10px;font-family:'Sora',sans-serif;font-size:0.845rem;font-weight:700;cursor:pointer;transition:all 0.12s; }
.prl-modal-confirm:hover { background:#a81f20; }
</style>
