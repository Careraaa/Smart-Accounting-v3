document.addEventListener("DOMContentLoaded", function () {
    const dateInputs = document.querySelectorAll('input[type="date"]');

    dateInputs.forEach((input) =>
        input.addEventListener("click", () => input.showPicker?.()),
    );
});
