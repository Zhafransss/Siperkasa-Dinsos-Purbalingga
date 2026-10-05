/* Admin panel behaviour: responsive sidebar drawer, password visibility toggle, image previews for uploads. */

/* ---- Sidebar drawer (below the lg breakpoint) ---- */
const sidebar = document.getElementById('admin-sidebar');
const backdrop = document.getElementById('admin-backdrop');
const menuButton = document.querySelector('[data-admin-menu-toggle]');

function setDrawer(open) {
    if (!sidebar) return;
    sidebar.classList.toggle('-translate-x-full', !open);
    backdrop?.classList.toggle('hidden', !open);
    menuButton?.setAttribute('aria-expanded', String(open));
}

menuButton?.addEventListener('click', () => setDrawer(sidebar.classList.contains('-translate-x-full')));
backdrop?.addEventListener('click', () => setDrawer(false));
document.addEventListener('keydown', (event) => event.key === 'Escape' && setDrawer(false));

/* ---- Show / hide a password field ---- */
document.querySelectorAll('[data-toggle-password]').forEach((button) => {
    button.addEventListener('click', () => {
        const input = document.getElementById(button.dataset.togglePassword);
        if (!input) return;

        const show = input.type === 'password';
        input.type = show ? 'text' : 'password';
        button.setAttribute('aria-label', show ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi');
        button.querySelector('.material-symbols-outlined').textContent = show ? 'visibility_off' : 'visibility';
    });
});

/* ---- Photo slots: preview the chosen file before it is uploaded ---- */
document.querySelectorAll('[data-photo-input]').forEach((input) => {
    input.addEventListener('change', () => {
        const slot = input.closest('[data-photo-slot]');
        const preview = slot?.querySelector('[data-photo-preview]');
        const file = input.files?.[0];
        if (!preview || !file) return;

        preview.src = URL.createObjectURL(file);
        preview.classList.remove('hidden');
        slot.querySelector('[data-photo-empty]')?.classList.add('hidden');
    });
});

/* ---- Filter forms: submit when a select changes, or shortly after typing stops ---- */
document.querySelectorAll('form[data-autosubmit]').forEach((form) => {
    let timer;
    form.querySelectorAll('select').forEach((select) => select.addEventListener('change', () => form.submit()));
    form.querySelectorAll('input[type="text"], input[type="search"]').forEach((input) => {
        input.addEventListener('input', () => {
            clearTimeout(timer);
            timer = setTimeout(() => form.submit(), 500);
        });
    });
});
