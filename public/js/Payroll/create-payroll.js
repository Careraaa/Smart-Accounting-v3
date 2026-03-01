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

    // Fetch attendance data and calculate
    async function calculateFromAttendance() {
        const employeeId = employeeSelect.value;
        const periodStart = periodStartInput.value;
        const periodEnd = periodEndInput.value;

        if (!employeeId || !periodStart || !periodEnd) {
            // Clear fields if not all data available
            daysWorkedDisplay.innerText = "0";
            hoursWorkedDisplay.innerText = "0.00";
            presentDaysDisplay.innerText = "0";
            basicSalaryDisplay.innerText = `₱0.00`;
            basicSalaryInput.value = "0";
            updateSalary();
            return;
        }

        try {
            const response = await fetch(
                `/api/attendance/summary?user_id=${employeeId}&period_start=${periodStart}&period_end=${periodEnd}`
            );

            if (!response.ok) {
                console.error("API response not OK, using default calculation");
                calculateAttendanceManual(employeeId, periodStart, periodEnd);
                return;
            }

            const data = await response.json();
            
            daysWorkedDisplay.innerText = data.present_days || 0;
            hoursWorkedDisplay.innerText = (data.total_hours_worked || 0).toFixed(2);
            presentDaysDisplay.innerText = data.present_days || 0;

            // Calculate basic salary
            const selectedOption = employeeSelect.options[employeeSelect.selectedIndex];
            const salaryRate = parseFloat(selectedOption?.dataset.salaryRate || 0);
            const basicSalary = salaryRate * (data.present_days || 0); // salaryRate is already daily rate

            basicSalaryDisplay.innerText = `₱${basicSalary.toFixed(2)}`;
            basicSalaryInput.value = basicSalary.toFixed(2);
            basicSalaryHint.innerText = `(${data.present_days} days worked)`;

            updateSalary();
        } catch (error) {
            console.error("Error fetching attendance:", error);
            // Use fallback calculation
            calculateAttendanceManual(employeeId, periodStart, periodEnd);
        }
    }

    // Fallback: calculate manually without API
    function calculateAttendanceManual(employeeId, periodStart, periodEnd) {
        const start = new Date(periodStart);
        const end = new Date(periodEnd);
        const daysInPeriod = Math.ceil((end - start) / (1000 * 60 * 60 * 24)) + 1;

        const selectedOption = employeeSelect.options[employeeSelect.selectedIndex];
        const salaryRate = parseFloat(selectedOption?.dataset.salaryRate || 0);
        const estimatedDaysWorked = Math.ceil(daysInPeriod / 1.5);
        const basicSalary = salaryRate * estimatedDaysWorked; // salaryRate is already daily rate

        daysWorkedDisplay.innerText = estimatedDaysWorked;
        hoursWorkedDisplay.innerText = (estimatedDaysWorked * 8).toFixed(2);
        presentDaysDisplay.innerText = estimatedDaysWorked;

        basicSalaryDisplay.innerText = `₱${basicSalary.toFixed(2)}`;
        basicSalaryInput.value = basicSalary.toFixed(2);

        updateSalary();
    }

    // --- Salary update ---
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

        allowanceTotalInput.value = totalAllowances;
        deductionTotalInput.value = totalDeductions;

        allowanceTotalDisplay.innerText = totalAllowances.toFixed(2);
        deductionTotalDisplay.innerText = totalDeductions.toFixed(2);

        const netSalary = basicSalary + totalAllowances - totalDeductions;
        netSalaryDisplay.innerText = `₱${netSalary.toFixed(2)}`;
    }

    // --- Statutory deductions ---
    function computeStatutoryDeductions(basicSalary, hasSSS, hasPagibig) {
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
                    sss.employee_share ??
                    monthlySalary * (sss.percentage_employee / 100);
                sssAmount /= 2; // semi-monthly
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
                    pagibig.employee_share && pagibig.employee_share > 0
                        ? pagibig.employee_share
                        : monthlySalary * (pagibig.percentage_employee / 100);
                pagibigAmount /= 2;
                deductions.push({
                    name: "Pag-IBIG",
                    amount: pagibigAmount,
                    statutory: true,
                });
            }
        }

        renderList(deductions, deductionList, "deduction");

        statutoryDisplay.innerHTML = `
            ${sssAmount ? `SSS: ₱${sssAmount.toFixed(2)}` : ""}
            ${sssAmount && pagibigAmount ? " | " : ""}
            ${pagibigAmount ? `Pag-IBIG: ₱${pagibigAmount.toFixed(2)}` : ""}
        `;

        updateSalary();
    }

    // --- Render list function ---
    function renderList(list, container, type) {
        container.innerHTML = "";
        const hiddenContainer = document.getElementById(
            type === "allowance" ? "allowances_inputs" : "deductions_inputs",
        );
        hiddenContainer.innerHTML = "";

        list.forEach((item, index) => {
            container.innerHTML += `
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span>
                        ${item.name}
                        ${item.statutory ? '<span class="badge bg-secondary ms-2">Statutory</span>' : ""}
                    </span>
                    <div>
                        ₱${item.amount.toFixed(2)}
                        ${item.statutory ? "" : `<button type="button" class="btn btn-sm btn-outline-danger ms-2" onclick="removeItem('${type}', ${index})">✕</button>`}
                    </div>
                </li>
            `;
            hiddenContainer.innerHTML += `
                <input type="hidden" name="${type}s[${index}][name]" value="${item.name}">
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
            document.getElementById("deduction_amount").value,
        );
        if (!name || amount <= 0) return;

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

    // --- Employee change ---
    employeeSelect.addEventListener("change", function () {
        const option = this.options[this.selectedIndex];
        const salaryRate = parseFloat(option.dataset.salaryRate || 0);
        const hasSSS = option.dataset.hasSss === "1";
        const hasPagibig = option.dataset.hasPagibig === "1";

        // Always recalculate from attendance
        calculateFromAttendance();
        
        // Get the calculated basic salary from the input
        const basicSalary = parseFloat(basicSalaryInput.value || 0);
        computeStatutoryDeductions(basicSalary, hasSSS, hasPagibig);
    });

    // Recalculate when date range changes
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
