
   //Speech to text  (Thai)
function startDictation(btn){
  const input = btn.closest('.input-group')?.querySelector('input');
  if(!input) return;

  if(!('webkitSpeechRecognition' in window)){
    alert('เบราว์เซอร์นี้ไม่รองรับการจดจำเสียง (Speech Recognition).');
    return;
  }

  const r = new webkitSpeechRecognition();
  r.lang = 'th-TH';            // ← use Thai for recording incoming information
  r.interimResults = false;
  r.maxAlternatives = 1;
  r.onresult = e => {
    input.value = e.results[0][0].transcript;
    input.dispatchEvent(new Event('input', {bubbles:true}));
  };
  r.start();
}
window.startDictation = startDictation;


/* =======================
   Rows & totals
   ======================= */
let rowIndex = 1;

function to2(n){ return (isFinite(n) ? Number(n) : 0).toFixed(2); }

function calcRow(row){
  if(!row) return;

  const amount = parseFloat(row.querySelector('input[name*="[amount]"]')?.value) || 0;
  const mUnit  = parseFloat(row.querySelector('input[name*="[material_unit_price]"]')?.value) || 0;
  const lUnit  = parseFloat(row.querySelector('input[name*="[labor_unit_price]"]')?.value) || 0;

  const mTotal = amount * mUnit;
  const lTotal = amount * lUnit;
  const gTotal = mTotal + lTotal;

  const mTotalEl = row.querySelector('input[name*="[material_total]"]');
  const lTotalEl = row.querySelector('input[name*="[labor_total]"]');
  const gTotalEl = row.querySelector('input[name*="[grand_total]"]');

  if(mTotalEl) mTotalEl.value = to2(mTotal);
  if(lTotalEl) lTotalEl.value = to2(lTotal);
  if(gTotalEl) gTotalEl.value = to2(gTotal);
}

function recalcSummary(){
  const grandInputs = document.querySelectorAll('input[name*="[grand_total]"]');
  const sum = Array.from(grandInputs).reduce((acc, el)=> acc + (parseFloat(el.value)||0), 0);

  const operating = sum * 0.15;
  const ab        = sum + operating;
  const vat       = ab * 0.07;
  const finalT    = ab + vat;

  const byId  = id => document.getElementById(id);
  const setTxt = (id,val)=>{ const el=byId(id); if(el) el.textContent = to2(val); };
  const setVal = (id,val)=>{ const el=byId(id); if(el) el.value = to2(val); };

  // UI labels
  setTxt('miscDisplay', sum);
  setTxt('operatingDisplay', operating);
  setTxt('abDisplay', ab);
  setTxt('vatDisplay', vat);
  setTxt('finalDisplay', finalT);

  // Hidden inputs (backend)
  setVal('misc_total', sum);
  setVal('operating_expenses', operating);
  setVal('category_ab_total', ab);
  setVal('vat_total', vat);
  setVal('final_total', finalT);
}

function recalcAll(){
  document.querySelectorAll('#rows-container .item-row').forEach(calcRow);
  recalcSummary();
}

function renumberRows(){
  const rows = document.querySelectorAll('#rows-container .item-row');
  rows.forEach((row, i) => {
    const serial = row.querySelector('.serial');
    if(serial) serial.value = i + 1;

    row.querySelectorAll('input[name^="items["]').forEach(inp => {
      if (inp.name) inp.name = inp.name.replace(/items\[\d+\]/, `items[${i}]`);
    });

    row.querySelectorAll('.remove-row, .remove-mobile').forEach(btn=>{
      if(i === 0) btn.setAttribute('disabled','disabled'); else btn.removeAttribute('disabled');
    });
  });
  recalcAll();
}


/* =======================
   Add Row
   ======================= */
function addRow(){
  const container = document.getElementById('rows-container');
  const card = document.createElement('div');
  card.className = 'card m-b-2 item-row';
  card.innerHTML = `
    <div class="card-header bg-white">
      <button type="button" class="btn btn-danger btn-sm remove-mobile">&times;</button>

      <div class="row g-3 g-compact align-items-end">
        <div class="col-3 col-sm-2 col-md-1 field-col">
          <label class="form-label">No</label>
          <div class="input-group input-42">
            <input type="text" class="form-control readonly-input serial" value="${rowIndex+1}" readonly>
          </div>
        </div>

        <div class="col-12 col-sm-10 col-md-6 field-col">
          <label class="form-label">Category / Details</label>
          <div class="input-group input-42">
            <input type="text" class="form-control" name="items[${rowIndex}][details]" placeholder="Enter item name" required>
            <button type="button" class="btn btn-outline-secondary mic-btn" onclick="startDictation(this)" title="Speak">
              <i class="fas fa-microphone"></i>
            </button>
          </div>
        </div>

        <div class="col-6 col-md-2 field-col">
          <label class="form-label">Amount</label>
          <div class="input-group input-42">
            <input type="number" step="0.01" class="form-control" name="items[${rowIndex}][amount]" placeholder=".00" required>
          </div>
        </div>

        <div class="col-6 col-md-3 field-col">
          <label class="form-label">Unit</label>
          <div class="input-group input-42">
            <input type="text" class="form-control" name="items[${rowIndex}][unit]" placeholder="Unit" required>
            <button type="button" class="btn btn-outline-secondary mic-btn" onclick="startDictation(this)" title="Speak">
              <i class="fas fa-microphone"></i>
            </button>
          </div>
        </div>
      </div>

      <div class="row g-3 g-compact align-items-end pt-2 fields-line-2">
        <div class="col-12 col-md field-col">
          <label class="form-label">Material Price / Unit</label>
          <div class="input-group input-42">
            <input type="number" step="0.01" class="form-control" name="items[${rowIndex}][material_unit_price]" placeholder=".00" required>
          </div>
        </div>

        <div class="col-12 col-md field-col">
          <label class="form-label">Material Total</label>
          <div class="input-group input-42">
            <input type="number" step="0.01" name="items[${rowIndex}][material_total]" class="form-control readonly-input" placeholder="0.00" readonly>
          </div>
        </div>

        <div class="col-12 col-md field-col">
          <label class="form-label">Labor Price / Unit</label>
          <div class="input-group input-42">
            <input type="number" step="0.01" class="form-control" name="items[${rowIndex}][labor_unit_price]" placeholder=".00" required>
          </div>
        </div>

        <div class="col-12 col-md field-col">
          <label class="form-label">Labor Total</label>
          <div class="input-group input-42">
            <input type="number" step="0.01" name="items[${rowIndex}][labor_total]" class="form-control readonly-input" placeholder="0.00" readonly>
          </div>
        </div>

        <div class="col-12 col-md field-col">
          <label class="form-label">Grand Total</label>
          <div class="input-group input-42">
            <input type="number" step="0.01" name="items[${rowIndex}][grand_total]" class="form-control readonly-input" placeholder="0.00" readonly>
          </div>
        </div>
      </div>

      <div class="row pt-2">
        <div class="col-12 d-flex justify-content-end">
          <button type="button" class="btn btn-sm btn-danger remove-row">&times;</button>
        </div>
      </div>
    </div>
  `;
  container.appendChild(card);

  card.querySelectorAll('input[name*="[material_total]"],input[name*="[labor_total]"],input[name*="[grand_total]"]').forEach(inp=>{
    inp.value = '0.00';
  });

  card.querySelector('.remove-mobile')?.addEventListener('click', () => { card.remove(); renumberRows(); });
  card.querySelector('.remove-row')?.addEventListener('click', () => { card.remove(); renumberRows(); });

  rowIndex++;
  renumberRows();
}

document.addEventListener('DOMContentLoaded', () => {
  document.getElementById('add-row-btn')?.addEventListener('click', addRow);

  document.getElementById('rows-container')?.addEventListener('input', (e) => {
    if(e.target.matches('input[name*="[amount]"], input[name*="[material_unit_price]"], input[name*="[labor_unit_price]"]')){
      const row = e.target.closest('.item-row');
      if(row){ calcRow(row); recalcSummary(); }
    }
  });

  const first = document.querySelector('#rows-container .item-row');
  if(first){
    first.querySelector('.remove-row')?.addEventListener('click', () => { first.remove(); renumberRows(); });
    first.querySelector('.remove-mobile')?.addEventListener('click', () => { first.remove(); renumberRows(); });
  }

  renumberRows();
});
