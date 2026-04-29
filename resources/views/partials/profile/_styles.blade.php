<style>
    @import url('https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Sora:wght@400;600;700;800&display=swap');

    .pf2-page { font-family: 'Sora', sans-serif; position: relative; padding-top: 28px; }
    .pf2-backdrop {
        position: absolute;
        inset: -40px -20px auto -20px;
        height: 340px;
        pointer-events: none;
        z-index: 0;
        background:
            radial-gradient(220px 220px at 10% 35%, rgba(200,41,42,0.14), transparent 60%),
            radial-gradient(260px 260px at 85% 10%, rgba(2,132,199,0.12), transparent 60%),
            radial-gradient(240px 240px at 70% 70%, rgba(22,163,74,0.10), transparent 60%),
            linear-gradient(to bottom, rgba(17,24,39,0.04), transparent 70%);
        filter: saturate(110%);
    }
    .pf2-grid {
        position: absolute;
        inset: 0;
        background-image:
            linear-gradient(to right, rgba(17,24,39,0.06) 1px, transparent 1px),
            linear-gradient(to bottom, rgba(17,24,39,0.06) 1px, transparent 1px);
        background-size: 48px 48px;
        mask-image: radial-gradient(closest-side at 50% 30%, rgba(0,0,0,0.75), transparent 80%);
        opacity: 0.5;
    }
    .pf2-content { position: relative; z-index: 1; }

    .pf2-topbar { display:flex; align-items:flex-start; justify-content:space-between; gap:16px; margin-bottom:20px; flex-wrap:wrap; }
    .pf2-title { font-size:1.35rem; font-weight:800; color:#111827; letter-spacing:-0.02em; margin:0 0 2px; }
    .pf2-sub { font-size:0.78rem; color:#9ca3af; margin:0; }
    .pf2-actions { display:flex; gap:8px; flex-wrap:wrap; align-items:center; }

    .pf2-btn {
        display:inline-flex; align-items:center; gap:7px; padding:9px 16px;
        background:#fff; color:#374151; border:1px solid #e5e7eb; border-radius:10px;
        font-family:'Sora',sans-serif; font-size:0.82rem; font-weight:700;
        text-decoration:none; cursor:pointer; transition:all 0.15s; white-space:nowrap;
    }
    .pf2-btn:hover { border-color:#c8292a; color:#c8292a; background:#fff5f5; transform: translateY(-1px); }

    .pf2-btn-primary {
        display:inline-flex; align-items:center; gap:10px; padding:11px 20px;
        background:#c8292a; color:#fff; border:none; border-radius:12px;
        font-family:'Sora',sans-serif; font-size:0.86rem; font-weight:800;
        cursor:pointer; transition:background 0.15s, box-shadow 0.15s, transform 0.15s;
        box-shadow:0 4px 20px rgba(200,41,42,0.5); white-space:nowrap; text-decoration:none;
    }
    .pf2-btn-primary:hover { background:#a81f20; color:#fff; box-shadow:0 10px 34px rgba(200,41,42,0.62); transform: translateY(-1px); }

    .pf2-hero {
        background: linear-gradient(135deg, #111827 0%, #0b1220 55%, #111827 100%);
        border-radius:18px; padding:22px 24px; margin-bottom:18px;
        display:flex; align-items:center; justify-content:space-between; gap:18px; flex-wrap:wrap;
        position:relative; overflow:hidden;
    }
    .pf2-hero::before { content:''; position:absolute; top:-70px; right:-70px; width:260px; height:260px; border-radius:50%; background:rgba(200,41,42,0.18); pointer-events:none; }
    .pf2-hero::after { content:''; position:absolute; bottom:-90px; left:-90px; width:260px; height:260px; border-radius:50%; background:rgba(2,132,199,0.14); pointer-events:none; }
    .pf2-hero-left { display:flex; align-items:center; gap:14px; position:relative; z-index:1; }
    .pf2-avatar {
        width:56px; height:56px; border-radius:18px;
        background:linear-gradient(135deg, rgba(255,255,255,0.12), rgba(255,255,255,0.06));
        display:flex; align-items:center; justify-content:center; color:#fff;
        font-family:'DM Mono', monospace; font-size:1.15rem; font-weight:700; letter-spacing:0.03em;
        border:1px solid rgba(255,255,255,0.10);
        box-shadow: inset 0 0 0 1px rgba(255,255,255,0.06);
    }
    .pf2-hero-name { font-size:1.05rem; font-weight:800; color:#fff; margin:0; letter-spacing:-0.02em; }
    .pf2-hero-meta { font-size:0.78rem; color:#9ca3af; margin:2px 0 0; }
    .pf2-hero-right { position:relative; z-index:1; display:flex; gap:10px; flex-wrap:wrap; }

    .pf2-chip-row { display:flex; flex-wrap:wrap; gap:8px; margin-top:10px; }
    .pf2-chip {
        display:inline-flex; align-items:center; gap:6px; padding:6px 10px;
        border:1px solid rgba(255,255,255,0.14); border-radius:999px; color:#e5e7eb;
        background: rgba(255,255,255,0.06); font-size:0.75rem;
    }

    .pf2-stats { display:grid; grid-template-columns:repeat(4,1fr); gap:14px; margin-bottom:18px; }
    @media (max-width:1100px) { .pf2-stats { grid-template-columns:repeat(2,1fr); } }
    @media (max-width:600px)  { .pf2-stats { grid-template-columns:1fr; } }

    .pf2-stat { background:rgba(255,255,255,0.92); backdrop-filter: blur(6px); border:1px solid rgba(229,231,235,0.9); border-radius:16px; padding:18px 20px; display:flex; align-items:flex-start; gap:14px; position:relative; overflow:hidden; transition:box-shadow 0.15s, transform 0.15s; }
    .pf2-stat:hover { box-shadow:0 10px 32px rgba(17,24,39,0.10); transform: translateY(-2px); }
    .pf2-stat::after { content:''; position:absolute; bottom:0; left:0; right:0; height:3px; border-radius:0 0 16px 16px; }
    .pf2-stat.s-red::after   { background:#c8292a; }
    .pf2-stat.s-green::after { background:#16a34a; }
    .pf2-stat.s-amber::after { background:#d97706; }
    .pf2-stat.s-blue::after  { background:#0284c7; }
    .pf2-stat-icon { width:40px; height:40px; border-radius:10px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
    .pf2-stat.s-red   .pf2-stat-icon { background:#fff0f0; color:#c8292a; }
    .pf2-stat.s-green .pf2-stat-icon { background:#f0fdf4; color:#16a34a; }
    .pf2-stat.s-amber .pf2-stat-icon { background:#fffbeb; color:#d97706; }
    .pf2-stat.s-blue  .pf2-stat-icon { background:#f0f9ff; color:#0284c7; }
    .pf2-stat-label { font-size:0.67rem; font-weight:800; text-transform:uppercase; letter-spacing:0.09em; color:#9ca3af; margin-bottom:4px; }
    .pf2-stat-value { font-size:1.15rem; font-weight:900; color:#111827; line-height:1.15; }
    .pf2-stat-sub { font-size:0.73rem; color:#9ca3af; margin-top:4px; }
    .pf2-mono { font-family:'DM Mono', monospace; font-variant-numeric:tabular-nums; }

    .pf2-two-col { display:grid; grid-template-columns:1fr 360px; gap:16px; align-items:start; }
    @media (max-width:900px) { .pf2-two-col { grid-template-columns:1fr; } }

    .pf2-card { background:rgba(255,255,255,0.92); backdrop-filter: blur(6px); border:1px solid rgba(229,231,235,0.9); border-radius:16px; overflow:hidden; box-shadow: 0 10px 30px rgba(17,24,39,0.06); }
    .pf2-card-head { padding:14px 18px; border-bottom:1px solid #f3f4f6; display:flex; align-items:center; justify-content:space-between; gap:10px; flex-wrap:wrap; }
    .pf2-card-title { font-size:0.82rem; font-weight:900; color:#111827; margin:0; display:flex; align-items:center; gap:8px; }
    .pf2-dot { width:8px; height:8px; border-radius:50%; background:#c8292a; display:inline-block; }
    .pf2-card-body { padding:18px; }

    .pf2-kv { display:grid; grid-template-columns: 130px 1fr; gap:10px 14px; align-items:center; }
    .pf2-k { font-size:0.68rem; font-weight:900; text-transform:uppercase; letter-spacing:0.10em; color:#9ca3af; }
    .pf2-v { font-size:0.88rem; color:#111827; font-weight:900; }
    .pf2-v-sub { font-size:0.76rem; color:#9ca3af; margin-top:2px; }
    .pf2-divider { height:1px; background:#f3f4f6; margin:14px 0; }

    .pf2-field-label { font-size:0.72rem; font-weight:900; text-transform:uppercase; letter-spacing:0.09em; color:#6b7280; margin-bottom:6px; }
    .pf2-input {
        width:100%; border:1px solid #e5e7eb; border-radius:10px; padding:10px 14px;
        font-size:0.845rem; font-family:'Sora',sans-serif; color:#111827; background:#fff; outline:none;
        transition:border-color 0.15s, box-shadow 0.15s;
    }
    .pf2-input:focus { border-color:#c8292a; box-shadow:0 0 0 3px rgba(200,41,42,0.08); }
    .pf2-help { font-size:0.78rem; color:#9ca3af; margin-top:6px; }
</style>

