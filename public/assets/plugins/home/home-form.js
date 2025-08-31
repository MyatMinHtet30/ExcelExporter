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

// ===== Row & totals calc =====
function calcRow(row){
  const v = sel => parseFloat($(sel,row)?.value.replace(/,/g,''))||0;
  const amount = v('input[name*="[amount]"]');
  const mUnit  = v('input[name*="[material_unit_price]"]');
  const lUnit  = v('input[name*="[labor_unit_price]"]');
  const mTotal = amount * mUnit;
  const lTotal = amount * lUnit;
  const gTotal = mTotal + lTotal;
  // const set = (sel,val)=>{ const el=$(sel,row); if(el) el.value = to2(val); };
  // set('input[name*="[material_total]"]', mTotal);
  // set('input[name*="[labor_total]"]',    lTotal);
  // set('input[name*="[grand_total]"]',    gTotal);
  const set = (sel,val)=>{
    const el=$(sel,row); 
    if(el) el.value = parseFloat(val).toLocaleString('en-US',{minimumFractionDigits:2,maximumFractionDigits:2});
  };

  set('input[name*="[material_total]"]', mTotal);
  set('input[name*="[labor_total]"]',    lTotal);
  set('input[name*="[grand_total]"]',    gTotal);
}

function recalcSummary(){
  const sum = $$('input[name*="[grand_total]"]').reduce((a,el)=>a+(parseFloat(el.value.replace(/,/g,''))||0),0);
  const operating = sum * 0.15;
  const ab        = sum + operating;
  const vat       = ab * 0.07;
  const finalT    = ab + vat;
  const fmt = n => parseFloat(n).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
  const setTxt=id=>val=>{ const el=$(id.startsWith('#')?id:'#'+id); if(el) el.textContent = fmt(val); };
  const setVal=id=>val=>{ const el=$(id.startsWith('#')?id:'#'+id); if(el) el.value      = fmt(val); };

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
  $$('#rows-container .item-row').forEach(calcRow);
  recalcSummary();
}

// ===== Renumber + reindex names =====
function renumberRows(){
  $$('#rows-container .item-row').forEach((row,i)=>{
    const serial = $('.serial',row);
    if(serial) serial.value = i+1;
    $$('input[name^="items["]',row).forEach(inp=>{
      inp.name = inp.name.replace(/items\[\d+\]/, `items[${i}]`);
    });
    const del = $('.remove-row',row);
    if(del){ i===0 ? del.setAttribute('disabled','disabled') : del.removeAttribute('disabled'); }
  });
  recalcAll();
}

// ===== Add row (clone first, like condo) =====
function addRow(){
  const container = $('#rows-container');
  const firstRow  = $('.item-row', container);
  if(!container || !firstRow) return;
  const clone = firstRow.cloneNode(true);
  $$('input', clone).forEach(inp=>{
    if(inp.classList.contains('serial')) return;
    const isRO = /\[(material_total|labor_total|grand_total)\]/.test(inp.name||'');
    inp.value = isRO ? '0.00' : '';
  });
  const del = $('.remove-row', clone);
  if(del) del.removeAttribute('disabled');
  container.appendChild(clone);
  renumberRows();
}
window.addRow = addRow; // (optional) allow calling from HTML if ever needed

// ===== Persist across language switch (localStorage) =====
(function persist(){
  const KEY = 'homeFormDraft';
  const form = () => $('form[action*="home"]') || $('form');
  const rows = () => $$('#rows-container .item-row');

  function collect(){
    const f = form(); if(!f) return null;
    const topNames = ['project_name','dear','list_name','house_no','trooper'];
    const data = { fields:{}, items:[] };
    topNames.forEach(n=> data.fields[n] = $(`[name="${n}"]`, f)?.value || '');
    rows().forEach((row,i)=>{
      const get = n => $(`input[name="items[${i}][${n}]"]`,row)?.value || '';
      data.items.push({
        details:get('details'),
        amount:get('amount'),
        unit:get('unit'),
        material_unit_price:get('material_unit_price'),
        material_total:get('material_total'),
        labor_unit_price:get('labor_unit_price'),
        labor_total:get('labor_total'),
        grand_total:get('grand_total')
      });
    });
    ['misc_total','operating_expenses','category_ab_total','vat_total','final_total'].forEach(id=>{
      const el = $('#'+id); if(el) data[id] = el.value;
    });
    return data;
  }

  function fill(data){
    if(!data) return;
    const f = form(); if(!f) return;

    // top
    Object.entries(data.fields||{}).forEach(([n,v])=>{ const el=$(`[name="${n}"]`,f); if(el) el.value=v; });

    // ensure enough rows
    const need = (data.items||[]).length;
    while(rows().length < need) addRow();

    // fill rows
    (data.items||[]).forEach((it,i)=>{
      const row = rows()[i]; if(!row) return;
      const set = (n,v)=>{ const el = $(`input[name="items[${i}][${n}]"]`,row); if(el) el.value = v??''; };
      set('details', it.details);
      set('amount', it.amount);
      set('unit', it.unit);
      set('material_unit_price', it.material_unit_price);
      set('material_total', it.material_total || '0.00');
      set('labor_unit_price', it.labor_unit_price);
      set('labor_total', it.labor_total || '0.00');
      set('grand_total', it.grand_total || '0.00');
    });

    renumberRows();
    recalcAll();

    // summary hidden
    ['misc_total','operating_expenses','category_ab_total','vat_total','final_total'].forEach(id=>{
      if(data[id]) { const el=$('#'+id); if(el) el.value=data[id]; }
    });
  }

  function save(){ const d=collect(); if(d) localStorage.setItem(KEY, JSON.stringify(d)); }
  function load(){ try{ const r=localStorage.getItem(KEY); return r?JSON.parse(r):null; }catch{return null;} }

  document.addEventListener('DOMContentLoaded', ()=>{
    // Restore if present
    const draft = load();
    if(draft && draft.items?.length){ fill(draft); }

    // Save just before language link navigates
    $$('a[href*="/lang/"]').forEach(a=>{
      a.addEventListener('click', save);
    });
  });
})();

// ===== Boot (events) =====
document.addEventListener('DOMContentLoaded', () => {
  const container = $('#rows-container');
  $('#add-row-btn')?.addEventListener('click', addRow);

  // recalc when key fields change
  container?.addEventListener('input', e=>{
    if(e.target.matches('input[name*="[amount]"], input[name*="[material_unit_price]"], input[name*="[labor_unit_price]"]')){
      const row = e.target.closest('.item-row'); if(row){ calcRow(row); recalcSummary(); }
    }
  });

  // remove row (keep at least one)
  container?.addEventListener('click', e=>{
    const btn = e.target.closest('.remove-row'); if(!btn) return;
    const all = $$('#rows-container .item-row');
    if(all.length <= 1){ alert('ต้องมีอย่างน้อย 1 แถว'); return; }
    const row = btn.closest('.item-row'); row?.remove(); renumberRows();
  });

  // initial
  renumberRows();

  // ===== Number formatting (like your original code) =====
function formatNumberInput(input) {
  // strip commas
  let value = input.value.replace(/,/g, '');
  if (value === '' || isNaN(value)) {
    input.value = '';
    return;
  }

  // split integer/decimal
  let parts = value.split('.');
  let intPart = parts[0];
  let decPart = parts[1] ? parts[1].slice(0, 3) : '';

  intPart = parseInt(intPart, 10).toLocaleString('en-US');

  if (decPart.length > 0) {
    input.value = intPart + '.' + decPart;
  } else {
    input.value = intPart;
  }
}

// Global event delegation for all .number-input
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

});
