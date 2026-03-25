// payroll-form.js — unified create + edit (detects page via meta[name="page-id"])
document.addEventListener("DOMContentLoaded", function () {
    const pageMeta = document.querySelector('meta[name="page-id"]');
    const pageId = pageMeta?.content;
    const isCreate = pageId === "payroll-create";
    const isEdit = pageId === "payroll-edit";

    if (!isCreate && !isEdit) return;

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
        allowances: [], // { name, amount }
        deductions: [], // { name, amount }
    };

    let fetchId = 0;
    let isBootstrapping = true;
    let isInitialLoad = true;

    // ── Helpers ───────────────────────────────────────────────────────────────
    const g = (id) => document.getElementById(id);
    const fmt = (n) => `₱${(+n || 0).toFixed(2)}`;
    const sum = (arr) =>
        arr.reduce((t, x) => t + (parseFloat(x.amount) || 0), 0);

    // ── Element map — all IDs use the prl_* convention (edit blade is updated
    //    to match; create blade already uses these IDs) ─────────────────────
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
                `[payroll-form] Missing element for key "${k}" (id: prl_${k}) — aborting.`,
            );
            return;
        }
    }

    // ── Statutory ─────────────────────────────────────────────────────────────
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
            S.sss = Math.round((fullSss / 2) * 100) / 100;
        }

        if (opt.dataset.hasPagibig === "1") {
            const row = cfg.find(
                (d) =>
                    d.name === "Pag-IBIG" &&
                    monthly >= (parseFloat(d.min_salary) || 0) &&
                    monthly <= (parseFloat(d.max_salary) || Infinity),
            );
            const fullPagibig = row
                ? Math.min(
                      monthly * (parseFloat(row.employee_share || 0) / 100),
                      100,
                  )
                : 0;
            S.pagibig = Math.round((fullPagibig / 2) * 100) / 100;
        }
    }

    // ── Render — only function that writes to the DOM ─────────────────────────
    function render() {
        const totalAllow = sum(S.allowances);
        const totalDeduct = sum(S.deductions);

        // Attendance chips
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

        // Hidden POST totals — mirror exactly what the controller expects:
        // total_allowances = manual allowances + OT pay
        // total_deductions = manual deductions + UT deduction + SSS + Pag-IBIG
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

        // Pills + hidden inputs
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

    // ── Pill renderer ─────────────────────────────────────────────────────────
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
            // Pill element
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
                render();
            });

            pill.append(textEl, dot, amtEl, rmBtn);
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

            hiddenWrap.append(ni, ai);
        });
    }

    // ── Full recalc (async — fires OT/UT fetch) ───────────────────────────────
    // ── Full recalc (async — now fetches pre-computed amounts) ─────────────────
    async function recalculateAll(savedBasicSalary = null) {
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
            if (EL.loadingRow) EL.loadingRow.style.display = "none";
            render();
            return;
        }

        const opt = EL.employeeSelect.options[EL.employeeSelect.selectedIndex];
        S.salaryRate = parseFloat(opt?.dataset.salaryRate || 0);
        S.days = 15;

        if (savedBasicSalary !== null) {
            S.basicSalary = savedBasicSalary;
            S.days = window._prl?.savedDaysWorked ?? 15;
        } else {
            S.days = 15;
            S.basicSalary = (S.salaryRate * 22) / 2;
        }

        computeStatutory();
        if (EL.loadingRow) EL.loadingRow.style.display = "flex";
        render();

        if (EL.loadingRow) EL.loadingRow.style.display = "flex";
        render();

        const myId = ++fetchId;

        try {
            if (!window._prl?.otUtUrl) {
                throw new Error("otUtUrl is missing in window._prl");
            }

            // Build URL safely (handles both full URL and relative path)
            let baseUrl = window._prl.otUtUrl;
            if (!baseUrl.startsWith("http")) {
                baseUrl =
                    window.location.origin +
                    (baseUrl.startsWith("/") ? "" : "/") +
                    baseUrl;
            }

            const url = `${baseUrl}?user_id=${empId}&start=${start}&end=${end}`;
            console.log("[payroll-form] Fetching OT/UT →", url);

            const res = await fetch(url, { cache: "no-store" });
            if (!res.ok)
                throw new Error(`HTTP ${res.status} - ${res.statusText}`);

            const data = await res.json();
            if (myId !== fetchId) return; // stale

            // Trust the backend (this is the key part)
            S.otHours = parseFloat(data.overtime || 0);
            S.utHours = parseFloat(data.undertime || 0);
            const totalAmount = parseFloat(data.ot_ut_amount || 0);
            S.otPay = totalAmount > 0 ? totalAmount : 0;
            S.utDeduct = totalAmount < 0 ? Math.abs(totalAmount) : 0;

            console.log("[payroll-form] OT/UT loaded successfully:", {
                otHours: S.otHours,
                utHours: S.utHours,
                otPay: S.otPay,
                utDeduct: S.utDeduct,
            });
        } catch (err) {
            if (myId !== fetchId) return;
            console.error("[payroll-form] OT/UT fetch failed:", err.message);
            S.otHours = S.utHours = S.otPay = S.utDeduct = 0;
        }

        if (EL.loadingRow) EL.loadingRow.style.display = "none";
        render();
    }

    // ── Add allowance / deduction ─────────────────────────────────────────────
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
        render();
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

    // ── Change listeners ──────────────────────────────────────────────────────
    function handleChange() {
        if (isEdit && isBootstrapping) return; // ✅ block during page load
        recalculateAll();
    }

    EL.employeeSelect.addEventListener("change", handleChange);
    EL.periodStart.addEventListener("change", handleChange);
    EL.periodEnd.addEventListener("change", handleChange);

    // ── Bootstrap — seed allowances/deductions + handle create vs edit ────────
    const rawAllowances =
        window.initialAllowances ?? window._prl?.initAllowances ?? [];
    const rawDeductions =
        window.initialDeductions ?? window._prl?.initDeductions ?? [];

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

    // Kick off the correct flow
    const savedBasic =
        isEdit && window._prl?.savedBasicSalary != null
            ? parseFloat(window._prl.savedBasicSalary)
            : null;

    if (isCreate) {
        recalculateAll(); // Create: full fetch
    } else if (isEdit) {
        S.basicSalary = savedBasic || 0;
        render(); // Show saved basic immediately

        // Still fetch current OT/UT for the saved period
        setTimeout(() => {
            recalculateAll(savedBasic);
        }, 100);
    }

    setTimeout(() => {
        isBootstrapping = false;
    }, 200);
});
