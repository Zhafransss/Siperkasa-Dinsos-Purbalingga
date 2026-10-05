/* Beranda: monthly fleet calendar. Days with a "Dipesan" / "Perawatan" chip open a modal with the details. */

const root = document.getElementById('calendar');

if (root) {
    const MONTHS = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    const label = root.querySelector('[data-calendar-label]');
    const grid = root.querySelector('[data-calendar-grid]');
    const modal = root.querySelector('[data-calendar-modal]');
    const modalDate = root.querySelector('[data-calendar-modal-date]');
    const detail = root.querySelector('[data-calendar-detail]');

    const today = root.dataset.today; // YYYY-MM-DD in the server's timezone
    let [year, month] = today.split('-').map(Number); // month is 1-based here
    let selected = null; // highlighted day while the modal is open
    let monthData = { booked: {}, maintenance: {} };

    const pad = (n) => String(n).padStart(2, '0');
    const iso = (y, m, d) => `${y}-${pad(m)}-${pad(d)}`;

    async function loadMonth() {
        const params = new URLSearchParams({ month: `${year}-${pad(month)}` });
        const response = await fetch(`${root.dataset.monthUrl}?${params}`, { headers: { Accept: 'application/json' } });
        monthData = response.ok ? await response.json() : { booked: {}, maintenance: {} };
        render();
    }

    async function openDetail(date) {
        selected = date;
        render();

        const [y, m, d] = date.split('-').map(Number);
        modalDate.textContent = new Date(y, m - 1, d).toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
        detail.innerHTML = '<p class="py-stack-lg text-center text-body-sm text-on-surface-variant md:col-span-2">Memuat…</p>';
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.classList.add('overflow-hidden');

        const params = new URLSearchParams({ date });
        const response = await fetch(`${root.dataset.dayUrl}?${params}`);
        // The fragment is escaped server-side by Blade, so it is safe to inject.
        detail.innerHTML = response.ok
            ? await response.text()
            : '<p class="py-stack-lg text-center text-body-sm text-error md:col-span-2">Gagal memuat detail peminjaman.</p>';
    }

    function closeDetail() {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.classList.remove('overflow-hidden');
        selected = null;
        render();
    }

    function render() {
        label.textContent = `${MONTHS[month - 1]} ${year}`;

        const lead = new Date(year, month - 1, 1).getDay(); // 0 = Sunday
        const daysInMonth = new Date(year, month, 0).getDate();
        const daysInPrev = new Date(year, month - 1, 0).getDate();
        const prevMonth = month === 1 ? 12 : month - 1;
        const prevYear = month === 1 ? year - 1 : year;
        const nextMonth = month === 12 ? 1 : month + 1;
        const nextYear = month === 12 ? year + 1 : year;

        const cells = [];
        for (let i = lead - 1; i >= 0; i--) cells.push({ date: iso(prevYear, prevMonth, daysInPrev - i), day: daysInPrev - i, fade: true });
        for (let d = 1; d <= daysInMonth; d++) cells.push({ date: iso(year, month, d), day: d, fade: false });
        for (let d = 1; cells.length % 7 !== 0; d++) cells.push({ date: iso(nextYear, nextMonth, d), day: d, fade: true });

        grid.innerHTML = '';
        cells.forEach((cell, index) => {
            const booked = cell.fade ? 0 : (monthData.booked[cell.date] ?? 0);
            const maintenance = cell.fade ? 0 : (monthData.maintenance[cell.date] ?? 0);
            const clickable = booked > 0 || maintenance > 0; // only days with a chip have details to show
            const isSelected = cell.date === selected;
            const isToday = !cell.fade && cell.date === today;

            const el = document.createElement('div');
            el.className = 'min-h-[90px] border-b border-outline-variant p-2 '
                + (index % 7 === 6 ? '' : 'border-r ')
                + (cell.fade ? 'bg-surface-container-low/30 opacity-50' : '')
                + (clickable ? ' cursor-pointer transition-colors hover:bg-surface-container-low' : '')
                + (isSelected ? ' bg-primary-container/5' : '');

            const number = document.createElement('span');
            number.textContent = cell.day;
            number.className = 'mb-1 block text-label-sm';
            if (isSelected) number.className = 'mb-1 inline-flex h-5 w-5 items-center justify-center rounded-full bg-primary text-label-sm text-white';
            else if (isToday) number.className = 'mb-1 inline-flex h-5 w-5 items-center justify-center rounded-full border-2 border-primary text-label-sm font-bold text-primary';
            el.appendChild(number);

            // Only days with something to report get a chip; free days stay empty.
            [
                [booked, 'Dipesan', 'border-primary-container bg-primary-container/10 text-primary'],
                [maintenance, 'Perawatan', 'border-amber-500 bg-amber-500/10 text-amber-600'],
            ].forEach(([count, name, tone]) => {
                if (count < 1) return;
                const chip = document.createElement('div');
                chip.className = `mb-1 rounded-sm border-l-2 p-1 ${tone}`;
                const text = document.createElement('p');
                text.className = 'truncate text-[10px] font-bold';
                text.textContent = `${count} ${name}`;
                chip.appendChild(text);
                el.appendChild(chip);
            });

            if (clickable) {
                el.setAttribute('role', 'button');
                el.tabIndex = 0;
                el.setAttribute('aria-label', `${cell.day} ${MONTHS[month - 1]}: ${booked} dipesan, ${maintenance} perawatan. Lihat detail`);
                el.addEventListener('click', () => openDetail(cell.date));
                el.addEventListener('keydown', (event) => {
                    if (event.key === 'Enter' || event.key === ' ') {
                        event.preventDefault();
                        openDetail(cell.date);
                    }
                });
            }

            grid.appendChild(el);
        });
    }

    function shiftMonth(delta) {
        month += delta;
        if (month > 12) { month = 1; year++; }
        if (month < 1) { month = 12; year--; }
        loadMonth();
    }

    root.querySelector('[data-calendar-prev]').addEventListener('click', () => shiftMonth(-1));
    root.querySelector('[data-calendar-next]').addEventListener('click', () => shiftMonth(1));

    root.querySelector('[data-calendar-modal-close]').addEventListener('click', closeDetail);
    modal.addEventListener('click', (event) => event.target === modal && closeDetail());
    document.addEventListener('keydown', (event) => event.key === 'Escape' && !modal.classList.contains('hidden') && closeDetail());

    render();
    loadMonth();
}
