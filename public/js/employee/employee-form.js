document.addEventListener("DOMContentLoaded", function () {
    const tabs = document.querySelectorAll("#employeeTabs button");
    const tabContents = document.querySelectorAll(".tab-pane");
    const prevBtn = document.getElementById("prevBtn");
    const nextBtn = document.getElementById("nextBtn");
    const submitBtn = document.getElementById("submitBtn");

    let currentTab = 0; // start with first tab

    function showTab(index) {
        // Activate correct tab
        tabs.forEach((tab, i) => {
            tab.classList.toggle("active", i === index);
            tabContents[i].classList.toggle("show", i === index);
            tabContents[i].classList.toggle("active", i === index);
        });

        // Handle buttons visibility
        prevBtn.style.display = index === 0 ? "none" : "inline-block";
        nextBtn.style.display =
            index === tabs.length - 1 ? "none" : "inline-block";
        submitBtn.style.display =
            index === tabs.length - 1 ? "inline-block" : "none";
    }

    prevBtn.addEventListener("click", function () {
        if (currentTab > 0) currentTab--;
        showTab(currentTab);
    });

    nextBtn.addEventListener("click", function () {
        if (currentTab < tabs.length - 1) currentTab++;
        showTab(currentTab);
    });

    // Initialize
    showTab(currentTab);
});
