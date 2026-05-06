/* Home form front-end validation
 * Uses data-error-required for translated messages
 */

(function () {
  const qs  = (s, r = document) => r.querySelector(s);
  const qsa = (s, r = document) => Array.from(r.querySelectorAll(s));

  const NUM_REGEX = /^\d+(\.\d{0,2})?$/;

  // ── Unique key: prefer id, fall back to auto-generated ──────────────────────
  function inputKey(input) {
    if (input.id) return input.id;
    if (!input.dataset.errKey) {
      input.dataset.errKey = 'ek_' + Math.random().toString(36).slice(2);
    }
    return input.dataset.errKey;
  }

  // ── Remove ALL error elements from the whole form ───────────────────────────
  function clearErrors(form) {
    qsa('.is-invalid', form).forEach(el => el.classList.remove('is-invalid'));
    qsa('.js-error-msg', form).forEach(el => el.remove());
  }

  // ── Remove the error element for ONE specific input ─────────────────────────
  function clearFieldError(input) {
    if (!input) return;
    input.classList.remove('is-invalid');
    const key = inputKey(input);
    const existing = qs(`.js-error-msg[data-for="${key}"]`);
    if (existing) existing.remove();
  }

  // ── Show an error message directly below a specific input ───────────────────
  function showError(input, msg) {
    if (!input) return;

    const key = inputKey(input);

    // Re-use existing error for THIS input if already present
    let msgEl = qs(`.js-error-msg[data-for="${key}"]`);

    if (!msgEl) {
      msgEl = document.createElement('small');
      msgEl.className = 'text-danger js-error-msg';
      msgEl.dataset.for   = key;
      msgEl.style.color      = '#dc3545';
      msgEl.style.fontSize   = '12px';
      msgEl.style.fontWeight = '400';
      msgEl.style.display    = 'block';

      const inProjectField = input.closest('.project-field-body');
      const inItemsCol     = input.closest('.items-field-col');

      if (inProjectField) {
        // Project Info: flows in normal document flow, card grows
        msgEl.style.position  = 'static';
        msgEl.style.marginTop = '6px';
        msgEl.style.lineHeight = '1.4';
        inProjectField.appendChild(msgEl);
      } else if (inItemsCol) {
        // Construction Items: absolute so nothing shifts
        // CSS handles position/top/z-index via .items-field-col .js-error-msg
        inItemsCol.appendChild(msgEl);
      } else {
        msgEl.style.position = 'absolute';
        msgEl.style.bottom   = '-20px';
        msgEl.style.left     = '0';
        msgEl.style.zIndex   = '1000';
        const col = input.closest('.form-group') || input.closest('.field-col') || input.parentElement;
        if (col) col.appendChild(msgEl);
      }
    }

    msgEl.textContent = msg;
  }

  // ── Numeric check ────────────────────────────────────────────────────────────
  function checkNumeric(input) {
    if (!input) return true;
    const v = input.value.trim().replace(/,/g, '');
    if (!v) return true;
    if (!NUM_REGEX.test(v)) {
      showError(input, input.dataset.errorNumber || 'Please enter a valid number');
      return false;
    }
    return true;
  }

  // ── Main validation ──────────────────────────────────────────────────────────
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
        // Both empty — show each field's own specific message
        if (mc) showError(mc, mc.dataset.errorRequired || 'Material price is required');
        if (lc) showError(lc, lc.dataset.errorRequired || 'Labor price is required');
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

    // ── input event ──────────────────────────────────────────────────────────
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

      if (input.value.trim()) {
        clearFieldError(input);
      }
    });

    // ── change event — selects ────────────────────────────────────────────────
    form.addEventListener('change', function (e) {
      const el = e.target;
      if (!(el instanceof HTMLSelectElement)) return;
      if (el.value.trim()) {
        clearFieldError(el);
      }
    });

    // ── keydown — block non-numeric keys ─────────────────────────────────────
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
