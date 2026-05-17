<style>
@import url('https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Sora:wght@400;600;700;800&display=swap');
.bn-form-page { font-family: 'Sora', sans-serif; }
.bn-form-topbar { display:flex;align-items:flex-start;justify-content:space-between;gap:16px;margin-bottom:24px;flex-wrap:wrap; }
.bn-form-title { font-size:1.35rem;font-weight:800;color:#111827;margin:0 0 2px; }
.bn-form-sub { font-size:0.78rem;color:#9ca3af;margin:0; }
.bn-btn-sec { display:inline-flex;align-items:center;gap:7px;padding:9px 16px;background:#fff;color:#374151;border:1px solid #e5e7eb;border-radius:10px;font-size:0.82rem;font-weight:600;text-decoration:none; }
.bn-btn-sec:hover { border-color:#c8292a;color:#c8292a;background:#fff5f5; }
.bn-form-wrap { max-width:760px;margin:0 auto; }
.bn-form-wrap.wide { max-width:1100px; }
.bn-card { background:#fff;border:1px solid #e5e7eb;border-radius:16px;overflow:hidden;margin-bottom:20px; }
.bn-card-header { padding:18px 24px;border-bottom:1px solid #f3f4f6; }
.bn-card-title { font-size:0.95rem;font-weight:800;color:#111827;margin:0; }
.bn-card-body { padding:24px; }
.bn-card-footer { padding:16px 24px;border-top:1px solid #f3f4f6;background:#fafafa;display:flex;gap:10px; }
.bn-label { display:block;font-size:0.72rem;font-weight:700;text-transform:uppercase;letter-spacing:0.09em;color:#6b7280;margin-bottom:6px; }
.bn-label .req { color:#c8292a; }
.bn-input, .bn-select, .bn-textarea { width:100%;border:1px solid #e5e7eb;border-radius:10px;padding:10px 14px;font-size:0.845rem;font-family:'Sora',sans-serif;color:#111827;outline:none; }
.bn-input:focus, .bn-select:focus, .bn-textarea:focus { border-color:#c8292a;box-shadow:0 0 0 3px rgba(200,41,42,0.08); }
.bn-textarea { min-height:90px;resize:vertical; }
.bn-row { display:grid;grid-template-columns:1fr 1fr;gap:16px; }
@media (max-width:560px) { .bn-row { grid-template-columns:1fr; } }
.bn-field { margin-bottom:18px; }
.bn-divider { font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:0.12em;color:#9ca3af;border-bottom:1px solid #f3f4f6;padding-bottom:10px;margin:8px 0 18px; }
.bn-hint { font-size:0.72rem;color:#9ca3af;margin-top:5px; }
.bn-vars { display:flex;flex-wrap:wrap;gap:6px;margin-top:8px; }
.bn-var-tag { font-family:'DM Mono',monospace;font-size:0.7rem;background:#f3f4f6;border:1px solid #e5e7eb;padding:3px 8px;border-radius:6px;color:#374151;cursor:pointer; }
.bn-var-tag:hover { background:#fff5f5;border-color:#fecaca;color:#c8292a; }
.bn-emp-list { max-height:200px;overflow-y:auto;border:1px solid #e5e7eb;border-radius:10px;padding:12px;display:grid;grid-template-columns:1fr 1fr;gap:8px; }
.bn-emp-item { display:flex;align-items:center;gap:8px;font-size:0.8rem; }
.bn-btn-submit { padding:11px 24px;background:#111827;color:#fff;border:none;border-radius:10px;font-weight:700;cursor:pointer; }
.bn-btn-submit:hover { background:#000; }
.bn-btn-cancel { padding:11px 18px;background:#fff;border:1px solid #e5e7eb;border-radius:10px;font-weight:600;text-decoration:none;color:#374151; }
.bn-flash.error { background:#fff0f0;border:1px solid #fecaca;color:#c8292a;padding:12px 16px;border-radius:10px;margin-bottom:20px;font-size:0.82rem; }
.bn-system-note { background:#fffbeb;border:1px solid #fde68a;color:#92400e;padding:10px 14px;border-radius:10px;font-size:0.78rem;margin-bottom:16px; }
</style>

