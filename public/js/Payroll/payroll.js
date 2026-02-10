document.addEventListener("DOMContentLoaded", function () {
    // Elements
    const employeeSelect = document.getElementById("employee_id");
    const basicSalaryDisplay = document.getElementById("basic_salary_display");
    const netSalaryDisplay = document.getElementById("net_salary_display");

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

    // Initialize arrays (allow old values from server if available)
    let allowances = window.initialAllowances || [];
    let deductions = window.initialDeductions || [];

    // Update the salary displays
    function updateSalary() {
        const selectedOption =
            employeeSelect.options[employeeSelect.selectedIndex];
        const salaryRate = parseFloat(selectedOption.dataset.salaryRate || 0);
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

    // Render allowances/deductions list and hidden inputs
    function renderList(list, container, type) {
        container.innerHTML = "";
        const hiddenContainer =
            type === "allowance"
                ? document.getElementById("allowances_inputs")
                : document.getElementById("deductions_inputs");
        hiddenContainer.innerHTML = "";

        list.forEach((item, index) => {
            container.innerHTML += `
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span>${item.name}</span>
                    <div>
                        ₱${item.amount.toFixed(2)}
                        <button type="button" class="btn btn-sm btn-outline-danger ms-2 border-1 rounded"
                            onclick="removeItem('${type}', ${index})">✕</button>
                    </div>
                </li>
            `;
            hiddenContainer.innerHTML += `
                <input type="hidden" name="${type}s[${index}][name]" value="${item.name}">
                <input type="hidden" name="${type}s[${index}][amount]" value="${item.amount}">
            `;
        });
    }

    // Functions to be globally available (for inline onclick in buttons)
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

        deductions.push({ name, amount });
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

    // Open date picker when clicking anywhere on date input
    const dateInputs = document.querySelectorAll('input[type="date"]');
    dateInputs.forEach((input) =>
        input.addEventListener("click", () => input.showPicker?.()),
    );

    // Initialize
    employeeSelect.addEventListener("change", updateSalary);
    renderList(allowances, allowanceList, "allowance");
    renderList(deductions, deductionList, "deduction");
    updateSalary();
});
