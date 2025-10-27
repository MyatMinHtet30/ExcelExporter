// ===== Speech to text (Thai) =====
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
window.startDictation = startDictation;

// ===== Helpers =====
const $  = (s, r=document) => r.querySelector(s);
const $$ = (s, r=document) => Array.from(r.querySelectorAll(s));
const to2 = n => (isFinite(n)?Number(n):0).toFixed(2);

// ===== Row & totals calc (aligned to details[*]) =====
function calcRow(row){
  const val = sel => parseFloat($(sel,row)?.value)||0;

  const amount = val('input[name*="[amount]"]');   // details[*][amount]
  const mc     = val('input[name*="[mc_price]"]'); // details[*][mc_price]
  const lc     = val('input[name*="[lc_price]"]'); // details[*][lc_price]

  const mTotal = amount * mc;
  const lTotal = amount * lc;
  const gTotal = mTotal + lTotal;

  const set = (cls, v) => { const el = $(cls, row); if(el) el.value = to2(v); };

  // display-only totals (no name=)
  set('.js-mat-total',  mTotal);
  set('.js-lab-total',  lTotal);
  set('.js-grand-total',gTotal);
}

function recalcSummary(){
  // Sum grand totals from each row (read from display-only inputs)
  const sum = $$('#rows-container .item-row .js-grand-total')
    .reduce((a,el)=> a + (parseFloat(el.value)||0), 0);

  const operating = sum * 0.15;
  const ab        = sum + operating;
  const vat       = ab * 0.07;
  const finalT    = ab + vat;

  const setTxt=id=>val=>{ const el=$(id.startsWith('#')?id:'#'+id); if(el) el.textContent = to2(val); };
  const setVal=id=>val=>{ const el=$(id.startsWith('#')?id:'#'+id); if(el) el.value      = to2(val); };

  setTxt('miscDisplay')(sum);
  setTxt('operatingDisplay')(operating);
  setTxt('abDisplay')(ab);
  setTxt('vatDisplay')(vat);
  setTxt('finalDisplay')(finalT);

  // Hidden inputs (safe to keep; backend ignores if not used)
  setVal('misc_total')(sum);
  setVal('operating_expenses')(operating);
  setVal('category_ab_total')(ab);
  setVal('vat_total')(vat);
  setVal('final_total')(finalT);
}

function recalcAll(){
  $$('#rows-container .item-row').forEach(calcRow);
  recalcSummary();
}

// ===== Renumber + reindex names (items[*] -> details[*]) =====
function renumberRows(){
  $$('#rows-container .item-row').forEach((row,i)=>{
    // Serial number
    const serial = $('.serial',row);
    if(serial) serial.value = i+1;

    // Update names to details[i][...]
    $$('input[name^="details["]',row).forEach(inp=>{
      inp.name = inp.name.replace(/details\[\d+\]/, `details[${i}]`);
    });

    // Enable remove except first row
    const del = $('.remove-row',row);
    if(del){ i===0 ? del.setAttribute('disabled','disabled') : del.removeAttribute('disabled'); }
  });
  recalcAll();
}

// ===== Add row (clone first, clear inputs properly) =====
function addRow(){
  const container = $('#rows-container');
  const firstRow  = $('.item-row', container);
  if(!container || !firstRow) return;

  const clone = firstRow.cloneNode(true);

  // Clear inputs; keep serial; set totals to 0.00
  $$('input', clone).forEach(inp=>{
    if(inp.classList.contains('serial')) return;
    if(inp.classList.contains('js-mat-total') ||
       inp.classList.contains('js-lab-total') ||
       inp.classList.contains('js-grand-total')) {
      inp.value = '0.00';
    } else {
      inp.value = '';
    }
  });

  const del = $('.remove-row', clone);
  if(del) del.removeAttribute('disabled');

  container.appendChild(clone);
  renumberRows();
}
window.addRow = addRow;

// ===== Boot (events) =====
document.addEventListener('DOMContentLoaded', () => {
  const container = $('#rows-container');
  $('#add-row-btn')?.addEventListener('click', addRow);

  // Recalc when key fields change
  container?.addEventListener('input', e=>{
    if(e.target.matches('input[name*="[amount]"], input[name*="[mc_price]"], input[name*="[lc_price]"]')){
      const row = e.target.closest('.item-row');
      if(row){ calcRow(row); recalcSummary(); }
    }
  });

  // Remove row (keep at least one)
  container?.addEventListener('click', e=>{
    const btn = e.target.closest('.remove-row'); if(!btn) return;
    const all = $$('#rows-container .item-row');
    if(all.length <= 1){ alert('ต้องมีอย่างน้อย 1 แถว'); return; }
    const row = btn.closest('.item-row'); row?.remove(); renumberRows();
  });

  // Initial
  renumberRows();
});
