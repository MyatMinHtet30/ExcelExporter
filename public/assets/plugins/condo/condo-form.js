// assets/plugins/condo/condo-form.js
(() => {
  // ---- Prevent double-binding if script is included twice ----
  if (window.__condoFormInitialized) return;
  window.__condoFormInitialized = true;

  // ---------- Utilities ----------
  function formatNumber(value) {
    const number = parseFloat(String(value).replace(/,/g, ''));
    return isNaN(number)
      ? ''
      : number.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  }
  function unformatNumber(value) {
    return parseFloat(String(value).replace(/,/g, '')) || 0;
  }
  // Allows typing "." and partial decimals while adding commas to int part
  function formatNumberLive(input) {
    let value = input.value.replace(/,/g, '');
    if (value === '' || (isNaN(value) && value !== '.')) {
      input.value = '';
      return;
    }
    const parts = value.split('.');
    const intPart = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    const decPart = parts[1] ? parts[1].slice(0, 3) : '';
    input.value = decPart ? `${intPart}.${decPart}` : intPart;
    if (value.endsWith('.') && decPart === '') input.value = `${intPart}.`;
  }

  // --- inline error helpers (moved from Blade) ---
  function clearInlineErrors(scope = document) {
    scope.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
    scope.querySelectorAll('.invalid-feedback.js').forEach(el => el.remove());
  }
  function showError(input, message) {
    if (!input) return;
    input.classList.add('is-invalid');
    const fb = document.createElement('small');
    fb.className = 'text-danger invalid-feedback js';
    fb.style.display = 'block';
    fb.textContent = message;
    input.closest('.form-group')?.appendChild(fb);
  }

  // ---------- Init after DOM ready ----------
  document.addEventListener('DOMContentLoaded', () => {
    const form          = document.getElementById('condo-form');
    const rowsContainer = document.getElementById('rows-container');
    const addRowBtn     = document.getElementById('add-row-btn');
    const tplEl         = document.getElementById('row-template');

    if (!form || !rowsContainer || !addRowBtn || !tplEl) return;

    const tplHtml = tplEl.innerHTML;

    // ---------- Validation (moved from Blade) ----------
    const cleanNumber = v => (v || '').toString().replace(/,/g, '').trim();
    const rowIsEmpty = row => {
      const f = ['.detail', '.amount', '.units', '.material', '.labor', '.price-per-unit'];
      return f.every(sel => !(row.querySelector(sel)?.value || '').trim());
    };

    function valHeader() {
      let ok = true;
      const must = [
        ['[name="customer_name"]', 'Customer Name is required'],
        ['[name="job_name"]', 'Job Name is required'],
        ['[name="quotation_number"]', 'Quotation Number is required'],
        ['[name="quotation_date"]', 'Date is required'],
      ];
      must.forEach(([sel, msg]) => {
        const el = form.querySelector(sel);
        if (!el || !el.value.trim()) { ok = false; showError(el, msg); }
      });
      return ok;
    }

    function valRows() {
      let ok = true;
      const rows = rowsContainer.querySelectorAll('.item-row');
      rows.forEach((row, i) => {
        if (rowIsEmpty(row)) return;
        const detail = row.querySelector('.detail');
        const amount = row.querySelector('.amount');
        if (!detail?.value.trim()) { ok = false; showError(detail, 'Details is required'); }
        if (!amount?.value.trim()) { ok = false; showError(amount, 'Amount is required'); }
        else if (isNaN(parseFloat(cleanNumber(amount.value)))) {
          ok = false; showError(amount, 'Amount must be a number');
        }
      });
      return ok;
    }

    // ---------- Reindex + protections ----------
    function enforceFirstRowProtection() {
      const rows = rowsContainer.querySelectorAll('.item-row');
      rows.forEach((row, i) => {
        const btn = row.querySelector('.remove-row');
        if (!btn) return;
        if (i === 0) {
          btn.disabled = true;
          btn.title = 'First row cannot be removed';
        } else {
          btn.disabled = false;
          btn.removeAttribute('title');
        }
      });
    }

    function reindexRows() {
      const rows = rowsContainer.querySelectorAll('.item-row');
      rows.forEach((row, idx) => {
        const serial = row.querySelector('.serial');
        if (serial) serial.value = idx + 1;

        let hiddenNo = row.querySelector('input[type="hidden"][name$="[no]"]');
        if (!hiddenNo) {
          hiddenNo = document.createElement('input');
          hiddenNo.type = 'hidden';
          hiddenNo.value = idx + 1;
          const firstBody = row.querySelector('.card-body') || row;
          firstBody.appendChild(hiddenNo);
        }
        hiddenNo.name = `items[${idx}][no]`;
        hiddenNo.value = idx + 1;

        const map = {
          details: '.detail',
          amount: '.amount',
          unit: '.units',
          material_cost: '.material',
          labor_cost: '.labor',
          price_per_unit_total: '.price-per-unit',
        };
        Object.entries(map).forEach(([field, sel]) => {
          const el = row.querySelector(sel);
          if (el) el.name = `items[${idx}][${field}]`;
        });
      });
      enforceFirstRowProtection();
    }

    // ---------- Calculations ----------
    function calculateRow(row) {
      const materialCostEl = row.querySelector('.material');
      const laborCostEl    = row.querySelector('.labor');
      const amountEl       = row.querySelector('.amount');
      const ppuEl          = row.querySelector('.price-per-unit');
      const subtotalEl     = row.querySelector('.subtotal');

      const materialCost = unformatNumber(materialCostEl?.value || '0');
      const laborCost    = unformatNumber(laborCostEl?.value || '0');
      const amount       = unformatNumber(amountEl?.value || '0');

      // If either material or labor filled, price per unit = sum
      if (ppuEl && (materialCost > 0 || laborCost > 0)) {
        ppuEl.value = formatNumber(materialCost + laborCost);
      }
      const pricePerUnit = unformatNumber(ppuEl?.value || '0');

      if (subtotalEl) {
        const subtotal = pricePerUnit * amount;
        subtotalEl.value = formatNumber(subtotal);
      }
    }

    function calculateTotals() {
      let total = 0;
      rowsContainer.querySelectorAll('.subtotal').forEach(input => {
        total += unformatNumber(input.value);
      });
      const tax   = total * 0.07;
      const grand = total + tax;

      const totalEl = document.getElementById('totalDisplay');
      const taxEl   = document.getElementById('taxDisplay');
      const grandEl = document.getElementById('totalPriceDisplay');

      if (totalEl) totalEl.textContent = formatNumber(total);
      if (taxEl)   taxEl.textContent   = formatNumber(tax);
      if (grandEl) grandEl.textContent = formatNumber(grand);
    }

    function recalcAll() {
      rowsContainer.querySelectorAll('.item-row').forEach(calculateRow);
      calculateTotals();
    }

    // ---------- Events (single source of truth) ----------
    addRowBtn.addEventListener('click', () => {
      const current = rowsContainer.querySelectorAll('.item-row').length;
      const html = tplHtml
        .replaceAll('__INDEX__', current)
        .replaceAll('__SERIAL__', current + 1);
      rowsContainer.insertAdjacentHTML('beforeend', html);
      reindexRows();
      recalcAll();
    });

    rowsContainer.addEventListener('click', (e) => {
      const btn = e.target.closest('.remove-row');
      if (!btn) return;
      const rows = rowsContainer.querySelectorAll('.item-row');
      const row  = btn.closest('.item-row');
      if (!row) return;
      if (rows.length <= 1) {
        alert('At least one row is required.');
        return;
      }
      const col = row.closest('.col-lg-12') || row;
      col.remove();
      reindexRows();
      recalcAll();
    });

    rowsContainer.addEventListener('input', (e) => {
      const input = e.target;
      if (
        input.classList.contains('amount') ||
        input.classList.contains('material') ||
        input.classList.contains('labor') ||
        input.classList.contains('price-per-unit')
      ) {
        input.value = input.value.replace(/[^0-9.]/g, '');
        formatNumberLive(input);
        const row = input.closest('.item-row');
        if (row) {
          calculateRow(row);
          calculateTotals();
        }
      }
    });

    rowsContainer.addEventListener('blur', (e) => {
      const input = e.target;
      if (
        input.classList.contains('amount') ||
        input.classList.contains('material') ||
        input.classList.contains('labor') ||
        input.classList.contains('price-per-unit')
      ) {
        input.value = formatNumber(input.value);
      }
    }, true);

    // Submit: validate header + rows (moved from Blade)
    form.addEventListener('submit', function (e) {
      clearInlineErrors(form);
      const okHeader = valHeader();
      const okRows   = valRows();
      if (!okHeader || !okRows) {
        e.preventDefault();
        const banner = form.querySelector('.alert.alert-danger');
        if (banner) banner.style.display = 'block';
        const first = form.querySelector('.is-invalid');
        if (first) first.scrollIntoView({ behavior: 'smooth', block: 'center' });
      }
    });

    // ---- First load: reindex & compute once ----
    reindexRows();
    recalcAll();

    // Keep first-row protection in sync on DOM mutations
    const mo = new MutationObserver(() => enforceFirstRowProtection());
    mo.observe(rowsContainer, { childList: true, subtree: true });
  });

  // -------- Voice dictation (kept global for inline onclicks) --------
  function startDictation(btn) {
    const input = btn.closest('.input-group')?.querySelector('input');
    if (!input) return;

    if (!('webkitSpeechRecognition' in window)) {
      alert('เบราว์เซอร์นี้ไม่รองรับการจดจำเสียง (Speech Recognition).');
      return;
    }
    const recognition = new webkitSpeechRecognition();
    recognition.lang = 'th-TH';
    recognition.interimResults = false;
    recognition.maxAlternatives = 1;
    recognition.onresult = (e) => {
      input.value = e.results[0][0].transcript;
      input.dispatchEvent(new Event('input', { bubbles: true }));
    };
    recognition.start();
  }
  window.startDictation = startDictation;
})();
