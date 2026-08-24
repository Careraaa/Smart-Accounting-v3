/**
 * payroll-batch-edit-adapter.js
 *
 * Loaded on the batch/edit-employee page BEFORE payroll-form.js.
 * It pre-populates the employee select + period inputs with values
 * injected by the blade via window._prl, so the existing payroll-form.js
 * fires a preview call immediately on page load with the correct context.
 *
 * Include order in your layout / stack:
 * 1. payroll-batch-edit-adapter.js ← this file
 * 2. payroll-form.js ← unchanged original
 */

(function () {
    // Only activate on the batch-edit page
    const pageMeta = document.querySelector('meta[name="page-id"]');
    if (
        !pageMeta ||
        !["payroll-create", "payroll-edit", "payroll-batch-edit"].includes(
            pageMeta.content,
        )
    )
        return;

    const cfg = window._prl || {};
    if (!cfg.prefillUserId || !cfg.prefillStart || !cfg.prefillEnd) return;

    // Wait for DOM to be ready (this script is in @push('head_scripts'), so DOM
    // may not exist yet — defer to DOMContentLoaded)
    document.addEventListener("DOMContentLoaded", function () {
        // ── Patch the employee <select> ───────────────────────────────
        const empSelect = document.getElementById("prl_user_id");
        if (empSelect) {
            // If the option exists, select it
            const opt = empSelect.querySelector(
                `option[value="${cfg.prefillUserId}"]`,
            );
            if (opt) {
                opt.selected = true;
            } else {
                // Option may not exist because this page only has one "employee"
                // If the select isn't present at all on the batch-edit page, skip
            }
        }

        // ── Patch the period inputs ───────────────────────────────────
        const startInput = document.getElementById("prl_period_start");
        const endInput = document.getElementById("prl_period_end");

        if (startInput && !startInput.value)
            startInput.value = cfg.prefillStart;
        if (endInput && !endInput.value) endInput.value = cfg.prefillEnd;

        // Force a preview fetch after a short tick so payroll-form.js's
        // bootstrap sequence has initialised by then
        setTimeout(function () {
            // Simulate a change event on the employee select to trigger payroll-form.js
            if (empSelect) {
                empSelect.dispatchEvent(new Event("change", { bubbles: true }));
            }
        }, 50);
    });
})();
