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
function initGlobalDatepickers() {
    document.querySelectorAll('input[type="date"]').forEach((original) => {
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

        /** DD/MM/YYYY (loose)  →  YYYY-MM-DD  or  "" if invalid */
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
        iconBtn.addEventListener("click", function () {
            if (text.readOnly) return;

            const wasDisabled = original.disabled;

            // ── FIX: save the current value and clear it before opening.
            // This is critical for edit mode: if the stored value matches
            // what the user picks, the browser fires no "change" event.
            // Clearing first guarantees a "change" fires on any selection.
            const prevNativeValue = original.value;
            original.value = "";

            // Temporarily expose the hidden input so showPicker() works.
            original.disabled = false;
            original.style.cssText = [
                "position:fixed",
                "opacity:0",
                "pointer-events:none",
                "top:0",
                "left:0",
                "width:1px", // FIX: must be non-zero or some browsers skip showPicker
                "height:1px", // FIX: same as above
            ].join(";");

            // ── FIX: define rehide before it is referenced in the timeout
            function rehide() {
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
                return;
            }

            // ── FIX: use a settled flag so the timeout and the change
            // handler can never both execute (avoids double-rehide / double
            // value restore that was breaking edit mode).
            let settled = false;

            function onPick() {
                if (settled) return;
                settled = true;

                if (original.value) {
                    // User picked a date — update the visible text input.
                    text.value = nativeToDisplay(original.value);
                    resetState();
                    syncHidden(); // FIX: was missing — keeps hidden input in sync
                } else {
                    // Picker closed without a selection — restore previous value.
                    original.value = prevNativeValue;
                    // text.value is already showing the correct formatted date;
                    // no visual change needed.
                }

                rehide();
            }

            original.addEventListener("change", onPick, { once: true });

            // Fallback: if the picker is closed without firing "change"
            // (e.g. Escape key, click-outside) restore state after a delay.
            // FIX: increased to 60 s so slow users aren't bitten; settled
            // flag ensures this is a true no-op once onPick has already run.
            setTimeout(function () {
                if (!settled) {
                    settled = true;
                    original.value = prevNativeValue;
                    rehide();
                }
            }, 60000);
        });
    });
}

document.addEventListener("DOMContentLoaded", function () {
    initGlobalDatepickers();
});
