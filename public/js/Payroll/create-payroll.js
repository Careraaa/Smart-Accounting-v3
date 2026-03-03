document.addEventListener("DOMContentLoaded", function () {
    // --- Payroll inputs ---
    const employeeSelect = document.getElementById("user_id");
    const basicSalaryDisplay = document.getElementById("basic_salary_display");
    const basicSalaryInput = document.getElementById("basic_salary_input");
    const netSalaryDisplay = document.getElementById("net_salary_display");
    const statutoryDisplay = document.getElementById("statutory_display");

    // Attendance fields
    const daysWorkedDisplay = document.getElementById("days_worked_display");
    const hoursWorkedDisplay = document.getElementById("hours_worked_display");
    const presentDaysDisplay = document.getElementById("present_days_display");
    const basicSalaryHint = document.getElementById("basic_salary_hint");
    const periodStartInput = document.getElementById("payroll_period_start");
    const periodEndInput = document.getElementById("payroll_period_end");

    // OT/UT fields
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

    let allowances = window.initialAllowances || [];
    let deductions = window.initialDeductions || [];

    // Tracks auto-injected OT/UT items so we can replace them on recalc
    let autoOvertimePay = 0;
    let autoUndertimeDeduct = 0;
    let autoOTHours = 0;
    let autoUTHours = 0;

    // --- Fetch attendance + OT/UT data from API ---
    async function calculateFromAttendance() {
        const employeeId = employeeSelect.value;
        const periodStart = periodStartInput.value;
        const periodEnd = periodEndInput.value;

        if (!employeeId || !periodStart || !periodEnd) {
            resetAttendanceFields();
            resetOTUTFields();
            updateSalary();
            return;
        }

        try {
            const response = await fetch(
                `/api/attendance/summary?user_id=${employeeId}&period_start=${periodStart}&period_end=${periodEnd}`,
            );

            if (!response.ok) {
                console.error(
                    "API response not OK, falling back to manual calculation",
                );
                calculateAttendanceManual(employeeId, periodStart, periodEnd);
                return;
            }

            const data = await response.json();

            // --- Attendance fields ---
            daysWorkedDisplay.innerText = data.present_days || 0;
            hoursWorkedDisplay.innerText = parseFloat(
                data.total_hours_worked || 0,
            ).toFixed(2);
            presentDaysDisplay.innerText = data.present_days || 0;

            // --- Basic salary ---
            const selectedOption =
                employeeSelect.options[employeeSelect.selectedIndex];
            const salaryRate = parseFloat(
                selectedOption?.dataset.salaryRate || 0,
            );
            const basicSalary = salaryRate * (data.present_days || 0);

            basicSalaryDisplay.innerText = `₱${basicSalary.toFixed(2)}`;
            basicSalaryInput.value = basicSalary.toFixed(2);
            basicSalaryHint.innerText = `(${data.present_days} days worked)`;

            // --- OT/UT fields ---
            autoOTHours = parseFloat(data.overtime_hours || 0);
            autoOvertimePay = parseFloat(data.overtime_pay || 0);
            autoUTHours = parseFloat(data.undertime_hours || 0);
            autoUndertimeDeduct = parseFloat(data.undertime_deduction || 0);

            if (otHoursDisplay)
                otHoursDisplay.innerText = autoOTHours.toFixed(2);
            if (otPayDisplay)
                otPayDisplay.innerText = `₱${autoOvertimePay.toFixed(2)}`;
            if (utHoursDisplay)
                utHoursDisplay.innerText = autoUTHours.toFixed(2);
            if (utDeductionDisplay)
                utDeductionDisplay.innerText = `₱${autoUndertimeDeduct.toFixed(2)}`;

            // Inject OT/UT into allowances/deductions arrays (replacing previous auto entries)
            syncAutoOTUT();

            // Recompute statutory with updated basic salary
            const hasSSS = selectedOption?.dataset.hasSss === "1";
            const hasPagibig = selectedOption?.dataset.hasPagibig === "1";
            computeStatutoryDeductions(basicSalary, hasSSS, hasPagibig);

            updateSalary();
        } catch (error) {
            console.error("Error fetching attendance:", error);
            calculateAttendanceManual(employeeId, periodStart, periodEnd);
        }
    }

    // --- Inject/replace OT as allowance and UT as deduction automatically ---
    function syncAutoOTUT() {
        // Remove previous auto OT allowance
        allowances = allowances.filter((a) => !a.auto_ot);
        if (autoOvertimePay > 0) {
            allowances.push({
                name: `Overtime Pay (${autoOTHours.toFixed(2)} hrs)`,
                amount: autoOvertimePay,
                auto_ot: true,
            });
        }

        // Remove previous auto UT deduction
        deductions = deductions.filter((d) => !d.auto_ut);
        if (autoUndertimeDeduct > 0) {
            deductions.push({
                name: `Undertime Deduction (${autoUTHours.toFixed(2)} hrs)`,
                amount: autoUndertimeDeduct,
                auto_ut: true,
                statutory: false,
            });
        }

        renderList(allowances, allowanceList, "allowance");
        renderList(deductions, deductionList, "deduction");
    }

    // --- Fallback: manual calculation without API ---
    function calculateAttendanceManual(employeeId, periodStart, periodEnd) {
        const start = new Date(periodStart);
        const end = new Date(periodEnd);
        const daysInPeriod =
            Math.ceil((end - start) / (1000 * 60 * 60 * 24)) + 1;

        const selectedOption =
            employeeSelect.options[employeeSelect.selectedIndex];
        const salaryRate = parseFloat(selectedOption?.dataset.salaryRate || 0);
        const estimatedDays = Math.ceil(daysInPeriod / 1.5);
        const basicSalary = salaryRate * estimatedDays;

        daysWorkedDisplay.innerText = estimatedDays;
        hoursWorkedDisplay.innerText = (estimatedDays * 8).toFixed(2);
        presentDaysDisplay.innerText = estimatedDays;

        basicSalaryDisplay.innerText = `₱${basicSalary.toFixed(2)}`;
        basicSalaryInput.value = basicSalary.toFixed(2);

        // OT/UT cannot be fetched in fallback — reset to zero
        resetOTUTFields();
        syncAutoOTUT();

        updateSalary();
    }

    // --- Reset helpers ---
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

    // --- Net salary update ---
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

    // --- Statutory deductions ---
    function computeStatutoryDeductions(basicSalary, hasSSS, hasPagibig) {
        // Remove old statutory entries but keep auto OT/UT and manual entries
        deductions = deductions.filter((d) => !d.statutory);

        const monthlySalary = basicSalary * 2;
        let sssAmount = 0;
        let pagibigAmount = 0;

        if (hasSSS) {
            const sss = window.statutoryDeductions.find(
                (d) =>
                    d.name === "SSS" &&
                    monthlySalary >= d.min_salary &&
                    monthlySalary <= d.max_salary,
            );
            if (sss) {
                sssAmount =
                    (sss.employee_share ??
                        monthlySalary * (sss.percentage_employee / 100)) / 2;
                deductions.push({
                    name: "SSS",
                    amount: sssAmount,
                    statutory: true,
                });
            }
        }

        if (hasPagibig) {
            const pagibig = window.statutoryDeductions.find(
                (d) =>
                    d.name === "Pag-IBIG" &&
                    monthlySalary >= d.min_salary &&
                    monthlySalary <= d.max_salary,
            );
            if (pagibig) {
                pagibigAmount =
                    (pagibig.employee_share && pagibig.employee_share > 0
                        ? pagibig.employee_share
                        : monthlySalary * (pagibig.percentage_employee / 100)) /
                    2;
                deductions.push({
                    name: "Pag-IBIG",
                    amount: pagibigAmount,
                    statutory: true,
                });
            }
        }

        statutoryDisplay.innerHTML = `
            ${sssAmount ? `SSS: ₱${sssAmount.toFixed(2)}` : ""}
            ${sssAmount && pagibigAmount ? " | " : ""}
            ${pagibigAmount ? `Pag-IBIG: ₱${pagibigAmount.toFixed(2)}` : ""}
        `;

        renderList(deductions, deductionList, "deduction");
        updateSalary();
    }

    // --- Render list ---
    function renderList(list, container, type) {
        container.innerHTML = "";
        const hiddenContainer = document.getElementById(
            type === "allowance" ? "allowances_inputs" : "deductions_inputs",
        );
        hiddenContainer.innerHTML = "";

        list.forEach((item, index) => {
            const isLocked = item.statutory || item.auto_ot || item.auto_ut;
            const badge = item.statutory
                ? `<span class="badge bg-secondary ms-2">Statutory</span>`
                : item.auto_ot
                  ? `<span class="badge ms-2" style="background:#dcfce7;color:#16a34a;">Auto • OT</span>`
                  : item.auto_ut
                    ? `<span class="badge ms-2" style="background:#fff1f2;color:#e11d48;">Auto • UT</span>`
                    : "";

            const amountColor = item.auto_ot
                ? "color:#16a34a;"
                : item.auto_ut
                  ? "color:#e11d48;"
                  : "";

            container.innerHTML += `
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span>${item.name}${badge}</span>
                    <div>
                        <span style="${amountColor}">₱${item.amount.toFixed(2)}</span>
                        ${
                            !isLocked
                                ? `<button type="button" class="btn btn-sm btn-outline-danger ms-2"
                                onclick="removeItem('${type}', ${index})">✕</button>`
                                : ""
                        }
                    </div>
                </li>
            `;

            hiddenContainer.innerHTML += `
                <input type="hidden" name="${type}s[${index}][name]"   value="${item.name}">
                <input type="hidden" name="${type}s[${index}][amount]" value="${item.amount}">
            `;
        });
    }

    // --- Add / Remove ---
    window.addAllowance = function () {
        const name = document.getElementById("allowance_name").value.trim();
        const amount = parseFloat(
            document.getElementById("allowance_amount").value,
        );
        if (!name || isNaN(amount) || amount <= 0) return;

        allowances.push({ name, amount });
        renderList(allowances, allowanceList, "allowance");

        document.getElementById("allowance_name").value = "";
        document.getElementById("allowance_amount").value = "";
        updateSalary();
    };

    window.addDeduction = function () {
        const name = document.getElementById("deduction_name").value.trim();
        const amount = parseFloat(
            document.getElementById("deduction_amount").value,
        );
        if (!name || isNaN(amount) || amount <= 0) return;

        deductions.push({ name, amount, statutory: false });
        renderList(deductions, deductionList, "deduction");

        document.getElementById("deduction_name").value = "";
        document.getElementById("deduction_amount").value = "";
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

    // --- Event listeners ---
    employeeSelect.addEventListener("change", function () {
        calculateFromAttendance();
    });

    periodStartInput.addEventListener("change", function () {
        calculateFromAttendance();
    });

    periodEndInput.addEventListener("change", function () {
        calculateFromAttendance();
    });

    // --- Initialize ---
    renderList(allowances, allowanceList, "allowance");
    renderList(deductions, deductionList, "deduction");
    updateSalary();
});
