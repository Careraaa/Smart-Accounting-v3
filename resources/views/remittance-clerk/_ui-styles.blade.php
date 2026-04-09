<style>
    @import url('https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Sora:wght@400;600;700;800&display=swap');

    :root {
        --remui-accent: #c8292a;
        --remui-accent-2: #e11d48;
        --remui-ink: #111827;
        --remui-muted: #6b7280;
        --remui-border: rgba(17, 24, 39, 0.10);
        --remui-card: rgba(255, 255, 255, 0.92);
        --remui-shadow: 0 14px 40px rgba(15, 23, 42, 0.08);
        --remui-radius: 16px;
    }

    /* Page shell */
    .remui-page {
        position: relative;
        padding: 12px 4px 24px;
        font-family: 'Sora', sans-serif;
    }

    .remui-backdrop {
        position: absolute;
        inset: -10px -10px auto -10px;
        height: 340px;
        background:
            radial-gradient(220px 220px at 10% 35%, rgba(200,41,42,0.14), transparent 60%),
            radial-gradient(260px 260px at 85% 10%, rgba(2,132,199,0.12), transparent 60%),
            radial-gradient(240px 240px at 70% 70%, rgba(22,163,74,0.10), transparent 60%),
            linear-gradient(to bottom, rgba(17,24,39,0.04), transparent 70%);
        pointer-events: none;
        filter: saturate(110%);
        z-index: 0;
    }

    /* Optional grid overlay (salary-computation / HR atmosphere) */
    .remui-grid {
        position: absolute;
        inset: 0;
        background-image: linear-gradient(to right, rgba(17, 24, 39, 0.06) 1px, transparent 1px),
            linear-gradient(to bottom, rgba(17, 24, 39, 0.06) 1px, transparent 1px);
        background-size: 48px 48px;
        mask-image: radial-gradient(closest-side at 50% 30%, rgba(0, 0, 0, 0.75), transparent 80%);
        opacity: 0.5;
        pointer-events: none;
    }

    /* Ensure content stays above backdrop without extra wrappers */
    .remui-page > :not(.remui-backdrop) {
        position: relative;
        z-index: 1;
    }

    /* Hero */
    .remui-hero {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        padding: 22px 24px;
        border-radius: 18px;
        background: linear-gradient(135deg, #111827 0%, #0b1220 55%, #111827 100%);
        border: 1px solid rgba(255,255,255,0.08);
        position: relative;
        overflow: hidden;
    }

    .remui-hero::before {
        content: '';
        position: absolute;
        top: -70px;
        right: -70px;
        width: 260px;
        height: 260px;
        border-radius: 50%;
        background: rgba(200,41,42,0.18);
        pointer-events: none;
    }

    .remui-hero::after {
        content: '';
        position: absolute;
        bottom: -90px;
        left: -90px;
        width: 260px;
        height: 260px;
        border-radius: 50%;
        background: rgba(2,132,199,0.14);
        pointer-events: none;
    }

    .remui-title {
        margin: 0;
        font-weight: 900;
        letter-spacing: -0.02em;
        color: #fff;
        font-size: 1.25rem;
    }

    .remui-subtitle {
        margin-top: 4px;
        margin-bottom: 0;
        font-size: 0.83rem;
        color: #9ca3af;
        line-height: 1.25rem;
    }

    /* Content cards */
    .remui-card {
        border: 1px solid var(--remui-border) !important;
        border-radius: var(--remui-radius) !important;
        background: var(--remui-card) !important;
        box-shadow: var(--remui-shadow) !important;
        overflow: hidden;
    }

    .remui-card .card-header {
        background: transparent !important;
        border-bottom: 1px solid var(--remui-border) !important;
        padding: 14px 16px !important;
    }

    .remui-card .card-title {
        font-size: 0.9rem;
        font-weight: 800;
        color: var(--remui-ink);
        letter-spacing: -0.01em;
    }

    .remui-card .card-body {
        padding: 16px !important;
    }

    /* Stats */
    .card-statistic {
        position: relative;
        border-radius: 14px !important;
        border: 1px solid var(--remui-border) !important;
        background: #fff !important;
        box-shadow: 0 10px 26px rgba(15, 23, 42, 0.06) !important;
        overflow: hidden;
    }

    .card-statistic .stat-label {
        font-size: 0.72rem;
        font-weight: 800;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: rgba(17, 24, 39, 0.58);
    }

    .card-statistic .card-icon {
        position: absolute;
        right: 14px;
        top: 16px;
        pointer-events: none;
    }

    /* Dashboard legacy blocks (dash-*) — restyled to match salary computation index */
    .remui-page .dash-label {
        font-size: 0.67rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.09em;
        color: #9ca3af;
    }

    .remui-page .dash-value {
        font-size: 1.6rem;
        font-weight: 800;
        color: #111827;
        line-height: 1;
        font-variant-numeric: tabular-nums;
        font-family: 'DM Mono', monospace;
    }

    .remui-page .dash-sub {
        font-size: 0.73rem;
        color: #9ca3af;
        margin-top: 4px;
    }

    .remui-page .dash-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        background: #f3f4f6;
        color: #6b7280;
    }

    .remui-page .dash-icon.di-green { background: #f0fdf4; color: #16a34a; }
    .remui-page .dash-icon.di-red { background: #fff1f2; color: #e11d48; }
    .remui-page .dash-icon.di-blue { background: #f0f9ff; color: #0284c7; }
    .remui-page .dash-icon.di-amber { background: #fffbeb; color: #d97706; }

    .remui-page .dash-progress {
        height: 8px;
        border-radius: 999px;
        background: #f3f4f6;
        overflow: hidden;
    }

    .remui-page .dash-progress .progress-bar {
        border-radius: 999px;
    }

    /* Tables */
    .remui-table thead th {
        font-size: 0.72rem;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        font-weight: 800;
        color: rgba(17, 24, 39, 0.62);
        background: rgba(250, 250, 252, 0.9) !important;
        border-bottom: 1px solid var(--remui-border) !important;
        padding: 12px 14px !important;
        white-space: nowrap;
    }

    .remui-table tbody td {
        padding: 12px 14px !important;
        border-bottom: 1px solid rgba(17, 24, 39, 0.06) !important;
        vertical-align: middle;
    }

    .remui-table tbody tr:hover {
        background: rgba(200, 41, 42, 0.04);
    }

    /* Small “pill” badges (mapped to existing emp-badge usage) */
    .emp-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 3px 10px;
        border-radius: 999px;
        font-size: 0.70rem;
        font-weight: 800;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        border: 1px solid transparent;
        white-space: nowrap;
    }

    .emp-badge-active,
    .emp-badge-approved {
        background: #f0fdf4;
        color: #16a34a;
        border-color: rgba(22, 163, 74, 0.20);
    }

    .emp-badge-pending {
        background: #fffbeb;
        color: #d97706;
        border-color: rgba(217, 119, 6, 0.20);
    }

    .emp-badge-inactive {
        background: #fff1f2;
        color: #e11d48;
        border-color: rgba(225, 29, 72, 0.18);
    }

    /* Action buttons (mapped to existing emp-action-btn usage) */
    .emp-action-btn {
        width: 30px;
        height: 30px;
        border-radius: 7px;
        border: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        text-decoration: none;
        font-size: 13px;
        transition: background 0.13s, color 0.13s, transform 0.13s;
        background: #f4f5f7;
        color: #6b7280;
        padding: 0;
        white-space: nowrap;
    }

    .emp-action-btn:hover {
        transform: translateY(-1px);
        background: #eff6ff;
        color: #3b82f6;
    }

    /* Buttons inside dark hero need "chip" styling (like HR) */
    .remui-hero .emp-action-btn {
        width: auto;
        height: auto;
        padding: 9px 14px;
        border-radius: 10px;
        border: 1px solid rgba(255, 255, 255, 0.14);
        background: rgba(255, 255, 255, 0.06);
        color: #e5e7eb;
        box-shadow: none;
        backdrop-filter: blur(6px);
        font-size: 0.82rem;
        font-weight: 800;
        gap: 6px;
    }

    .remui-hero .emp-action-btn:hover {
        border-color: rgba(255, 255, 255, 0.28);
        background: rgba(255, 255, 255, 0.10);
        color: #ffffff;
        transform: translateY(-1px);
    }

    .emp-action-view {
        /* icon button state handled by hover; keep semantic class */
    }

    .emp-action-edit {
        /* icon button state handled by hover; keep semantic class */
    }

    .emp-action-back {
        /* icon button state handled by hover; keep semantic class */
    }

    .emp-action-danger {
        /* icon button danger hover */
    }

    .emp-action-btn.emp-action-danger:hover {
        background: #fff1f2;
        color: #e11d48;
    }

    .emp-action-approve {
        /* icon button approve hover */
    }

    .emp-action-btn.emp-action-approve:hover {
        background: #f0fdf4;
        color: #16a34a;
    }

    /* Sort links */
    .sort-link {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: inherit;
        text-decoration: none;
        font-weight: 800;
    }

    .sort-link:hover {
        color: var(--remui-accent);
        text-decoration: none;
    }

    /* Form section divider (used in create remittance) */
    .prl-section-divider {
        margin: 10px 0 16px;
        font-size: 0.72rem;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 0.10em;
        color: rgba(17, 24, 39, 0.60);
    }

    /* Mobile tweaks */
    @media (max-width: 576px) {
        .remui-hero {
            align-items: flex-start;
            flex-direction: column;
        }
    }
</style>

