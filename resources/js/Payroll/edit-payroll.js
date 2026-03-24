document.addEventListener("DOMContentLoaded", function () {
    // --- Elements ---
    const employeeSelect = document.getElementById("user_id");
    const basicSalaryDisplay = document.getElementById("basic_salary_display");
    const basicSalaryInput = document.getElementById("basic_salary_input");
    const netSalaryDisplay = document.getElementById("net_salary_display");

    const periodStartInput = document.getElementById("payroll_period_start");
    const periodEndInput = document.getElementById("payroll_period_end");

    const daysWorkedDisplay = document.getElementById("days_worked_display");
    const hoursWorkedDisplay = document.getElementById("hours_worked_display");
    const presentDaysDisplay = document.getElementById("present_days_display");

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

    // --- Data ---
    let allowances = (window.initialAllowances || []).map((a) => ({
        name: a.name,
        amount: parseFloat(a.amount) || 0,
    }));
    let deductions = (window.initialDeductions || []).map((d) => ({
        name: d.name,
        amount: parseFloat(d.amount) || 0,
        statutory: !!d.statutory,
    }));

    let autoOTHours = 0,
        autoOvertimePay = 0,
        autoUTHours = 0,
        autoUndertimeDeduct = 0;

    // --- Attendance + Salary ---
    function calculateFromAttendance() {
        const employeeId = employeeSelect.value;
        const periodStart = periodStartInput.value;
        const periodEnd = periodEndInput.value;

        if (!employeeId || !periodStart || !periodEnd) {
            resetAttendanceFields();
            resetOTUTFields();
            syncAutoDeductions([], []);
            updateSalary();
            return;
        }

        calculateAttendanceManual(employeeId, periodStart, periodEnd);
    }

    function calculateAttendanceManual(employeeId, periodStart, periodEnd) {
        const start = new Date(periodStart);
        const end = new Date(periodEnd);

        if (isNaN(start) || isNaN(end) || end < start) return;

        const daysInPeriod =
            Math.ceil((end - start) / (1000 * 60 * 60 * 24)) + 1;
        const selectedOption =
            employeeSelect.options[employeeSelect.selectedIndex];
        const salaryRate = parseFloat(selectedOption?.dataset.salaryRate || 0);

        // actual basic salary for period
        const basicSalary = salaryRate * daysInPeriod;
        basicSalaryDisplay.innerText = `₱${basicSalary.toFixed(2)}`;
        basicSalaryInput.value = basicSalary.toFixed(2);

        // Attendance display
        daysWorkedDisplay.innerText = daysInPeriod;
        hoursWorkedDisplay.innerText = (daysInPeriod * 8).toFixed(2);
        presentDaysDisplay.innerText = daysInPeriod;

        // OT/UT for now
        autoOTHours = 0;
        autoOvertimePay = 0;
        autoUTHours = 0;
        autoUndertimeDeduct = 0;
        resetOTUTFields();

        // Statutory deductions
        const hasSSS = selectedOption?.dataset.hasSss === "1";
        const hasPagibig = selectedOption?.dataset.hasPagibig === "1";

        computeStatutoryDeductions(basicSalary, hasSSS, hasPagibig);

        syncAutoDeductions([], []);
        updateSalary();
    }

    // --- Auto OT/UT / Cash Advances / Loans ---
    function syncAutoDeductions(cashAdvances, salaryLoans) {
        allowances = allowances.filter((a) => !a.auto_ot);
        deductions = deductions.filter(
            (d) => !d.auto_ut && !d.auto_ca && !d.auto_loan,
        );

        if (autoOvertimePay > 0) {
            allowances.push({
                name: `Overtime Pay (${autoOTHours.toFixed(2)} hrs)`,
                amount: parseFloat(autoOvertimePay),
                auto_ot: true,
            });
        }
        if (autoUndertimeDeduct > 0) {
            deductions.push({
                name: `Undertime Deduction (${autoUTHours.toFixed(2)} hrs)`,
                amount: parseFloat(autoUndertimeDeduct),
                auto_ut: true,
                statutory: false,
            });
        }

        cashAdvances.forEach((ca) => {
            deductions.push({
                name: `Cash Advance${ca.request_date ? " (requested " + ca.request_date + ")" : ""}`,
                amount: parseFloat(ca.amount),
                auto_ca: true,
                statutory: false,
            });
        });

        salaryLoans.forEach((loan) => {
            const instalment = Math.min(
                parseFloat(loan.monthly_deduction),
                parseFloat(loan.remaining_balance),
            );
            deductions.push({
                name: `Salary Loan (₱${parseFloat(loan.remaining_balance).toFixed(2)} remaining)`,
                amount: instalment,
                auto_loan: true,
                statutory: false,
            });
        });

        renderList(allowances, allowanceList, "allowance");
        renderList(deductions, deductionList, "deduction");
        updateSalary();
    }

    // --- Statutory ---
    function computeStatutoryDeductions(basicSalary, hasSSS, hasPagibig) {
        deductions = deductions.filter((d) => !d.statutory);
        const monthlySalary = parseFloat(basicSalary) * 2; // for table check

        if (hasSSS && Array.isArray(window.statutoryDeductions)) {
            const sss = window.statutoryDeductions.find(
                (d) =>
                    d.name === "SSS" &&
                    monthlySalary >= parseFloat(d.min_salary) &&
                    monthlySalary <= parseFloat(d.max_salary),
            );
            const sssAmount = parseFloat(sss?.employee_share || 0);
            if (sssAmount > 0)
                deductions.push({
                    name: "SSS",
                    amount: sssAmount,
                    statutory: true,
                });
        }

        if (hasPagibig && Array.isArray(window.statutoryDeductions)) {
            const pagibig = window.statutoryDeductions.find(
                (d) =>
                    d.name === "Pag-IBIG" &&
                    monthlySalary >= parseFloat(d.min_salary) &&
                    monthlySalary <= parseFloat(d.max_salary),
            );
            const pagibigAmount = parseFloat(pagibig?.employee_share || 0);
            if (pagibigAmount > 0)
                deductions.push({
                    name: "Pag-IBIG",
                    amount: pagibigAmount,
                    statutory: true,
                });
        }

        renderList(deductions, deductionList, "deduction");
    }

    // --- Reset helpers ---
    function resetAttendanceFields() {
        daysWorkedDisplay.innerText = "0";
        hoursWorkedDisplay.innerText = "0.00";
        presentDaysDisplay.innerText = "0";
        basicSalaryDisplay.innerText = "₱0.00";
        basicSalaryInput.value = "0";
    }
    function resetOTUTFields() {
        if (otHoursDisplay) otHoursDisplay.innerText = "0.00";
        if (otPayDisplay) otPayDisplay.innerText = "₱0.00";
        if (utHoursDisplay) utHoursDisplay.innerText = "0.00";
        if (utDeductionDisplay) utDeductionDisplay.innerText = "₱0.00";
    }

    // --- Salary update ---
    function updateSalary() {
        const basicSalary = parseFloat(basicSalaryInput.value || 0);
        const totalAllowances = allowances.reduce(
            (sum, a) => sum + parseFloat(a.amount || 0),
            0,
        );
        const totalDeductions = deductions.reduce(
            (sum, d) => sum + parseFloat(d.amount || 0),
            0,
        );

        allowanceTotalInput.value = totalAllowances.toFixed(2);
        deductionTotalInput.value = totalDeductions.toFixed(2);
        allowanceTotalDisplay.innerText = totalAllowances.toFixed(2);
        deductionTotalDisplay.innerText = totalDeductions.toFixed(2);

        const netSalary = basicSalary + totalAllowances - totalDeductions;
        netSalaryDisplay.innerText = `₱${netSalary.toFixed(2)}`;
    }

    // --- List rendering ---
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
                : "";
            container.innerHTML += `
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span>${item.name}${badge}</span>
                    <div>
                        ₱${parseFloat(item.amount || 0).toFixed(2)}
                        ${!isLocked ? `<button type="button" class="btn btn-sm btn-outline-danger ms-2" onclick="removeItem('${type}', ${index})">✕</button>` : ""}
                    </div>
                </li>
            `;
            hiddenContainer.innerHTML += `
                <input type="hidden" name="${type}s[${index}][name]" value="${item.name}">
                <input type="hidden" name="${type}s[${index}][amount]" value="${item.amount}">
            `;
        });
    }

    // --- Add/Remove ---
    window.addAllowance = function () {
        const name = document.getElementById("allowance_name").value.trim();
        const amount = parseFloat(
            document.getElementById("allowance_amount").value || 0,
        );
        if (!name || amount <= 0) return;
        allowances.push({ name, amount });
        renderList(allowances, allowanceList, "allowance");
        document.getElementById("allowance_name").value = "";
        document.getElementById("allowance_amount").value = "";
        updateSalary();
    };
    window.addDeduction = function () {
        const name = document.getElementById("deduction_name").value.trim();
        const amount = parseFloat(
            document.getElementById("deduction_amount").value || 0,
        );
        if (!name || amount <= 0) return;
        deductions.push({ name, amount, statutory: false });
        renderList(deductions, deductionList, "deduction");
        document.getElementById("deduction_name").value = "";
        document.getElementById("deduction_amount").value = "";
        updateSalary();
    };
    window.removeItem = function (type, index) {
        if (type === "allowance") allowances.splice(index, 1);
        else deductions.splice(index, 1);
        renderList(
            type === "allowance" ? allowances : deductions,
            type === "allowance" ? allowanceList : deductionList,
            type,
        );
        updateSalary();
    };

    // --- Event listeners ---
    employeeSelect.addEventListener("change", calculateFromAttendance);
    periodStartInput.addEventListener("change", calculateFromAttendance);
    periodEndInput.addEventListener("change", calculateFromAttendance);

    // --- Init ---
    renderList(allowances, allowanceList, "allowance");
    renderList(deductions, deductionList, "deduction");
    calculateFromAttendance();
});
