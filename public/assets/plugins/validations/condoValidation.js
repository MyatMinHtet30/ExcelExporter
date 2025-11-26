/* Condo form front-end validation
 * Uses data-error-required / data-error-number for messages
 */

(function () {
  const $  = (s, r = document) => r.querySelector(s);
  const $$ = (s, r = document) => Array.from(r.querySelectorAll(s));

  // digits, optional .xx (2 decimals)
  const NUM_REGEX = /^\d+(\.\d{0,2})?$/;

  // ---------- helpers ----------
  function clearErrors(form) {
    $$('.is-invalid', form).forEach(el => el.classList.remove('is-invalid'));
    $$('.js-error-msg', form).forEach(el => {
      el.textContent = '';
    });
  }

  function showError(input, msg) {
    if (!input) return;

    input.classList.add('is-invalid');

    let group =
      input.closest('.form-group') ||
      input.closest('.card-body') ||
      input.parentElement;

    if (!group) group = input;

    let msgEl = group.querySelector('.js-error-msg');
    if (!msgEl) {
      msgEl = document.createElement('small');
      msgEl.className = 'text-danger d-block mt-1 js-error-msg';
      group.appendChild(msgEl);
    }

    msgEl.textContent = msg;
  }

  function rowIsEmpty(row) {
    const sels = ['.detail', '.amount', '.units', '.material', '.labor', '.price-per-unit'];
    return sels.every(sel => {
      const el = row.querySelector(sel);
      return !el || !el.value.trim();
    });
  }

  // ---------- main validator ----------
  function validateCondoForm(form) {
    clearErrors(form);
    let valid = true;

    function checkRequired(selector) {
      const input = form.querySelector(selector);
      if (!input) return;
      if (!input.value.trim()) {
        const msg =
          input.dataset.errorRequired ||
          (input.name + ' is required');
        showError(input, msg);
        valid = false;
      }
    }

    // Header required fields
    checkRequired('input[name="customer_name"]');
    checkRequired('input[name="job_name"]');
    checkRequired('input[name="address"]');
    checkRequired('input[name="quotation_number"]');
    checkRequired('input[name="quotation_date"]');

    // Line items
    $$('#rows-container .item-row').forEach(row => {
      
      const detail = row.querySelector('.detail');
      const amount = row.querySelector('.amount');
      const unit    = row.querySelector('.units'); 
      const material = row.querySelector('.material');
      const labor = row.querySelector('.labor');
      const price = row.querySelector('.price-per-unit');

      // Details required
      if (detail && !detail.value.trim()) {
        const msg =
          detail.dataset.errorRequired ||
          'Details is required';
        showError(detail, msg);
        valid = false;
      }

      // Amount required + numeric
      if (amount) {
        const raw = amount.value.trim();
        const cleaned = raw.replace(/,/g, '');
        if (!raw) {
          const msg =
            amount.dataset.errorRequired ||
            'Amount is required';
          showError(amount, msg);
          valid = false;
        } else if (!NUM_REGEX.test(cleaned)) {
          const msg =
            amount.dataset.errorNumber ||
            'Please enter number only';
          showError(amount, msg);
          valid = false;
        }
      }

       if (unit && !unit.value.trim()) {
        const msg =
          unit.dataset.errorRequired ||
          'Unit is required';
        showError(unit, msg);
        valid = false;
      }

      function checkNumericField(field) {
        if (!field) return;
        const raw = field.value.trim();
        if (!raw) return; // optional
        const cleaned = raw.replace(/,/g, '');
        if (!NUM_REGEX.test(cleaned)) {
          const msg =
            field.dataset.errorNumber ||
            'Please enter number only';
          showError(field, msg);
          valid = false;
        }
      }

      checkNumericField(material);
      checkNumericField(labor);
       if (price) {
        const raw = price.value.trim();
        const cleaned = raw.replace(/,/g, '');
        if (!raw) {
          const msg =
            price.dataset.errorRequired ||
            'Price per unit is required';
          showError(price, msg);
          valid = false;
        } else if (!NUM_REGEX.test(cleaned)) {
          const msg =
            price.dataset.errorNumber ||
            'Please enter number only';
          showError(price, msg);
          valid = false;
        }
      }
    });

    return valid;
  }

  // Expose global
  window.validateCondoForm = validateCondoForm;

  // ---------- live behaviour (like homeValidation) ----------
  const form = $('#condo-form');
  if (form) {
    // Clear error when input becomes non-empty
    form.addEventListener('input', function (e) {
      const input = e.target;
      if (!(input instanceof HTMLInputElement)) return;

      if (input.value.trim()) {
        let group =
          input.closest('.form-group') ||
          input.closest('.card-body') ||
          input.parentElement;
        if (!group) group = input;

        const msgEl = group.querySelector('.js-error-msg');
        if (msgEl) msgEl.textContent = '';
        input.classList.remove('is-invalid');
      }
    });

    // Block letters / "-" on numeric fields and show error message
    form.addEventListener('keydown', function (e) {
      const input = e.target;
      if (!(input instanceof HTMLInputElement)) return;

      const isNumericField =
        input.classList.contains('amount') ||
        input.classList.contains('material') ||
        input.classList.contains('labor') ||
        input.classList.contains('price-per-unit');

      if (!isNumericField) return;

      const allowedKeys = [
        'Backspace', 'Delete', 'Tab',
        'ArrowLeft', 'ArrowRight', 'Home', 'End'
      ];

      if (allowedKeys.includes(e.key)) return;
      if (e.key === '.' && !input.value.includes('.')) return;
      if (e.key >= '0' && e.key <= '9') return;

      // block everything else (letters, minus, etc.)
      e.preventDefault();

      const msg =
        input.dataset.errorNumber ||
        'Please enter number only';

      showError(input, msg);
    });
  }
})();
