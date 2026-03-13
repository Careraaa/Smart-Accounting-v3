document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll('input[type="date"]').forEach((input) => {
        input.addEventListener("click", function () {
            // Only open the visual picker if the field is empty
            // so clicking on a filled field doesn't interrupt typing
            this.showPicker?.();
        });
    });
});
