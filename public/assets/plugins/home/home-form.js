/* -------------------------------------------------------
 * Home Form – unified logic for Create + Edit pages
 * - Row add/remove (with soft-delete on Edit)
 * - Reindexing details[i][...]
 * - Totals + summary
 * - Preview-on-Edit (POST without _method=PUT)
 * - Speech-to-text (Thai)
 * - Number formatting
 * -----------------------------------------------------*/

(function () {
  // ====== Shortcuts ======
  const $  = (s, r=document) => r.querySelector(s);
  const $$ = (s, r=document) => Array.from(r.querySelectorAll(s));
  const to2 = n => (isFinite(n) ? Number(n) : 0).toFixed(2);

  const rowsContainer = $('#rows-container');
  if (!rowsContainer) return;

  const homeForm = document.getElementById('home-form');
  if (homeForm && typeof window.validateHomeForm === 'function') {
    homeForm.addEventListener('submit', function (e) {
      const ok = window.validateHomeForm(homeForm);
      if (!ok) {
        e.preventDefault();
        homeForm.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }
    });
  }

  const addBtns = [
    $('#add-row-btn'),       // Create page
    $('#add-row-btn-edit'),  // Edit page
  ].filter(Boolean);

  const rowTemplate = $('#row-template'); // Present on Edit page
  const previewBtn  = $('#preview-btn');  // Present on Edit page

  // ====== Speech-to-text (Thai) ======
  function startDictation(btn){
    const input = btn.closest('.input-group')?.querySelector('input');
    if(!input) return;
    if(!('webkitSpeechRecognition' in window)){
      alert('เบราว์เซอร์นี้ไม่รองรับการจดจำเสียง (Speech Recognition).');
      return;
    }
    const r = new webkitSpeechRecognition();
    r.lang = 'th-TH'; r.interimResults = false; r.maxAlternatives = 1;
    r.onresult = e => {
      input.value = e.results[0][0].transcript;
      input.dispatchEvent(new Event('input',{bubbles:true}));
    };
    r.start();
  }
  // expose for inline buttons
  window.startDictation = startDictation;

  // ====== Helpers about soft-delete (Edit page) ======
  const isSoftRow  = (row) => !!row.querySelector('.js-delete-flag');
  const isDeleted  = (row) => {
    const f = row.querySelector('.js-delete-flag');
    return f ? f.value === '1' : false;
  };
  const setDeleted = (row, val) => {
    const f = row.querySelector('.js-delete-flag');
    if (!f) return;
    f.value = val ? '1' : '0';
    row.classList.toggle('row-deleted', !!val);
  };

  const getRows = () => $$('.item-row', rowsContainer);

  // ====== Per-row totals ======
  function calcRow(row){
    const val = sel => parseFloat($(sel,row)?.value) || 0;

    const amount = val('input[name*="[amount]"]');
    const mc     = val('input[name*="[mc_price]"]');
    const lc     = val('input[name*="[lc_price]"]');

    const mTotal = amount * mc;
    const lTotal = amount * lc;
    const gTotal = mTotal + lTotal;

    const set = (cls, v) => { const el = $(cls, row); if(el) el.value = to2(v); };
    set('.js-mat-total',   mTotal);
    set('.js-lab-total',   lTotal);
    set('.js-grand-total', gTotal);
  }

  // ====== Summary ======
  function recalcSummary(){
    // Sum only ACTIVE (non-deleted) rows' grand totals
    const sum = getRows()
   .map(r => $('.js-grand-total', r))
   .reduce((a,el) => a + (parseFloat(el?.value) || 0), 0);

    const operating = sum * 0.15;
    const ab        = sum + operating;
    const vat       = ab * 0.07;
    const finalT    = ab + vat;

    const setTxt=id=>val=>{ const el=$('#'+id); if(el) el.textContent = to2(val); };
    const setVal=id=>val=>{ const el=$('#'+id); if(el) el.value      = to2(val); };

    setTxt('miscDisplay')(sum);
    setTxt('operatingDisplay')(operating);
    setTxt('abDisplay')(ab);
    setTxt('vatDisplay')(vat);
    setTxt('finalDisplay')(finalT);

    setVal('misc_total')(sum);
    setVal('operating_expenses')(operating);
    setVal('category_ab_total')(ab);
    setVal('vat_total')(vat);
    setVal('final_total')(finalT);
  }
  function recalcAll(){
    getRows().forEach(calcRow);
    recalcSummary();
  }
  // Keep old name for compatibility if you used window.recomputeSummary elsewhere
  window.recomputeSummary = recalcSummary;

  // ====== Reindex + serials ======
  function reindexRows(){
    const rows = getRows();

    // Update name indices
    rows.forEach((row, idx) => {
      $$('input, select, textarea', row).forEach(el => {
        if (!el.name) return;
        el.name = el.name.replace(/details\[\d+\]/, `details[${idx}]`);
      });
    });

    // Serial numbers for ACTIVE rows only
    let serialCounter = 1;
    rows.forEach(row => {
      const serial = $('.serial', row);
      if (!serial) return;
      if (!isDeleted(row)) {
        serial.value = serialCounter++;
      } else {
        // keep its shown number or blank—choose your UX
        // serial.value = '';
      }
    });

    updateRemoveButtons();
    recalcAll();
  }

  // ====== Hide delete when only one ACTIVE row ======
  function updateRemoveButtons() {
    const rows = getRows();
    const onlyOne = rows.length === 1;
    rows.forEach(row => {
        const btn = row.querySelector('.remove-row');
        if (!btn) return;
        btn.classList.toggle('d-none', onlyOne);
        btn.disabled = onlyOne;
    });
    }

  // ====== Add row ======
  function addRow(){
    let newRow;

    if (rowTemplate) {
      // Edit page: create from template
      const nextIndex = getRows().length;
      const ser = nextIndex + 1;
      const html = rowTemplate.innerHTML
        .replaceAll('__INDEX__', nextIndex)
        .replaceAll('__SER__', ser);
      const wrap = document.createElement('div');
      wrap.innerHTML = html.trim();
      newRow = wrap.firstElementChild;
    } else {
      // Create page: clone first row structure
      const first = $('.item-row', rowsContainer);
      if (!first) return;
      newRow = first.cloneNode(true);

      // Clear inputs except serial & readonly totals (reset totals to 0.00)
      $$('input', newRow).forEach(inp => {
        if (inp.classList.contains('serial')) return;
        if (inp.classList.contains('js-mat-total') ||
            inp.classList.contains('js-lab-total') ||
            inp.classList.contains('js-grand-total')) {
          inp.value = '0.00';
        } else if (inp.type === 'hidden' && inp.classList.contains('js-delete-flag')) {
          inp.value = '0';
        } else {
          inp.value = '';
        }
      });
      // Make sure it's not visually deleted
      newRow.classList.remove('row-deleted');
    }

    rowsContainer.appendChild(newRow);
    reindexRows();
  }
  window.addRow = addRow; // optional global

  // ====== Remove / Soft-delete (delegated) ======
    // put this near the top with other refs
const deletedBin = document.getElementById('deleted-bin');

// ====== Remove / Delete (delegated) ======
document.addEventListener('click', function (e) {
  const btn = e.target.closest('.remove-row');
  if (!btn) return;
  const row = btn.closest('.item-row');
  if (!row || !rowsContainer.contains(row)) return;

  // keep at least one visible row
  const visibleCount = getRows().length;
  if (visibleCount <= 1) {
    alert('ต้องมีอย่างน้อย 1 แถว'); // At least one row required
    return;
  }

  // existing DB row?
  const idInput = row.querySelector('input[name*="[id]"]');
  const existingId = idInput && idInput.value;

  if (existingId && deletedBin) {
    // add a hidden field to request
    const h = document.createElement('input');
    h.type  = 'hidden';
    h.name  = 'deleted_detail_ids[]';
    h.value = existingId;
    deletedBin.appendChild(h);
  }

  // remove visually
  row.remove();

  // reindex and recompute
  reindexRows();
});

  // ====== Totals recalc on inputs ======
    // ====== Totals recalc on inputs + no-negative enforcement ======
  rowsContainer.addEventListener('input', function (e) {
    const target = e.target;

    // 1) Enforce no negative numbers
    if (target.matches('input.no-negative')) {
      let val = target.value || '';

      // Strip any "-" that got in (paste, etc.)
      if (val.includes('-')) {
        val = val.replace(/-/g, '');
        target.value = val;
        target.classList.add('is-invalid');
      } else if (val !== '' && parseFloat(val) < 0) {
        target.value = Math.abs(parseFloat(val)).toString();
        target.classList.add('is-invalid');
      } else {
        // OK value => clear error
        target.classList.remove('is-invalid');
      }
    }

    // 2) Existing totals logic
    if (target.matches('input[name*="[amount]"], input[name*="[mc_price]"], input[name*="[lc_price]"]')) {
      const row = target.closest('.item-row');
      if (row) {
        calcRow(row);
        recalcSummary();
      }
    }
  });

  // ====== Observe DOM changes (rows injected elsewhere) ======
  const mo = new MutationObserver(() => {
    reindexRows();
  });
  mo.observe(rowsContainer, { childList: true });

    // ====== Block negative values on .no-negative inputs ======
  // ====== Block negative values on .no-negative inputs ======
document.addEventListener('keydown', function (e) {
  const target = e.target;
  if (!target.matches('input.no-negative')) return;

  // Block "-", "+", and "e" (scientific notation)
  if (e.key === '-' || e.key === '+' || e.key.toLowerCase() === 'e') {
    e.preventDefault();
    target.classList.add('is-invalid');

    // 🔹 also add has-error to this row so spacing appears immediately
    const row = target.closest('.item-row');
    if (row) {
      row.classList.add('has-error');
    }
  }
});

  // ====== Number formatting (.number-input) ======
  function formatNumberInput(input) {
    let value = input.value.replace(/,/g, '');
    if (value === '' || isNaN(value)) { input.value = ''; return; }
    let parts = value.split('.');
    let intPart = parts[0];
    let decPart = parts[1] ? parts[1].slice(0, 3) : '';
    intPart = parseInt(intPart, 10).toLocaleString('en-US');
    input.value = decPart.length > 0 ? (intPart + '.' + decPart) : intPart;
  }
  document.addEventListener('input', function(e){
    if(e.target.classList.contains('number-input')){
      formatNumberInput(e.target);
    }
  });
  document.addEventListener('blur', function(e){
    if(e.target.classList.contains('number-input')){
      let value = e.target.value.replace(/,/g, '');
      if (value && !isNaN(value)) {
        e.target.value = parseFloat(value).toLocaleString('en-US', {
          minimumFractionDigits: 2,
          maximumFractionDigits: 2
        });
      }
    }
  }, true);

  // ====== Preview button (Edit page) – submit as POST to preview ======
  if (previewBtn) {
    previewBtn.addEventListener('click', function (e) {
      e.preventDefault();

      const form = this.form || document.getElementById('home-form');
      if (!form) return;

      if (typeof window.validateHomeForm === 'function') {
      const ok = window.validateHomeForm(form);
      if (!ok) {
        form.scrollIntoView({ behavior: 'smooth', block: 'start' });
        return; // ⬅️ stop here, do NOT preview
      }
    }

      const spoof = form.querySelector('input[name="_method"]');
      let spoofWasDisabled = false;
      if (spoof) { spoof.disabled = true; spoofWasDisabled = true; }

      const originalAction = form.getAttribute('action');
      const originalMethod = form.getAttribute('method');

      form.setAttribute('action', form.dataset.previewAction || form.getAttribute('data-preview-action') || form.dataset.preview || (typeof HOME_PREVIEW_URL !== 'undefined' ? HOME_PREVIEW_URL : ''));
      if (!form.getAttribute('action')) {
        // fallback for Blade: set data attr in template (see instructions)
        console.warn('Preview action missing: set data-preview-action on the form.');
      }
      form.setAttribute('method', 'POST');
      form.submit();

      // Restore
      form.setAttribute('action', originalAction);
      form.setAttribute('method', originalMethod || 'POST');
      if (spoof && spoofWasDisabled) spoof.disabled = false;
    });
  }

  document.addEventListener('input', function (e) {
    if (!e.target.closest('.item-row')) return;

    const row = e.target.closest('.item-row');
    const hasError = row.querySelector('.form-control.is-invalid');

    if (hasError) {
        row.classList.add('has-error');
    } else {
        row.classList.remove('has-error');
    }
});

  // ====== Add buttons ======
  addBtns.forEach(btn => btn.addEventListener('click', addRow));

  // ====== Initial ======
  // compute any prefilled rows (Edit), lock delete if only one active, update summary
  reindexRows();
})();
