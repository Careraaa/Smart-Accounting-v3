/**
 * global-datepicker.js
 *
 * Strategy: replace every input[type="date"] with a visible text input (for
 * display + keyboard entry) backed by the original hidden input[type="date"]
 * (for form submission, keeps the original `name` attribute).
 *
 * Display format shown to user : DD/MM/YYYY
 * Value stored in hidden input  : YYYY-MM-DD  (what Laravel/servers expect)
 *
 * Keyboard behaviour (segment order: DD → MM → YYYY):
 *   Digits       → fill current segment; auto-advance when segment is full
 *   / - Space    → manually advance to next segment
 *   Backspace    → erase last typed digit; if buffer empty, step back a segment
 *   ArrowLeft/Right → move between segments without changing the value
 *   Tab          → normal browser focus move (no interference)
 */

/**
 * Global helper — exposed at window scope so employee-form.js and any other
 * script can call displayToNative() without being inside the closure.
 *
 * DD/MM/YYYY (loose)  →  YYYY-MM-DD  or  "" if invalid
 */
function displayToNative(display) {
    const digits = display.replace(/\D/g, "");
    if (digits.length < 8) return "";
    const dd = digits.slice(0, 2);
    const mm = digits.slice(2, 4);
    const yyyy = digits.slice(4, 8);
    const d = parseInt(dd, 10),
        mo = parseInt(mm, 10),
        y = parseInt(yyyy, 10);
    if (mo < 1 || mo > 12 || d < 1 || d > 31 || y < 1900) return "";
    return `${yyyy}-${mm}-${dd}`;
}

function initGlobalDatepickers() {
    // If the browser can't programmatically open the native picker, don't
    // replace date inputs at all — keep the native <input type="date"> UX.
    // (Otherwise the "calendar" button would appear to do nothing.)
    if (typeof HTMLInputElement === "undefined") return;
    if (typeof HTMLInputElement.prototype?.showPicker !== "function") return;

    document.querySelectorAll('input[type="date"]').forEach((original) => {
        // Allow specific pages/sections to opt out and use the native date input.
        // (Employee create/edit uses native picker due to layout positioning issues.)
        if (original.closest('[data-global-datepicker="off"]')) return;

        // If already initialized but wrapper is missing, allow re-init
        if (original.dataset.datepickerInitialized === "true") {
            if (original.closest(".datepicker-wrapper")) {
                return;
            }
            // Wrapper was lost (tab reload / partial render) — allow reinitialization
        }
        original.dataset.datepickerInitialized = "true";

        /* ── 1. Build replacement markup ─────────────────────────── */

        const wrapper = document.createElement("div");
        wrapper.className = "datepicker-wrapper";
        wrapper.style.cssText = "position:relative;display:block;";

        // Visible text input
        const text = document.createElement("input");
        text.type = "text";
        text.placeholder = "DD/MM/YYYY";
        text.autocomplete = "off";
        text.spellcheck = false;
        text.className = original.className; // carry over emp-input etc.

        // Move id to text input so <label for="..."> keeps working
        if (original.id) {
            text.id = original.id;
            original.id = original.id + "__hidden";
        }

        if (original.readOnly) text.readOnly = true;
        if (original.disabled) text.disabled = true;
        if (original.required) text.required = true;

        // Hide the native input (keep name + value for form submission)
        original.style.cssText = "display:none!important;";
        original.tabIndex = -1;

        // Small calendar icon button
        const iconBtn = document.createElement("button");
        iconBtn.type = "button";
        iconBtn.tabIndex = -1;
        iconBtn.setAttribute("aria-label", "Open date picker");
        iconBtn.style.cssText = [
            "position:absolute;right:10px;top:50%;transform:translateY(-50%);",
            "background:none;border:none;padding:2px;cursor:pointer;",
            "color:#9ca3af;display:flex;align-items:center;line-height:1;",
        ].join("");
        iconBtn.innerHTML = `<svg width="15" height="15" fill="none" viewBox="0 0 24 24"
            stroke="currentColor" stroke-width="2">
            <rect x="3" y="4" width="18" height="18" rx="2"/>
            <line x1="16" y1="2" x2="16" y2="6"/>
            <line x1="8"  y1="2" x2="8"  y2="6"/>
            <line x1="3"  y1="10" x2="21" y2="10"/>
        </svg>`;

        wrapper.appendChild(text);
        wrapper.appendChild(iconBtn);
        original.parentNode.insertBefore(wrapper, original);
        wrapper.appendChild(original); // keep original in DOM (hidden)

        /* ── 2. Value conversion helpers ─────────────────────────── */

        /** YYYY-MM-DD  →  DD/MM/YYYY */
        function nativeToDisplay(val) {
            if (!val || !/^\d{4}-\d{2}-\d{2}$/.test(val)) return "";
            const [y, m, d] = val.split("-");
            return `${d}/${m}/${y}`;
        }

        // NOTE: displayToNative() is defined globally above — no local copy needed.

        function syncHidden() {
            original.value = displayToNative(text.value);
        }

        // Pre-populate text box in edit mode
        if (original.value) {
            text.value = nativeToDisplay(original.value);
            resetState();
        }

        /* ── 3. Segment rendering ─────────────────────────────────── */
        const SEG_LEN = [2, 2, 4];

        function readSegments() {
            const raw = text.value.replace(/\D/g, "");
            return [raw.slice(0, 2), raw.slice(2, 4), raw.slice(4, 8)];
        }

        function writeSegments(segs) {
            const [dd, mm, yyyy] = segs;
            let out = dd ? dd.padStart(2, "0") : "";
            if (mm) out += "/" + mm.padStart(2, "0");
            if (yyyy) out += "/" + yyyy.padStart(4, "0");
            text.value = out;
        }

        /* ── 4. Keyboard state ────────────────────────────────────── */
        let seg = 0;
        let buffer = "";

        function resetState() {
            seg = 0;
            buffer = "";
        }

        function commitBuffer() {
            if (!buffer) return;
            const segs = readSegments();
            segs[seg] = buffer;
            writeSegments(segs);
            syncHidden();
        }

        function advanceSeg() {
            commitBuffer();
            buffer = "";
            if (seg < 2) seg++;
        }

        /* ── 5. keydown handler ───────────────────────────────────── */
        text.addEventListener("keydown", function (e) {
            if (text.readOnly || text.disabled) return;

            if (e.key === "Tab") {
                resetState();
                return;
            }

            if (/^\d$/.test(e.key)) {
                e.preventDefault();
                if (buffer.length < SEG_LEN[seg]) {
                    buffer += e.key;
                    commitBuffer();
                    if (buffer.length >= SEG_LEN[seg]) advanceSeg();
                }
                return;
            }

            if (["/", " ", "-", "Enter"].includes(e.key)) {
                e.preventDefault();
                advanceSeg();
                return;
            }

            if (e.key === "Backspace") {
                e.preventDefault();
                if (buffer.length > 0) {
                    buffer = buffer.slice(0, -1);
                    const segs = readSegments();
                    segs[seg] = buffer;
                    writeSegments(segs);
                    syncHidden();
                } else if (seg > 0) {
                    seg--;
                    buffer = "";
                }
                return;
            }

            if (e.key === "ArrowLeft") {
                e.preventDefault();
                buffer = "";
                if (seg > 0) seg--;
                return;
            }
            if (e.key === "ArrowRight") {
                e.preventDefault();
                buffer = "";
                if (seg < 2) seg++;
                return;
            }

            if (e.key.length === 1) e.preventDefault();
        });

        text.addEventListener("input", function () {
            const segs = readSegments().map((s, i) => s.slice(0, SEG_LEN[i]));
            writeSegments(segs);
            syncHidden();
        });

        text.addEventListener("blur", function () {
            syncHidden();
            resetState();
        });

        text.addEventListener("focus", function () {
            const segs = readSegments();
            buffer = segs[seg] || "";
        });

        /* ── 6. Calendar icon → native picker ────────────────────── */
        // UX: clicking the field should also open the picker (not just the icon).
        text.addEventListener("click", function () {
            if (text.readOnly || text.disabled) return;
            iconBtn.click();
        });

        iconBtn.addEventListener("click", function () {
            if (text.readOnly) return;

            const wasDisabled = original.disabled;

            // Save current value; clear it so "change" always fires even when
            // the user picks the same date that was already stored (edit mode).
            const prevNativeValue = original.value;
            original.value = "";

            // ── POSITION FIX ─────────────────────────────────────────────
            // Bootstrap templates commonly apply CSS transforms (translateX,
            // translateZ, will-change etc.) to sidebar/layout wrappers.
            // Any ancestor with a transform creates a new containing block,
            // which breaks position:fixed — the browser positions the element
            // relative to that ancestor instead of the viewport, landing the
            // native date-picker in the wrong place (usually top-left).
            //
            // Solution: temporarily move the hidden input to <body> (which
            // never has a transform) so position:fixed is always relative to
            // the true viewport, then put it back inside the wrapper after
            // the picker closes.
            const rect = text.getBoundingClientRect();
            const originalParent = original.parentNode;
            const originalNextSibling = original.nextSibling;

            original.disabled = false;
            original.style.cssText = [
                // Use fixed positioning and viewport-relative coordinates.
                // This matches how the native picker is typically anchored in Chrome.
                // (The previous absolute+scroll approach can still mis-anchor to 0,0
                // in some layouts / zoom levels.)
                "position:fixed",
                `top:${rect.bottom}px`,
                `left:${rect.left}px`,
                "opacity:0",
                "pointer-events:none",
                "width:" + rect.width + "px",
                "height:1px",
                "z-index:99999",
            ].join(";");

            // Teleport to <body> so no ancestor transform can affect it
            document.body.appendChild(original);

            function rehide() {
                // Move original back to its place in the wrapper
                if (originalNextSibling) {
                    originalParent.insertBefore(original, originalNextSibling);
                } else {
                    originalParent.appendChild(original);
                }
                original.style.cssText = "display:none!important;";
                original.disabled = wasDisabled;
            }

            try {
                original.showPicker();
            } catch (_) {
                // showPicker not supported or not triggered by user gesture —
                // restore previous state and bail out gracefully.
                original.value = prevNativeValue;
                rehide();
                // Best-effort fallback for browsers without showPicker().
                try {
                    original.focus({ preventScroll: true });
                    original.click();
                } catch (_) {}
                return;
            }

            // settled flag prevents both the timeout and the change handler
            // from executing (avoids double-rehide in edit mode).
            let settled = false;

            function onPick() {
                if (settled) return;
                settled = true;

                if (original.value) {
                    // User picked a date — update the visible text input.
                    text.value = nativeToDisplay(original.value);
                    resetState();
                    syncHidden();
                } else {
                    // Picker closed without a selection — restore previous value.
                    original.value = prevNativeValue;
                }

                rehide();
            }

            original.addEventListener("change", onPick, { once: true });

            // When the picker is dismissed (Esc / click-outside), many browsers
            // don't fire "change". "blur" is a more reliable close signal.
            original.addEventListener(
                "blur",
                function () {
                    if (settled) return;
                    settled = true;
                    original.value = prevNativeValue;
                    rehide();
                },
                { once: true },
            );

            // Fallback: if the picker is dismissed without firing "change"
            // (e.g. Escape key, click-outside) restore state after a delay.
            setTimeout(function () {
                if (!settled) {
                    settled = true;
                    original.value = prevNativeValue;
                    rehide();
                }
            }, 4000);
        });
    });
}

// Expose helpers for pages/scripts that expect globals (tabs, dynamic rows, etc.).
// When bundled by Vite, top-level functions are module-scoped unless attached to window.
window.displayToNative = displayToNative;
window.initGlobalDatepickers = initGlobalDatepickers;

function bootGlobalDatepickers() {
    try {
        initGlobalDatepickers();
    } catch (_) {}
}

if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", bootGlobalDatepickers);
} else {
    bootGlobalDatepickers();
}

// Auto-init for dynamically added date inputs (modals, AJAX partials, etc.).
// Installed once to avoid duplicate observers when bundlers reload modules.
if (!window.__globalDatepickerAutoInitInstalled) {
    window.__globalDatepickerAutoInitInstalled = true;

    let scheduled = false;
    function scheduleInit() {
        if (scheduled) return;
        scheduled = true;
        setTimeout(function () {
            scheduled = false;
            bootGlobalDatepickers();
        }, 50);
    }

    // Any new input[type=date] inserted into the DOM should be initialized.
    const mo = new MutationObserver(function (mutations) {
        for (const m of mutations) {
            for (const node of m.addedNodes) {
                if (!(node instanceof Element)) continue;
                if (node.matches?.('input[type="date"]')) {
                    scheduleInit();
                    return;
                }
                if (node.querySelector?.('input[type="date"]')) {
                    scheduleInit();
                    return;
                }
            }
        }
    });

    if (document.body) {
        mo.observe(document.body, { childList: true, subtree: true });
    } else {
        document.addEventListener(
            "DOMContentLoaded",
            function () {
                mo.observe(document.body, { childList: true, subtree: true });
            },
            { once: true },
        );
    }

    // Fallback: if a date input receives focus before we initialized it,
    // initialize immediately (helps with very fast modal openings).
    document.addEventListener(
        "focusin",
        function (e) {
            const el = e.target;
            if (!(el instanceof HTMLInputElement)) return;
            if (el.type !== "date") return;
            scheduleInit();
        },
        true,
    );
}
