<script>
document.addEventListener('DOMContentLoaded', function () {
    const tabBtns = document.querySelectorAll('[data-tab]');
    const panes = document.querySelectorAll('[data-tab-pane]');
    const prevBtn = document.querySelector('[data-direction="prev"]');
    const nextBtn = document.querySelector('[data-direction="next"]');
    const primarySubmit = document.querySelector('[data-primary-submit]');
    const tabKeys = Array.from(tabBtns).map(btn => btn.dataset.tab);
    let current = 0;

    function updateUI(index) {
        panes.forEach((pane, i) => {
            if (i === index) {
                pane.classList.remove('hidden');
            } else {
                pane.classList.add('hidden');
            }
        });

        tabBtns.forEach((btn, i) => {
            const active = i === index;
            const clr = btn.dataset.color;
            const num = btn.querySelector('span:first-child');

            btn.classList.toggle('active', active);

            if (active) {
                btn.classList.add('text-' + clr + '-500', 'dark:text-' + clr + '-400', 'border-' + clr + '-500', 'dark:border-' + clr + '-400', 'bg-' + clr + '-50', 'dark:bg-' + clr + '-900/20');
                btn.classList.remove('text-gray-500', 'dark:text-gray-400', 'border-transparent');
            } else {
                btn.classList.remove('text-' + clr + '-500', 'dark:text-' + clr + '-400', 'border-' + clr + '-500', 'dark:border-' + clr + '-400', 'bg-' + clr + '-50', 'dark:bg-' + clr + '-900/20');
                btn.classList.add('text-gray-500', 'dark:text-gray-400', 'border-transparent');
            }

            if (num) {
                if (active) {
                    num.classList.add('bg-' + clr + '-500', 'dark:bg-' + clr + '-400', 'text-white');
                    num.classList.remove('bg-gray-200', 'dark:bg-gray-700', 'text-gray-500', 'dark:text-gray-400');
                } else {
                    num.classList.remove('bg-' + clr + '-500', 'dark:bg-' + clr + '-400', 'text-white');
                    num.classList.add('bg-gray-200', 'dark:bg-gray-700', 'text-gray-500', 'dark:text-gray-400');
                }
            }
        });

        if (prevBtn) {
            prevBtn.classList.toggle('opacity-50', index === 0);
            prevBtn.classList.toggle('pointer-events-none', index === 0);
        }

        if (nextBtn) {
            nextBtn.classList.toggle('hidden', index === tabKeys.length - 1);
        }

        if (primarySubmit) {
            primarySubmit.classList.toggle('hidden', index !== tabKeys.length - 1);
        }

        current = index;
    }

    function activateTab(index) {
        if (index < 0 || index >= tabKeys.length || index === current) return;
        updateUI(index);
    }

    tabBtns.forEach((btn, i) => {
        btn.addEventListener('click', () => activateTab(i));
    });

    if (prevBtn) {
        prevBtn.addEventListener('click', () => activateTab(current - 1));
    }

    if (nextBtn) {
        nextBtn.addEventListener('click', (e) => {
            if (current < tabKeys.length - 1) {
                e.preventDefault();
                activateTab(current + 1);
            }
        });
    }

    if (primarySubmit) primarySubmit.classList.add('hidden');

    document.addEventListener('keydown', function (e) {
        if ((e.ctrlKey || e.metaKey) && (e.key === 'ArrowRight' || e.key === 'ArrowLeft')) {
            e.preventDefault();
            if (e.key === 'ArrowRight' && current < tabKeys.length - 1) activateTab(current + 1);
            if (e.key === 'ArrowLeft' && current > 0) activateTab(current - 1);
        }
    });
});
</script>