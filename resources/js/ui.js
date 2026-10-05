/* Shared UI behaviour: mobile menu, toast, confirm dialog. */

const $ = (selector, root = document) => root.querySelector(selector);

/* ---- Mobile navigation ---- */
const navToggle = $('[data-mobile-nav-toggle]');
const mobileNav = $('#mobile-nav');
navToggle?.addEventListener('click', () => {
    const open = mobileNav.classList.toggle('hidden') === false;
    mobileNav.classList.toggle('flex', open);
    navToggle.setAttribute('aria-expanded', String(open));
});

/* ---- Toast ---- */
const toast = $('#toast');
let toastTimer;

export function showToast(message) {
    if (!toast || !message) return;
    toast.textContent = message;
    toast.classList.remove('hidden');
    toast.classList.add('flex');
    clearTimeout(toastTimer);
    toastTimer = setTimeout(() => {
        toast.classList.add('hidden');
        toast.classList.remove('flex');
    }, 3500);
}
showToast(toast?.dataset.message);

/* ---- Confirm dialog for forms carrying data-confirm-* attributes ----
 * data-confirm-input="field" additionally asks for a text (e.g. a rejection reason); it is sent as that field. */
const modal = $('#confirm-modal');
let pendingForm = null;

function closeModal() {
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    pendingForm = null;
}

if (modal) {
    const inputWrap = $('#confirm-input-wrap');
    const input = $('#confirm-input');
    const inputError = $('#confirm-input-error');

    document.addEventListener('submit', (event) => {
        const form = event.target;
        if (!(form instanceof HTMLFormElement) || !form.dataset.confirmMessage || form.dataset.confirmed) return;

        event.preventDefault();
        pendingForm = form;
        $('#confirm-title').textContent = form.dataset.confirmTitle || 'Konfirmasi';
        $('#confirm-message').textContent = form.dataset.confirmMessage;
        const okButton = $('[data-confirm-ok]', modal);
        okButton.textContent = form.dataset.confirmOk || 'Ya';
        // Destructive by default (red); data-confirm-tone="primary" is for positive actions such as approving.
        const primary = form.dataset.confirmTone === 'primary';
        okButton.classList.toggle('bg-error', !primary);
        okButton.classList.toggle('text-on-error', !primary);
        okButton.classList.toggle('bg-navy', primary);
        okButton.classList.toggle('text-white', primary);

        const wantsInput = Boolean(form.dataset.confirmInput);
        inputWrap.classList.toggle('hidden', !wantsInput);
        inputError.classList.add('hidden');
        input.value = '';
        if (wantsInput) $('#confirm-input-label').textContent = form.dataset.confirmInputLabel || 'Keterangan';

        modal.classList.remove('hidden');
        modal.classList.add('flex');
        (wantsInput ? input : $('[data-confirm-cancel]', modal)).focus();
    });

    $('[data-confirm-cancel]', modal).addEventListener('click', closeModal);
    modal.addEventListener('click', (event) => event.target === modal && closeModal());
    document.addEventListener('keydown', (event) => event.key === 'Escape' && closeModal());

    $('[data-confirm-ok]', modal).addEventListener('click', () => {
        const form = pendingForm;
        if (!form) return;

        const field = form.dataset.confirmInput;
        if (field) {
            const text = input.value.trim();
            if (!text && form.dataset.confirmInputRequired !== undefined) {
                inputError.textContent = (form.dataset.confirmInputLabel || 'Keterangan') + ' wajib diisi.';
                inputError.classList.remove('hidden');
                input.focus();
                return;
            }
            const hidden = document.createElement('input');
            hidden.type = 'hidden';
            hidden.name = field;
            hidden.value = text;
            form.appendChild(hidden);
        }

        closeModal();
        form.dataset.confirmed = '1';
        form.submit();
    });
}

