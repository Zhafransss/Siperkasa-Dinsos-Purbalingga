/* Booking form: 3-step wizard. Step 1 verifies the NIP against the server; the server re-validates everything on submit. */

const root = document.getElementById('booking-wizard');

if (root) {
    const TOTAL_STEPS = 3;
    const form = root.querySelector('[data-wizard-form]');
    const steps = [...root.querySelectorAll('[data-step]')];
    const headers = [...root.querySelectorAll('[data-step-header]')];
    const progress = root.querySelector('[data-progress]');
    const prevBtn = root.querySelector('[data-prev]');
    const nextBtn = root.querySelector('[data-next]');
    const submitBtn = root.querySelector('[data-submit]');

    const nipInput = form.elements.nip;
    const nipError = root.querySelector('[data-nip-error]');
    const nipErrorText = root.querySelector('[data-nip-error-text]');
    const step2Error = root.querySelector('[data-step2-error]');
    const step2ErrorList = root.querySelector('[data-step2-error-list]');

    const csrf = document.querySelector('meta[name="csrf-token"]').content;
    let current = Number(root.dataset.initialStep) || 1;
    // Known after step 1; pre-filled when the server sends the user back to this form with the NIP already verified.
    let employeeName = root.dataset.employeeName || '';

    const show = (el, visible, display = 'flex') => {
        el.classList.toggle('hidden', !visible);
        el.classList.toggle(display, visible);
    };

    function updateUI() {
        steps.forEach((step) => { step.hidden = Number(step.dataset.step) !== current; });

        headers.forEach((header) => {
            const reached = Number(header.dataset.stepHeader) <= current;
            const circle = header.querySelector('[data-step-circle]');
            const label = header.querySelector('[data-step-label]');
            circle.classList.toggle('bg-primary', reached);
            circle.classList.toggle('text-white', reached);
            circle.classList.toggle('bg-surface-container-high', !reached);
            circle.classList.toggle('text-outline', !reached);
            label.classList.toggle('text-primary', reached);
            label.classList.toggle('text-outline', !reached);
        });

        progress.style.width = `${((current - 1) / (TOTAL_STEPS - 1)) * 100}%`;
        show(prevBtn, current > 1);
        show(nextBtn, current < TOTAL_STEPS);
        show(submitBtn, current === TOTAL_STEPS);
    }

    async function verifyNip() {
        const nip = nipInput.value.trim();

        if (!/^\d{18}$/.test(nip)) {
            nipErrorText.textContent = 'NIP harus terdiri dari 18 digit angka.';
            show(nipError, true);
            return false;
        }

        nextBtn.disabled = true;
        try {
            const response = await fetch(root.dataset.verifyUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', Accept: 'application/json', 'X-CSRF-TOKEN': csrf },
                body: JSON.stringify({ nip }),
            });
            const body = await response.json().catch(() => ({}));

            if (!response.ok) {
                nipErrorText.textContent = response.status === 429
                    ? 'Terlalu banyak percobaan. Silakan coba lagi sebentar lagi.'
                    : (body.message || 'NIP tidak dapat diverifikasi.');
                show(nipError, true);
                return false;
            }

            employeeName = body.name;
            show(nipError, false);
            return true;
        } catch {
            nipErrorText.textContent = 'Tidak dapat terhubung ke server. Periksa koneksi Anda.';
            show(nipError, true);
            return false;
        } finally {
            nextBtn.disabled = false;
        }
    }

    function validateStep2() {
        const errors = [];
        const fields = ['departs_on', 'returns_on', 'destination', 'purpose'];
        fields.forEach((name) => form.elements[name].classList.remove('border-error', 'ring-2', 'ring-error/30'));

        if (root.dataset.hasVehicle !== '1') errors.push({ message: 'Kendaraan belum dipilih — pilih dari Katalog Mobil.' });

        const departs = form.elements.departs_on.value;
        const returns = form.elements.returns_on.value;
        if (!departs) errors.push({ message: 'Tanggal Keberangkatan wajib diisi.', field: 'departs_on' });
        // Mirrors BookingWindow on the server: ISO dates compare correctly as strings.
        else if (departs.slice(0, 10) < root.dataset.earliest) {
            errors.push({
                message: `Pengajuan paling lambat 1 hari kerja sebelum keberangkatan — hari H tidak dapat diajukan. Tanggal berangkat paling cepat: ${root.dataset.earliestLabel}.`,
                field: 'departs_on',
            });
        }
        if (!returns) errors.push({ message: 'Tanggal Kembali wajib diisi.', field: 'returns_on' });
        // Whole days: returning on the day of departure is allowed. ISO dates compare correctly as strings.
        else if (departs && returns < departs) errors.push({ message: 'Tanggal Kembali tidak boleh sebelum Tanggal Keberangkatan.', field: 'returns_on' });
        if (!form.elements.destination.value.trim()) errors.push({ message: 'Lokasi Tujuan wajib diisi.', field: 'destination' });
        if (!form.elements.purpose.value.trim()) errors.push({ message: 'Keperluan wajib diisi.', field: 'purpose' });

        step2ErrorList.replaceChildren(...errors.map(({ message }) => Object.assign(document.createElement('li'), { textContent: message })));
        show(step2Error, errors.length > 0);
        errors.forEach(({ field }) => field && form.elements[field].classList.add('border-error', 'ring-2', 'ring-error/30'));

        if (errors.length) step2Error.scrollIntoView({ behavior: 'smooth', block: 'start' });
        return errors.length === 0;
    }

    function buildSummary() {
        // "YYYY-MM-DD" is parsed as UTC midnight, so format in UTC to avoid showing the previous day.
        const format = (value) => new Date(value).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric', timeZone: 'UTC' });
        const set = (key, text) => { root.querySelector(`[data-summary="${key}"]`).textContent = text || '-'; };

        set('employee', employeeName);
        set('nip', nipInput.value.trim());
        set('vehicle', root.querySelector('[data-vehicle-name]').value);
        const departs = form.elements.departs_on.value;
        const returns = form.elements.returns_on.value;
        set('schedule', departs === returns ? format(departs) : `${format(departs)} s/d ${format(returns)}`);
        set('destination', form.elements.destination.value);
        set('purpose', form.elements.purpose.value);
    }

    nextBtn.addEventListener('click', async () => {
        if (current === 1 && !(await verifyNip())) return;
        if (current === 2) {
            if (!validateStep2()) return;
            buildSummary();
        }
        if (current < TOTAL_STEPS) { current++; updateUI(); }
    });

    prevBtn.addEventListener('click', () => {
        if (current > 1) { current--; updateUI(); }
    });

    // Enter in the NIP field advances instead of submitting the whole form early.
    form.addEventListener('keydown', (event) => {
        if (event.key === 'Enter' && event.target.tagName === 'INPUT') {
            event.preventDefault();
            if (current < TOTAL_STEPS) nextBtn.click();
        }
    });

    // The return date can never be earlier than the departure date: the picker greys those days out, and a
    // return date that becomes too early after the departure is moved is reset to the departure date.
    const departsInput = form.elements.departs_on;
    const returnsInput = form.elements.returns_on;

    function syncReturnMin(resetInvalidValue) {
        const min = departsInput.value || root.dataset.earliest;
        returnsInput.min = min;
        if (resetInvalidValue && returnsInput.value && returnsInput.value < min) returnsInput.value = min;
    }

    departsInput.addEventListener('input', () => syncReturnMin(true));
    departsInput.addEventListener('change', () => syncReturnMin(true));
    syncReturnMin(false); // on load only the limit is set, so a value sent back by the server is not silently altered

    // When the server sent us back after a validation error the summary is rebuilt from the repopulated fields.
    if (current >= 2 && form.elements.departs_on.value && form.elements.returns_on.value) buildSummary();
    updateUI();
}
