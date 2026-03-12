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
        // Free navigation — no validation on Next/Prev/tab click
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

    // Returns index of first tab with errors, or -1 if all valid
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
                    const phonePattern = /^(09\d{9}|\+639\d{9})$/;
                    if (!phonePattern.test(field.value.trim())) {
                        error =
                            "Phone must be 09XXXXXXXXX or +639XXXXXXXXX format.";
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

    // Intercept form submit
    const form = document
        .querySelector("#employeeTabsContent")
        ?.closest("form");
    if (form) {
        form.addEventListener("submit", function (e) {
            assembleAddress();

            const { firstErrorTab, firstErrorField } =
                validateAllAndFindFirstError();

            if (firstErrorTab !== -1) {
                e.preventDefault();

                // Jump to the tab with the first error
                currentTab = firstErrorTab;
                showTab(currentTab);

                // Scroll and focus the offending field
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

    // Clear error styling as user corrects the field
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

    function generateRandomPassword(length = 12) {
        const uppercase = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        const lowercase = "abcdefghijklmnopqrstuvwxyz";
        const numbers = "0123456789";
        const allChars = uppercase + lowercase + numbers;
        
        let password = "";
        // Ensure at least one uppercase, one lowercase, one number
        password += uppercase.charAt(Math.floor(Math.random() * uppercase.length));
        password += lowercase.charAt(Math.floor(Math.random() * lowercase.length));
        password += numbers.charAt(Math.floor(Math.random() * numbers.length));
        
        // Fill the rest with random characters
        for (let i = password.length; i < length; i++) {
            password += allChars.charAt(Math.floor(Math.random() * allChars.length));
        }
        
        // Shuffle the password
        return password.split('').sort(() => Math.random() - 0.5).join('');
    }

    function updatePasswordDisplay() {
        if (!passwordDisplay) return;
        const randomPassword = generateRandomPassword(12);
        passwordDisplay.value = randomPassword;
        if (passwordHidden) {
            passwordHidden.value = randomPassword;
        }
    }

    // Generate password on page load if elements exist (create mode only)
    if (passwordDisplay) {
        updatePasswordDisplay();
    }

    // -------------------------
    // Spouse field — only when married
    // -------------------------
    const civilStatusSelect = document.getElementById("civil_status");
    const spouseField = document.getElementById("spouse_name");

    function toggleSpouseField() {
        if (!civilStatusSelect || !spouseField) return;
        const isMarried = civilStatusSelect.value === "married";
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

    // -------------------------
    // Work Experience
    // -------------------------
    const experienceList = document.getElementById("experienceList");
    const addExperienceBtn = document.getElementById("addExperience");
    const experienceTemplate = document.getElementById("experienceTemplate");

    if (addExperienceBtn && experienceList && experienceTemplate) {
        let idx = experienceList.querySelectorAll(".experience-row").length;
        addExperienceBtn.addEventListener("click", () => {
            const w = document.createElement("div");
            w.innerHTML = experienceTemplate.innerHTML.replaceAll(
                "__INDEX__",
                idx++,
            );
            experienceList.appendChild(w.firstElementChild);
        });
        experienceList.addEventListener("click", (e) => {
            if (e.target.classList.contains("remove-experience"))
                e.target.closest(".experience-row").remove();
        });
    }

    // -------------------------
    // Special Skills
    // -------------------------
    const skillList = document.getElementById("skillList");
    const addSkillBtn = document.getElementById("addSkill");
    const skillTemplate = document.getElementById("skillTemplate");

    if (addSkillBtn && skillList && skillTemplate) {
        let idx = skillList.querySelectorAll(".skill-row").length;
        addSkillBtn.addEventListener("click", () => {
            const w = document.createElement("div");
            w.innerHTML = skillTemplate.innerHTML.replaceAll(
                "__INDEX__",
                idx++,
            );
            skillList.appendChild(w.firstElementChild);
        });
        skillList.addEventListener("click", (e) => {
            if (e.target.classList.contains("remove-skill"))
                e.target.closest(".skill-row").remove();
        });
    }

    // -------------------------
    // Beneficiaries
    // -------------------------
    const beneficiaryList = document.getElementById("beneficiaryList");
    const addBeneficiaryBtn = document.getElementById("addBeneficiary");
    const beneficiaryTemplate = document.getElementById("beneficiaryTemplate");

    if (addBeneficiaryBtn && beneficiaryList && beneficiaryTemplate) {
        let idx = beneficiaryList.querySelectorAll(".beneficiary-row").length;
        addBeneficiaryBtn.addEventListener("click", () => {
            const w = document.createElement("div");
            w.innerHTML = beneficiaryTemplate.innerHTML.replaceAll(
                "__INDEX__",
                idx++,
            );
            beneficiaryList.appendChild(w.firstElementChild);
        });
        beneficiaryList.addEventListener("click", (e) => {
            if (e.target.classList.contains("remove-beneficiary"))
                e.target.closest(".beneficiary-row").remove();
        });
    }

    // -------------------------
    // Character References
    // -------------------------
    const referenceList = document.getElementById("referenceList");
    const addReferenceBtn = document.getElementById("addReference");
    const referenceTemplate = document.getElementById("referenceTemplate");

    if (addReferenceBtn && referenceList && referenceTemplate) {
        let idx = referenceList.querySelectorAll(".reference-row").length;
        addReferenceBtn.addEventListener("click", () => {
            const w = document.createElement("div");
            w.innerHTML = referenceTemplate.innerHTML.replaceAll(
                "__INDEX__",
                idx++,
            );
            referenceList.appendChild(w.firstElementChild);
        });
        referenceList.addEventListener("click", (e) => {
            if (e.target.classList.contains("remove-reference"))
                e.target.closest(".reference-row").remove();
        });
    }
});
