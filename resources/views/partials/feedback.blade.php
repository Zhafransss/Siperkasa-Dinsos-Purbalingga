{{-- Toast (flash messages) --}}
<div id="toast" role="status" data-message="{{ session('toast') }}"
     class="fixed bottom-8 left-1/2 z-[60] hidden -translate-x-1/2 items-center gap-4 rounded-xl bg-[#2d3135] px-8 py-4 text-body-sm text-white shadow-lg"></div>

{{--
    Confirm dialog (replaces window.confirm). A <form> opts in with data-confirm-message (+ title / ok label).
    Add data-confirm-input="field_name" to also collect a text (e.g. a rejection reason): it is submitted as that field.
--}}
<div id="confirm-modal" class="fixed inset-0 z-[70] hidden items-center justify-center bg-black/40 px-4" role="dialog" aria-modal="true" aria-labelledby="confirm-title">
    <div class="w-full max-w-sm rounded-xl border border-outline-variant bg-surface-container-lowest p-stack-lg shadow-lg">
        <h3 id="confirm-title" class="mb-2 text-headline-sm text-on-background"></h3>
        <p id="confirm-message" class="mb-stack-md text-body-sm text-on-surface-variant"></p>
        <div id="confirm-input-wrap" class="mb-stack-md hidden">
            <label for="confirm-input" id="confirm-input-label" class="mb-1 block text-label-md text-on-surface"></label>
            <textarea id="confirm-input" rows="3" maxlength="500" class="w-full rounded-lg border border-outline-variant p-3 text-body-sm focus:border-primary focus:ring-2 focus:ring-primary/20"></textarea>
            <p id="confirm-input-error" class="mt-1 hidden text-body-sm text-error"></p>
        </div>
        <div class="flex justify-end gap-3">
            <button type="button" data-confirm-cancel class="rounded-lg border border-outline-variant px-4 py-2 text-label-md text-on-surface-variant hover:bg-surface-container-low">Batal</button>
            <button type="button" data-confirm-ok class="rounded-lg bg-error px-4 py-2 text-label-md text-on-error hover:brightness-110"></button>
        </div>
    </div>
</div>
