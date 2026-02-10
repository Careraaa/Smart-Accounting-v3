document.addEventListener("DOMContentLoaded", function () {
    // Automatically open datepicker when clicking anywhere on date input
    const dateInputs = document.querySelectorAll('input[type="date"]');
    dateInputs.forEach((input) =>
        input.addEventListener("click", () => input.showPicker?.()),
    );

    // You can add more employee-related JS here in the future
});
