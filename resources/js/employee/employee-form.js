document.addEventListener("DOMContentLoaded", function () {
    // -------------------------
    // Tab navigation
    // -------------------------
    const tabs = document.querySelectorAll("#employeeTabs button");
    const tabContents = document.querySelectorAll(".tab-pane");
    const prevBtn = document.getElementById("prevBtn");
    const nextBtn = document.getElementById("nextBtn");
    const submitBtn = document.getElementById("submitBtn");

    let currentTab = 0;

    function showTab(index) {
        tabs.forEach((tab, i) => {
            tab.classList.toggle("active", i === index);
            tabContents[i].classList.toggle("show", i === index);
            tabContents[i].classList.toggle("active", i === index);
        });
        prevBtn.style.display = index === 0 ? "none" : "inline-block";
        nextBtn.style.display =
            index === tabs.length - 1 ? "none" : "inline-block";
        submitBtn.style.display =
            index === tabs.length - 1 ? "inline-block" : "none";
    }

    if (tabs.length && prevBtn && nextBtn && submitBtn) {
        prevBtn.addEventListener("click", function () {
            if (currentTab > 0) {
                currentTab--;
                showTab(currentTab);
            }
        });

        nextBtn.addEventListener("click", function () {
            if (currentTab < tabs.length - 1) {
                currentTab++;
                showTab(currentTab);
            }
        });

        tabs.forEach((tab, i) => {
            tab.addEventListener("click", function () {
                currentTab = i;
                showTab(currentTab);
                // Re-init datepickers when switching tabs (handles dynamically
                // shown panes that weren't visible on DOMContentLoaded)
                if (typeof initGlobalDatepickers === "function") {
                    initGlobalDatepickers();
                }
            });
        });

        showTab(currentTab);
    }

    // -------------------------
    // Validation helpers
    // -------------------------
    function showFieldError(field, message) {
        field.classList.add("is-invalid");
        const parent = field.closest(".input-group") ?? field.parentNode;
        const existing = parent.querySelector(".tab-invalid-feedback");
        if (existing) existing.remove();
        const feedback = document.createElement("div");
        feedback.classList.add("invalid-feedback", "tab-invalid-feedback");
        feedback.textContent = message;
        parent.appendChild(feedback);
    }

    function clearFieldError(field) {
        field.classList.remove("is-invalid");
        const parent = field.closest(".input-group") ?? field.parentNode;
        const existing = parent.querySelector(".tab-invalid-feedback");
        if (existing) existing.remove();
    }

    function validateAllAndFindFirstError() {
        let firstErrorTab = -1;
        let firstErrorField = null;

        tabContents.forEach((pane, paneIndex) => {
            const requiredFields = pane.querySelectorAll(
                "input[required], select[required], textarea[required]",
            );

            requiredFields.forEach((field) => {
                clearFieldError(field);

                let error = null;

                if (!field.value.trim()) {
                    error = "This field is required.";
                } else if (field.name === "phone") {
                    const phonePattern = /^\d{10}$/;
                    if (!phonePattern.test(field.value.trim())) {
                        error =
                            "Phone must be exactly 10 digits.";
                    }
                } else if (field.type === "number") {
                    const val = parseFloat(field.value);
                    const min = field.hasAttribute("min")
                        ? parseFloat(field.min)
                        : null;
                    if (isNaN(val)) {
                        error = "Please enter a valid number.";
                    } else if (min !== null && val < min) {
                        error = `Value must be ${min} or greater.`;
                    }
                }

                if (error) {
                    showFieldError(field, error);
                    if (firstErrorTab === -1) {
                        firstErrorTab = paneIndex;
                        firstErrorField = field;
                    }
                }
            });
        });

        return { firstErrorTab, firstErrorField };
    }

    const form = document
        .querySelector("#employeeTabsContent")
        ?.closest("form");
    if (form) {
        form.addEventListener("submit", function (e) {
            // Sync all datepicker text inputs → hidden date inputs before
            // validation. displayToNative() is now a global function defined
            // in global-datepicker.js so it is always in scope here.
            document
                .querySelectorAll('.datepicker-wrapper input[type="text"]')
                .forEach(function (textInput) {
                    const hiddenInput = textInput
                        .closest(".datepicker-wrapper")
                        .querySelector('input[type="date"]');
                    if (hiddenInput && typeof displayToNative === "function") {
                        hiddenInput.value = displayToNative(textInput.value);
                    }
                });

            assembleAddress();

            const { firstErrorTab, firstErrorField } =
                validateAllAndFindFirstError();

            if (firstErrorTab !== -1) {
                e.preventDefault();

                currentTab = firstErrorTab;
                showTab(currentTab);

                setTimeout(() => {
                    firstErrorField.scrollIntoView({
                        behavior: "smooth",
                        block: "center",
                    });
                    firstErrorField.focus();
                }, 80);
            }
        });
    }

    document.addEventListener("input", function (e) {
        if (e.target.classList.contains("is-invalid"))
            clearFieldError(e.target);
    });
    document.addEventListener("change", function (e) {
        if (e.target.classList.contains("is-invalid"))
            clearFieldError(e.target);
    });

    // -------------------------
    // Username preview (create only)
    // -------------------------
    const firstNameField = document.getElementById("first_name");
    const lastNameField = document.getElementById("last_name");
    const usernamePreview = document.getElementById("usernamePreview");

    function updateUsernamePreview() {
        if (!usernamePreview) return;
        const first = (firstNameField?.value ?? "")
            .trim()
            .toLowerCase()
            .replace(/\s+/g, "");
        const last = (lastNameField?.value ?? "")
            .trim()
            .toLowerCase()
            .replace(/\s+/g, "");
        if (first && last) usernamePreview.value = `${first}.${last}`;
        else if (first) usernamePreview.value = first;
        else usernamePreview.value = "";
    }

    if (firstNameField)
        firstNameField.addEventListener("input", updateUsernamePreview);
    if (lastNameField)
        lastNameField.addEventListener("input", updateUsernamePreview);
    updateUsernamePreview();

    // -------------------------
    // Random password generation (create only)
    // -------------------------
    const passwordDisplay = document.getElementById("defaultPassword");
    const passwordHidden = document.getElementById("hiddenPassword");

    function generateDefaultPassword() {
        const firstName = (firstNameField?.value ?? "")
            .replace(/[^a-zA-Z]/g, "")
            .trim();
        const baseRaw = firstName || "Employee";
        const base =
            baseRaw.charAt(0).toUpperCase() +
            baseRaw.slice(1, 6).toLowerCase();
        const suffix = String(Math.floor(Math.random() * 10000)).padStart(
            4,
            "0",
        );

        // Example: Juan1234 (easy to type and communicate)
        return `${base}${suffix}`;
    }

    function updatePasswordDisplay() {
        if (!passwordDisplay) return;
        const generatedPassword = generateDefaultPassword();
        passwordDisplay.value = generatedPassword;
        if (passwordHidden) {
            passwordHidden.value = generatedPassword;
        }
    }

    if (passwordDisplay) {
        updatePasswordDisplay();
    }

    // Regenerate a readable default password when first name changes.
    if (firstNameField && passwordDisplay) {
        firstNameField.addEventListener("input", updatePasswordDisplay);
    }

    // -------------------------
    // Spouse field — only when married
    // -------------------------
    const civilStatusSelect = document.getElementById("civil_status");
    const spouseField = document.getElementById("spouse_name");

    function toggleSpouseField() {
        if (!civilStatusSelect || !spouseField) return;
        const isMarried = civilStatusSelect.value === "Married";
        spouseField.disabled = !isMarried;
        spouseField.classList.toggle("bg-light", !isMarried);
        if (!isMarried) spouseField.value = "";
    }

    if (civilStatusSelect) {
        civilStatusSelect.addEventListener("change", toggleSpouseField);
        toggleSpouseField();
    }

    // -------------------------
    // Address assembly
    // -------------------------
    function assembleAddress() {
        const combined = document.getElementById("address_combined");
        if (!combined) return;
        combined.value = JSON.stringify({
            street:
                document.getElementById("address_street")?.value.trim() ?? "",
            barangay:
                document.getElementById("address_barangay")?.value.trim() ?? "",
            city: document.getElementById("address_city")?.value.trim() ?? "",
            province:
                document.getElementById("address_province")?.value.trim() ?? "",
        });
    }

    // -------------------------
    // Government number toggles
    // -------------------------
    document.querySelectorAll(".gov-toggle").forEach((checkbox) => {
        const input = document.getElementById(checkbox.dataset.target);
        if (!input) return;

        function toggle() {
            input.disabled = !checkbox.checked;
            input.classList.toggle("bg-light", !checkbox.checked);
            if (!checkbox.checked) input.value = "";
        }

        toggle();
        checkbox.addEventListener("change", toggle);
    });

    // -------------------------
    // Government number auto-dash formatters
    // -------------------------

    // SSS: XX-XXXXXXX-X
    function formatSSS(raw) {
        const d = raw.replace(/\D/g, "").slice(0, 10);
        if (d.length <= 2) return d;
        if (d.length <= 9) return d.slice(0, 2) + "-" + d.slice(2);
        return d.slice(0, 2) + "-" + d.slice(2, 9) + "-" + d.slice(9);
    }

    // TIN: XXX-XXX-XXX
    function formatTIN(raw) {
        const d = raw.replace(/\D/g, "").slice(0, 9);
        if (d.length <= 3) return d;
        if (d.length <= 6) return d.slice(0, 3) + "-" + d.slice(3);
        return d.slice(0, 3) + "-" + d.slice(3, 6) + "-" + d.slice(6);
    }

    // Pag-IBIG: XXXX-XXXX-XXXX
    function formatPagibig(raw) {
        const d = raw.replace(/\D/g, "").slice(0, 12);
        if (d.length <= 4) return d;
        if (d.length <= 8) return d.slice(0, 4) + "-" + d.slice(4);
        return d.slice(0, 4) + "-" + d.slice(4, 8) + "-" + d.slice(8);
    }

    // PhilHealth: XX-XXXXXXXXX-X  (12 digits total)
    function formatPhilHealth(raw) {
        const d = raw.replace(/\D/g, "").slice(0, 12);
        if (d.length <= 2) return d;
        if (d.length <= 11) return d.slice(0, 2) + "-" + d.slice(2);
        return d.slice(0, 2) + "-" + d.slice(2, 11) + "-" + d.slice(11);
    }

    function attachFormatter(id, formatterFn) {
        const input = document.getElementById(id);
        if (!input) return;
        input.addEventListener("input", function () {
            const pos = this.selectionStart;
            const before = this.value.length;
            this.value = formatterFn(this.value);
            const diff = this.value.length - before;
            this.setSelectionRange(pos + diff, pos + diff);
        });
    }

    attachFormatter("sss_number", formatSSS);
    attachFormatter("tin_number", formatTIN);
    attachFormatter("pagibig_number", formatPagibig);
    attachFormatter("philhealth_number", formatPhilHealth);

    // -------------------------
    // Dynamic list helper
    // -------------------------
    function initDynamicList({ listId, addBtnId, templateId, removeBtnClass }) {
        const list = document.getElementById(listId);
        const addBtn = document.getElementById(addBtnId);
        const template = document.getElementById(templateId);
        if (!list || !addBtn || !template) return;

        let idx = list.querySelectorAll(".emp-dynamic-row").length;

        addBtn.addEventListener("click", () => {
            const wrapper = document.createElement("div");
            wrapper.innerHTML = template.innerHTML.replaceAll(
                "__INDEX__",
                idx++,
            );
            list.appendChild(wrapper.firstElementChild);

            // Init any date inputs that appeared inside the new row
            if (typeof initGlobalDatepickers === "function") {
                initGlobalDatepickers();
            }
        });

        list.addEventListener("click", (e) => {
            const btn = e.target.closest("." + removeBtnClass);
            if (btn) btn.closest(".emp-dynamic-row").remove();
        });
    }

    initDynamicList({
        listId: "experienceList",
        addBtnId: "addExperience",
        templateId: "experienceTemplate",
        removeBtnClass: "remove-experience",
    });

    initDynamicList({
        listId: "skillList",
        addBtnId: "addSkill",
        templateId: "skillTemplate",
        removeBtnClass: "remove-skill",
    });

    initDynamicList({
        listId: "beneficiaryList",
        addBtnId: "addBeneficiary",
        templateId: "beneficiaryTemplate",
        removeBtnClass: "remove-beneficiary",
    });

    initDynamicList({
        listId: "referenceList",
        addBtnId: "addReference",
        templateId: "referenceTemplate",
        removeBtnClass: "remove-reference",
    });
});
