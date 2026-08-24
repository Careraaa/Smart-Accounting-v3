<style>
    @import url('https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Sora:wght@400;600;700;800&display=swap');

    .empui-page { font-family: 'Sora', sans-serif; }

    /* ── Backdrop ─────────────────────────────────────────────── */
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

    /* ── Hero ─────────────────────────────────────────────────── */
    .empui-hero {
        background: linear-gradient(135deg, #111827 0%, #0b1220 55%, #111827 100%);
        border-radius: 18px; padding: 22px 24px;
        margin-top: 24px; margin-bottom: 18px;
        display: flex; align-items: center; justify-content: space-between; gap: 18px; flex-wrap: wrap;
        position: relative; overflow: hidden;
        border: 1px solid rgba(255,255,255,0.08);
    }
    .empui-hero::before { content: ''; position: absolute; top: -70px; right: -70px; width: 260px; height: 260px; border-radius: 50%; background: rgba(200,41,42,0.18); pointer-events: none; }
    .empui-hero::after  { content: ''; position: absolute; bottom: -90px; left: -90px; width: 260px; height: 260px; border-radius: 50%; background: rgba(2,132,199,0.14); pointer-events: none; }
    .empui-hero-left  { position: relative; z-index: 1; }
    .empui-hero-right { position: relative; z-index: 1; display: flex; gap: 10px; flex-wrap: wrap; align-items: center; }
    .empui-title { font-size: 1.25rem; font-weight: 900; color: #fff; margin: 0 0 6px; letter-spacing: -0.02em; }
    .empui-sub   { font-size: .82rem; color: #9ca3af; margin: 0; }
    .empui-chip  {
        display: inline-flex; align-items: center; gap: 6px; padding: 6px 10px;
        border: 1px solid rgba(255,255,255,0.14); border-radius: 999px; color: #e5e7eb;
        background: rgba(255,255,255,0.06); font-size: .75rem;
    }

    /* ── Buttons ──────────────────────────────────────────────── */
    .empui-btn {
        display: inline-flex; align-items: center; gap: 10px; padding: 11px 18px;
        background: #c8292a; color: #fff; border: none; border-radius: 12px;
        font-family: 'Sora', sans-serif; font-size: .86rem; font-weight: 900;
        cursor: pointer; transition: background .15s, box-shadow .15s, transform .15s;
        text-decoration: none; box-shadow: 0 4px 20px rgba(200,41,42,.5); white-space: nowrap;
    }
    .empui-btn:hover { background: #a81f20; color: #fff; box-shadow: 0 10px 34px rgba(200,41,42,.62); transform: translateY(-1px); }
    .empui-btn:disabled, .empui-btn[disabled] { opacity: .5; cursor: not-allowed; transform: none; box-shadow: none; }

    .empui-btn-sec {
        display: inline-flex; align-items: center; gap: 7px; padding: 9px 14px;
        background: #fff; color: #374151; border: 1px solid #e5e7eb; border-radius: 10px;
        font-family: 'Sora', sans-serif; font-size: .82rem; font-weight: 800;
        text-decoration: none; cursor: pointer; transition: all .15s; white-space: nowrap;
    }
    .empui-btn-sec:hover { border-color: #c8292a; color: #c8292a; background: #fff5f5; transform: translateY(-1px); }

    /* ── Stat cards ───────────────────────────────────────────── */
    .empui-stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; margin-bottom: 18px; }
    @media (max-width: 1100px) { .empui-stats { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 600px)  { .empui-stats { grid-template-columns: 1fr; } }

    .empui-stat {
        background: rgba(255,255,255,0.95); backdrop-filter: blur(6px);
        border: 1px solid rgba(229,231,235,0.9); border-radius: 16px;
        padding: 18px 20px; display: flex; gap: 14px; align-items: flex-start;
        position: relative; overflow: hidden;
        box-shadow: 0 10px 30px rgba(17,24,39,0.06);
        transition: box-shadow .15s, transform .15s;
    }
    .empui-stat:hover { box-shadow: 0 14px 36px rgba(17,24,39,0.11); transform: translateY(-2px); }
    .empui-stat::after { content: ''; position: absolute; bottom: 0; left: 0; right: 0; height: 3px; border-radius: 0 0 14px 14px; }
    .empui-stat.s-blue::after  { background: #0284c7; }
    .empui-stat.s-amber::after { background: #d97706; }
    .empui-stat.s-green::after { background: #16a34a; }
    .empui-stat.s-red::after   { background: #c8292a; }

    .empui-ico { width: 40px; height: 40px; border-radius: 11px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .empui-stat.s-blue  .empui-ico { background: #f0f9ff; color: #0284c7; }
    .empui-stat.s-amber .empui-ico { background: #fffbeb; color: #d97706; }
    .empui-stat.s-green .empui-ico { background: #f0fdf4; color: #16a34a; }
    .empui-stat.s-red   .empui-ico { background: #fff0f0; color: #c8292a; }

    .empui-lbl  { font-size: .67rem; font-weight: 700; text-transform: uppercase; letter-spacing: .09em; color: #9ca3af; margin-bottom: 4px; }
    .empui-val  { font-size: 1.1rem; font-weight: 900; color: #111827; line-height: 1.15; }
    .empui-mono { font-family: 'DM Mono', monospace; font-variant-numeric: tabular-nums; }
    .empui-muted { color: #6b7280; font-size: .82rem; }

    /* ── Cards ────────────────────────────────────────────────── */
    .empui-card {
        background: rgba(255,255,255,0.95); backdrop-filter: blur(6px);
        border: 1px solid rgba(229,231,235,0.9); border-radius: 16px; overflow: hidden;
        box-shadow: 0 10px 30px rgba(17,24,39,0.06); margin-bottom: 16px;
    }
    .empui-card:last-child { margin-bottom: 0; }
    .empui-card-head {
        padding: 14px 18px; border-bottom: 1px solid #f3f4f6;
        display: flex; align-items: center; justify-content: space-between; gap: 10px; flex-wrap: wrap;
    }
    .empui-card-title { font-size: .82rem; font-weight: 900; color: #111827; margin: 0; display: flex; align-items: center; gap: 8px; }
    .empui-dot { width: 8px; height: 8px; border-radius: 50%; background: #c8292a; display: inline-block; flex-shrink: 0; }
    .empui-card-body { padding: 18px; }

    /* ── Table ────────────────────────────────────────────────── */
    .empui-table { margin: 0; }
    .empui-table thead tr { background: #f8f9fb; }
    .empui-table thead th {
        font-size: .68rem; font-weight: 700; text-transform: uppercase; letter-spacing: .10em;
        color: #6b7280; border-bottom: 1px solid #eef2f7 !important; padding: 11px 16px;
        white-space: nowrap;
    }
    .empui-table tbody td { padding: 12px 16px; vertical-align: middle; border-bottom: 1px solid #f3f4f6; }
    .empui-table tbody tr:last-child td { border-bottom: none; }
    .empui-table tbody tr:hover td { background: #fafafa; }
    .empui-clickable-row:hover td { background: #f5f7ff !important; }
    .empui-clickable-row:active td { background: #eef1fb !important; }

    /* ── Status pills ─────────────────────────────────────────── */
    .empui-pill {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 4px 11px; border-radius: 999px;
        font-size: .7rem; font-weight: 700; letter-spacing: .05em; text-transform: uppercase;
        border: 1px solid transparent; white-space: nowrap;
    }
    .empui-pill::before { content: ''; width: 5px; height: 5px; border-radius: 50%; flex-shrink: 0; }
    .empui-pill.pending  { background: #fffbeb; color: #b45309; border-color: #fde68a; }
    .empui-pill.pending::before  { background: #d97706; }
    .empui-pill.approved { background: #f0fdf4; color: #15803d; border-color: #bbf7d0; }
    .empui-pill.approved::before { background: #16a34a; }
    .empui-pill.rejected { background: #fff1f2; color: #be123c; border-color: #fecdd3; }
    .empui-pill.rejected::before { background: #e11d48; }
    .empui-pill.active   { background: #eff6ff; color: #1d4ed8; border-color: #bfdbfe; }
    .empui-pill.active::before   { background: #3b82f6; }
    .empui-pill.settled  { background: #f0fdf4; color: #15803d; border-color: #bbf7d0; }
    .empui-pill.settled::before  { background: #16a34a; }
    .empui-pill.deducted { background: #f5f3ff; color: #6d28d9; border-color: #ddd6fe; }
    .empui-pill.deducted::before { background: #8b5cf6; }
    .empui-pill.neutral  { background: #f4f5f7; color: #6b7280; border-color: #e5e7eb; }
    .empui-pill.neutral::before  { background: #9ca3af; }
    .empui-pill.overtime  { background: #eff6ff; color: #1d4ed8; border-color: #bfdbfe; }
    .empui-pill.overtime::before  { background: #3b82f6; }
    .empui-pill.undertime { background: #fffbeb; color: #b45309; border-color: #fde68a; }
    .empui-pill.undertime::before { background: #d97706; }

    /* Back-compat aliases */
    .emp-badge { display: inline-flex; align-items: center; padding: 4px 10px; border-radius: 999px; font-size: .7rem; font-weight: 700; border: 1px solid transparent; }
    .emp-badge-pending  { background: #fffbeb; color: #b45309; border-color: #fde68a; }
    .emp-badge-approved { background: #f0fdf4; color: #15803d; border-color: #bbf7d0; }
    .emp-badge-inactive { background: #fff1f2; color: #be123c; border-color: #fecdd3; }
    .emp-badge-active   { background: #eff6ff; color: #1d4ed8; border-color: #bfdbfe; }

    /* ── Filter tabs ──────────────────────────────────────────── */
    .empui-filter { display: flex; gap: 6px; flex-wrap: wrap; }
    .empui-filter a {
        display: inline-flex; align-items: center; padding: 6px 14px; border-radius: 999px;
        font-size: .78rem; font-weight: 700; border: 1px solid #e5e7eb; text-decoration: none;
        transition: all .15s;
    }
    .empui-filter a.is-active { background: #111827; border-color: #111827; color: #fff; }
    .empui-filter a:not(.is-active) { background: #fff; color: #374151; }
    .empui-filter a:not(.is-active):hover { border-color: #c8292a; color: #c8292a; background: #fff5f5; }

    /* ── Flash messages ───────────────────────────────────────── */
    .empui-flash {
        display: flex; align-items: center; gap: 10px; padding: 12px 16px;
        border-radius: 10px; font-size: .82rem; font-weight: 500; margin-bottom: 16px;
        animation: empFlashIn .3s ease;
    }
    .empui-flash.success { background: #f0fdf4; border: 1px solid #bbf7d0; color: #15803d; }
    .empui-flash.error   { background: #fff0f0; border: 1px solid #fecaca; color: #c8292a; }
    .empui-flash.warning { background: #fffbeb; border: 1px solid #fde68a; color: #b45309; }
    .empui-flash.info    { background: #eff6ff; border: 1px solid #bfdbfe; color: #1d4ed8; }
    @keyframes empFlashIn { from { opacity:0; transform:translateY(-6px); } to { opacity:1; transform:translateY(0); } }

    /* ── Form inputs ──────────────────────────────────────────── */
    .empui-field-label {
        display: block; font-size: .72rem; font-weight: 700;
        text-transform: uppercase; letter-spacing: .09em; color: #6b7280; margin-bottom: 6px;
    }
    .empui-field-label .opt { color: #9ca3af; font-weight: 400; text-transform: none; letter-spacing: 0; font-size: .72rem; }
    .empui-input, .empui-select, .empui-textarea {
        width: 100%; border: 1px solid #e5e7eb; border-radius: 10px; padding: 10px 14px;
        font-size: .845rem; font-family: 'Sora', sans-serif; color: #111827; background: #fff;
        outline: none; transition: border-color .15s, box-shadow .15s;
        appearance: none; -webkit-appearance: none;
    }
    .empui-input:focus, .empui-select:focus, .empui-textarea:focus {
        border-color: #c8292a; box-shadow: 0 0 0 3px rgba(200,41,42,0.08);
    }
    .empui-input:disabled, .empui-select:disabled, .empui-textarea:disabled {
        background: #f9fafb; color: #9ca3af; cursor: not-allowed;
    }
    .empui-field-hint { font-size: .74rem; color: #9ca3af; margin-top: 5px; }
    .empui-field-hint.highlight { color: #0284c7; font-weight: 600; }

    /* ── Blocked notice ───────────────────────────────────────── */
    .empui-notice {
        display: flex; align-items: flex-start; gap: 10px; padding: 12px 16px;
        background: #fffbeb; border: 1px solid #fde68a; border-radius: 10px;
        font-size: .82rem; color: #b45309; margin-bottom: 16px; line-height: 1.5;
    }
    .empui-notice svg { flex-shrink: 0; margin-top: 1px; }

    /* ── Progress bar ─────────────────────────────────────────── */
    .empui-progress-wrap { display: flex; align-items: center; gap: 8px; }
    .empui-progress-bar  { flex: 1; height: 6px; background: #f3f4f6; border-radius: 4px; overflow: hidden; }
    .empui-progress-fill { height: 100%; background: #16a34a; border-radius: 4px; transition: width .3s; }
    .empui-progress-pct  { font-size: .72rem; color: #9ca3af; white-space: nowrap; font-family: 'DM Mono', monospace; }
    .empui-progress-sub  { font-size: .72rem; color: #9ca3af; margin-top: 3px; font-family: 'DM Mono', monospace; }

    /* ── Empty state ──────────────────────────────────────────── */
    .empui-empty { text-align: center; padding: 48px 20px; }
    .empui-empty-icon { opacity: .2; margin-bottom: 12px; }
    .empui-empty-text { font-size: .845rem; color: #9ca3af; }

    /* ── Action buttons in table ──────────────────────────────── */
    .empui-tbl-btn {
        display: inline-flex; align-items: center; gap: 5px; padding: 6px 12px;
        border-radius: 8px; font-size: .78rem; font-weight: 700; border: 1px solid #e5e7eb;
        background: #fff; color: #374151; text-decoration: none; cursor: pointer;
        transition: all .13s; white-space: nowrap; font-family: 'Sora', sans-serif;
    }
    .empui-tbl-btn:hover       { background: #eff6ff; border-color: #bfdbfe; color: #1d4ed8; }
    .empui-tbl-btn.edit:hover  { background: #fffbeb; border-color: #fde68a; color: #d97706; }
    .empui-tbl-btn.del:hover   { background: #fff1f2; border-color: #fecdd3; color: #e11d48; }
    .empui-tbl-btn.del { border-color: #fecdd3; color: #e11d48; background: #fff1f2; }
</style>
