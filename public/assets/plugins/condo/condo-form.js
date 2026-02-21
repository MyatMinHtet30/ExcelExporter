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


    // ---------- Reindex + protections ----------
    function updateRemoveButtons() {
      const rows = rowsContainer.querySelectorAll('.item-row');
      const onlyOne = rows.length <= 1;
      rows.forEach(row => {
        const btn = row.querySelector('.remove-row');
        if (!btn) return;
        btn.disabled = onlyOne;
        btn.classList.toggle('d-none', onlyOne); // hide when only one
        btn.removeAttribute('title');
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
      updateRemoveButtons();
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

      // Desktop displays
      const totalEl = document.getElementById('totalDisplay');
      const taxEl   = document.getElementById('taxDisplay');
      const grandEl = document.getElementById('totalPriceDisplay');

      if (totalEl) totalEl.textContent = formatNumber(total);
      if (taxEl)   taxEl.textContent   = formatNumber(tax);
      if (grandEl) grandEl.textContent = formatNumber(grand);

      // Mobile displays
      const totalElMobile = document.getElementById('totalDisplayMobile');
      const taxElMobile   = document.getElementById('taxDisplayMobile');
      const grandElMobile = document.getElementById('totalPriceDisplayMobile');

      if (totalElMobile) totalElMobile.textContent = formatNumber(total);
      if (taxElMobile)   taxElMobile.textContent   = formatNumber(tax);
      if (grandElMobile) grandElMobile.textContent = formatNumber(grand);
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

    // Mobile add row button
    const addRowBtnMobile = document.getElementById('add-row-btn-mobile');
    if (addRowBtnMobile) {
      addRowBtnMobile.addEventListener('click', () => {
        const current = rowsContainer.querySelectorAll('.item-row').length;
        const html = tplHtml
          .replaceAll('__INDEX__', current)
          .replaceAll('__SERIAL__', current + 1);
        rowsContainer.insertAdjacentHTML('beforeend', html);
        reindexRows();
        recalcAll();
        // Scroll to the new row
        const newRow = rowsContainer.querySelector('.item-row:last-child');
        if (newRow) {
          newRow.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
      });
    }

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
      if (typeof window.validateCondoForm === 'function') {
        const ok = window.validateCondoForm(form);
        if (!ok) {
          e.preventDefault();
          const banner = form.querySelector('.alert.alert-danger');
          if (banner) banner.style.display = 'block';
          const first = form.querySelector('.is-invalid');
          if (first) first.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
      }
    });

    // ---- First load: reindex & compute once ----
    reindexRows();
    recalcAll();

    const mo = new MutationObserver(() => updateRemoveButtons());
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
