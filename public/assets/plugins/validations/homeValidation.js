/* Home form front-end validation
 * Uses data-error-required for translated messages
 */

(function () {
  const $  = (s, r = document) => r.querySelector(s);
  const $$ = (s, r = document) => Array.from(r.querySelectorAll(s));

  const NUM_REGEX = /^\d+(\.\d{0,2})?$/; 

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
      input.closest('.field-col') ||
      input.parentElement ||
      input;

    let msgEl = group.querySelector('.js-error-msg');

    if (!msgEl) {
      msgEl = document.createElement('small');
      msgEl.className = 'text-danger d-block mt-1 js-error-msg';
      group.appendChild(msgEl);
    }

    msgEl.textContent = msg;
  }

  function checkNumeric(input) {
    if (!input) return;
    const v = input.value.trim();
    if (!v) return; 
    if (!NUM_REGEX.test(v)) {
      const msg =
        input.dataset.errorNumber ||
        'Please enter a valid number';
      showError(input, msg);
      return false;
    }
    return true;
  }

  function validateHomeForm(form) {
    clearErrors(form);
    let valid = true;

    function checkRequired(input) {
      if (!input) return;
      if (!input.value.trim()) {
        const msg =
          input.dataset.errorRequired ||
          (input.name + ' is required');
        showError(input, msg);
        valid = false;
      }
    }

    checkRequired(form.querySelector('input[name="project_name"]'));
    checkRequired(form.querySelector('input[name="list_name"]'));
    checkRequired(form.querySelector('input[name="trooper"]'));

    $$('#rows-container .item-row').forEach(row => {
      const cat  = row.querySelector('input[name*="[category_name]"]');
      const amt  = row.querySelector('input[name*="[amount]"]');
      const unit = row.querySelector('input[name*="[unit]"]');
      const mc   = row.querySelector('input[name*="[mc_price]"]');
      const lc   = row.querySelector('input[name*="[lc_price]"]');

      checkRequired(cat);
      checkRequired(amt);
      checkRequired(unit);

      const mcVal = mc && mc.value.trim();
      const lcVal = lc && lc.value.trim();

      if (!mcVal && !lcVal) {
        const msg =
          (mc && mc.dataset.errorRequired) ||
          (lc && lc.dataset.errorRequired) ||
          'Material cost or labor cost is required';

        showError(mc, msg);
        showError(lc, msg);
        valid = false;
      }

      // numeric checks (only if not empty)
      if (amt && !checkNumeric(amt)) valid = false;
      if (mc && mcVal && !checkNumeric(mc)) valid = false;
      if (lc && lcVal && !checkNumeric(lc)) valid = false;
    });

    return valid;
  }

  window.validateHomeForm = validateHomeForm;

  const form = $('#home-form');
  if (form) {
    form.addEventListener('input', function (e) {
      const input = e.target;
      if (!(input instanceof HTMLInputElement)) return;

      const name = input.name || '';
      const isAmount = name.includes('[amount]');
      const isMc     = name.includes('[mc_price]');
      const isLc     = name.includes('[lc_price]');
      const isNumericField = isAmount || isMc || isLc;

      if (isNumericField) {
        const v = input.value.trim();
        const numOk = (v === '') || NUM_REGEX.test(v);

        if (!numOk) {
          const msg =
            input.dataset.errorNumber ||
            'Please enter a valid number';
          showError(input, msg);
        } else {
          let group =
            input.closest('.form-group') ||
            input.closest('.field-col') ||
            input.parentElement;
          const msgEl = group && group.querySelector('.js-error-msg');
          if (msgEl) msgEl.textContent = '';
          input.classList.remove('is-invalid');
        }

        if (isMc || isLc) {
          const row = input.closest('.item-row');
          if (row) {
            const mc = row.querySelector('input[name*="[mc_price]"]');
            const lc = row.querySelector('input[name*="[lc_price]"]');
            const mcVal = mc && mc.value.trim();
            const lcVal = lc && lc.value.trim();
            const mcOk  = !mcVal || NUM_REGEX.test(mcVal);
            const lcOk  = !lcVal || NUM_REGEX.test(lcVal);

            if ((mcVal || lcVal) && mcOk && lcOk) {
              [mc, lc].forEach(f => {
                if (!f) return;
                f.classList.remove('is-invalid');
                const g =
                  f.closest('.form-group') ||
                  f.closest('.field-col') ||
                  f.parentElement;
                const msgEl = g && g.querySelector('.js-error-msg');
                if (msgEl) msgEl.textContent = '';
              });
            }
          }
        }

        return; 
      }

      if (input.value.trim()) {
        const group =
          input.closest('.form-group') ||
          input.closest('.field-col') ||
          input.parentElement;
        const msgEl = group && group.querySelector('.js-error-msg');
        if (msgEl) msgEl.textContent = '';
        input.classList.remove('is-invalid');
      }
    });

    form.addEventListener('keydown', function (e) {
      const input = e.target;
      if (!(input instanceof HTMLInputElement)) return;

      const name = input.name || '';
      if (
        !name.includes('[amount]') &&
        !name.includes('[mc_price]') &&
        !name.includes('[lc_price]')
      ) return;

      const allowedKeys = [
        'Backspace', 'Delete', 'Tab', 'ArrowLeft', 'ArrowRight',
        'Home', 'End'
      ];

      if (allowedKeys.includes(e.key)) return;        
      if (e.key === '.' && !input.value.includes('.')) return; 
      if (e.key >= '0' && e.key <= '9') return;       

      e.preventDefault();

      const msg =
        input.dataset.errorNumber ||
        'Please enter number only';

      showError(input, msg);
    });
  }
})();
