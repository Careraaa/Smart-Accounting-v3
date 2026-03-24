// UPDATED 2026-03-24
document.addEventListener("DOMContentLoaded", function () {
    if (
        !document.querySelector(
            'meta[name="page-id"][content="payroll-create"]',
        )
    )
        return;
    const employeeSelect = document.getElementById("user_id");
    const basicSalaryDisplay = document.getElementById("basic_salary_display");
    const basicSalaryInput = document.getElementById("basic_salary_input");
    const netSalaryDisplay = document.getElementById("net_salary_display");
    const daysWorkedDisplay = document.getElementById("days_worked_display");
    const hoursWorkedDisplay = document.getElementById("hours_worked_display");
    const presentDaysDisplay = document.getElementById("present_days_display");
    const basicSalaryHint = document.getElementById("basic_salary_hint");
    const periodStartInput = document.getElementById("payroll_period_start");
    const periodEndInput = document.getElementById("payroll_period_end");
    const otHoursDisplay = document.getElementById("ot_hours_display");
    const otPayDisplay = document.getElementById("ot_pay_display");
    const utHoursDisplay = document.getElementById("ut_hours_display");
    const utDeductionDisplay = document.getElementById("ut_deduction_display");
    const allowanceList = document.getElementById("allowance_list");
    const deductionList = document.getElementById("deduction_list");
    const allowanceTotalInput = document.getElementById("total_allowances");
    const deductionTotalInput = document.getElementById("total_deductions");
    const allowanceTotalDisplay = document.getElementById(
        "allowance_total_display",
    );
    const deductionTotalDisplay = document.getElementById(
        "deduction_total_display",
    );

    let allowances = (window.initialAllowances || []).map((a) => ({
        name: a.name,
        amount: parseFloat(a.amount) || 0,
    }));

    let deductions = (window.initialDeductions || []).map((d) => ({
        name: d.name,
        amount: parseFloat(d.amount) || 0,
        statutory: !!d.statutory,
    }));

    let autoOvertimePay = 0;
    let autoUndertimeDeduct = 0;
    let autoOTHours = 0;
    let autoUTHours = 0;

    // ── Single entry point for all recalculation ──────────────────────
    // ── Single entry point for all recalculation ──────────────────────
    async function recalculateAll() {
        const employeeId = employeeSelect.value;
        const periodStart = periodStartInput.value;
        const periodEnd = periodEndInput.value;

        if (!employeeId || !periodStart || !periodEnd) {
            resetAttendanceFields();
            resetOTUTFields();
            allowances = allowances.filter((a) => !a.auto_ot);
            deductions = deductions.filter(
                (d) => !d.auto_ut && !d.auto_ca && !d.auto_loan && !d.statutory,
            );
            renderList(allowances, allowanceList, "allowance");
            renderList(deductions, deductionList, "deduction");
            updateSalary();
            return;
        }

        const selectedOption =
            employeeSelect.options[employeeSelect.selectedIndex];
        const salaryRate = parseFloat(selectedOption?.dataset.salaryRate || 0);
        const hasSSS = selectedOption?.dataset.hasSss === "1";
        const hasPagibig = selectedOption?.dataset.hasPagibig === "1";

        const start = new Date(periodStart);
        const end = new Date(periodEnd);
        if (isNaN(start) || isNaN(end) || end < start) return;

        const daysInPeriod =
            Math.ceil((end - start) / (1000 * 60 * 60 * 24)) + 1;
        const basicSalary = salaryRate * daysInPeriod;

        daysWorkedDisplay.innerText = daysInPeriod;
        hoursWorkedDisplay.innerText = (daysInPeriod * 8).toFixed(2);
        presentDaysDisplay.innerText = daysInPeriod;
        basicSalaryDisplay.innerText = `₱${basicSalary.toFixed(2)}`;
        basicSalaryInput.value = basicSalary.toFixed(2);

        // ── Fetch live OT/UT ──────────────────────────────────────────────
        let otUt = { overtime: 0, undertime: 0 };
        try {
            const res = await fetch(
                `${window.otUtUrl}?user_id=${employeeId}&start=${periodStart}&end=${periodEnd}`,
                { headers: { "X-Requested-With": "XMLHttpRequest" } },
            );
            if (res.ok) otUt = await res.json();
        } catch (e) {
            console.error("OT/UT fetch failed", e);
        }

        autoOTHours = otUt.overtime;
        autoOvertimePay = autoOTHours * (salaryRate / 8);
        autoUTHours = otUt.undertime;
        autoUndertimeDeduct = autoUTHours * (salaryRate / 8);

        otHoursDisplay.innerText = autoOTHours.toFixed(2);
        otPayDisplay.innerText = `₱${autoOvertimePay.toFixed(2)}`;
        utHoursDisplay.innerText = autoUTHours.toFixed(2);
        utDeductionDisplay.innerText = `₱${autoUndertimeDeduct.toFixed(2)}`;

        // ── Rebuild auto items ────────────────────────────────────────────
        allowances = allowances.filter((a) => !a.auto_ot);
        deductions = deductions.filter(
            (d) => !d.auto_ut && !d.auto_ca && !d.auto_loan && !d.statutory,
        );

        if (autoOvertimePay > 0) {
            allowances.push({
                name: `Overtime Pay (${autoOTHours.toFixed(2)} hrs)`,
                amount: autoOvertimePay,
                auto_ot: true,
            });
        }
        if (autoUndertimeDeduct > 0) {
            deductions.push({
                name: `Undertime Deduction (${autoUTHours.toFixed(2)} hrs)`,
                amount: autoUndertimeDeduct,
                auto_ut: true,
                statutory: false,
            });
        }

        // ── Statutory deductions ──────────────────────────────────────────
        const monthlySalary = Math.round(salaryRate * 22);

        if (hasSSS && Array.isArray(window.statutoryDeductions)) {
            const sss = window.statutoryDeductions.find((d) => {
                if (d.name !== "SSS") return false;
                const min = parseFloat(d.min_salary) || 0;
                const max = parseFloat(d.max_salary) || Infinity;
                return monthlySalary >= min && monthlySalary <= max + 0.01;
            });
            const sssAmount = parseFloat(
                sss?.employee_share ?? (monthlySalary >= 20000 ? 1000 : 0),
            );
            if (sssAmount > 0) {
                deductions.push({
                    name: "SSS",
                    amount: sssAmount,
                    statutory: true,
                });
            }
        }

        if (hasPagibig && Array.isArray(window.statutoryDeductions)) {
            const pagibig = window.statutoryDeductions.find((d) => {
                if (d.name !== "Pag-IBIG") return false;
                const min = parseFloat(d.min_salary) || 0;
                const max = parseFloat(d.max_salary) || Infinity;
                return monthlySalary >= min && monthlySalary <= max;
            });
            const rate = parseFloat(pagibig?.employee_share || 0) / 100;
            const pagibigAmount = Math.min(monthlySalary * rate, 100);
            if (pagibigAmount > 0) {
                deductions.push({
                    name: "Pag-IBIG",
                    amount: pagibigAmount,
                    statutory: true,
                });
            }
        }

        renderList(allowances, allowanceList, "allowance");
        renderList(deductions, deductionList, "deduction");
        updateSalary();
    }

    // ── Reset helpers ─────────────────────────────────────────────────
    function resetAttendanceFields() {
        daysWorkedDisplay.innerText = "0";
        hoursWorkedDisplay.innerText = "0.00";
        presentDaysDisplay.innerText = "0";
        basicSalaryDisplay.innerText = "₱0.00";
        basicSalaryInput.value = "0";
        if (basicSalaryHint)
            basicSalaryHint.innerText = "(calculated from attendance)";
    }

    function resetOTUTFields() {
        autoOTHours = 0;
        autoOvertimePay = 0;
        autoUTHours = 0;
        autoUndertimeDeduct = 0;
        if (otHoursDisplay) otHoursDisplay.innerText = "0.00";
        if (otPayDisplay) otPayDisplay.innerText = "₱0.00";
        if (utHoursDisplay) utHoursDisplay.innerText = "0.00";
        if (utDeductionDisplay) utDeductionDisplay.innerText = "₱0.00";
    }

    // ── Update net salary ─────────────────────────────────────────────
    function updateSalary() {
        const basicSalary = parseFloat(basicSalaryInput.value || 0);
        const totalAllowances = allowances.reduce(
            (sum, a) => sum + a.amount,
            0,
        );
        const totalDeductions = deductions.reduce(
            (sum, d) => sum + d.amount,
            0,
        );

        allowanceTotalInput.value = totalAllowances.toFixed(2);
        deductionTotalInput.value = totalDeductions.toFixed(2);
        allowanceTotalDisplay.innerText = totalAllowances.toFixed(2);
        deductionTotalDisplay.innerText = totalDeductions.toFixed(2);

        const netSalary = basicSalary + totalAllowances - totalDeductions;
        netSalaryDisplay.innerText = `₱${netSalary.toFixed(2)}`;
    }

    // ── Render lists ──────────────────────────────────────────────────
    function renderList(list, container, type) {
        container.innerHTML = "";
        const hiddenContainer = document.getElementById(
            type === "allowance" ? "allowances_inputs" : "deductions_inputs",
        );
        hiddenContainer.innerHTML = "";

        list.forEach((item, index) => {
            const isLocked =
                item.statutory ||
                item.auto_ot ||
                item.auto_ut ||
                item.auto_ca ||
                item.auto_loan;

            const badge = item.statutory
                ? `<span class="badge bg-secondary ms-2">Statutory</span>`
                : item.auto_ot
                  ? `<span class="badge ms-2" style="background:#dcfce7;color:#16a34a;">Auto • OT</span>`
                  : item.auto_ut
                    ? `<span class="badge ms-2" style="background:#fff1f2;color:#e11d48;">Auto • UT</span>`
                    : item.auto_ca
                      ? `<span class="badge ms-2" style="background:#fef3c7;color:#d97706;">Auto • Cash Advance</span>`
                      : item.auto_loan
                        ? `<span class="badge ms-2" style="background:#ede9fe;color:#7c3aed;">Auto • Loan</span>`
                        : "";

            const amountColor = item.auto_ot
                ? "color:#16a34a;"
                : item.auto_ut
                  ? "color:#e11d48;"
                  : item.auto_ca
                    ? "color:#d97706;"
                    : item.auto_loan
                      ? "color:#7c3aed;"
                      : "";

            container.innerHTML += `
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span>${item.name}${badge}</span>
                    <div>
                        <span style="${amountColor}">₱${parseFloat(item.amount || 0).toFixed(2)}</span>
                        ${!isLocked ? `<button type="button" class="btn btn-sm btn-outline-danger ms-2" onclick="removeItem('${type}', ${index})">✕</button>` : ""}
                    </div>
                </li>`;

            hiddenContainer.innerHTML += `
                <input type="hidden" name="${type}s[${index}][name]" value="${item.name}">
                <input type="hidden" name="${type}s[${index}][amount]" value="${item.amount}">`;
        });
    }

    // ── Add / Remove ──────────────────────────────────────────────────
    window.addAllowance = function () {
        const name = document.getElementById("allowance_name").value.trim();
        const amount =
            parseFloat(document.getElementById("allowance_amount").value) || 0;
        if (!name || amount <= 0) return;
        allowances.push({ name, amount });
        renderList(allowances, allowanceList, "allowance");
        document.getElementById("allowance_name").value = "";
        document.getElementById("allowance_amount").value = "";
        updateSalary();
    };

    window.addDeduction = function () {
        const name = document.getElementById("deduction_name").value.trim();
        const amount =
            parseFloat(document.getElementById("deduction_amount").value) || 0;
        if (!name || amount <= 0) return;
        deductions.push({ name, amount, statutory: false });
        document.getElementById("deduction_name").value = "";
        document.getElementById("deduction_amount").value = "";
        renderList(deductions, deductionList, "deduction");
        updateSalary();
    };

    window.removeItem = function (type, index) {
        if (type === "allowance") {
            allowances.splice(index, 1);
            renderList(allowances, allowanceList, "allowance");
        } else {
            deductions.splice(index, 1);
            renderList(deductions, deductionList, "deduction");
        }
        updateSalary();
    };

    // ── Event listeners (no duplicates) ──────────────────────────────
    employeeSelect.addEventListener("change", recalculateAll);
    periodStartInput.addEventListener("change", recalculateAll);
    periodEndInput.addEventListener("change", recalculateAll);

    // ── Init ──────────────────────────────────────────────────────────
    renderList(allowances, allowanceList, "allowance");
    renderList(deductions, deductionList, "deduction");
    updateSalary();

    if (
        employeeSelect.value &&
        periodStartInput.value &&
        periodEndInput.value
    ) {
        recalculateAll();
    }
});
