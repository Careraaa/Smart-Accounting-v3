<script>
document.addEventListener('DOMContentLoaded', function () {
    const tabBtns = document.querySelectorAll('[data-tab]');
    const panes = document.querySelectorAll('[data-tab-pane]');
    const prevBtn = document.querySelector('[data-direction="prev"]');
    const nextBtn = document.querySelector('[data-direction="next"]');
    const primarySubmit = document.querySelector('[data-primary-submit]');
    const tabKeys = Array.from(tabBtns).map(btn => btn.dataset.tab);
    let current = 0;
    function shakeField(field) {
        try { window.errPlay(); } catch(e){}
        const origBorder = field.style.borderColor;
        field.classList.add('shake-error');
        field.style.borderColor = '#ef4444';
        field.style.boxShadow = '0 0 0 3px rgba(239,68,68,0.15)';
        setTimeout(() => {
            field.classList.remove('shake-error');
            field.style.borderColor = origBorder || '';
            field.style.boxShadow = '';
        }, 800);
    }

    (function() {
        const form = document.querySelector('form[novalidate]');
        if (!form) return;
        form.addEventListener('submit', function(e) {
            const reqs = form.querySelectorAll('[required]');
            let first = null;
            for (const f of reqs) {
                if (f.type !== 'file' && !f.value.trim()) { first = f; break; }
            }
            if (first) {
                e.preventDefault();
                const pane = first.closest('[data-tab-pane]');
                if (pane) {
                    const idx = tabKeys.indexOf(pane.dataset.tabPane);
                    if (idx >= 0) activateTab(idx);
                }
                setTimeout(() => { first.focus(); first.scrollIntoView({ behavior: 'smooth', block: 'center' }); shakeField(first); }, 100);
            }
        });
    })();

    function updateUI(index) {
        panes.forEach((pane, i) => {
            if (i === index) {
                pane.classList.remove('hidden');
            } else {
                pane.classList.add('hidden');
            }
        });

        const numColors = {
            rose:    '#f43f5e', orange:  '#f97316', amber:   '#f59e0b',
            emerald: '#10b981', teal:    '#14b8a6', cyan:    '#06b6d4',
            blue:    '#3b82f6', violet:  '#8b5cf6', fuchsia: '#d946ef',
        };
        const numColorsDark = {
            rose:    '#fb7185', orange:  '#fb923c', amber:   '#fbbf24',
            emerald: '#34d399', teal:    '#2dd4bf', cyan:    '#22d3ee',
            blue:    '#60a5fa', violet:  '#a78bfa', fuchsia: '#f0abfc',
        };

        const borderColors = {
            rose:    '#f43f5e', orange:  '#f97316', amber:   '#f59e0b',
            emerald: '#10b981', teal:    '#14b8a6', cyan:    '#06b6d4',
            blue:    '#3b82f6', violet:  '#8b5cf6', fuchsia: '#d946ef',
        };
        const borderColorsDark = {
            rose:    '#fb7185', orange:  '#fb923c', amber:   '#fbbf24',
            emerald: '#34d399', teal:    '#2dd4bf', cyan:    '#22d3ee',
            blue:    '#60a5fa', violet:  '#a78bfa', fuchsia: '#f0abfc',
        };

        tabBtns.forEach((btn, i) => {
            const active = i === index;
            const clr = btn.dataset.color;
            const num = btn.querySelector('.tab-num');
            const dark = document.documentElement.classList.contains('dark');

            btn.classList.toggle('active', active);

            if (active) {
                btn.classList.add('text-gray-900', 'dark:text-gray-100', 'bg-white', 'dark:bg-gray-900/80');
                btn.classList.remove('text-gray-500', 'dark:text-gray-400');
                btn.style.borderBottomColor = dark && borderColorsDark[clr] ? borderColorsDark[clr] : (borderColors[clr] || '');
            } else {
                btn.classList.remove('text-gray-900', 'dark:text-gray-100', 'bg-white', 'dark:bg-gray-900/80');
                btn.classList.add('text-gray-500', 'dark:text-gray-400');
                btn.style.borderBottomColor = 'transparent';
            }

            if (num) {
                if (numColors[clr]) {
                    const base = dark ? numColorsDark[clr] : numColors[clr];
                    if (active) {
                        num.style.backgroundColor = base;
                        num.style.color = '#fff';
                    } else {
                        num.style.backgroundColor = dark ? base + '30' : base + '20';
                        num.style.color = dark ? base : numColors[clr] + '99';
                    }
                } else {
                    num.style.backgroundColor = '';
                    num.style.color = '';
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
            primarySubmit.style.display = index === tabKeys.length - 1 ? '' : 'none';
        }

        current = index;
    }

    function activateTab(index) {
        if (index < 0 || index >= tabKeys.length || index === current) return;
        updateUI(index);
        window.scrollTo({ top: 0, behavior: 'smooth' });
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

    if (primarySubmit) {
        primarySubmit.style.display = 'none';
    }

    // Initialize first tab's number badge color
    updateUI(0);

    document.addEventListener('keydown', function (e) {
        if ((e.ctrlKey || e.metaKey) && (e.key === 'ArrowRight' || e.key === 'ArrowLeft')) {
            e.preventDefault();
            if (e.key === 'ArrowRight' && current < tabKeys.length - 1) activateTab(current + 1);
            if (e.key === 'ArrowLeft' && current > 0) activateTab(current - 1);
        }
    });
});
</script>