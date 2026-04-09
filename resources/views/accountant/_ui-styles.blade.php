@include('remittance-clerk._ui-styles')
<style>
    /* Hero chips (dark bar) */
    .remui-hero-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 10px;
        border: 1px solid rgba(255, 255, 255, 0.14);
        border-radius: 999px;
        color: #e5e7eb;
        background: rgba(255, 255, 255, 0.06);
        font-size: 0.75rem;
    }

    /* Chart panels (HR dashboard pattern) */
    .acd-page {
        font-family: 'Sora', sans-serif;
        --acd-border: #e8e8ef;
        --acd-muted: #9ca3af;
        --acd-ink: #111827;
        --acd-red: #c8292a;
    }
    .acd-charts {
        display: grid;
        grid-template-columns: 1.55fr 1fr;
        gap: 14px;
        margin-bottom: 14px;
    }
    @media (max-width: 1100px) {
        .acd-charts {
            grid-template-columns: 1fr;
        }
    }
    .acd-charts2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 14px;
        margin-bottom: 22px;
    }
    @media (max-width: 900px) {
        .acd-charts2 {
            grid-template-columns: 1fr;
        }
    }
    .acd-panel {
        border: 1px solid var(--acd-border);
        border-radius: 14px;
        background: #fff;
        overflow: hidden;
    }
    .acd-panel-hd {
        padding: 14px 18px;
        border-bottom: 1px solid #f3f4f6;
        display: flex;
        align-items: baseline;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
    }
    .acd-panel-hd h2 {
        margin: 0;
        font-size: 0.88rem;
        font-weight: 700;
        color: var(--acd-ink);
        letter-spacing: -0.02em;
    }
    .acd-panel-hd span {
        font-size: 0.72rem;
        color: var(--acd-muted);
        font-weight: 500;
    }
    .acd-panel-bd {
        padding: 8px 12px 4px;
    }
    .acd-chart {
        min-height: 260px;
    }

    /* Recent list feed */
    .acd-feed {
        margin: 0;
        padding: 0;
        list-style: none;
    }
    .acd-feed li {
        border-top: 1px solid #f3f4f6;
    }
    .acd-feed li:first-child {
        border-top: none;
    }
    .acd-feed a {
        display: flex;
        align-items: flex-start;
        gap: 14px;
        padding: 14px 18px;
        text-decoration: none;
        color: inherit;
        transition: background 0.12s;
    }
    .acd-feed a:hover {
        background: #fafafa;
    }
    .acd-av {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        background: #f4f4f6;
        color: #6b7280;
        font-size: 0.72rem;
        font-weight: 800;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        border: 1px solid #ececec;
    }
    .acd-feed-body {
        flex: 1;
        min-width: 0;
    }
    .acd-feed-title {
        font-size: 0.84rem;
        font-weight: 700;
        color: var(--acd-ink);
        margin: 0 0 4px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .acd-feed-meta {
        font-size: 0.74rem;
        color: #6b7280;
        margin: 0;
        line-height: 1.45;
    }
    .acd-feed-meta code {
        font-family: 'DM Mono', monospace;
        font-size: 0.72rem;
        background: #f8fafc;
        padding: 1px 6px;
        border-radius: 4px;
        color: #64748b;
    }
    .acd-feed-right {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 6px;
        flex-shrink: 0;
    }
    .acd-pill {
        font-size: 0.65rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        padding: 4px 10px;
        border-radius: 999px;
        border: 1px solid transparent;
    }
    .acd-pill-paid {
        background: #f0fdf4;
        border-color: #bbf7d0;
        color: #15803d;
    }
    .acd-pill-approved {
        background: #f0f9ff;
        border-color: #bae6fd;
        color: #0284c7;
    }
    .acd-pill-pending {
        background: #fffbeb;
        border-color: #fde68a;
        color: #b45309;
    }
    .acd-pill-rejected {
        background: #fef2f2;
        border-color: #fecaca;
        color: #b91c1c;
    }
    .acd-chevron {
        color: #d1d5db;
        font-size: 18px;
        margin-top: 2px;
    }
    .acd-empty {
        text-align: center;
        padding: 40px 20px;
        color: var(--acd-muted);
        font-size: 0.84rem;
    }

    /* Salary-computation-style flash */
    .acd-flash {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 12px 16px;
        border-radius: 10px;
        font-size: 0.82rem;
        font-weight: 500;
        margin-bottom: 16px;
    }
    .acd-flash.success {
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        color: #15803d;
    }
    .acd-flash.error {
        background: #fff0f0;
        border: 1px solid #fecaca;
        color: #c8292a;
    }
    .acd-flash.info {
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        color: #1d4ed8;
    }

    /* Batch cards (payroll approval index) */
    .acd-batch-card {
        border: 1px solid var(--remui-border, rgba(17, 24, 39, 0.1));
        border-radius: 14px;
        background: #fff;
        box-shadow: 0 10px 26px rgba(15, 23, 42, 0.06);
        transition: box-shadow 0.15s, transform 0.15s;
        overflow: hidden;
    }
    .acd-batch-card:hover {
        box-shadow: 0 14px 36px rgba(15, 23, 42, 0.1);
        transform: translateY(-2px);
    }
    .acd-batch-inner {
        display: flex;
        align-items: center;
        gap: 18px;
        padding: 18px 20px;
        flex-wrap: wrap;
    }
    .acd-batch-icon {
        width: 52px;
        height: 52px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 1.35rem;
        flex-shrink: 0;
    }
    .acd-batch-icon.a {
        background: linear-gradient(135deg, #c8292a, #9f1e1f);
    }
    .acd-batch-icon.b {
        background: linear-gradient(135deg, #0284c7, #0369a1);
    }
    .acd-batch-stat {
        text-align: center;
        min-width: 88px;
    }
    .acd-batch-stat small {
        display: block;
        font-size: 0.68rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: #9ca3af;
        margin-bottom: 4px;
    }
    .acd-batch-stat strong {
        font-family: 'DM Mono', monospace;
        font-size: 0.92rem;
        color: #111827;
    }

    /* Filter row (reports) — prl-inspired */
    .acd-filter-bar {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 14px 16px;
        margin-bottom: 18px;
        display: flex;
        flex-wrap: wrap;
        gap: 14px;
        align-items: flex-end;
    }
    .acd-filter-bar .form-label {
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: #9ca3af;
        margin-bottom: 6px;
    }
    .acd-filter-bar .form-select {
        border-radius: 8px;
        border-color: #e5e7eb;
        font-size: 0.84rem;
        min-width: 140px;
    }
</style>
