/*
 * Action feedback, shared by every page.
 *  1. Toasts (partials/flash.blade.php): close button, auto-hide for success/info (paused on hover/focus).
 *  2. POST forms: once a submit really goes ahead (not cancelled by a confirm() or other handler),
 *     the clicked button shows a spinner + "Saving…" (or its data-loading-text) and further clicks are
 *     ignored, so the user knows the request is on its way and can't send it twice.
 */
(function () {
    'use strict';

    // ---- 1. Toasts ----
    function dismiss(toast) {
        if (!toast || toast.classList.contains('is-leaving')) return;
        toast.classList.add('is-leaving');
        setTimeout(function () { toast.remove(); }, 200);
    }

    function initToast(toast) {
        var close = toast.querySelector('[data-toast-close]');
        if (close) close.addEventListener('click', function () { dismiss(toast); });

        var timeout = parseInt(toast.dataset.timeout, 10);
        if (!timeout) return;

        var timer = null;
        function start() { timer = setTimeout(function () { dismiss(toast); }, timeout); }
        function stop() { clearTimeout(timer); }
        toast.addEventListener('mouseenter', stop);
        toast.addEventListener('mouseleave', start);
        toast.addEventListener('focusin', stop);
        toast.addEventListener('focusout', start);
        start();
    }

    document.addEventListener('keydown', function (e) {
        if (e.key !== 'Escape') return;
        document.querySelectorAll('[data-toast]').forEach(dismiss);
    });

    // ---- 2. Pending state on POST forms ----
    // Registered on window so it runs after handlers on the form and document (which may cancel the submit).
    window.addEventListener('submit', function (e) {
        var form = e.target;
        if (e.defaultPrevented || !(form instanceof HTMLFormElement)) return;
        if ((form.getAttribute('method') || 'get').toLowerCase() !== 'post' || form.hasAttribute('data-no-loading')) return;

        if (form.dataset.submitting) {      // already on its way
            e.preventDefault();
            return;
        }
        form.dataset.submitting = '1';
        form.setAttribute('aria-busy', 'true');

        var button = e.submitter || form.querySelector('button[type="submit"], button:not([type])');
        if (!button || button.tagName !== 'BUTTON') return;

        // Wait a tick so the button's own value (if any) is still sent with the form.
        setTimeout(function () {
            button.dataset.originalHtml = button.innerHTML;
            button.setAttribute('data-loading', '');
            button.setAttribute('aria-disabled', 'true');
            // Icon-only buttons keep their size; text buttons get a clear label.
            if (button.textContent.trim().length > 2) {
                button.textContent = button.dataset.loadingText || 'Saving…';
            }
        }, 0);
    });

    // Coming back with the browser's Back button: restore buttons frozen by a previous submit.
    window.addEventListener('pageshow', function (e) {
        if (!e.persisted) return;
        document.querySelectorAll('form[data-submitting]').forEach(function (form) {
            delete form.dataset.submitting;
            form.removeAttribute('aria-busy');
        });
        document.querySelectorAll('button[data-loading]').forEach(function (button) {
            button.removeAttribute('data-loading');
            button.removeAttribute('aria-disabled');
            if (button.dataset.originalHtml !== undefined) button.innerHTML = button.dataset.originalHtml;
        });
    });

    function init() { document.querySelectorAll('[data-toast]').forEach(initToast); }
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init);
    else init();
})();
