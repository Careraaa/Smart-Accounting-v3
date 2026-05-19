// payroll-form.js
// ✅ SINGLE SOURCE OF TRUTH: ALL payroll math happens on the backend.
//    JS only:  (1) collects form inputs
//              (2) calls /payroll/preview to get server-computed values
//              (3) renders what the server returned — NO client-side money math.
document.addEventListener("DOMContentLoaded", function () {
    const pageMeta = document.querySelector('meta[name="page-id"]');
    const pageId = pageMeta?.content;
    const isCreate = pageId === "payroll-create";
    const isEdit = pageId === "payroll-edit";
    const isBatchEdit = pageId === "payroll-batch-edit";

    if (!isCreate && !isEdit && !isBatchEdit) return;

    // ── State (display-only — never submitted as money) ───────────────────
    const S = {
        // Attendance
        daysWorked: 0,
        hoursWorked: 0,
        presentDays: 0,
        lateDays: 0,
        absentDays: 0,
        // Salary
        basicSalary: 0,
        dailyRate: 0,
        // OT / UT
        otHours: 0,
        utHours: 0,
        otPay: 0,
        utDeduct: 0,
        // Statutory
        sss: 0,
        pagibig: 0,
        // Computed totals (from server — fully resolved, including manual items)
        adjustedGross: 0,
        netPay: 0,
        // Line items (HR-entered; these ARE submitted to the server)
        allowances: [], // { name, amount }
        deductions: [], // { name, amount }
    };

    let fetchId = 0;
    let isBootstrapping = true;

    // ── Helpers ───────────────────────────────────────────────────────────
    const g = (id) => document.getElementById(id);
    const fmt = (n) => `₱${(+n || 0).toFixed(2)}`;
    const sum = (arr) =>
        arr.reduce((t, x) => t + (parseFloat(x.amount) || 0), 0);

    // ── Element map ───────────────────────────────────────────────────────
    const EL = {
        employeeSelect: g("prl_user_id"),
        periodStart: g("prl_period_start"),
        periodEnd: g("prl_period_end"),

        daysWorked: g("prl_days_worked"),
        hoursWorked: g("prl_hours_worked"),
        daysAbsent: g("prl_days_absent"),

        basicDisplay: g("prl_basic_display"),
        basicInput: g("prl_basic_input"),

        otRow: g("prl_ot_row"),
        otHrs: g("prl_ot_hrs"),
        otPay: g("prl_ot_pay"),

        utRow: g("prl_ut_row"),
        utHrs: g("prl_ut_hrs"),
        utDeduct: g("prl_ut_deduct"),

        sssRow: g("prl_sss_row"),
        sssVal: g("prl_sss_val"),

        pagibigRow: g("prl_pagibig_row"),
        pagibigVal: g("prl_pagibig_val"),

        loadingRow: g("prl_loading_row"),
        adjusted: g("prl_adjusted"),
        netSalary: g("prl_net_salary"),

        allowName: g("prl_allow_name"),
        allowAmount: g("prl_allow_amount"),
        allowPills: g("prl_allow_pills"),
        allowEmpty: g("prl_allow_empty"),
        allowSubtotal: g("prl_allow_subtotal"),
        allowTotal: g("prl_allow_total"),
        allowHiddenWrap: g("prl_allow_hidden"),

        deductName: g("prl_deduct_name"),
        deductAmount: g("prl_deduct_amount"),
        deductPills: g("prl_deduct_pills"),
        deductEmpty: g("prl_deduct_empty"),
        deductSubtotal: g("prl_deduct_subtotal"),
        deductTotal: g("prl_deduct_total"),
        deductHiddenWrap: g("prl_deduct_hidden"),
    };

    // Guard — abort if any critical element is missing
    const critical = [
        "employeeSelect",
        "periodStart",
        "periodEnd",
        "basicDisplay",
        "netSalary",
        "allowPills",
        "deductPills",
    ];
    for (const k of critical) {
        if (!EL[k]) {
            console.error(
                `[payroll-form] Missing element for key "${k}" — aborting.`,
            );
            return;
        }
    }

    // ── Render — only function that writes to the DOM ─────────────────────
    // ✅ All displayed values come from S, which is populated entirely by the
    //    server's preview response. JS performs NO monetary calculations here.
    function render() {
        const totalAllow = sum(S.allowances);
        const totalDeduct = sum(S.deductions);

        // Attendance chips
        if (EL.daysWorked) EL.daysWorked.textContent = S.daysWorked;
        if (EL.hoursWorked)
            EL.hoursWorked.textContent = S.hoursWorked.toFixed(2);
        if (EL.daysAbsent) EL.daysAbsent.textContent = S.absentDays;

        // Basic salary (server-computed, hidden input is reference only)
        if (EL.basicDisplay) EL.basicDisplay.textContent = fmt(S.basicSalary);
        if (EL.basicInput) EL.basicInput.value = S.basicSalary.toFixed(2);

        // OT
        if (EL.otRow) EL.otRow.style.display = S.otPay > 0 ? "flex" : "none";
        if (S.otPay > 0) {
            if (EL.otHrs) EL.otHrs.textContent = `${S.otHours.toFixed(2)} hrs`;
            if (EL.otPay) EL.otPay.textContent = fmt(S.otPay);
        }

        // UT
        if (EL.utRow) EL.utRow.style.display = S.utDeduct > 0 ? "flex" : "none";
        if (S.utDeduct > 0) {
            if (EL.utHrs) EL.utHrs.textContent = `${S.utHours.toFixed(2)} hrs`;
            if (EL.utDeduct) EL.utDeduct.textContent = fmt(S.utDeduct);
        }

        // SSS
        if (EL.sssRow) EL.sssRow.style.display = S.sss > 0 ? "flex" : "none";
        if (S.sss > 0 && EL.sssVal) EL.sssVal.textContent = fmt(S.sss);

        // Pag-IBIG
        if (EL.pagibigRow)
            EL.pagibigRow.style.display = S.pagibig > 0 ? "flex" : "none";
        if (S.pagibig > 0 && EL.pagibigVal)
            EL.pagibigVal.textContent = fmt(S.pagibig);

        // Adjusted gross
        if (EL.adjusted) EL.adjusted.textContent = fmt(S.adjustedGross);

        // ✅ FIX #5: net_pay from the server already includes manual allowances
        //    and deductions (we sent them in the preview request). Display it
        //    directly — do NOT add totalAllow or subtract totalDeduct again.
        if (EL.netSalary) EL.netSalary.textContent = fmt(S.netPay);

        // Subtotals (display only — not added to net pay client-side)
        if (EL.allowTotal) EL.allowTotal.textContent = totalAllow.toFixed(2);
        if (EL.deductTotal) EL.deductTotal.textContent = totalDeduct.toFixed(2);
        if (EL.allowSubtotal)
            EL.allowSubtotal.style.display = S.allowances.length
                ? "flex"
                : "none";
        if (EL.deductSubtotal)
            EL.deductSubtotal.style.display = S.deductions.length
                ? "flex"
                : "none";

        // Pills + hidden POST inputs
        renderPills(
            "allow",
            S.allowances,
            EL.allowPills,
            EL.allowEmpty,
            EL.allowHiddenWrap,
            "allowances",
        );
        renderPills(
            "deduct",
            S.deductions,
            EL.deductPills,
            EL.deductEmpty,
            EL.deductHiddenWrap,
            "deductions",
        );
    }

    // ── Pill renderer ─────────────────────────────────────────────────────
    function renderPills(
        colorKey,
        list,
        pillArea,
        emptyEl,
        hiddenWrap,
        inputPrefix,
    ) {
        pillArea.querySelectorAll(".prl-pill").forEach((el) => el.remove());
        hiddenWrap.innerHTML = "";

        if (list.length === 0) {
            emptyEl.style.display = "";
            return;
        }
        emptyEl.style.display = "none";

        const colorClass = colorKey === "allow" ? "p-green" : "p-red";

        list.forEach((item, i) => {
            const pill = document.createElement("span");
            pill.className = `prl-pill ${colorClass}`;

            const textEl = document.createElement("span");
            textEl.className = "prl-pill-text";
            textEl.title = item.name;
            textEl.textContent = item.name;

            const dot = document.createTextNode(" · ");
            const amtEl = document.createElement("span");
            amtEl.className = "prl-pill-amt";
            amtEl.textContent = fmt(item.amount);

            const rmBtn = document.createElement("button");
            rmBtn.type = "button";
            rmBtn.className = "prl-pill-rm";
            rmBtn.setAttribute("aria-label", `Remove ${item.name}`);
            rmBtn.innerHTML = "&times;";
            rmBtn.addEventListener("click", () => {
                list.splice(i, 1);
                // Re-fetch so the server recomputes net pay without this item
                fetchPreview();
            });

            pill.append(textEl, dot, amtEl, rmBtn);
            pillArea.appendChild(pill);

            // Hidden POST inputs — submitted to the server on form save
            const ni = document.createElement("input");
            ni.type = "hidden";
            ni.name = `${inputPrefix}[${i}][name]`;
            ni.value = item.name;

            const ai = document.createElement("input");
            ai.type = "hidden";
            ai.name = `${inputPrefix}[${i}][amount]`;
            ai.value = item.amount;

            hiddenWrap.append(ni, ai);
        });
    }

    // ── fetchPreview — POSTs to backend, populates S, then renders ────────
    // ✅ Backend computes everything including manual allowances/deductions.
    //    JS displays what the server returned — no math performed here.
    async function fetchPreview() {
        const empId = EL.employeeSelect.value;
        const start = EL.periodStart.value;
        const end = EL.periodEnd.value;

        if (!empId || !start || !end) {
            Object.assign(S, {
                daysWorked: 0,
                hoursWorked: 0,
                presentDays: 0,
                lateDays: 0,
                absentDays: 0,
                basicSalary: 0,
                dailyRate: 0,
                otHours: 0,
                utHours: 0,
                otPay: 0,
                utDeduct: 0,
                sss: 0,
                pagibig: 0,
                adjustedGross: 0,
                netPay: 0,
            });
            if (EL.loadingRow) EL.loadingRow.style.display = "none";
            render();
            return;
        }

        if (EL.loadingRow) EL.loadingRow.style.display = "flex";

        const myId = ++fetchId;

        // ✅ Manual allowances/deductions are sent to the server so it can
        //    include them in the net_pay it returns.
        const body = new URLSearchParams({
            user_id: empId,
            period_start: start,
            period_end: end,
        });
        S.allowances.forEach((a, i) => {
            body.append(`allowances[${i}][name]`, a.name);
            body.append(`allowances[${i}][amount]`, a.amount);
        });
        S.deductions.forEach((d, i) => {
            body.append(`deductions[${i}][name]`, d.name);
            body.append(`deductions[${i}][amount]`, d.amount);
        });

        try {
            const previewUrl = window._prl?.previewUrl;
            if (!previewUrl)
                throw new Error("previewUrl missing from window._prl");

            const res = await fetch(previewUrl, {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded",
                    "X-CSRF-TOKEN":
                        document.querySelector('meta[name="csrf-token"]')
                            ?.content ?? "",
                    "X-Requested-With": "XMLHttpRequest",
                },
                body: body.toString(),
                cache: "no-store",
            });

            if (!res.ok) throw new Error(`HTTP ${res.status}`);
            const data = await res.json();
            if (myId !== fetchId) return; // stale — a newer fetch supersedes this

            // ✅ Populate S entirely from server response — zero client math
            S.daysWorked = data.days_worked ?? 0;
            S.hoursWorked = data.hours_worked ?? 0;
            S.presentDays = data.present_days ?? 0;
            S.lateDays = data.late_days ?? 0;
            S.absentDays = data.days_absent ?? 0;
            S.basicSalary = data.basic_salary ?? 0;
            S.dailyRate = data.daily_rate ?? 0;
            S.otHours = data.overtime_hours ?? 0;
            S.utHours = data.undertime_hours ?? 0;
            S.otPay = data.overtime_pay ?? 0;
            S.utDeduct = data.undertime_deduction ?? 0;
            S.sss = data.sss ?? 0;
            S.pagibig = data.pagibig ?? 0;
            S.adjustedGross = data.adjusted_gross ?? 0;
            // ✅ net_pay is fully computed by the server (includes manual items)
            S.netPay = data.net_pay ?? 0;
        } catch (err) {
            if (myId !== fetchId) return;
            console.error("[payroll-form] Preview fetch failed:", err.message);
            // ✅ No fallback math — show zeros. Do not fabricate numbers.
        }

        if (EL.loadingRow) EL.loadingRow.style.display = "none";
        render();
    }

    // ── Add allowance / deduction ─────────────────────────────────────────
    function addAllowance() {
        const name = EL.allowName.value.trim();
        const amount = parseFloat(EL.allowAmount.value);
        if (!name) {
            EL.allowName.focus();
            return;
        }
        if (isNaN(amount) || amount <= 0) {
            EL.allowAmount.focus();
            return;
        }
        S.allowances.push({ name, amount });
        EL.allowName.value = EL.allowAmount.value = "";
        EL.allowName.focus();
        // Re-fetch so server recomputes net_pay with the new allowance included
        fetchPreview();
    }

    function addDeduction() {
        const name = EL.deductName.value.trim();
        const amount = parseFloat(EL.deductAmount.value);
        if (!name) {
            EL.deductName.focus();
            return;
        }
        if (isNaN(amount) || amount <= 0) {
            EL.deductAmount.focus();
            return;
        }
        S.deductions.push({ name, amount });
        EL.deductName.value = EL.deductAmount.value = "";
        EL.deductName.focus();
        // Re-fetch so server recomputes net_pay with the new deduction included
        fetchPreview();
    }

    // ── Wire up buttons & keyboard ────────────────────────────────────────
    const allowBtn = g("prl_allow_btn");
    const deductBtn = g("prl_deduct_btn");

    if (allowBtn) allowBtn.addEventListener("click", addAllowance);
    if (deductBtn) deductBtn.addEventListener("click", addDeduction);

    EL.allowAmount.addEventListener("keydown", (e) => {
        if (e.key === "Enter") {
            e.preventDefault();
            addAllowance();
        }
    });
    EL.deductAmount.addEventListener("keydown", (e) => {
        if (e.key === "Enter") {
            e.preventDefault();
            addDeduction();
        }
    });

    // ── Change listeners ──────────────────────────────────────────────────
    function handleChange() {
        if (isBootstrapping) return;
        fetchPreview();
    }

    EL.employeeSelect.addEventListener("change", handleChange);
    EL.periodStart.addEventListener("change", handleChange);
    EL.periodEnd.addEventListener("change", handleChange);

    // ── Bootstrap — seed manual line items then kick off first fetch ──────
    const rawAllowances = window._prl?.initAllowances ?? [];
    const rawDeductions = window._prl?.initDeductions ?? [];

    S.allowances = rawAllowances
        .map((a) => ({
            name: String(a.name || ""),
            amount: parseFloat(a.amount) || 0,
        }))
        .filter((a) => a.name && a.amount > 0);

    S.deductions = rawDeductions
        .map((d) => ({
            name: String(d.name || ""),
            amount: parseFloat(d.amount) || 0,
        }))
        .filter((d) => d.name && d.amount > 0);

    // For batch edit, start from persisted payroll values so this page
    // matches the net pay shown on the batch confirm table.
    if (isBatchEdit && window._prl?.initComputed) {
        const c = window._prl.initComputed;
        S.daysWorked = Number(c.daysWorked ?? 0);
        S.hoursWorked = Number(c.hoursWorked ?? 0);
        S.basicSalary = Number(c.basicSalary ?? 0);
        S.adjustedGross = Number(c.adjustedGross ?? 0);
        S.netPay = Number(c.netPay ?? 0);
    }

    // Initial render from current state
    render();

    // Kick off the first server fetch for create/edit forms.
    // Batch edit intentionally starts from persisted values to avoid
    // a first-load mismatch with batch confirm net pay.
    if (!isBatchEdit) {
        fetchPreview();
    }

    setTimeout(() => {
        isBootstrapping = false;
    }, 200);
});
