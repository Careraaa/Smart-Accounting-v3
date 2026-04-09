<style>
    @import url('https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Sora:wght@400;600;700;800&display=swap');

    .emp-page {
        font-family: 'Sora', sans-serif;
    }

    /* ── Topbar ─────────────────────────────────────────────────── */
    .emp-topbar {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 24px;
        flex-wrap: wrap;
    }

    .emp-topbar-title {
        font-size: 1.35rem;
        font-weight: 800;
        color: #111827;
        letter-spacing: -0.02em;
        margin: 0 0 2px;
    }

    .emp-topbar-sub {
        font-size: 0.78rem;
        color: #9ca3af;
        margin: 0;
    }

    .emp-btn-sec {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 9px 16px;
        background: #fff;
        color: #374151;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        font-family: 'Sora', sans-serif;
        font-size: 0.82rem;
        font-weight: 600;
        text-decoration: none;
        cursor: pointer;
        transition: all 0.15s;
        white-space: nowrap;
    }

    .emp-btn-sec:hover {
        border-color: #c8292a;
        color: #c8292a;
        background: #fff5f5;
    }

    /* ── Tab card shell ─────────────────────────────────────────── */
    .emp-form-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        overflow: hidden;
    }

    /* ── Tab nav ────────────────────────────────────────────────── */
    .emp-tab-nav {
        display: flex;
        overflow-x: auto;
        border-bottom: 1px solid #e5e7eb;
        background: #f8f9fb;
        scrollbar-width: none;
        -ms-overflow-style: none;
    }

    .emp-tab-nav::-webkit-scrollbar {
        display: none;
    }

    .emp-tab-btn {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 13px 18px;
        font-family: 'Sora', sans-serif;
        font-size: 0.78rem;
        font-weight: 600;
        color: #6b7280;
        background: none;
        border: none;
        border-bottom: 2px solid transparent;
        cursor: pointer;
        white-space: nowrap;
        transition: color 0.13s, border-color 0.13s;
        margin-bottom: -1px;
    }

    .emp-tab-btn:hover {
        color: #111827;
    }

    .emp-tab-btn.active {
        color: #c8292a;
        border-bottom-color: #c8292a;
        background: #fff;
    }

    .emp-tab-num {
        width: 18px;
        height: 18px;
        border-radius: 50%;
        background: #f3f4f6;
        color: #9ca3af;
        font-size: 0.62rem;
        font-weight: 800;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background 0.13s, color 0.13s;
        flex-shrink: 0;
    }

    .emp-tab-btn.active .emp-tab-num {
        background: #c8292a;
        color: #fff;
    }

    .emp-tab-btn.done .emp-tab-num {
        background: #16a34a;
        color: #fff;
    }

    /* ── Tab content ────────────────────────────────────────────── */
    .emp-tab-body {
        padding: 28px 28px 8px;
    }

    .emp-tab-pane {
        display: none;
    }

    .emp-tab-pane.active {
        display: block;
    }

    /* ── Form nav footer ────────────────────────────────────────── */
    .emp-form-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px 28px;
        border-top: 1px solid #f3f4f6;
        background: #fafafa;
    }

    .emp-form-footer-right {
        display: flex;
        gap: 10px;
        align-items: center;
    }

    .emp-btn-nav {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 10px 20px;
        border-radius: 10px;
        font-family: 'Sora', sans-serif;
        font-size: 0.845rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.15s;
        border: none;
        text-decoration: none;
    }

    .emp-btn-prev {
        background: #fff;
        color: #374151;
        border: 1px solid #e5e7eb;
    }

    .emp-btn-prev:hover {
        border-color: #c8292a;
        color: #c8292a;
        background: #fff5f5;
    }

    .emp-btn-next {
        background: #111827;
        color: #fff;
    }

    .emp-btn-next:hover {
        background: #000;
        color: #fff;
    }

    .emp-btn-submit {
        background: #c8292a;
        color: #fff;
        box-shadow: 0 4px 14px rgba(200, 41, 42, 0.35);
    }

    .emp-btn-submit:hover {
        background: #a81f20;
        color: #fff;
    }

    /* ── Shared form field styles (used by all partials) ────────── */
    .emp-section-title-form {
        font-size: 1rem;
        font-weight: 800;
        color: #111827;
        letter-spacing: -0.01em;
        margin: 0 0 20px;
    }

    .emp-section-sub {
        font-size: 0.78rem;
        color: #9ca3af;
        margin-top: -14px;
        margin-bottom: 18px;
    }

    .emp-label {
        display: block;
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.09em;
        color: #6b7280;
        margin-bottom: 6px;
    }

    .emp-label .req {
        color: #c8292a;
    }

    .emp-label .opt {
        color: #9ca3af;
        font-weight: 400;
        text-transform: none;
        letter-spacing: 0;
        font-size: 0.72rem;
    }

    .emp-input,
    .emp-select,
    .emp-textarea {
        width: 100%;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        padding: 10px 14px;
        font-size: 0.845rem;
        font-family: 'Sora', sans-serif;
        color: #111827;
        background: #fff;
        outline: none;
        transition: border-color 0.15s, box-shadow 0.15s;
        appearance: none;
        -webkit-appearance: none;
    }

    .emp-input:focus,
    .emp-select:focus,
    .emp-textarea:focus {
        border-color: #c8292a;
        box-shadow: 0 0 0 3px rgba(200, 41, 42, 0.08);
    }

    .emp-input::placeholder,
    .emp-textarea::placeholder {
        color: #9ca3af;
    }

    .emp-input:read-only,
    .emp-input[readonly] {
        background: #f9fafb;
        color: #6b7280;
    }

    .emp-input.is-invalid,
    .emp-select.is-invalid,
    .emp-textarea.is-invalid {
        border-color: #ef4444 !important;
    }

    .emp-invalid {
        display: block;
        font-size: 0.75rem;
        color: #ef4444;
        margin-top: 4px;
    }

    /* Select wrapper with chevron */
    .emp-select-wrap {
        position: relative;
    }

    .emp-select-wrap .emp-chevron {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #9ca3af;
        pointer-events: none;
    }

    .emp-select-wrap .emp-select {
        padding-right: 36px;
    }

    /* Input with prefix */
    .emp-input-group {
        display: flex;
        align-items: stretch;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        overflow: hidden;
        transition: border-color 0.15s, box-shadow 0.15s;
    }

    .emp-input-group:focus-within {
        border-color: #c8292a;
        box-shadow: 0 0 0 3px rgba(200, 41, 42, 0.08);
    }

    .emp-input-group-text {
        display: flex;
        align-items: center;
        padding: 0 12px;
        background: #f9fafb;
        color: #9ca3af;
        font-size: 0.845rem;
        border-right: 1px solid #e5e7eb;
        white-space: nowrap;
        font-family: 'DM Mono', monospace;
    }

    .emp-input-group .emp-input {
        border: none;
        border-radius: 0;
        box-shadow: none !important;
    }

    /* Hint text */
    .emp-hint {
        font-size: 0.75rem;
        color: #9ca3af;
        margin-top: 5px;
    }

    /* Section divider */
    .emp-divider {
        font-size: 0.68rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.12em;
        color: #9ca3af;
        border-bottom: 1px solid #f3f4f6;
        padding-bottom: 10px;
        margin: 24px 0 18px;
    }

    /* Checkbox row */
    .emp-check-row {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 8px;
    }

    .emp-check-input {
        width: 16px;
        height: 16px;
        border-radius: 4px;
        border: 1.5px solid #e5e7eb;
        accent-color: #c8292a;
        cursor: pointer;
    }

    .emp-check-label {
        font-size: 0.845rem;
        font-weight: 600;
        color: #374151;
        cursor: pointer;
    }

    /* Dynamic rows (beneficiaries, skills, etc.) */
    .emp-dynamic-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
        margin-bottom: 12px;
    }

    .emp-dynamic-row {
        background: #fafafa;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 16px;
        position: relative;
    }

    .emp-dynamic-remove {
        position: absolute;
        top: 12px;
        right: 12px;
        width: 26px;
        height: 26px;
        border-radius: 6px;
        background: #fff;
        border: 1px solid #fecaca;
        color: #c8292a;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 14px;
        transition: background 0.13s;
        padding: 0;
    }

    .emp-dynamic-remove:hover {
        background: #fff0f0;
    }

    .emp-dynamic-add {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 9px 16px;
        background: #fff;
        color: #374151;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        font-family: 'Sora', sans-serif;
        font-size: 0.82rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.15s;
    }

    .emp-dynamic-add:hover {
        border-color: #c8292a;
        color: #c8292a;
        background: #fff5f5;
    }

    /* Alert boxes */
    .emp-alert {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        padding: 14px 16px;
        border-radius: 10px;
        font-size: 0.82rem;
        font-weight: 500;
        margin-bottom: 16px;
    }

    .emp-alert.warning {
        background: #fffbeb;
        border: 1px solid #fde68a;
        color: #b45309;
    }

    .emp-alert.info {
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        color: #1d4ed8;
    }

    .emp-alert.error {
        background: #fff0f0;
        border: 1px solid #fecaca;
        color: #c8292a;
    }

    /* Attachment card in form */
    .emp-att-form-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 14px;
        display: flex;
        flex-direction: column;
        gap: 10px;
        transition: border-color 0.13s;
    }

    .emp-att-form-card.has-file {
        border-color: #16a34a;
    }

    .emp-att-form-title {
        font-size: 0.78rem;
        font-weight: 700;
        color: #111827;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .emp-att-preview {
        width: 100%;
        height: 60px;
        object-fit: cover;
        border-radius: 6px;
    }

    .emp-att-file-row {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 8px 10px;
        background: #f9fafb;
        border-radius: 8px;
    }

    .emp-att-file-row a {
        font-size: 0.78rem;
        color: #374151;
        text-decoration: none;
        flex: 1;
        min-width: 0;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .emp-att-file-row a:hover {
        color: #c8292a;
    }

    .emp-att-time {
        font-size: 0.7rem;
        color: #9ca3af;
    }

    /* Errors summary */
    .emp-errors-card {
        background: #fff0f0;
        border: 1px solid #fecaca;
        border-radius: 12px;
        padding: 16px 20px;
        margin-bottom: 20px;
    }

    .emp-errors-title {
        font-size: 0.82rem;
        font-weight: 700;
        color: #c8292a;
        margin: 0 0 8px;
        display: flex;
        align-items: center;
        gap: 7px;
    }

    .emp-errors-list {
        margin: 0;
        padding-left: 18px;
        font-size: 0.8rem;
        color: #b91c1c;
        line-height: 1.7;
    }

    /* Grid helpers */
    .emp-row {
        display: grid;
        gap: 16px;
    }

    .emp-row.cols-2 {
        grid-template-columns: 1fr 1fr;
    }

    .emp-row.cols-3 {
        grid-template-columns: 1fr 1fr 1fr;
    }

    .emp-row.cols-4 {
        grid-template-columns: 1fr 1fr 1fr 1fr;
    }

    @media (max-width:768px) {

        .emp-row.cols-3,
        .emp-row.cols-4 {
            grid-template-columns: 1fr 1fr;
        }
    }

    @media (max-width:520px) {

        .emp-row.cols-2,
        .emp-row.cols-3,
        .emp-row.cols-4 {
            grid-template-columns: 1fr;
        }
    }

    .emp-field {
        /* just a container */
    }

    /* Username preview box */
    .emp-preview-box {
        background: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        padding: 10px 14px;
        font-family: 'DM Mono', monospace;
        font-size: 0.845rem;
        color: #6b7280;
        min-height: 42px;
    }
</style>
