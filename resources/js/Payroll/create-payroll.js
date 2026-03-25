// payroll-create.js — CLEAN REWRITE (prl_ prefixed IDs, no collision with bundle)
document.addEventListener("DOMContentLoaded", function () {
    if (
        !document.querySelector(
            'meta[name="page-id"][content="payroll-create"]',
        )
    )
        return;

    // ── Single source of truth ────────────────────────────────────────────────
    const S = {
        basicSalary: 0,
        salaryRate: 0,
        days: 0,
        otHours: 0,
        utHours: 0,
        otPay: 0,
        utDeduct: 0,
        sss: 0,
        pagibig: 0,
        allowances: [], // { name:string, amount:number }
        deductions: [], // { name:string, amount:number }
    };

    let fetchId = 0;

    // ── DOM shorthand (all IDs are prl_* — no collision with app bundle) ──────
    const g = (id) => document.getElementById(id);
    const fmt = (n) => `₱${(+n || 0).toFixed(2)}`;
    const sum = (arr) =>
        arr.reduce((t, x) => t + (parseFloat(x.amount) || 0), 0);

    // Grab every element once — if any is null we'll know immediately in console
    const EL = {
        employeeSelect: g("prl_user_id"),
        periodStart: g("prl_period_start"),
        periodEnd: g("prl_period_end"),

        daysWorked: g("prl_days_worked"),
        hoursWorked: g("prl_hours_worked"),
        presentDays: g("prl_present_days"),

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
        totalAllowances: g("prl_total_allowances"),

        deductName: g("prl_deduct_name"),
        deductAmount: g("prl_deduct_amount"),
        deductPills: g("prl_deduct_pills"),
        deductEmpty: g("prl_deduct_empty"),
        deductSubtotal: g("prl_deduct_subtotal"),
        deductTotal: g("prl_deduct_total"),
        deductHiddenWrap: g("prl_deduct_hidden"),
        totalDeductions: g("prl_total_deductions"),
    };

    // Guard — abort if any critical element is missing (wrong page / old blade)
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
                `[payroll-create] Missing element: prl_${k} — aborting.`,
            );
            return;
        }
    }

    // ── Statutory → writes S.sss / S.pagibig ─────────────────────────────────
    function computeStatutory() {
        S.sss = S.pagibig = 0;
        const opt = EL.employeeSelect.options[EL.employeeSelect.selectedIndex];
        if (!opt?.value) return;

        const monthly = S.salaryRate * 22;
        const cfg = window._prl?.statutory || [];

        if (opt.dataset.hasSss === "1") {
            const row = cfg.find(
                (d) =>
                    d.name === "SSS" &&
                    monthly >= (parseFloat(d.min_salary) || 0) &&
                    monthly <= (parseFloat(d.max_salary) || Infinity) + 0.01,
            );
            const fullSss = parseFloat(row?.employee_share ?? 0) || 0;
            S.sss = Math.round((fullSss / 2) * 100) / 100; // half + proper rounding
        }

        if (opt.dataset.hasPagibig === "1") {
            const row = cfg.find(
                (d) =>
                    d.name === "Pag-IBIG" &&
                    monthly >= (parseFloat(d.min_salary) || 0) &&
                    monthly <= (parseFloat(d.max_salary) || Infinity),
            );
            const fullPagibig = Math.min(
                monthly * (parseFloat(row?.employee_share || 0) / 100),
                100,
            );
            S.pagibig = Math.round((fullPagibig / 2) * 100) / 100; // half
        }
    }

    // ── THE render function — only thing that touches the DOM ─────────────────
    function render() {
        const totalAllow = sum(S.allowances);
        const totalDeduct = sum(S.deductions);

        // Attendance
        EL.daysWorked.textContent = S.days;
        EL.hoursWorked.textContent = (S.days * 8).toFixed(2);
        EL.presentDays.textContent = S.days;

        // Basic
        EL.basicDisplay.textContent = fmt(S.basicSalary);
        EL.basicInput.value = S.basicSalary.toFixed(2);

        // OT
        EL.otRow.style.display = S.otPay > 0 ? "flex" : "none";
        if (S.otPay > 0) {
            EL.otHrs.textContent = `${S.otHours.toFixed(2)} hrs`;
            EL.otPay.textContent = fmt(S.otPay);
        }

        // UT
        EL.utRow.style.display = S.utDeduct > 0 ? "flex" : "none";
        if (S.utDeduct > 0) {
            EL.utHrs.textContent = `${S.utHours.toFixed(2)} hrs`;
            EL.utDeduct.textContent = fmt(S.utDeduct);
        }

        // SSS
        EL.sssRow.style.display = S.sss > 0 ? "flex" : "none";
        if (S.sss > 0) EL.sssVal.textContent = fmt(S.sss);

        // Pag-IBIG
        EL.pagibigRow.style.display = S.pagibig > 0 ? "flex" : "none";
        if (S.pagibig > 0) EL.pagibigVal.textContent = fmt(S.pagibig);

        // Adjusted gross & net
        const adj = S.basicSalary + S.otPay - S.utDeduct - S.sss - S.pagibig;
        EL.adjusted.textContent = fmt(adj);
        EL.netSalary.textContent = fmt(adj + totalAllow - totalDeduct);

        // Hidden POST values (controller adds OT/UT itself server-side)
        // Hidden POST values
        EL.totalAllowances.value = (totalAllow + S.otPay).toFixed(2);
        EL.totalDeductions.value = (
            totalDeduct +
            S.utDeduct +
            S.sss +
            S.pagibig
        ).toFixed(2);

        // Subtotals
        EL.allowTotal.textContent = totalAllow.toFixed(2);
        EL.deductTotal.textContent = totalDeduct.toFixed(2);
        EL.allowSubtotal.style.display = S.allowances.length ? "flex" : "none";
        EL.deductSubtotal.style.display = S.deductions.length ? "flex" : "none";

        // Pill areas
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

    // ── Pill renderer — called only from render() ─────────────────────────────
    function renderPills(
        colorKey,
        list,
        pillArea,
        emptyEl,
        hiddenWrap,
        inputPrefix,
    ) {
        // Remove existing pills (leave the empty placeholder in place)
        pillArea.querySelectorAll(".prl-pill").forEach((el) => el.remove());
        hiddenWrap.innerHTML = "";

        if (list.length === 0) {
            emptyEl.style.display = "";
            return;
        }
        emptyEl.style.display = "none";

        const colorClass = colorKey === "allow" ? "p-green" : "p-red";

        list.forEach((item, i) => {
            // Pill
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
            rmBtn.addEventListener("click", function () {
                list.splice(i, 1);
                render();
            });

            pill.appendChild(textEl);
            pill.appendChild(dot);
            pill.appendChild(amtEl);
            pill.appendChild(rmBtn);
            pillArea.appendChild(pill);

            // Hidden POST inputs
            const ni = document.createElement("input");
            ni.type = "hidden";
            ni.name = `${inputPrefix}[${i}][name]`;
            ni.value = item.name;
            const ai = document.createElement("input");
            ai.type = "hidden";
            ai.name = `${inputPrefix}[${i}][amount]`;
            ai.value = item.amount;
            hiddenWrap.appendChild(ni);
            hiddenWrap.appendChild(ai);
        });
    }

    // ── Full recalc — only fires when employee or period changes ──────────────
    async function recalculateAll() {
        const empId = EL.employeeSelect.value;
        const start = EL.periodStart.value;
        const end = EL.periodEnd.value;

        if (!empId || !start || !end) {
            Object.assign(S, {
                basicSalary: 0,
                salaryRate: 0,
                days: 0,
                otHours: 0,
                utHours: 0,
                otPay: 0,
                utDeduct: 0,
                sss: 0,
                pagibig: 0,
            });
            EL.loadingRow.style.display = "none";
            render();
            return;
        }

        const opt = EL.employeeSelect.options[EL.employeeSelect.selectedIndex];
        S.salaryRate = parseFloat(opt?.dataset.salaryRate || 0);
        // Determine cutoff (1–15 or 16–end)
        const startDay = new Date(start).getDate();

        // Monthly = daily rate × 22 days
        const monthly = S.salaryRate * 22;

        // Semi-month logic
        if (startDay <= 15) {
            S.days = 15;
            S.basicSalary = monthly / 2;
        } else {
            S.days = 15; // or 16 if you want exact, but usually fixed 15
            S.basicSalary = monthly / 2;
        }
        S.otHours = S.utHours = S.otPay = S.utDeduct = 0;

        computeStatutory();
        EL.loadingRow.style.display = "flex";
        render(); // show basic + statutory right away

        const myId = ++fetchId;
        try {
            const url = `${window._prl.otUtUrl}?user_id=${empId}&start=${start}&end=${end}`;
            const res = await fetch(url);
            if (!res.ok) throw new Error(`HTTP ${res.status}`);
            const data = await res.json();

            if (myId !== fetchId) return; // stale — newer call already running

            const hourly = S.salaryRate / 8;
            S.otHours = parseFloat(data.overtime || 0);
            S.utHours = parseFloat(data.undertime || 0);
            S.otPay = S.otHours * hourly;
            S.utDeduct = S.utHours * hourly;
        } catch (err) {
            if (myId !== fetchId) return;
            console.error("[payroll-create] OT/UT fetch error:", err);
        }

        EL.loadingRow.style.display = "none";
        render(); // final render with everything
    }

    // ── Add allowance ─────────────────────────────────────────────────────────
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
        render(); // S scalars are unchanged
    }

    // ── Add deduction ─────────────────────────────────────────────────────────
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
        render();
    }

    // ── Wire up buttons & keyboard ────────────────────────────────────────────
    g("prl_allow_btn").addEventListener("click", addAllowance);
    g("prl_deduct_btn").addEventListener("click", addDeduction);

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

    // ── Recalc listeners ──────────────────────────────────────────────────────
    EL.employeeSelect.addEventListener("change", recalculateAll);
    EL.periodStart.addEventListener("change", recalculateAll);
    EL.periodEnd.addEventListener("change", recalculateAll);

    // ── Init — restore old() values if present (after validation failure) ─────
    S.allowances = (window._prl?.initAllowances || [])
        .map((a) => ({
            name: String(a.name || ""),
            amount: parseFloat(a.amount) || 0,
        }))
        .filter((a) => a.name && a.amount > 0);

    S.deductions = (window._prl?.initDeductions || [])
        .map((d) => ({
            name: String(d.name || ""),
            amount: parseFloat(d.amount) || 0,
        }))
        .filter((d) => d.name && d.amount > 0);

    if (EL.employeeSelect.value && EL.periodStart.value && EL.periodEnd.value) {
        recalculateAll(); // async → render() fires once all scalars are set
    } else {
        render();
    }
});
