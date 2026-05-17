/**
 * sm-bootstrap-shim.js
 *
 * Lightweight vanilla-JS replacement for bootstrap.js (80 KB).
 * Implements only the subset of the Bootstrap 5 JS API used by Ski-Manager:
 *
 *   bootstrap.Modal        – show / hide / static-backdrop / keyboard
 *   data-bs-toggle="dropdown"  – open / close .dropdown-menu
 *   data-bs-toggle="tab"       – activate tab panes
 *   data-bs-toggle="collapse"  – expand / collapse panels
 *   data-bs-toggle="modal"     – open a modal from a trigger
 *   data-bs-dismiss="modal"    – close the containing modal
 *   data-bs-dismiss="alert"    – remove the containing alert
 *
 * Note: Tooltips are now DaisyUI CSS-only (.tooltip[data-tip]) and require
 * no JavaScript initialisation.
 */
(function (global) {
    'use strict';

    /* ─── helpers ─────────────────────────────────────────────── */
    function qs(sel, ctx) { return (ctx || document).querySelector(sel); }
    function qsa(sel, ctx) { return Array.from((ctx || document).querySelectorAll(sel)); }
    function on(el, ev, fn, opts) { if (el) el.addEventListener(ev, fn, opts || false); }
    function off(el, ev, fn) { if (el) el.removeEventListener(ev, fn); }
    function hasClass(el, c) { return el && el.classList.contains(c); }
    function addClass(el, c) { if (el) el.classList.add(c); }
    function removeClass(el, c) { if (el) el.classList.remove(c); }

    /* ─── Tooltip (stub) ──────────────────────────────────────
     * All tooltips are now DaisyUI CSS-only (.tooltip[data-tip]).
     * This stub keeps bootstrap.Tooltip.getInstance() working in
     * case any legacy code references it.
     */
    function Tooltip() {}
    Tooltip.getInstance = function () { return null; };
    Tooltip.prototype.dispose = function () {};

    /* ─── Modal ────────────────────────────────────────────────
     * Implements Bootstrap 5 Modal with static backdrop and
     * keyboard-escape support, using the same CSS classes that
     * Bootstrap uses (already defined in tailwind.css compat layer).
     * Backdrop overlay is applied via CSS directly on .modal.show
     * (which fills the viewport) – no separate backdrop div needed.
     */
    var _activeModals = [];

    function Modal(el, opts) {
        if (!el) return;
        this._el = el;
        this._opts = opts || {};
        this._staticBackdrop = (this._opts.backdrop === 'static');
        this._keyboardClose  = (this._opts.keyboard !== false);
        this._shown = false;
        this._boundKeydown = null;
        this._boundBackdropClick = null;
    }

    Modal.prototype.show = function () {
        var self = this;
        var el = self._el;
        if (self._shown) return;
        self._shown = true;

        // Push to active stack
        _activeModals.push(self);

        // Display the modal – the semi-transparent backdrop overlay is applied
        // via CSS on .modal.show (the modal element fills the viewport).
        el.style.display = 'block';
        el.removeAttribute('aria-hidden');
        el.setAttribute('aria-modal', 'true');
        el.setAttribute('role', 'dialog');
        requestAnimationFrame(function () {
            addClass(el, 'show');
            addClass(document.body, 'modal-open');
            document.body.style.overflow = 'hidden';
            document.body.style.paddingRight = '0px';

            // Focus first focusable element inside the dialog
            setTimeout(function () {
                var focusable = el.querySelector('button:not([disabled]), [href], input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])');
                if (focusable) focusable.focus();
            }, 150);
        });

        // Keyboard close
        if (self._keyboardClose) {
            self._boundKeydown = function (e) {
                if (e.key === 'Escape') { self.hide(); }
            };
            on(document, 'keydown', self._boundKeydown);
        }

        // Clicking on the modal backdrop area (outside .modal-dialog) closes non-static modals
        self._boundBackdropClick = function (e) {
            if (e.target === el) {
                if (!self._staticBackdrop) { self.hide(); }
            }
        };
        on(el, 'click', self._boundBackdropClick);
    };

    Modal.prototype.hide = function () {
        var self = this;
        var el = self._el;
        if (!self._shown) return;

        removeClass(el, 'show');

        // Remove from active stack
        var idx = _activeModals.indexOf(self);
        if (idx !== -1) _activeModals.splice(idx, 1);

        if (_activeModals.length === 0) {
            setTimeout(function () {
                removeClass(document.body, 'modal-open');
                document.body.style.overflow = '';
                document.body.style.paddingRight = '';
            }, 200);
        }

        setTimeout(function () {
            el.style.display = 'none';
            el.setAttribute('aria-hidden', 'true');
            el.removeAttribute('aria-modal');
        }, 300);

        if (self._boundKeydown) { off(document, 'keydown', self._boundKeydown); self._boundKeydown = null; }
        if (self._boundBackdropClick) { off(el, 'click', self._boundBackdropClick); self._boundBackdropClick = null; }
        self._shown = false;
    };

    Modal.prototype.dispose = function () { this.hide(); };

    /* Static accessor so existing new bootstrap.Modal() calls work */
    Modal.getInstance = function (el) {
        for (var i = 0; i < _activeModals.length; i++) {
            if (_activeModals[i]._el === el) return _activeModals[i];
        }
        return null;
    };

    /* ─── Dropdown ─────────────────────────────────────────────
     * Toggles .show on the sibling (or parent's) .dropdown-menu.
     */
    function getDropdownMenu(triggerEl) {
        var parent = triggerEl.closest('.btn-group, .dropdown');
        if (parent) return qs('.dropdown-menu', parent);
        var next = triggerEl.nextElementSibling;
        if (next && hasClass(next, 'dropdown-menu')) return next;
        return null;
    }

    function closeAllDropdowns(except) {
        qsa('.dropdown-menu.show').forEach(function (menu) {
            if (menu !== except) {
                removeClass(menu, 'show');
                var btn = menu.previousElementSibling || (menu.parentElement && menu.parentElement.querySelector('[data-bs-toggle="dropdown"]'));
                if (btn) { btn.setAttribute('aria-expanded', 'false'); }
            }
        });
    }

    function initDropdowns() {
        on(document, 'click', function (e) {
            var trigger = e.target.closest('[data-bs-toggle="dropdown"]');
            if (trigger) {
                e.preventDefault();
                e.stopPropagation();
                var menu = getDropdownMenu(trigger);
                if (!menu) return;
                var isOpen = hasClass(menu, 'show');
                closeAllDropdowns(null);
                if (!isOpen) {
                    addClass(menu, 'show');
                    trigger.setAttribute('aria-expanded', 'true');
                } else {
                    trigger.setAttribute('aria-expanded', 'false');
                }
                return;
            }
            // Click outside closes all dropdowns
            if (!e.target.closest('.dropdown-menu')) {
                closeAllDropdowns(null);
            }
        });
    }

    /* ─── Tabs ─────────────────────────────────────────────────
     * Activates tab panes via data-bs-toggle="tab" +
     * data-bs-target="#panel-id".
     */
    function initTabs() {
        on(document, 'click', function (e) {
            var trigger = e.target.closest('[data-bs-toggle="tab"]');
            if (!trigger) return;
            e.preventDefault();
            var targetSel = trigger.getAttribute('data-bs-target') || trigger.getAttribute('href');
            if (!targetSel) return;
            var targetPane = qs(targetSel);
            if (!targetPane) return;

            // Deactivate sibling tabs in the same nav
            var nav = trigger.closest('.nav, .tabs');
            if (nav) {
                qsa('.nav-link.active, .tab.active', nav).forEach(function (t) {
                    removeClass(t, 'active');
                    t.setAttribute('aria-selected', 'false');
                });
            } else {
                // Fallback: deactivate all active tabs that share the same pane container
            }
            addClass(trigger, 'active');
            trigger.setAttribute('aria-selected', 'true');

            // Deactivate sibling panes
            var paneContainer = targetPane.parentElement;
            if (paneContainer) {
                qsa('.tab-pane.active', paneContainer).forEach(function (p) {
                    removeClass(p, 'active');
                    removeClass(p, 'show');
                });
            }
            addClass(targetPane, 'active');
            addClass(targetPane, 'show');

            // Fire shown.bs.tab event (home.js listens to this for DataTable refresh)
            // Dispatch both the CustomEvent and a jQuery-compatible trigger if jQuery is present
            try {
                var ev = new CustomEvent('shown.bs.tab', { bubbles: true, detail: { target: trigger } });
                trigger.dispatchEvent(ev);
                /* jQuery listens for 'shown.bs.tab' as a namespaced event.
                   Trigger it explicitly so DataTable resize callbacks fire. */
                if (typeof jQuery !== 'undefined') {
                    jQuery(trigger).trigger('shown.bs.tab');
                }
            } catch (ex) {}
        });
    }

    /* ─── Collapse ─────────────────────────────────────────────
     * Toggles .show on data-bs-target panel.
     * Supports data-bs-parent for accordion-style (only one open
     * at a time within the parent).
     *
     * Also sets style.display directly to ensure visibility is
     * correct regardless of CSS cascade (DaisyUI .collapse
     * conflict with Bootstrap accordion panels).
     */
    function collapsePanel(panel) {
        removeClass(panel, 'show');
        panel.style.display = 'none';
    }

    function expandPanel(panel) {
        addClass(panel, 'show');
        panel.style.display = 'block';
    }

    function initAccordionState() {
        // Set correct initial display state for all accordion panels so that
        // non-expanded panels are hidden regardless of the CSS cascade.
        qsa('.accordion-collapse.collapse').forEach(function (panel) {
            if (!hasClass(panel, 'show')) {
                panel.style.display = 'none';
            } else {
                panel.style.display = 'block';
            }
        });
    }

    function initCollapse() {
        on(document, 'click', function (e) {
            var trigger = e.target.closest('[data-bs-toggle="collapse"]');
            if (!trigger) return;
            e.preventDefault();
            var targetSel = trigger.getAttribute('data-bs-target') || trigger.getAttribute('href');
            if (!targetSel) return;
            var target = qs(targetSel);
            if (!target) return;

            var isExpanded = hasClass(target, 'show');

            // Accordion: if data-bs-parent, close siblings.
            // Bootstrap 5 places data-bs-parent on the collapse *panel* (target),
            // but it may also appear on the trigger button – check both.
            var parentSel = trigger.getAttribute('data-bs-parent') || target.getAttribute('data-bs-parent');
            if (parentSel) {
                var parentEl = qs(parentSel);
                if (parentEl) {
                    qsa('.accordion-collapse.show', parentEl).forEach(function (p) {
                        if (p !== target) {
                            collapsePanel(p);
                            var sibBtn = parentEl.querySelector('[data-bs-target="#' + p.id + '"]');
                            if (sibBtn) {
                                addClass(sibBtn, 'collapsed');
                                sibBtn.setAttribute('aria-expanded', 'false');
                            }
                        }
                    });
                }
            }

            if (isExpanded) {
                collapsePanel(target);
                addClass(trigger, 'collapsed');
                trigger.setAttribute('aria-expanded', 'false');
            } else {
                expandPanel(target);
                removeClass(trigger, 'collapsed');
                trigger.setAttribute('aria-expanded', 'true');
            }
        });
    }

    /* ─── Modal trigger / dismiss ──────────────────────────────
     * data-bs-toggle="modal" → open the target modal
     * data-bs-dismiss="modal" → close the containing modal
     * data-bs-dismiss="alert" → remove the containing alert
     */
    var _modalInstances = new WeakMap();

    function getOrCreateModal(el, opts) {
        if (!_modalInstances.has(el)) {
            _modalInstances.set(el, new Modal(el, opts || {}));
        }
        return _modalInstances.get(el);
    }

    function initModalTriggers() {
        on(document, 'click', function (e) {
            // data-bs-toggle="modal"
            var openTrigger = e.target.closest('[data-bs-toggle="modal"]');
            if (openTrigger) {
                var targetSel = openTrigger.getAttribute('data-bs-target') || openTrigger.getAttribute('href');
                var targetEl  = targetSel ? qs(targetSel) : null;
                if (targetEl) {
                    if (targetEl.tagName === 'DIALOG') {
                        // DaisyUI native <dialog> modal
                        var ev = new CustomEvent('show.bs.modal', { bubbles: true, cancelable: true });
                        ev.relatedTarget = openTrigger;
                        targetEl.dispatchEvent(ev);
                        targetEl.showModal();
                    } else {
                        var m = getOrCreateModal(targetEl, {});
                        m.show();
                    }
                }
                return;
            }

            // data-bs-dismiss="modal"
            var dismissModal = e.target.closest('[data-bs-dismiss="modal"]');
            if (dismissModal) {
                var modalEl = dismissModal.closest('.modal');
                if (modalEl) {
                    if (modalEl.tagName === 'DIALOG') {
                        // DaisyUI native <dialog> modal
                        modalEl.close();
                    } else {
                        var mi = Modal.getInstance(modalEl) || getOrCreateModal(modalEl, {});
                        mi.hide();
                    }
                }
                return;
            }

            // data-bs-dismiss="alert"
            var dismissAlert = e.target.closest('[data-bs-dismiss="alert"]');
            if (dismissAlert) {
                var alertEl = dismissAlert.closest('.alert');
                if (alertEl && alertEl.parentNode) alertEl.parentNode.removeChild(alertEl);
                return;
            }
        });
    }

    /* ─── Bootstrap namespace ──────────────────────────────────
     * Expose as window.bootstrap so existing code that does
     * `new bootstrap.Modal(...)` or `bootstrap.Tooltip.getInstance(...)`
     * works without modification.
     */
    var bootstrap = {
        Modal: Modal,
        Tooltip: Tooltip,
        /* Stubs for anything else that might be referenced */
        Collapse: function () {},
        Tab: function () {},
        Dropdown: function () {}
    };
    global.bootstrap = bootstrap;

    /* ─── Boot ─────────────────────────────────────────────────
     * Wire up all data-attribute handlers after the DOM is ready.
     */
    function boot() {
        initDropdowns();
        initTabs();
        initAccordionState();
        initCollapse();
        initModalTriggers();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', boot);
    } else {
        boot();
    }

}(window));
