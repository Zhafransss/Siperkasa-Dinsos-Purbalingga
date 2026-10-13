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

/* ---- Photo slots: preview the chosen file before it is uploaded; X discards it again ---- */
document.querySelectorAll('[data-photo-input]').forEach((input) => {
    const slot = input.closest('[data-photo-slot]');
    const preview = slot?.querySelector('[data-photo-preview]');
    const empty = slot?.querySelector('[data-photo-empty]');
    const clear = slot?.querySelector('[data-photo-clear]');
    const storedSrc = preview?.getAttribute('src') || '';

    input.addEventListener('change', () => {
        const file = input.files?.[0];
        if (!preview || !file) return;

        preview.src = URL.createObjectURL(file);
        preview.classList.remove('hidden');
        empty?.classList.add('hidden');
        // A stored photo already has its own delete button in the same corner.
        if (!storedSrc) clear?.classList.remove('hidden');
        clear?.classList.toggle('flex', !storedSrc);

        // Offer the next empty card so another photo can be added beside this one.
        slot.parentElement.querySelector('[data-photo-slot].hidden')?.classList.remove('hidden');
    });

    clear?.addEventListener('click', () => {
        input.value = '';
        preview.removeAttribute('src');
        preview.classList.add('hidden');
        empty?.classList.remove('hidden');
        empty?.classList.add('flex');
        clear.classList.add('hidden');
        clear.classList.remove('flex');

        // Keep exactly one empty card: hide the extra ones that were offered while this slot was filled.
        let emptyKept = false;
        slot.parentElement.querySelectorAll('[data-photo-slot]').forEach((other) => {
            if (!other.querySelector('[data-photo-preview]').classList.contains('hidden')) return;
            if (emptyKept) other.classList.add('hidden');
            emptyKept = true;
        });
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

/* ---- Dropdown that always opens below its button (keyboard: arrows, Enter, Escape) ---- */
document.querySelectorAll('[data-dropdown]').forEach((root) => {
    const button = root.querySelector('[data-dropdown-button]');
    const list = root.querySelector('[data-dropdown-list]');
    const hidden = root.querySelector('[data-dropdown-value]');
    const label = root.querySelector('[data-dropdown-label]');
    const items = [...list.querySelectorAll('[role="option"]')];

    const close = () => { list.hidden = true; button.setAttribute('aria-expanded', 'false'); };
    const open = () => {
        list.hidden = false;
        button.setAttribute('aria-expanded', 'true');
        (items.find((i) => i.getAttribute('aria-selected') === 'true') ?? items[0])?.focus();
    };
    const choose = (item) => {
        hidden.value = item.dataset.value;
        label.textContent = item.textContent.trim();
        label.classList.remove('text-outline');
        items.forEach((i) => i.setAttribute('aria-selected', String(i === item)));
        close();
        button.focus();
    };

    button.addEventListener('click', () => (list.hidden ? open() : close()));
    button.addEventListener('keydown', (e) => {
        if (e.key === 'ArrowDown' || e.key === 'ArrowUp') { e.preventDefault(); open(); }
    });
    items.forEach((item, index) => {
        item.addEventListener('click', () => choose(item));
        item.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); choose(item); }
            else if (e.key === 'ArrowDown') { e.preventDefault(); items[Math.min(index + 1, items.length - 1)].focus(); }
            else if (e.key === 'ArrowUp') { e.preventDefault(); items[Math.max(index - 1, 0)].focus(); }
            else if (e.key === 'Escape') { close(); button.focus(); }
            else if (e.key === 'Tab') { close(); }
        });
    });
    document.addEventListener('click', (e) => { if (!root.contains(e.target)) close(); });
});
