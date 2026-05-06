/* Home form front-end validation
 * Uses data-error-required for translated messages
 */

(function () {
  const qs  = (s, r = document) => r.querySelector(s);
  const qsa = (s, r = document) => Array.from(r.querySelectorAll(s));

  const NUM_REGEX = /^\d+(\.\d{0,2})?$/;

  // Remove all error elements entirely — zero DOM footprint left behind
  function clearErrors(form) {
    qsa('.is-invalid', form).forEach(el => el.classList.remove('is-invalid'));
    qsa('.js-error-msg', form).forEach(el => el.remove());
  }

  // Remove the error element for a single field — card snaps back to natural height
  function clearFieldError(el) {
    if (!el) return;
    el.classList.remove('is-invalid');

    const containers = [
      el.closest('.project-field-body'),  // Project Info section
      el.closest('.form-group'),
      el.closest('.field-col'),
      el.parentElement,
    ];

    for (const container of containers) {
      if (!container) continue;
      const msgEl = container.querySelector('.js-error-msg');
      if (msgEl) {
        msgEl.remove(); // remove entirely — no leftover margin/height
        break;
      }
    }
  }

  function showError(input, msg) {
    if (!input) return;

    let group =
      input.closest('.form-group') ||
      input.closest('.field-col') ||
      input.parentElement ||
      input;

    // Re-use existing error element if present, otherwise create one
    let msgEl = group.querySelector('.js-error-msg');
    if (!msgEl) {
      msgEl = document.createElement('small');
      msgEl.className = 'text-danger js-error-msg';
      msgEl.style.color = '#dc3545';
      msgEl.style.fontSize = '12px';
      msgEl.style.fontWeight = '400';

      // Project Info section: flow in normal document flow so card grows/shrinks
      const inProjectField = input.closest('.project-field-body');
      if (inProjectField) {
        msgEl.style.position = 'static';
        msgEl.style.display = 'block';
        msgEl.style.marginTop = '6px';
        msgEl.style.lineHeight = '1.4';
        group = inProjectField; // append to .project-field-body, not .input-group
      } else {
        msgEl.style.position = 'absolute';
        msgEl.style.bottom = '-20px';
        msgEl.style.left = '0';
        msgEl.style.zIndex = '1000';
      }

      group.appendChild(msgEl);
    }

    msgEl.style.display = 'block';
    msgEl.textContent = msg;
  }

  function checkNumeric(input) {
    if (!input) return;
    const v = input.value.trim().replace(/,/g, '');
    if (!v) return;
    if (!NUM_REGEX.test(v)) {
      showError(input, input.dataset.errorNumber || 'Please enter a valid number');
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
        showError(input, input.dataset.errorRequired || (input.name + ' is required'));
        valid = false;
      }
    }

    checkRequired(form.querySelector('input[name="project_name"]'));
    checkRequired(form.querySelector('input[name="list_name"]'));
    // trooper is a <select>, not an <input>
    checkRequired(form.querySelector('select[name="trooper"]'));

    qsa('#rows-container .item-row', form).forEach(row => {
      const cat  = row.querySelector('input[name*="[category_name]"]');
      const amt  = row.querySelector('input[name*="[amount]"]');
      const unit = row.querySelector('.units');
      const mc   = row.querySelector('input[name*="[mc_price]"]');
      const lc   = row.querySelector('input[name*="[lc_price]"]');

      checkRequired(cat);
      checkRequired(amt);
      checkRequired(unit);

      const mcVal = mc && mc.value.trim().replace(/,/g, '');
      const lcVal = lc && lc.value.trim().replace(/,/g, '');

      if (!mcVal && !lcVal) {
        const msg =
          (mc && mc.dataset.errorRequired) ||
          (lc && lc.dataset.errorRequired) ||
          'Material cost or labor cost is required';
        showError(mc, msg);
        showError(lc, msg);
        valid = false;
      }

      if (amt && !checkNumeric(amt)) valid = false;
      if (mc && mcVal && !checkNumeric(mc)) valid = false;
      if (lc && lcVal && !checkNumeric(lc)) valid = false;
    });

    return valid;
  }

  window.validateHomeForm = validateHomeForm;

  const form = qs('#home-form');
  if (form) {

    // input event — text and numeric <input> fields
    form.addEventListener('input', function (e) {
      const input = e.target;
      if (!(input instanceof HTMLInputElement)) return;

      const name = input.name || '';
      const isAmount = name.includes('[amount]');
      const isMc     = name.includes('[mc_price]');
      const isLc     = name.includes('[lc_price]');
      const isNumeric = isAmount || isMc || isLc;

      if (isNumeric) {
        const v = input.value.trim().replace(/,/g, '');
        const numOk = (v === '') || NUM_REGEX.test(v);

        if (!numOk) {
          showError(input, input.dataset.errorNumber || 'Please enter a valid number');
        } else {
          clearFieldError(input);
        }

        if (isMc || isLc) {
          const row = input.closest('.item-row');
          if (row) {
            const mc = row.querySelector('input[name*="[mc_price]"]');
            const lc = row.querySelector('input[name*="[lc_price]"]');
            const mcVal = mc && mc.value.trim().replace(/,/g, '');
            const lcVal = lc && lc.value.trim().replace(/,/g, '');
            const mcOk  = !mcVal || NUM_REGEX.test(mcVal);
            const lcOk  = !lcVal || NUM_REGEX.test(lcVal);
            if ((mcVal || lcVal) && mcOk && lcOk) {
              [mc, lc].forEach(f => clearFieldError(f));
            }
          }
        }
        return;
      }

      // Any other text input — clear error as soon as it has a value
      if (input.value.trim()) {
        clearFieldError(input);
      }
    });

    // change event — <select> fields (trooper, unit, etc.)
    form.addEventListener('change', function (e) {
      const el = e.target;
      if (!(el instanceof HTMLSelectElement)) return;
      if (el.value.trim()) {
        clearFieldError(el);
      }
    });

    // keydown — block non-numeric keys in numeric fields
    form.addEventListener('keydown', function (e) {
      const input = e.target;
      if (!(input instanceof HTMLInputElement)) return;

      const name = input.name || '';
      if (
        !name.includes('[amount]') &&
        !name.includes('[mc_price]') &&
        !name.includes('[lc_price]')
      ) return;

      const allowedKeys = ['Backspace', 'Delete', 'Tab', 'ArrowLeft', 'ArrowRight', 'Home', 'End'];
      if (allowedKeys.includes(e.key)) return;
      if (e.key === '.' && !input.value.includes('.')) return;
      if (e.key >= '0' && e.key <= '9') return;

      e.preventDefault();
      showError(input, input.dataset.errorNumber || 'Please enter number only');
    });
  }
})();
