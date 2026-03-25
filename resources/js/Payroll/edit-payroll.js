document.addEventListener("DOMContentLoaded", function () {
    if (!document.querySelector('meta[name="page-id"][content="payroll-edit"]'))
        return;

    // ── Single source of truth (exactly same as create) ─────────────────────
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
        allowances: [],
        deductions: [],
    };

    let fetchId = 0;

    const g = (id) => document.getElementById(id);
    const fmt = (n) => `₱${(+n || 0).toFixed(2)}`;
    const sum = (arr) =>
        arr.reduce((t, x) => t + (parseFloat(x.amount) || 0), 0);

    const EL = {
        employeeSelect: g("user_id"),
        periodStart: g("payroll_period_start"),
        periodEnd: g("payroll_period_end"),

        daysWorked: g("prl_days_worked"),
        hoursWorked: g("prl_hours_worked"),
        presentDays: g("prl_present_days"),

        basicDisplay: g("basic_salary_display"),
        basicInput: g("basic_salary_input"),

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
        netSalary: g("net_salary_display"),

        allowName: g("allowance_name"),
        allowAmount: g("allowance_amount"),
        allowPills: g("allowance_list"),
        allowEmpty: g("prl_allow_empty"),
        allowSubtotal: g("prl_allow_subtotal"),
        allowTotal: g("allowance_total_display"),
        allowHiddenWrap: g("allowances_inputs"),

        deductName: g("deduction_name"),
        deductAmount: g("deduction_amount"),
        deductPills: g("deduction_list"),
        deductEmpty: g("prl_deduct_empty"),
        deductSubtotal: g("prl_deduct_subtotal"),
        deductTotal: g("deduction_total_display"),
        deductHiddenWrap: g("deductions_inputs"),

        totalAllowances: g("total_allowances"),
        totalDeductions: g("total_deductions"),
    };

    // Guard
    const critical = [
        "employeeSelect",
        "periodStart",
        "periodEnd",
        "basicDisplay",
        "netSalary",
    ];
    for (const k of critical) {
        if (!EL[k]) {
            console.error(`[payroll-edit] Missing element: ${k} — aborting.`);
            return;
        }
    }

    // Statutory - halved (same as controller + create)
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
            const full = parseFloat(row?.employee_share ?? 0) || 0;
            S.sss = Math.round((full / 2) * 100) / 100;
        }

        if (opt.dataset.hasPagibig === "1") {
            const row = cfg.find(
                (d) =>
                    d.name === "Pag-IBIG" &&
                    monthly >= (parseFloat(d.min_salary) || 0) &&
                    monthly <= (parseFloat(d.max_salary) || Infinity),
            );
            const full = row
                ? Math.min(
                      monthly * (parseFloat(row.employee_share || 0) / 100),
                      100,
                  )
                : 0;
            S.pagibig = Math.round((full / 2) * 100) / 100;
        }
    }

    // Render - identical to create
    function render() {
        const totalAllow = sum(S.allowances);
        const totalDeduct = sum(S.deductions);

        // Attendance
        if (EL.daysWorked) EL.daysWorked.textContent = S.days;
        if (EL.hoursWorked)
            EL.hoursWorked.textContent = (S.days * 8).toFixed(2);
        if (EL.presentDays) EL.presentDays.textContent = S.days;

        // Basic
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

        // SSS & Pag-IBIG
        if (EL.sssRow) EL.sssRow.style.display = S.sss > 0 ? "flex" : "none";
        if (S.sss > 0 && EL.sssVal) EL.sssVal.textContent = fmt(S.sss);

        if (EL.pagibigRow)
            EL.pagibigRow.style.display = S.pagibig > 0 ? "flex" : "none";
        if (S.pagibig > 0 && EL.pagibigVal)
            EL.pagibigVal.textContent = fmt(S.pagibig);

        // Adjusted + Net
        const adj = S.basicSalary + S.otPay - S.utDeduct - S.sss - S.pagibig;
        if (EL.adjusted) EL.adjusted.textContent = fmt(adj);
        if (EL.netSalary)
            EL.netSalary.textContent = fmt(adj + totalAllow - totalDeduct);

        // Hidden totals sent to controller
        if (EL.totalAllowances)
            EL.totalAllowances.value = (totalAllow + S.otPay).toFixed(2);
        if (EL.totalDeductions)
            EL.totalDeductions.value = (
                totalDeduct +
                S.utDeduct +
                S.sss +
                S.pagibig
            ).toFixed(2);

        // Subtotals
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

        // Render pills
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

    function renderPills(
        colorKey,
        list,
        pillArea,
        emptyEl,
        hiddenWrap,
        inputPrefix,
    ) {
        if (!pillArea) return;
        pillArea.querySelectorAll(".prl-pill").forEach((el) => el.remove());
        if (hiddenWrap) hiddenWrap.innerHTML = "";

        if (list.length === 0) {
            if (emptyEl) emptyEl.style.display = "";
            return;
        }
        if (emptyEl) emptyEl.style.display = "none";

        const colorClass = colorKey === "allow" ? "p-green" : "p-red";

        list.forEach((item, i) => {
            const pill = document.createElement("span");
            pill.className = `prl-pill ${colorClass}`;

            const textEl = document.createElement("span");
            textEl.className = "prl-pill-text";
            textEl.textContent = item.name;

            const dot = document.createTextNode(" · ");
            const amtEl = document.createElement("span");
            amtEl.className = "prl-pill-amt";
            amtEl.textContent = fmt(item.amount);

            const rmBtn = document.createElement("button");
            rmBtn.type = "button";
            rmBtn.className = "prl-pill-rm";
            rmBtn.innerHTML = "&times;";
            rmBtn.addEventListener("click", () => {
                list.splice(i, 1);
                render();
            });

            pill.append(textEl, dot, amtEl, rmBtn);
            pillArea.appendChild(pill);

            if (hiddenWrap) {
                const ni = document.createElement("input");
                ni.type = "hidden";
                ni.name = `${inputPrefix}[${i}][name]`;
                ni.value = item.name;
                const ai = document.createElement("input");
                ai.type = "hidden";
                ai.name = `${inputPrefix}[${i}][amount]`;
                ai.value = item.amount;
                hiddenWrap.append(ni, ai);
            }
        });
    }

    // Recalculate - matches controller + create
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
            render();
            return;
        }

        const opt = EL.employeeSelect.options[EL.employeeSelect.selectedIndex];
        S.salaryRate = parseFloat(opt?.dataset.salaryRate || 0);

        S.days = 15;
        S.basicSalary = (S.salaryRate * 22) / 2;
        S.otHours = S.utHours = S.otPay = S.utDeduct = 0;

        computeStatutory();
        render();

        const myId = ++fetchId;
        try {
            const res = await fetch(
                `${window._prl.otUtUrl}?user_id=${empId}&start=${start}&end=${end}`,
            );
            if (!res.ok) throw new Error();
            const data = await res.json();
            if (myId !== fetchId) return;

            const hourly = S.salaryRate / 8;
            S.otHours = parseFloat(data.overtime || 0);
            S.utHours = parseFloat(data.undertime || 0);
            S.otPay = S.otHours * hourly;
            S.utDeduct = S.utHours * hourly;
        } catch (e) {
            console.error("[payroll-edit] OT/UT fetch error:", e);
        }

        render();
    }

    // Manual add
    function addAllowance() {
        const name = EL.allowName ? EL.allowName.value.trim() : "";
        const amount = parseFloat(EL.allowAmount ? EL.allowAmount.value : 0);
        if (!name || isNaN(amount) || amount <= 0) return;
        S.allowances.push({ name, amount });
        if (EL.allowName) EL.allowName.value = "";
        if (EL.allowAmount) EL.allowAmount.value = "";
        render();
    }

    function addDeduction() {
        const name = EL.deductName ? EL.deductName.value.trim() : "";
        const amount = parseFloat(EL.deductAmount ? EL.deductAmount.value : 0);
        if (!name || isNaN(amount) || amount <= 0) return;
        S.deductions.push({ name, amount });
        if (EL.deductName) EL.deductName.value = "";
        if (EL.deductAmount) EL.deductAmount.value = "";
        render();
    }

    window.addAllowance = addAllowance;
    window.addDeduction = addDeduction;

    // Event listeners
    EL.employeeSelect.addEventListener("change", recalculateAll);
    EL.periodStart.addEventListener("change", recalculateAll);
    EL.periodEnd.addEventListener("change", recalculateAll);

    // Init with data from blade
    S.allowances = (window.initialAllowances || [])
        .map((a) => ({
            name: String(a.name || ""),
            amount: parseFloat(a.amount) || 0,
        }))
        .filter((a) => a.name && a.amount > 0);

    S.deductions = (window.initialDeductions || [])
        .map((d) => ({
            name: String(d.name || ""),
            amount: parseFloat(d.amount) || 0,
        }))
        .filter((d) => d.name && d.amount > 0);

    recalculateAll();
});
