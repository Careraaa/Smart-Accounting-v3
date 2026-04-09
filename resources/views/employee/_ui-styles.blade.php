<style>
    @import url('https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Sora:wght@400;600;700;800&display=swap');

    .empui-page { font-family: 'Sora', sans-serif; position: relative; }
    .empui-wrap { position: relative; }
    .empui-backdrop {
        position: absolute; inset: -40px -20px auto -20px; height: 340px; pointer-events: none; z-index: 0;
        background:
            radial-gradient(220px 220px at 10% 35%, rgba(200,41,42,0.14), transparent 60%),
            radial-gradient(260px 260px at 85% 10%, rgba(2,132,199,0.12), transparent 60%),
            radial-gradient(240px 240px at 70% 70%, rgba(22,163,74,0.10), transparent 60%),
            linear-gradient(to bottom, rgba(17,24,39,0.04), transparent 70%);
        filter: saturate(110%);
    }
    .empui-grid {
        position: absolute; inset: 0;
        background-image:
            linear-gradient(to right, rgba(17,24,39,0.06) 1px, transparent 1px),
            linear-gradient(to bottom, rgba(17,24,39,0.06) 1px, transparent 1px);
        background-size: 48px 48px;
        mask-image: radial-gradient(closest-side at 50% 30%, rgba(0,0,0,0.75), transparent 80%);
        opacity: 0.5;
    }
    .empui-content { position: relative; z-index: 1; }

    .empui-hero {
        background: linear-gradient(135deg, #111827 0%, #0b1220 55%, #111827 100%);
        border-radius: 18px; padding: 22px 24px; margin-bottom: 18px;
        display: flex; align-items: center; justify-content: space-between; gap: 18px; flex-wrap: wrap;
        position: relative; overflow: hidden;
        border: 1px solid rgba(255,255,255,0.08);
    }
    .empui-hero::before { content: ''; position: absolute; top: -70px; right: -70px; width: 260px; height: 260px; border-radius: 50%; background: rgba(200,41,42,0.18); pointer-events: none; }
    .empui-hero::after { content: ''; position: absolute; bottom: -90px; left: -90px; width: 260px; height: 260px; border-radius: 50%; background: rgba(2,132,199,0.14); pointer-events: none; }
    .empui-hero-left { position: relative; z-index: 1; }
    .empui-title { font-size: 1.25rem; font-weight: 900; color: #fff; margin: 0 0 6px; letter-spacing: -0.02em; }
    .empui-sub { font-size: .82rem; color: #9ca3af; margin: 0; }
    .empui-hero-right { position: relative; z-index: 1; display: flex; gap: 10px; flex-wrap: wrap; align-items: center; }
    .empui-chip {
        display: inline-flex; align-items: center; gap: 6px; padding: 6px 10px;
        border: 1px solid rgba(255,255,255,0.14); border-radius: 999px; color: #e5e7eb;
        background: rgba(255,255,255,0.06); font-size: .75rem;
    }
    .empui-btn {
        display: inline-flex; align-items: center; gap: 10px; padding: 11px 18px; background: #c8292a; color: #fff;
        border: none; border-radius: 12px; font-family: 'Sora', sans-serif; font-size: .86rem; font-weight: 900;
        cursor: pointer; transition: background .15s, box-shadow .15s, transform .15s; text-decoration: none;
        box-shadow: 0 4px 20px rgba(200,41,42,.5); white-space: nowrap;
    }
    .empui-btn:hover { background: #a81f20; color: #fff; box-shadow: 0 10px 34px rgba(200,41,42,.62); transform: translateY(-1px); }
    .empui-btn-sec {
        display: inline-flex; align-items: center; gap: 7px; padding: 9px 14px; background: #fff; color: #374151;
        border: 1px solid #e5e7eb; border-radius: 10px; font-family: 'Sora', sans-serif; font-size: .82rem; font-weight: 800;
        text-decoration: none; cursor: pointer; transition: all .15s; white-space: nowrap;
    }
    .empui-btn-sec:hover { border-color: #c8292a; color: #c8292a; background: #fff5f5; transform: translateY(-1px); }

    .empui-stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; margin-bottom: 18px; }
    @media (max-width: 1100px) { .empui-stats { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 600px) { .empui-stats { grid-template-columns: 1fr; } }
    .empui-stat {
        background: rgba(255,255,255,0.92); backdrop-filter: blur(6px);
        border: 1px solid rgba(229,231,235,0.9); border-radius: 16px;
        padding: 16px 18px; display: flex; gap: 12px; align-items: flex-start;
        position: relative; overflow: hidden; box-shadow: 0 10px 30px rgba(17,24,39,0.06);
        transition: box-shadow .15s, transform .15s;
    }
    .empui-stat:hover { box-shadow: 0 12px 34px rgba(17,24,39,0.10); transform: translateY(-2px); }
    .empui-stat::after { content: ''; position: absolute; bottom: 0; left: 0; right: 0; height: 3px; }
    .empui-stat.s-blue::after { background: #0284c7; }
    .empui-stat.s-amber::after { background: #d97706; }
    .empui-stat.s-green::after { background: #16a34a; }
    .empui-stat.s-red::after { background: #c8292a; }
    .empui-ico { width: 38px; height: 38px; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .empui-stat.s-blue .empui-ico { background: #f0f9ff; color: #0284c7; }
    .empui-stat.s-amber .empui-ico { background: #fffbeb; color: #d97706; }
    .empui-stat.s-green .empui-ico { background: #f0fdf4; color: #16a34a; }
    .empui-stat.s-red .empui-ico { background: #fff0f0; color: #c8292a; }
    .empui-lbl { font-size: .67rem; font-weight: 900; text-transform: uppercase; letter-spacing: .09em; color: #9ca3af; margin-bottom: 4px; }
    .empui-val { font-size: 1.05rem; font-weight: 900; color: #111827; line-height: 1.15; }
    .empui-mono { font-family: 'DM Mono', monospace; font-variant-numeric: tabular-nums; }

    .empui-card {
        background: rgba(255,255,255,0.92); backdrop-filter: blur(6px);
        border: 1px solid rgba(229,231,235,0.9); border-radius: 16px; overflow: hidden;
        box-shadow: 0 10px 30px rgba(17,24,39,0.06);
    }
    .empui-card-head { padding: 14px 18px; border-bottom: 1px solid #f3f4f6; display: flex; align-items: center; justify-content: space-between; gap: 10px; flex-wrap: wrap; }
    .empui-card-title { font-size: .82rem; font-weight: 900; color: #111827; margin: 0; display: flex; align-items: center; gap: 8px; }
    .empui-dot { width: 8px; height: 8px; border-radius: 50%; background: #c8292a; display: inline-block; }
    .empui-card-body { padding: 18px; }

    .empui-table { margin: 0; }
    .empui-table thead th { font-size: .72rem; text-transform: uppercase; letter-spacing: .10em; color: #6b7280; border-bottom: 1px solid #eef2f7 !important; }
    .empui-table td { vertical-align: middle; }
    .empui-muted { color: #6b7280; font-size: .82rem; }

    .empui-pill {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 5px 12px; border-radius: 999px;
        font-size: .73rem; font-weight: 900; letter-spacing: .06em; text-transform: uppercase;
        border: 1px solid transparent;
    }
    .empui-pill.pending { background: #fffbeb; color: #d97706; border-color: #fde68a; }
    .empui-pill.approved { background: #f0fdf4; color: #16a34a; border-color: #bbf7d0; }
    .empui-pill.rejected { background: #fff1f2; color: #e11d48; border-color: #fecdd3; }
    .empui-pill.active { background: #eff6ff; color: #1d4ed8; border-color: #bfdbfe; }
    .empui-pill.neutral { background: #f4f5f7; color: #6b7280; border-color: #e5e7eb; }

    /* Back-compat classes used by existing employee finance pages */
    .emp-badge { display: inline-flex; align-items: center; justify-content: center; padding: 4px 10px; border-radius: 999px; font-size: .74rem; font-weight: 800; border: 1px solid transparent; }
    .emp-badge-pending { background: #fffbeb; color: #d97706; border-color: #fde68a; }
    .emp-badge-approved { background: #f0fdf4; color: #16a34a; border-color: #bbf7d0; }
    .emp-badge-inactive { background: #fff1f2; color: #e11d48; border-color: #fecdd3; }
    .emp-badge-active { background: #eff6ff; color: #1d4ed8; border-color: #bfdbfe; }

    .empui-filter { display: flex; gap: 8px; flex-wrap: wrap; }
    .empui-filter a {
        display: inline-flex; align-items: center; justify-content: center;
        padding: 7px 12px; border-radius: 999px; font-size: .78rem; font-weight: 900;
        border: 1px solid #e5e7eb; text-decoration: none;
        transition: all .15s;
    }
    .empui-filter a.is-active { background: #111827; border-color: #111827; color: #fff; }
    .empui-filter a:not(.is-active) { background: #fff; color: #374151; }
    .empui-filter a:not(.is-active):hover { border-color: #c8292a; color: #c8292a; background: #fff5f5; transform: translateY(-1px); }
</style>
