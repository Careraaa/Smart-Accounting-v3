<script>
    (function() {
        let current = 0;

        const tabs = document.querySelectorAll('.emp-tab-btn');
        const panes = document.querySelectorAll('.emp-tab-pane');
        const prev = document.getElementById('empPrev');
        const next = document.getElementById('empNext');
        const submit = document.getElementById('empSubmit');

        if (!tabs.length || !panes.length || !prev || !next || !submit) return;
        const totalTabs = Math.min(tabs.length, panes.length);
        if (totalTabs <= 0) return;

        function goTo(idx) {
            if (idx < 0 || idx >= totalTabs) return;
            tabs[current].classList.remove('active');
            panes[current].classList.remove('active');
            if (idx > current) tabs[current].classList.add('done');
            else tabs[idx].classList.remove('done');

            current = idx;
            tabs[current].classList.add('active');
            panes[current].classList.add('active');

            prev.style.visibility = current === 0 ? 'hidden' : 'visible';
            next.style.display = current === totalTabs - 1 ? 'none' : 'inline-flex';
            submit.style.display = current === totalTabs - 1 ? 'inline-flex' : 'none';
        }

        tabs.forEach((btn, i) => btn.addEventListener('click', () => goTo(i)));
        prev.addEventListener('click', () => {
            if (current > 0) goTo(current - 1);
        });
        next.addEventListener('click', () => {
            if (current < totalTabs - 1) goTo(current + 1);
        });

        // If validation failed, open the first tab containing an invalid field.
        const firstInvalidPane = document.querySelector('.emp-tab-pane .is-invalid')?.closest('.emp-tab-pane');
        if (firstInvalidPane) {
            const paneIndex = Number(firstInvalidPane.getAttribute('data-pane'));
            if (!Number.isNaN(paneIndex) && paneIndex >= 0 && paneIndex < totalTabs) {
                goTo(paneIndex);
                return;
            }
        }

        // Ensure footer buttons are correct on first render.
        goTo(0);
    })();
</script>

