document.addEventListener("DOMContentLoaded", function () {
    // -------------------------
    // Multi-tab form navigation
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

    function showFieldError(field, message) {
        field.classList.add("is-invalid");
        const existing =
            field.parentNode.querySelector(".tab-invalid-feedback") ??
            field
                .closest(".input-group")
                ?.querySelector(".tab-invalid-feedback");
        if (existing) existing.remove();

        const feedback = document.createElement("div");
        feedback.classList.add("invalid-feedback", "tab-invalid-feedback");
        feedback.textContent = message;

        const parent = field.closest(".input-group") ?? field.parentNode;
        parent.appendChild(feedback);
    }

    function validateCurrentTab() {
        const currentPane = tabContents[currentTab];
        const requiredFields = currentPane.querySelectorAll(
            "input[required], select[required], textarea[required]",
        );

        let valid = true;
        let firstInvalid = null;

        requiredFields.forEach((field) => {
            field.classList.remove("is-invalid");
            const existing =
                field.parentNode.querySelector(".tab-invalid-feedback") ??
                field
                    .closest(".input-group")
                    ?.querySelector(".tab-invalid-feedback");
            if (existing) existing.remove();

            if (!field.value.trim()) {
                valid = false;
                showFieldError(field, "This field is required.");
                if (!firstInvalid) firstInvalid = field;
            }
        });

        // Phone format validation
        const phoneField = currentPane.querySelector('input[name="phone"]');
        if (phoneField && phoneField.value.trim()) {
            const phonePattern = /^(09\d{9}|\+639\d{9})$/;
            if (!phonePattern.test(phoneField.value.trim())) {
                valid = false;
                showFieldError(
                    phoneField,
                    "Phone must be 09XXXXXXXXX or +639XXXXXXXXX format.",
                );
                if (!firstInvalid) firstInvalid = phoneField;
            }
        }

        if (firstInvalid) {
            firstInvalid.scrollIntoView({
                behavior: "smooth",
                block: "center",
            });
            firstInvalid.focus();
        }

        return valid;
    }

    if (tabs.length && prevBtn && nextBtn && submitBtn) {
        prevBtn.addEventListener("click", function () {
            if (currentTab > 0) currentTab--;
            showTab(currentTab);
        });

        nextBtn.addEventListener("click", function () {
            if (!validateCurrentTab()) return;
            if (currentTab < tabs.length - 1) currentTab++;
            showTab(currentTab);
        });
        tabs.forEach((tab, i) => {
            tab.addEventListener("click", function () {
                if (i > currentTab && !validateCurrentTab()) return;
                currentTab = i;
                showTab(currentTab);
            });
        });

        showTab(currentTab);
    }

    // Clear error on input/change
    document.addEventListener("input", function (e) {
        if (e.target.classList.contains("is-invalid")) {
            e.target.classList.remove("is-invalid");
            const feedback =
                e.target.parentNode.querySelector(".tab-invalid-feedback") ??
                e.target
                    .closest(".input-group")
                    ?.querySelector(".tab-invalid-feedback");
            if (feedback) feedback.remove();
        }
    });

    document.addEventListener("change", function (e) {
        if (e.target.classList.contains("is-invalid")) {
            e.target.classList.remove("is-invalid");
            const feedback =
                e.target.parentNode.querySelector(".tab-invalid-feedback") ??
                e.target
                    .closest(".input-group")
                    ?.querySelector(".tab-invalid-feedback");
            if (feedback) feedback.remove();
        }
    });

    // -------------------------
    // Government number toggles
    // -------------------------
    document.querySelectorAll(".gov-toggle").forEach((checkbox) => {
        const targetId = checkbox.dataset.target;
        const input = document.getElementById(targetId);

        if (!input) return;

        // Set initial visual state
        toggleGovInput(checkbox, input);

        checkbox.addEventListener("change", function () {
            toggleGovInput(this, input);
        });
    });

    function toggleGovInput(checkbox, input) {
        if (checkbox.checked) {
            input.disabled = false;
            input.classList.remove("bg-light");
        } else {
            input.disabled = true;
            input.value = "";
            input.classList.add("bg-light");
        }
    }

    // -------------------------
    // Work Experience
    // -------------------------
    const experienceList = document.getElementById("experienceList");
    const addExperienceBtn = document.getElementById("addExperience");
    const experienceTemplate = document.getElementById("experienceTemplate");

    if (addExperienceBtn && experienceList && experienceTemplate) {
        let experienceIndex =
            experienceList.querySelectorAll(".experience-row").length;

        addExperienceBtn.addEventListener("click", function () {
            const html = experienceTemplate.innerHTML.replaceAll(
                "__INDEX__",
                experienceIndex,
            );
            const wrapper = document.createElement("div");
            wrapper.innerHTML = html;
            experienceList.appendChild(wrapper.firstElementChild);
            experienceIndex++;
        });

        experienceList.addEventListener("click", function (e) {
            if (e.target.classList.contains("remove-experience")) {
                e.target.closest(".experience-row").remove();
            }
        });
    }

    // -------------------------
    // Special Skills
    // -------------------------
    const skillList = document.getElementById("skillList");
    const addSkillBtn = document.getElementById("addSkill");
    const skillTemplate = document.getElementById("skillTemplate");

    if (addSkillBtn && skillList && skillTemplate) {
        let skillIndex = skillList.querySelectorAll(".skill-row").length;

        addSkillBtn.addEventListener("click", function () {
            const html = skillTemplate.innerHTML.replaceAll(
                "__INDEX__",
                skillIndex,
            );
            const wrapper = document.createElement("div");
            wrapper.innerHTML = html;
            skillList.appendChild(wrapper.firstElementChild);
            skillIndex++;
        });

        skillList.addEventListener("click", function (e) {
            if (e.target.classList.contains("remove-skill")) {
                e.target.closest(".skill-row").remove();
            }
        });
    }

    // -------------------------
    // Beneficiaries
    // -------------------------
    const beneficiaryList = document.getElementById("beneficiaryList");
    const addBeneficiaryBtn = document.getElementById("addBeneficiary");
    const beneficiaryTemplate = document.getElementById("beneficiaryTemplate");

    if (addBeneficiaryBtn && beneficiaryList && beneficiaryTemplate) {
        let beneficiaryIndex =
            beneficiaryList.querySelectorAll(".beneficiary-row").length;

        addBeneficiaryBtn.addEventListener("click", function () {
            const html = beneficiaryTemplate.innerHTML.replaceAll(
                "__INDEX__",
                beneficiaryIndex,
            );
            const wrapper = document.createElement("div");
            wrapper.innerHTML = html;
            beneficiaryList.appendChild(wrapper.firstElementChild);
            beneficiaryIndex++;
        });

        beneficiaryList.addEventListener("click", function (e) {
            if (e.target.classList.contains("remove-beneficiary")) {
                e.target.closest(".beneficiary-row").remove();
            }
        });
    }

    // -------------------------
    // Character References
    // -------------------------
    const referenceList = document.getElementById("referenceList");
    const addReferenceBtn = document.getElementById("addReference");
    const referenceTemplate = document.getElementById("referenceTemplate");

    if (addReferenceBtn && referenceList && referenceTemplate) {
        let referenceIndex =
            referenceList.querySelectorAll(".reference-row").length;

        addReferenceBtn.addEventListener("click", function () {
            const html = referenceTemplate.innerHTML.replaceAll(
                "__INDEX__",
                referenceIndex,
            );
            const wrapper = document.createElement("div");
            wrapper.innerHTML = html;
            referenceList.appendChild(wrapper.firstElementChild);
            referenceIndex++;
        });

        referenceList.addEventListener("click", function (e) {
            if (e.target.classList.contains("remove-reference")) {
                e.target.closest(".reference-row").remove();
            }
        });
    }
});
