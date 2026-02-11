document.addEventListener("DOMContentLoaded", function () {
    const employeeSelect = document.getElementById("employee_id");
    const basicSalaryDisplay = document.getElementById("basic_salary_display");
    const netSalaryDisplay = document.getElementById("net_salary_display");
    const statutoryDisplay = document.getElementById("statutory_display");

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

    function updateSalary() {
        const selectedOption =
            employeeSelect.options[employeeSelect.selectedIndex];
        const salaryRate = parseFloat(selectedOption?.dataset.salaryRate || 0);
        const basicSalary = salaryRate * 15;

        basicSalaryDisplay.innerText = `₱${basicSalary.toFixed(2)}`;

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

    function computeStatutoryDeductions(basicSalary, hasSSS, hasPagibig) {
        // Remove old statutory deductions
        deductions = deductions.filter((d) => !d.statutory);

        // Convert 15-day basic to monthly equivalent
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
                // Use table fixed share or percentage of monthly salary
                sssAmount =
                    sss.employee_share ??
                    monthlySalary * (sss.percentage_employee / 100);

                // Semi-monthly portion
                sssAmount = sssAmount / 2;

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
                // Use employee_share if > 0, else use percentage
                pagibigAmount =
                    pagibig.employee_share && pagibig.employee_share > 0
                        ? pagibig.employee_share
                        : monthlySalary * (pagibig.percentage_employee / 100);

                pagibigAmount = pagibigAmount / 2; // semi-monthly
                deductions.push({
                    name: "Pag-IBIG",
                    amount: pagibigAmount,
                    statutory: true,
                });
            }
        }

        renderList(deductions, deductionList, "deduction");

        // Update statutory display
        statutoryDisplay.innerHTML = `
        ${sssAmount ? `SSS: ₱${sssAmount.toFixed(2)}` : ""}
        ${sssAmount && pagibigAmount ? " | " : ""}
        ${pagibigAmount ? `Pag-IBIG: ₱${pagibigAmount.toFixed(2)}` : ""}
    `;

        updateSalary();
    }

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

    employeeSelect.addEventListener("change", function () {
        const option = this.options[this.selectedIndex];
        const salaryRate = parseFloat(option.dataset.salaryRate || 0);
        const basicSalary = salaryRate * 15;

        const hasSSS = option.dataset.hasSss === "1";
        const hasPagibig = option.dataset.hasPagibig === "1";

        basicSalaryDisplay.innerText = `₱${basicSalary.toFixed(2)}`;

        computeStatutoryDeductions(basicSalary, hasSSS, hasPagibig);
    });

    renderList(allowances, allowanceList, "allowance");
    renderList(deductions, deductionList, "deduction");
    updateSalary();
});
