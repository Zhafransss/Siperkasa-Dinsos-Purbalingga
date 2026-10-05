/* Destination suggestions (OpenStreetMap data, via our own /lokasi/cari endpoint). Free text always stays possible. */

const MIN_LENGTH = 3;
const DEBOUNCE_MS = 350;

document.querySelectorAll('input[data-place-search]').forEach((input) => {
    const list = input.parentElement.querySelector('[data-place-list]');
    let timer;
    let controller;
    let options = [];
    let active = -1;

    const open = () => { list.classList.remove('hidden'); input.setAttribute('aria-expanded', 'true'); };
    const close = () => {
        list.classList.add('hidden');
        input.setAttribute('aria-expanded', 'false');
        input.removeAttribute('aria-activedescendant');
        active = -1;
    };

    function setActive(index) {
        active = index;
        options.forEach((el, i) => {
            const on = i === active;
            el.classList.toggle('bg-surface-container-low', on);
            el.setAttribute('aria-selected', String(on));
            if (on) {
                input.setAttribute('aria-activedescendant', el.id);
                el.scrollIntoView({ block: 'nearest' });
            }
        });
    }

    function choose(place) {
        input.value = place.value;
        clearTimeout(timer); // a pending search for the half-typed text must not reopen the list
        close();
    }

    const note = (text) => {
        const li = document.createElement('li');
        li.className = 'px-4 py-2 text-label-sm text-on-surface-variant';
        li.textContent = text;
        return li;
    };

    function render(places) {
        list.replaceChildren();
        options = [];

        places.forEach((place, i) => {
            const li = document.createElement('li');
            li.id = `${list.id}-${i}`;
            li.setAttribute('role', 'option');
            li.className = 'flex cursor-pointer items-start gap-3 px-4 py-2 hover:bg-surface-container-low';

            const icon = document.createElement('span');
            icon.className = 'material-symbols-outlined mt-0.5 text-[20px] text-outline';
            icon.textContent = 'location_on';

            const text = document.createElement('div');
            const name = document.createElement('p');
            name.className = 'text-body-sm font-semibold text-on-surface';
            name.textContent = place.name;
            text.appendChild(name);
            if (place.detail) {
                const detail = document.createElement('p');
                detail.className = 'text-label-sm text-on-surface-variant';
                detail.textContent = place.detail;
                text.appendChild(detail);
            }

            li.append(icon, text);
            // mousedown (not click) so the choice lands before the input loses focus.
            li.addEventListener('mousedown', (event) => { event.preventDefault(); choose(place); });
            list.appendChild(li);
            options.push(li);
        });

        if (!places.length) list.appendChild(note('Tidak ada saran. Anda tetap dapat menulis lokasi secara bebas.'));
        const credit = note('Data peta © OpenStreetMap contributors');
        credit.className += ' border-t border-outline-variant';
        list.appendChild(credit);

        list._places = places;
        setActive(-1);
        open();
    }

    async function search() {
        const query = input.value.trim();
        if (query.length < MIN_LENGTH) { close(); return; }

        controller?.abort();
        controller = new AbortController();

        try {
            const response = await fetch(`${input.dataset.placeSearch}?${new URLSearchParams({ q: query })}`, {
                headers: { Accept: 'application/json' },
                signal: controller.signal,
            });
            if (!response.ok) { close(); return; }
            render((await response.json()).places ?? []);
        } catch {
            close(); // aborted or offline: the user simply keeps typing
        }
    }

    input.addEventListener('input', () => {
        clearTimeout(timer);
        timer = setTimeout(search, DEBOUNCE_MS);
    });

    input.addEventListener('keydown', (event) => {
        const isOpen = !list.classList.contains('hidden');

        if (event.key === 'ArrowDown' && isOpen && options.length) {
            event.preventDefault();
            setActive((active + 1) % options.length);
        } else if (event.key === 'ArrowUp' && isOpen && options.length) {
            event.preventDefault();
            setActive((active - 1 + options.length) % options.length);
        } else if (event.key === 'Enter' && isOpen && active >= 0) {
            // Pick the suggestion instead of letting the wizard treat Enter as "next step".
            event.preventDefault();
            event.stopPropagation();
            choose(list._places[active]);
        } else if (event.key === 'Escape' && isOpen) {
            event.stopPropagation();
            close();
        }
    });

    input.addEventListener('blur', close);
});
