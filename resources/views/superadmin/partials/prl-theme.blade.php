{{-- Shared “salary computation” visual language for Super Admin screens --}}
<style>
@import url('https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Sora:wght@400;600;700;800&display=swap');
.prl-page { font-family: 'Sora', sans-serif; max-width: 1200px; margin: 0 auto; }

.prl-topbar { display:flex;align-items:flex-start;justify-content:space-between;gap:16px;margin-bottom:24px;flex-wrap:wrap; }
.prl-topbar-title { font-size:1.35rem;font-weight:800;color:#111827;letter-spacing:-0.02em;margin:0 0 2px; }
.prl-topbar-sub   { font-size:0.78rem;color:#9ca3af;margin:0; }
.prl-topbar-actions { display:flex;gap:8px;flex-wrap:wrap;align-items:center; }

.prl-btn-sec {
    display:inline-flex;align-items:center;gap:7px;padding:9px 16px;background:#fff;color:#374151;
    border:1px solid #e5e7eb;border-radius:10px;font-family:'Sora',sans-serif;font-size:0.82rem;
    font-weight:600;text-decoration:none;cursor:pointer;transition:all 0.15s;white-space:nowrap;
}
.prl-btn-sec:hover { border-color:#c8292a;color:#c8292a;background:#fff5f5; }

.prl-btn-generate {
    display:inline-flex;align-items:center;gap:10px;padding:11px 22px;background:#c8292a;color:#fff;
    border:none;border-radius:12px;font-family:'Sora',sans-serif;font-size:0.86rem;font-weight:700;
    cursor:pointer;transition:background 0.15s,box-shadow 0.15s;
    box-shadow:0 4px 20px rgba(200,41,42,0.45);white-space:nowrap;text-decoration:none;
}
.prl-btn-generate:hover { background:#a81f20;color:#fff;box-shadow:0 8px 28px rgba(200,41,42,0.55); }
.prl-btn-generate:disabled { background:#9ca3af;box-shadow:none;cursor:not-allowed; }

.prl-stats { display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:24px; }
@media (max-width:1100px) { .prl-stats { grid-template-columns:repeat(2,1fr); } }
@media (max-width:600px)  { .prl-stats { grid-template-columns:1fr; } }

.prl-stat { background:#fff;border:1px solid #e5e7eb;border-radius:14px;padding:18px 20px;display:flex;align-items:flex-start;gap:14px;position:relative;overflow:hidden;transition:box-shadow 0.15s; }
.prl-stat:hover { box-shadow:0 4px 20px rgba(0,0,0,0.07); }
.prl-stat::after { content:'';position:absolute;bottom:0;left:0;right:0;height:3px;border-radius:0 0 14px 14px; }
.prl-stat.s-red::after   { background:#c8292a; }
.prl-stat.s-green::after { background:#16a34a; }
.prl-stat.s-amber::after { background:#d97706; }
.prl-stat.s-blue::after  { background:#0284c7; }
.prl-stat-icon { width:40px;height:40px;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0; }
.prl-stat.s-red   .prl-stat-icon { background:#fff0f0;color:#c8292a; }
.prl-stat.s-green .prl-stat-icon { background:#f0fdf4;color:#16a34a; }
.prl-stat.s-amber .prl-stat-icon { background:#fffbeb;color:#d97706; }
.prl-stat.s-blue  .prl-stat-icon { background:#f0f9ff;color:#0284c7; }
.prl-stat-label { font-size:0.67rem;font-weight:700;text-transform:uppercase;letter-spacing:0.09em;color:#9ca3af;margin-bottom:4px; }
.prl-stat-value { font-size:1.5rem;font-weight:800;color:#111827;line-height:1;font-variant-numeric:tabular-nums;font-family:'DM Mono',monospace; }
.prl-stat-sub   { font-size:0.73rem;color:#9ca3af;margin-top:4px; }

.prl-generate-card {
    background:#111827;border-radius:16px;padding:26px 30px;
    display:flex;align-items:center;justify-content:space-between;gap:24px;
    margin-bottom:24px;flex-wrap:wrap;position:relative;overflow:hidden;
}
.prl-generate-card::before { content:'';position:absolute;top:-60px;right:-60px;width:200px;height:200px;border-radius:50%;background:rgba(200,41,42,0.15);pointer-events:none; }
.prl-generate-left { position:relative;z-index:1; }
.prl-generate-eyebrow { font-size:0.67rem;font-weight:700;text-transform:uppercase;letter-spacing:0.12em;color:#c8292a;margin-bottom:6px; }
.prl-generate-title { font-size:1.12rem;font-weight:800;color:#fff;margin:0 0 6px;letter-spacing:-0.02em; }
.prl-generate-period { font-size:0.82rem;color:#9ca3af;font-family:'DM Mono',monospace; }
.prl-generate-right { position:relative;z-index:1;display:flex;flex-wrap:wrap;gap:10px;align-items:center;justify-content:flex-end; }

.prl-two-col { display:grid;grid-template-columns:1fr 1fr;gap:16px;align-items:start;margin-bottom:20px; }
@media (max-width:900px) { .prl-two-col { grid-template-columns:1fr; } }

.prl-section-head { display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;flex-wrap:wrap;gap:10px; }
.prl-section-title { font-size:0.88rem;font-weight:700;color:#111827;margin:0;display:flex;align-items:center;gap:8px; }
.prl-dot { width:8px;height:8px;border-radius:50%;background:#c8292a;display:inline-block; }

.prl-filter-bar { background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:12px 16px;display:flex;align-items:center;gap:10px;margin-bottom:14px;flex-wrap:wrap; }
.prl-search-wrap { position:relative;flex:1;min-width:180px; }
.prl-search-wrap svg { position:absolute;left:11px;top:50%;transform:translateY(-50%);color:#9ca3af;pointer-events:none; }
.prl-search-input { width:100%;border:1px solid #e5e7eb;border-radius:8px;padding:8px 12px 8px 34px;font-size:0.82rem;font-family:'Sora',sans-serif;color:#111827;background:#f9fafb;outline:none;transition:border-color 0.15s,background 0.15s; }
.prl-search-input:focus { border-color:#c8292a;background:#fff;box-shadow:0 0 0 3px rgba(200,41,42,0.08); }
.prl-filter-select { border:1px solid #e5e7eb;border-radius:8px;padding:8px 12px;font-size:0.82rem;font-family:'Sora',sans-serif;color:#374151;background:#f9fafb;outline:none;cursor:pointer;transition:border-color 0.15s; }
.prl-filter-select:focus { border-color:#c8292a; }

.prl-table-card { background:#fff;border:1px solid #e5e7eb;border-radius:14px;overflow:hidden; }
.prl-table-scroll { overflow-x:auto; }
.prl-table { width:100%;border-collapse:collapse;font-size:0.835rem; }
.prl-table thead tr { background:#f8f9fb;border-bottom:1px solid #e5e7eb; }
.prl-table thead th { padding:11px 16px;font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:0.09em;color:#6b7280;white-space:nowrap;font-family:'Sora',sans-serif; }
.prl-table tbody tr { border-bottom:1px solid #f3f4f6;transition:background 0.1s; }
.prl-table tbody tr:last-child { border-bottom:none; }
.prl-table tbody tr:hover { background:#fafafa; }
.prl-table tbody td { padding:12px 16px;color:#374151;vertical-align:middle; }

.prl-emp-cell { display:flex;align-items:center;gap:10px; }
.prl-emp-avatar { width:32px;height:32px;border-radius:50%;background:#f3f4f6;display:flex;align-items:center;justify-content:center;font-size:0.72rem;font-weight:700;color:#6b7280;flex-shrink:0;border:1.5px solid #e5e7eb;text-transform:uppercase; }
.prl-emp-name { font-weight:600;color:#111827;font-size:0.845rem; }
.prl-mono { font-family:'DM Mono',monospace;font-size:0.82rem;font-variant-numeric:tabular-nums; }

.prl-actions { display:flex;gap:6px;align-items:center;justify-content:flex-end; }
.prl-action-btn { width:30px;height:30px;border-radius:7px;border:1px solid #e5e7eb;background:#fff;display:inline-flex;align-items:center;justify-content:center;cursor:pointer;text-decoration:none;color:#6b7280;transition:all 0.13s;padding:0; }
.prl-action-btn:hover { background:#f4f5f7;color:#111827;border-color:#d1d5db; }

.prl-empty { display:flex;flex-direction:column;align-items:center;justify-content:center;padding:56px 24px;text-align:center; }
.prl-empty-icon { width:56px;height:56px;background:#f3f4f6;border-radius:16px;display:flex;align-items:center;justify-content:center;margin-bottom:14px;color:#d1d5db; }
.prl-empty-title { font-size:0.9rem;font-weight:700;color:#374151;margin:0 0 6px; }
.prl-empty-sub   { font-size:0.78rem;color:#9ca3af;margin:0; }

.prl-flash { display:flex;align-items:center;gap:10px;padding:12px 16px;border-radius:10px;font-size:0.82rem;font-weight:500;margin-bottom:20px;animation:saFlashIn 0.3s ease; }
.prl-flash.success { background:#f0fdf4;border:1px solid #bbf7d0;color:#15803d; }
.prl-flash.error   { background:#fff0f0;border:1px solid #fecaca;color:#c8292a; }
.prl-flash.info    { background:#eff6ff;border:1px solid #bfdbfe;color:#1d4ed8; }
@keyframes saFlashIn { from{opacity:0;transform:translateY(-6px)}to{opacity:1;transform:translateY(0)} }

.prl-pagination-strip { display:flex;justify-content:space-between;align-items:center;padding:12px 16px;border-top:1px solid #f3f4f6;background:#fafafa;flex-wrap:wrap;gap:10px; }
.prl-pagination-info  { font-size:0.75rem;color:#9ca3af; }
.prl-pagination-info strong { color:#374151; }

/* Account role / status (super admin) */
.sa-role { display:inline-flex;align-items:center;padding:3px 10px;border-radius:20px;font-size:0.68rem;font-weight:700;text-transform:capitalize;letter-spacing:0.04em;white-space:nowrap; }
.sa-role.r-superadmin { background:#fef3c7;color:#b45309;border:1px solid #fde68a; }
.sa-role.r-hr { background:#dbeafe;color:#0369a1;border:1px solid #bae6fd; }
.sa-role.r-accountant { background:#f3e8ff;color:#7c3aed;border:1px solid #ddd6fe; }
.sa-role.r-remittance_clerk { background:#fce7f3;color:#be185d;border:1px solid #fbcfe8; }
.sa-role.r-employee { background:#f0fdf4;color:#15803d;border:1px solid #bbf7d0; }
.sa-role.r-qr_admin { background:#f5f3ff;color:#6d28d9;border:1px solid #ddd6fe; }

.sa-status { display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border-radius:20px;font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;white-space:nowrap; }
.sa-status::before { content:'';width:5px;height:5px;border-radius:50%; }
.sa-status.s-active   { background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0; }
.sa-status.s-active::before { background:#16a34a; }
.sa-status.s-inactive { background:#fff0f0;color:#c8292a;border:1px solid #fecaca; }
.sa-status.s-inactive::before { background:#ef4444; }

.sa-toggle { position:relative;display:inline-block;width:44px;height:24px; }
.sa-toggle input { opacity:0;width:0;height:0; }
.sa-toggle-slider { position:absolute;cursor:pointer;top:0;left:0;right:0;bottom:0;background-color:#d1d5db;transition:0.3s;border-radius:24px; }
.sa-toggle-slider:before { position:absolute;content:"";height:18px;width:18px;left:3px;bottom:3px;background-color:#fff;transition:0.3s;border-radius:50%; }
.sa-toggle input:checked + .sa-toggle-slider { background-color:#16a34a; }
.sa-toggle input:checked + .sa-toggle-slider:before { transform:translateX(20px); }

/* Forms (salary-style cards) */
.prl-wrap { max-width:720px;margin:0 auto;padding-bottom:48px; }
.prl-back-link {
    display:inline-flex;align-items:center;gap:6px;font-size:0.78rem;font-weight:600;color:#9ca3af;
    text-decoration:none;margin-bottom:14px;transition:color 0.13s;
}
.prl-back-link:hover { color:#c8292a; }
.prl-page-title { font-size:1.25rem;font-weight:800;color:#111827;letter-spacing:-0.02em;margin:0 0 4px; }
.prl-page-sub { font-size:0.78rem;color:#9ca3af;margin:0 0 22px; }

.prl-card {
    background:#fff;border:1px solid #e5e7eb;border-radius:14px;overflow:hidden;margin-bottom:16px;
}
.prl-card-head {
    padding:16px 22px;border-bottom:1px solid #f3f4f6;display:flex;align-items:flex-start;gap:12px;
}
.prl-card-head-icon {
    width:36px;height:36px;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;
}
.prl-card-head-icon.red   { background:#fff0f0;color:#c8292a; }
.prl-card-head-icon.amber { background:#fffbeb;color:#d97706; }
.prl-card-head-icon.green { background:#f0fdf4;color:#16a34a; }
.prl-card-head-icon.blue  { background:#eff6ff;color:#2563eb; }
.prl-card-head-icon.slate { background:#f3f4f6;color:#111827; }
.prl-card-head-title { font-size:0.9rem;font-weight:700;color:#111827;margin:0 0 2px; }
.prl-card-head-sub   { font-size:0.72rem;color:#9ca3af;margin:0; }
.prl-card-body { padding:20px 22px 22px; }

.prl-form-row { display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px; }
@media (max-width:640px) { .prl-form-row { grid-template-columns:1fr; } }
.prl-field { margin-bottom:4px; }
.prl-lbl { display:block;font-size:0.775rem;font-weight:600;color:#374151;margin-bottom:6px; }
.prl-lbl .req { color:#ef4444;margin-left:2px; }
.prl-ctrl {
    width:100%;border:1px solid #e5e7eb;border-radius:8px;padding:10px 12px;font-size:0.84rem;
    font-family:'Sora',sans-serif;color:#111827;background:#f9fafb;outline:none;transition:border-color 0.15s,background 0.15s,box-shadow 0.15s;
}
.prl-ctrl:focus { border-color:#c8292a;background:#fff;box-shadow:0 0 0 3px rgba(200,41,42,0.08); }
.prl-err { font-size:0.74rem;color:#ef4444;margin-top:5px;display:block; }

.prl-form-footer { display:flex;gap:12px;justify-content:flex-end;margin-top:8px;padding-top:20px;border-top:1px solid #f3f4f6;flex-wrap:wrap; }
.prl-btn-cancel {
    display:inline-flex;align-items:center;gap:6px;padding:10px 18px;background:#fff;color:#374151;border:1px solid #e5e7eb;border-radius:10px;
    font-family:'Sora',sans-serif;font-size:0.82rem;font-weight:600;text-decoration:none;cursor:pointer;transition:all 0.15s;
}
.prl-btn-cancel:hover { border-color:#c8292a;color:#c8292a;background:#fff5f5; }

.prl-alert { padding:12px 16px;border-radius:10px;font-size:0.82rem;margin-bottom:18px;border:1px solid transparent; }
.prl-alert.error { background:#fff0f0;border-color:#fecaca;color:#c8292a; }
.prl-alert.success { background:#f0fdf4;border-color:#bbf7d0;color:#15803d; }
.prl-alert.info { background:#eff6ff;border-color:#bfdbfe;color:#1d4ed8; }

.prl-info-panel {
    display:flex;gap:12px;padding:14px 16px;background:#f0f9ff;border:1px solid #bae6fd;border-radius:10px;font-size:0.8rem;color:#0369a1;line-height:1.5;margin-bottom:18px;
}
.prl-warn-panel {
    display:flex;gap:12px;padding:14px 16px;background:#fffbeb;border:1px solid #fde68a;border-radius:10px;font-size:0.8rem;color:#92400e;line-height:1.5;margin-bottom:18px;
}
.prl-code-box {
    background:#f9fafb;border:2px dashed #16a34a;border-radius:12px;padding:16px 18px;margin-bottom:18px;
}
.prl-code-box .prl-code-label { font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;color:#6b7280;margin:0 0 10px; }
.prl-code-box code { font-family:'DM Mono',monospace;font-size:1.05rem;font-weight:700;color:#111827;letter-spacing:0.12em; }

/* Configuration tabs */
.prl-tabs-wrap { margin-bottom:22px; }
.prl-tabs { display:flex;gap:4px;flex-wrap:wrap;padding:5px;background:#f3f4f6;border:1px solid #e5e7eb;border-radius:12px; }
.prl-tab-btn {
    border:none;background:transparent;padding:10px 18px;border-radius:10px;font-size:0.82rem;font-weight:600;
    color:#6b7280;font-family:'Sora',sans-serif;cursor:pointer;transition:all 0.15s;white-space:nowrap;
}
.prl-tab-btn:hover { color:#111827; }
.prl-tab-btn.active { background:#fff;color:#c8292a;box-shadow:0 1px 4px rgba(0,0,0,0.07); }
.prl-tab-panel { display:none; }
.prl-tab-panel.active { display:block; }

/* Dashboard decorative surface */
.sa-dash { position:relative;font-family:'Sora',sans-serif; }
.sa-dash-bg {
    position:absolute;inset:-32px -16px auto -16px;height:320px;pointer-events:none;z-index:0;border-radius:0;
    background:
        radial-gradient(220px 220px at 8% 40%, rgba(200,41,42,0.12), transparent 60%),
        radial-gradient(240px 240px at 88% 15%, rgba(2,132,199,0.10), transparent 55%),
        linear-gradient(to bottom, rgba(17,24,39,0.03), transparent 75%);
}
.sa-dash-inner { position:relative;z-index:1; }

.prl-side-card { background:#fff;border:1px solid #e5e7eb;border-radius:14px;overflow:hidden;margin-bottom:16px; }
.prl-side-head { padding:14px 18px;border-bottom:1px solid #f3f4f6;font-size:0.85rem;font-weight:700;color:#111827;display:flex;align-items:baseline;justify-content:space-between;gap:10px;flex-wrap:wrap; }
.prl-side-head span { font-size:0.72rem;color:#9ca3af;font-weight:500; }
.prl-feed { margin:0;padding:0;list-style:none; }
.prl-feed li { border-top:1px solid #f3f4f6; }
.prl-feed li:first-child { border-top:none; }
.prl-feed a {
    display:flex;align-items:flex-start;gap:12px;padding:14px 18px;text-decoration:none;color:inherit;transition:background 0.12s;
}
.prl-feed a:hover { background:#fafafa; }
.prl-feed-av {
    width:38px;height:38px;border-radius:10px;background:#f3f4f6;color:#6b7280;font-size:0.7rem;font-weight:800;
    display:flex;align-items:center;justify-content:center;flex-shrink:0;border:1px solid #e8e8e8;
}
.prl-feed-body { flex:1;min-width:0; }
.prl-feed-title { font-size:0.84rem;font-weight:700;color:#111827;margin:0 0 4px; }
.prl-feed-meta { font-size:0.74rem;color:#6b7280;margin:0;line-height:1.45; }
.prl-feed-meta code { font-family:'DM Mono',monospace;font-size:0.72rem;background:#f1f5f9;padding:1px 6px;border-radius:4px;color:#64748b; }
.prl-feed-right { flex-shrink:0;text-align:right; }
.prl-mini-pill { font-size:0.65rem;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;padding:4px 10px;border-radius:999px;border:1px solid #e5e7eb;background:#f9fafb;color:#64748b; }

.prl-chart-box { min-height:260px;padding:4px 8px 8px; }

/* Configuration / backup utility buttons */
.prl-btn-success-solid {
    display:inline-flex;align-items:center;gap:8px;padding:10px 16px;background:#16a34a;color:#fff;border:none;border-radius:10px;
    font-size:0.82rem;font-weight:600;font-family:'Sora',sans-serif;cursor:pointer;transition:background 0.15s;white-space:nowrap;text-decoration:none;
}
.prl-btn-success-solid:hover { background:#15803d;color:#fff; }
.prl-btn-danger-solid {
    display:inline-flex;align-items:center;gap:8px;padding:10px 16px;background:#ef4444;color:#fff;border:none;border-radius:10px;
    font-size:0.82rem;font-weight:600;font-family:'Sora',sans-serif;cursor:pointer;transition:background 0.15s;white-space:nowrap;text-decoration:none;
}
.prl-btn-danger-solid:hover { background:#dc2626;color:#fff; }

.prl-cfg-toolbar {
    display:flex;align-items:center;justify-content:space-between;gap:16px;padding:16px 18px;background:#f9fafb;border:1px solid #e5e7eb;border-radius:12px;flex-wrap:wrap;
}
.prl-cfg-status-pill {
    display:inline-flex;align-items:center;gap:6px;padding:6px 12px;border-radius:20px;font-size:0.75rem;font-weight:600;margin-top:12px;
}
.prl-cfg-status-pill.on { background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0; }
.prl-cfg-status-pill.off { background:#fff0f0;color:#c8292a;border:1px solid #fecaca; }

.cfg-toggle-lg { position:relative;display:inline-block;width:50px;height:28px;flex-shrink:0; }
.cfg-toggle-lg input { opacity:0;width:0;height:0; }
.cfg-toggle-lg .cfg-slider { position:absolute;cursor:pointer;top:0;left:0;right:0;bottom:0;background:#d1d5db;transition:0.3s;border-radius:28px; }
.cfg-toggle-lg .cfg-slider:before { position:absolute;content:"";height:22px;width:22px;left:3px;bottom:3px;background:#fff;transition:0.3s;border-radius:50%; }
.cfg-toggle-lg input:checked + .cfg-slider { background:#16a34a; }
.cfg-toggle-lg input:checked + .cfg-slider:before { transform:translateX(22px); }

.prl-cfg-block { background:#fff;border:1px solid #e5e7eb;border-radius:14px;padding:22px 24px;margin-bottom:18px; }
.prl-cfg-block-title { font-size:0.92rem;font-weight:700;color:#111827;margin:0 0 16px;display:flex;align-items:center;gap:10px; }
.prl-cfg-block-title svg { width:20px;height:20px;color:#c8292a;flex-shrink:0; }
.prl-cfg-desc { font-size:0.75rem;color:#9ca3af;margin-top:4px;line-height:1.4; }
.prl-cfg-form-row { display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:16px; }
.prl-cfg-field { margin-bottom:16px; }
.prl-cfg-field:last-child { margin-bottom:0; }
.prl-cfg-label { display:block;font-size:0.82rem;font-weight:600;color:#374151;margin-bottom:8px; }
.prl-cfg-label .req { color:#c8292a; }
</style>
