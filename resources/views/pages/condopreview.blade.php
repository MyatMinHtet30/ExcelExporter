<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width">
  <title>Condo Quotation Preview</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />

  <style>
    :root{
      --w:1180px;
      --b:#000;
      --col-no:   60px;
      --col-amt:  73px;
      --col-unit: 73px;
      --col-pu:  110px;
      --col-tp:  120px;
      --band-right: calc((3 * var(--col-pu)) + var(--col-tp) + 0.5px);
      --totals-w: calc((2 * var(--col-pu)) + var(--col-tp) + 0.6px);
    }

    *{ box-sizing:border-box }
    html,body{ margin:0; background:#fff; font-family:'Times New Roman', Times, serif; color:#000; }

     .page-viewport{
      width:100vw;
    }

    .page{ width:var(--w); margin:20px auto; border:2px solid var(--b); background:#fff }
    .p{ padding:12px 16px }
    .center{text-align:center}
    .right{text-align:right}
    .bold{font-weight:700}

    /* Header */
    .company{ display:grid; grid-template-columns:1fr 250px; gap:10px }
    .company h1{ font-weight: bold; font-size:18px; margin:10px 0 6px }
    .company .lines{ line-height:2; font-size:18px }
    .logo img{ width:175px; height:auto }
    .title-wrap{ margin:12px 0 6px }
    .title{
      display:inline-block; border:2px solid var(--b); border-radius:10px;
      padding:8px 22px; font-size:20px; font-weight:700; background:#fff
    }

    .meta-wrap{
      display:grid; grid-template-columns:1fr var(--band-right);
      gap:0; margin:10px 14px 0;
      border-right:1px solid var(--b);
      border-left: 1px solid var(--b);
    }
    table.metaL, table.metaR{ width:100%; border-collapse:separate; border-spacing:0; background:#fff }
    table.metaL{ border:1px solid var(--b); border-bottom:0; border-right:0 }
    .metaL td{ padding:8px 10px; font-size:14px; border:none }
    .metaL td.label{ width:190px; border-right:1px solid var(--b) }
    .metaL tr:first-child td{ border-top:1px solid var(--b) }

    table.metaR{ border:1px solid var(--b); border-bottom:0 }
    .metaR td{ padding:8px 10px; font-size:14px; border:none }
    .metaR td.label{ width:300px; font-weight:600 }
    .metaR tr:first-child td{ border-top:1px solid var(--b) }
    .metaR td:last-child{ text-align:left }

    table.items{
      width:calc(100% - 28px); margin:0 14px;
      border-collapse:collapse; table-layout:fixed; border-top:1px solid var(--b); background:#fff;

      border-left: 2px solid var(--b);
      border-right: 2px solid var(--b);
    }
    .items th,.items td{ border:1px solid var(--b); padding:6px 8px; font-size:13px; vertical-align:middle }
    .items tbody td{ background:#fff }
    .c{text-align:center}.r{text-align:right}
    .col-no{ width:var(--col-no) }
    .col-amt{ width:var(--col-amt) }
    .col-unit{ width:var(--col-unit) }
    .col-pu{ width:var(--col-pu) }
    .col-tp{ width:var(--col-tp) }

    .bottom-wrap{
      display:grid;
      grid-template-columns: calc(100% - var(--totals-w)) var(--totals-w);
      margin: -1px 14px 0;

      border-top: 0;
      border-bottom: 0;
      border-bottom: 1px solid var(--b);

      border-left: 2px solid var(--b);
      border-right: 1px solid var(--b);

      align-items: stretch;
    }

    .wordsCell{
      padding:10px; font-size:14px; white-space:nowrap;
      display:flex; align-items:flex-end;
      border-top: 1px solid var(--b);
      border-bottom: 1px solid var(--b);
    }

    table.totals{
      width:100%; border-collapse:collapse; margin:0; background:#fff;
      border-top: 1px solid var(--b);
      border-left: 1px solid var(--b);
    }
    .totals td{ border:1px solid var(--b); padding:8px 10px; font-size:14px }
    .totals tr:first-child td{ border-top:0 }
    .totals .label{ font-weight:700 }
    .totals .val{ width:119.5px; text-align:right }
    .totals tr:last-child td{ font-weight:700; border-bottom:1px solid var(--b); }

    .disclaimer{ color:#c00; text-align:center; font-size:13px; margin:8px 14px }

    .thanks {
      width: calc(100% - 28px);
      margin: 10px 14px 6px;
      text-align: left;
      color: #c00;
      font-size: 13px;
    }

    :root { --sig-line-w: 82%; }
    .signs2{
      width: calc(100% - 28px);
      margin: 12px 14px 18px;
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 44px;
      break-inside: avoid;
    }
    .sig{ text-align:center; font-size:14px; }
    .sig .head{ font-weight:700; margin-bottom:26px; }
    .sig .line{ width:var(--sig-line-w); margin:0 auto 8px; border-top:1px solid var(--b); height:1px; }
    .sig .name{ margin-top:2px; }
    .sig .date{ margin-top:18px; font-size:13px; }

    .btn-footer{
      width: 1180px;        
      margin: 20px auto;
      display: flex;
      justify-content: center;
      gap: 20px;
    }
    .btn-lg{
      padding: 10px 26px;
      font-size: 16px;
      font-weight: 600;
      border: 1px solid #000;
      background: #eee;
      cursor: pointer;
    }
    .btn-lg:hover{ background:#ddd; }
    @media print{ 
      .no-print{ display:none !important; width: 1180px; }
       
    }

    /* Photo Gallery Styles */
    .photo-gallery-wrap {
      page-break-inside: avoid;
      break-inside: avoid;
    }
    
    .photo-item img {
      max-width: 100% !important;
      height: auto !important;
    }
    
    .photo-grid {
      display: grid !important;
      grid-template-columns: repeat(5, 1fr) !important;
      gap: 8px !important;
    }
    
    @media print {
      .photo-gallery-wrap {
        page-break-before: always;
        margin-top: 0;
      }
      
      .photo-grid {
        display: grid !important;
        gap: 5px !important;
      }
      
      .photo-item {
        border: 1px solid #000 !important;
        break-inside: avoid;
      }
    }
    
    @media (max-width: 768px) {
      .photo-grid {
        grid-template-columns: repeat(2, 1fr) !important;
        gap: 8px;
      }
      
      .photo-item {
        width: auto !important;
        height: 120px !important;
      }
    }
    
    @media (max-width: 480px) {
      .photo-grid {
        grid-template-columns: 1fr !important;
      }
      
      .photo-item {
        height: 200px !important;
      }
    }
    
  </style>
</head>
<body>
  
<div class= "page-viewport">
  <div class="page">
    <!-- Header -->
    <div class="p company">
      <div>
        <h1 class="mb-1">Address of PITA BUILD Company Limited (Head Office)</h1>
        <div class="lines">
          <span class="bold">32/20 Village No. 6, Bang Talat Subdistrict, Pak Kret District, Nonthaburi Province 11120</span><br>
          Phone : 062-604-2054 , 086-901-2500 &nbsp;&nbsp;&nbsp; Email : k.tanapon191@gmail.com<br>
          Taxpayer Identification Number 0125565022982
        </div>
      </div>
      <div class="logo d-flex align-items-start justify-content-end">
        <img src="{{ asset('assets/images/PITA_logo.jpg') }}" alt="PITA Logo">
      </div>
    </div>

    <div class="title-wrap center">
      <div class="title">ใบเสนอราคา / Quotation</div>
    </div>

    @php
      // Clean numeric strings like "2,000" => 2000.0
      function n($v){ return (float) preg_replace('/[^\d\.\-]/', '', (string)($v ?? 0)); }
    @endphp

    <!-- Meta (left/right tables) -->
    <div class="meta-wrap">
      <table class="metaL">
        <tr><td class="label bold">Customer Name</td><td>{{ $customer_name }}</td></tr>
        <tr><td class="label bold">Address</td><td>{{ $address }}</td></tr>
        <tr><td class="label bold">Job name</td><td>{{ $job_name }}</td></tr>
        <tr><td class="label bold">เลขประจำตัวผู้เสียภาษีอากร</td><td>&nbsp;</td></tr>
      </table>

      <table class="metaR">
        <tr>
          <td class="label">Quotation number</td>
          <td class="right">{{ $quotation_number }}</td>
        </tr>
        <tr>
          <td class="label">Date (D/M/Y)</td>
          <td class="right">
            @if(!empty($quotation_date))
              {{ \Carbon\Carbon::parse($quotation_date)->format('d/m/Y') }}
            @endif
          </td>
        </tr>
        <tr>
          <td class="label">Payment terms</td>
          <td class="right">{{ $payment_term }}</td>
        </tr>
        <tr>
          <td class="label">credit</td>
          <td class="right">{{ $credits }}</td>
        </tr>
      </table>
    </div>

    <!-- Items -->
    <table class="items">
      <colgroup>
        <col class="col-no">
        <col> <!-- Details expands -->
        <col class="col-amt">
        <col class="col-unit">
        <col class="col-pu"><col class="col-pu"><col class="col-pu">
        <col class="col-tp">
      </colgroup>
      <thead>
        <tr>
          <th class="c" rowspan="2">No</th>
          <th class="c" rowspan="2">Details</th>
          <th class="c" rowspan="2">Amount</th>
          <th class="c" rowspan="2">Units</th>
          <th class="c" colspan="3">Price per unit</th>
          <th class="c" rowspan="2">Total price</th>
        </tr>
        <tr>
          <th class="c">Material cost</th>
          <th class="c">Labor price</th>
          <th class="c">Total</th>
        </tr>
      </thead>
      <tbody>
        @php $n=1; @endphp
        @forelse($rows as $r)
        @php
            $mc = n($r['mc_price'] ?? 0);
            $lc = n($r['lc_price'] ?? 0);
            $ppu = n($r['ppu'] ?? ($mc + $lc));
        @endphp
          <tr>
            <td class="c">{{ $r['no'] ?? $n }}</td>
            <td>{{ $r['details'] }}</td>
            <td class="c">{{ number_format(n($r['amount']),2) }}</td>
            <td class="c">{{ $r['unit'] }}</td>
            @if($mc == 0 && $lc == 0)
              <td class="c" colspan="2">เหมา</td>
            @else
              <td class="c">{{ number_format($mc,2) }}</td>
              <td class="c">{{ number_format($lc,2) }}</td>
            @endif
            <td class="c">{{ number_format(n($r['ppu'] ?? (n($r['mc_price']) + n($r['lc_price']))),2) }}</td>
            <td class="r">{{ number_format(n($r['subtotal']),2) }}</td>
          </tr>
          @php $n++; @endphp
        @empty
          <tr><td colspan="8" class="center">No items.</td></tr>
        @endforelse
      </tbody>
    </table>

    <div class="bottom-wrap" id="bottomWrap">
      <div class="wordsCell">
        ตัวอักษร ( <span id="bahtText"></span> )
      </div>

      <div class="totalsCell">
        <table class="totals" id="totals" data-grand="{{ n($grand) }}">
          <tr><td class="label">Total</td><td class="val">{{ number_format(n($total),2) }}</td></tr>
          <tr><td class="label">Tax 7%</td><td class="val">{{ number_format(n($vat),2) }}</td></tr>
          <tr><td class="label">Total price</td><td class="val">{{ number_format(n($grand),2) }}</td></tr>
        </table>
      </div>
    </div>

    <div class="disclaimer">The above price does not include various expenses related to the condominium juristic person.</div>

    <div class="thanks">ขอขอบพระคุณที่ท่านให้ความสนใจในบริการของเรา</div>

    <!-- Signatures -->
    <div class="signs2">
      <div class="sig">
        <div class="head">จัดทำโดย / Prepared By</div>
        <div class="line"></div>
        <div class="name">( ........................................ )</div>
        <div class="date">วันที่ ___ / ___ / ______</div>
      </div>
      <div class="sig">
        <div class="head">สั่งซื้อโดย / Order By</div>
        <div class="line"></div>
        <div class="name">( ........................................ )</div>
        <div class="date">วันที่ ___ / ___ / ______</div>
      </div>
      <div class="sig">
        <div class="head">ผู้มีอำนาจผูกพันบริษัท / Authorized Signature</div>
        <div class="line"></div>
        <div class="name">( ........................................ )</div>
        <div class="date">วันที่ ___ / ___ / ______</div>
      </div>
    </div>
  </div>

  {{-- Photo Gallery Section --}}
  @php
    \Log::info('Condo Preview Blade - Photos variable', [
        'isset' => isset($photos),
        'is_collection' => isset($photos) && $photos instanceof \Illuminate\Support\Collection,
        'count' => isset($photos) ? (is_countable($photos) ? count($photos) : 'not countable') : 'not set',
        'type' => isset($photos) ? gettype($photos) : 'not set'
    ]);
  @endphp
  

  @if($photos && count($photos) > 0)
    <div class="photo-gallery-wrap" style="width: var(--w); margin: 20px auto; background: #fff; border: 2px solid #000; padding: 20px; position: relative; page-break-inside: avoid;">
      
      @php
        $portraitPhotos = [];
        $landscapePhotos = [];
        
        foreach($photos as $photo) {
          // Try public_path first (for symlinked storage)
          $imagePath = public_path('storage/' . $photo->image_path);
          
          // If not found, try storage_path (for temp files before symlink)
          if (!file_exists($imagePath)) {
            $imagePath = storage_path('app/public/' . $photo->image_path);
          }
          
          if (file_exists($imagePath)) {
            $imageSize = @getimagesize($imagePath);
            if ($imageSize) {
              $width = $imageSize[0];
              $height = $imageSize[1];
              
              if ($height > $width) {
                $portraitPhotos[] = $photo;
              } else {
                $landscapePhotos[] = $photo;
              }
            } else {
              // If getimagesize fails, default to landscape
              $landscapePhotos[] = $photo;
            }
          } else {
            // File not found, default to landscape
            $landscapePhotos[] = $photo;
          }
        }
      @endphp
      
      {{-- Portrait Photos (bigger size - 4 per row) --}}
      @if(count($portraitPhotos) > 0)
        <div class="portrait-photos" style="margin-bottom: 15px;">
          <div class="photo-grid" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px;">
            @foreach($portraitPhotos as $photo)
              <div class="photo-item" style="width: 2in; height: 2.67in; border: 1px solid #000; overflow: hidden; position: relative; z-index: 10;">
                <img src="{{ asset('storage/' . $photo->image_path) }}" 
                     alt="Portrait Photo" 
                     style="width: 100%; height: 100%; object-fit: cover;">
              </div>
            @endforeach
          </div>
        </div>
      @endif
      
      {{-- Landscape Photos (bigger size - 4 per row) --}}
      @if(count($landscapePhotos) > 0)
        <div class="landscape-photos">
          <div class="photo-grid" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px;">
            @foreach($landscapePhotos as $photo)
              <div class="photo-item" style="width: 2.4in; height: 1.6in; border: 1px solid #000; overflow: hidden; position: relative; z-index: 10;">
                <img src="{{ asset('storage/' . $photo->image_path) }}" 
                     alt="Landscape Photo" 
                     style="width: 100%; height: 100%; object-fit: cover;">
              </div>
            @endforeach
          </div>
        </div>
      @endif
    </div>
  @endif

  <div class="no-print btn-footer">
    <button type="button" onclick="goBackToCondoForm()" class="btn-lg">Cancel</button>
    <button type="button" onclick="downloadCondoExcel()" class="btn-lg">Download Excel</button>
    <button type="button" onclick="downloadCondoPdf()" class="btn-lg">Download PDF</button>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/exceljs@4.4.0/dist/exceljs.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
function goBackToCondoForm() {
    @php
        $condoId = request()->input('condo_id') ?? (session()->get('condo_preview_form_data')['condo_id'] ?? null);
    @endphp
    
    @if($condoId)
        // Editing existing condo
        window.location.href = '{{ route("condo.edit", ":id") }}'.replace(':id', '{{ $condoId }}') + '?restore=1';
    @else
        // Creating new condo
        window.location.href = '{{ route("condo.create") }}?restore=1';
    @endif
}

(function(){
  function bahtText(n){
    n = (typeof n === 'number') ? n : parseFloat(String(n).replace(/,/g,''));
    if (isNaN(n)) return '';
    const nums=['ศูนย์','หนึ่ง','สอง','สาม','สี่','ห้า','หก','เจ็ด','แปด','เก้า'];
    function readInt(x){
      x=Math.floor(x); if(x===0) return '';
      const s=String(x), len=s.length; let out='';
      if(len>7){ const left=s.slice(0,len-6), right=s.slice(-6);
        return readInt(parseInt(left,10))+'ล้าน'+readInt(parseInt(right,10)); }
      for(let i=0;i<len;i++){
        const d=s.charCodeAt(i)-48, pos=len-i-1; if(!d) continue;
        if(pos===1){ if(d===1){out+='สิบ';continue;} if(d===2){out+='ยี่สิบ';continue;} out+=nums[d]+'สิบ'; continue; }
        if(pos===0){ out+=(d===1 && len>1)?'เอ็ด':nums[d]; }
        else{ out+=nums[d]+['','สิบ','ร้อย','พัน','หมื่น','แสน','ล้าน'][pos]; }
      }
      return out;
    }
    const baht=Math.floor(Math.abs(n));
    let satang=Math.round((Math.abs(n)-baht)*100);
    let text=(n<0?'ลบ':'')+(baht===0?'ศูนย์':readInt(baht))+'บาท';
    if(satang===0){ text+='ถ้วน'; }
    else{
      const ten=Math.floor(satang/10), one=satang%10;
      if(ten===1) text+='สิบ';
      else if(ten===2) text+='ยี่สิบ';
      else if(ten>2) text+=nums[ten]+'สิบ';
      if(one>0) text+=(one===1 && ten>0)?'เอ็ด':nums[one];
      text+='สตางค์';
    }
    return text;
  }

  const totalsEl=document.getElementById('totals');
  const grand=totalsEl ? parseFloat(totalsEl.getAttribute('data-grand')) : null;
  const tgt=document.getElementById('bahtText');
  if(!isNaN(grand) && tgt) tgt.textContent=bahtText(grand);

  function setTotalsWidth(){
    if(!totalsEl) return;
    const w = totalsEl.getBoundingClientRect().width;
    document.documentElement.style.setProperty('--totals-w', w + 'px');
  }
  setTotalsWidth();
  window.addEventListener('load', setTotalsWidth);
  window.addEventListener('resize', setTotalsWidth);
})();

async function toBase64(url) {
  const res = await fetch(url);
  const blob = await res.blob();
  return new Promise((resolve, reject) => {
    const r = new FileReader();
    r.onloadend = () => resolve(r.result.split(',')[1]);
    r.onerror = reject;
    r.readAsDataURL(blob);
  });
}
  const _num = (x) => Number(String(x ?? 0).replace(/,/g,'')) || 0;

async function downloadCondoExcel() {
  // Get localized labels from server
  const t = @json($translations ?? []);
  const storageBaseUrl = @json(asset('storage'));
  const photos = @json(collect($photos ?? [])->pluck('image_path')->values()->all());
  
  Swal.fire({
    icon: 'success',
    title: t.download || 'Download',
    text: t.excel_success || 'Excel downloaded successfully.',
    timer: 2000,
    showConfirmButton: false
  })
  function bahtTextLocal(n){
    n = (typeof n === 'number') ? n : parseFloat(String(n).replace(/,/g,''));
    if (isNaN(n)) return '';
    const nums=['ศูนย์','หนึ่ง','สอง','สาม','สี่','ห้า','หก','เจ็ด','แปด','เก้า'];
    function readInt(x){
      x=Math.floor(x); if(x===0) return '';
      const s=String(x), len=s.length; let out='';
      if(len>7){ const l=s.slice(0,len-6), r=s.slice(-6);
        return readInt(parseInt(l,10))+'ล้าน'+readInt(parseInt(r,10)); }
      for(let i=0;i<len;i++){
        const d=s.charCodeAt(i)-48, p=len-i-1; if(!d) continue;
        if(p===1){ if(d===1){out+='สิบ';continue;} if(d===2){out+='ยี่สิบ';continue;} out+=nums[d]+'สิบ'; continue; }
        if(p===0){ out+=(d===1 && len>1)?'เอ็ด':nums[d]; }
        else{ out+=nums[d]+['','สิบ','ร้อย','พัน','หมื่น','แสน','ล้าน'][p]; }
      }
      return out;
    }
    const b=Math.floor(Math.abs(n));
    let s=Math.round((Math.abs(n)-b)*100);
    let t=(n<0?'ลบ':'')+(b===0?'ศูนย์':readInt(b))+'บาท';
    if(s===0){ t+='ถ้วน'; }
    else{
      const ten=Math.floor(s/10), one=s%10;
      if(ten===1) t+='สิบ';
      else if(ten===2) t+='ยี่สิบ';
      else if(ten>2) t+=nums[ten]+'สิบ';
      if(one>0) t+=(one===1 && ten>0)?'เอ็ด':nums[one];
      t+='สตางค์';
    }
    return t;
  }

  const rows = @json($rows ?? []);
  const meta = {
    customer: @json($customer_name ?? ''),
    address:  @json($address ?? ''),
    job:      @json($job_name ?? ''),
    qno:      @json($quotation_number ?? ''),
    qdate:    @json(!empty($quotation_date) ? \Carbon\Carbon::parse($quotation_date)->format('d/m/Y') : ''),
    pterm:    @json($payment_term ?? ''),
    credit:   @json($credits ?? ''),
    total:    Number(String(@json($total ?? 0)).replace(/,/g,'')) || 0,
    vat:      Number(String(@json($vat ?? 0)).replace(/,/g,'')) || 0,
    grand:    Number(String(@json($grand ?? 0)).replace(/,/g,'')) || 0,
  };
  const toNum = v => Number(String(v ?? 0).replace(/,/g,'')) || 0;

  function styleRange (ws, r1,c1,r2,c2, style={}) {
    for (let r=r1; r<=r2; r++) for (let c=c1; c<=c2; c++) {
      const cell = ws.getCell(r,c);
      if (style.alignment) cell.alignment = { ...(cell.alignment||{}), ...style.alignment };
      if (style.font)      cell.font      = { ...(cell.font||{}),      ...style.font };
      if (style.fill)      cell.fill      = style.fill;
      if (style.numFmt)    cell.numFmt    = style.numFmt;
      if (style.border)    cell.border    = { ...(cell.border||{}),    ...style.border };
    }
  }
  function setRowBorder(ws, r, left, right, opts={style:'thin'}) {
    for (let c=left; c<=right; c++) {
      const cell = ws.getCell(r,c);
      cell.border = { ...(cell.border||{}), top:opts, bottom:opts, left:opts, right:opts };
    }
  }
  const ANG16  = { name:'Angsana New', size:16 };
  const ANG18  = { name:'Angsana New', size:18 };
  const ANG22B = { name:'Angsana New', size:22, bold:true };

  const wb = new ExcelJS.Workbook();
  const ws = wb.addWorksheet(t.quotation || 'Quotation', {
    views: [{ state:'normal', showGridLines:false }],
    properties: { defaultRowHeight: 22 }
  });


  ws.columns = [
    {key:'A', width:6}, {key:'B', width:20}, {key:'C', width:18},
    {key:'D', width:18}, {key:'E', width:18}, {key:'F', width:18},
    {key:'G', width:12}, {key:'H', width:10}, {key:'I', width:14},
    {key:'J', width:14}, {key:'K', width:14}, {key:'L', width:16}, {key:'M', width:6},
  ];
  const M = (a1,a2) => ws.mergeCells(`${a1}:${a2}`);

  ws.getCell('B2').value = t.company_address || 'Address of PITA BUILD Company Limited (Head Office)';
  ws.getCell('B3').value = '32/20 Village No. 6, Bang Talat Subdistrict, Pak Kret District, Nonthaburi Province 11120';
  ws.getCell('B4').value = (t.phone || 'Phone') + ' : 062-604-2054 , 086-901-2500                ' + (t.email || 'Email') + ' : k.tanapon191@gmail.com';
  ws.getCell('B5').value = (t.taxpayer_number || 'Taxpayer Identification Number') + ' 0125565022982';
  styleRange(ws,2,2,5,2,{font:ANG22B, alignment:{ vertical:'middle' }});
  ws.getRow(2).height = 26;

  M('D7','H7');
  ws.getCell('D7').value = 'ใบเสนอราคา / ' + (t.quotation || 'Quotation');
  styleRange(ws,7,4,7,8,{font:ANG22B, alignment:{horizontal:'center', vertical:'middle'},
                         border:{top:{style:'thin'},bottom:{style:'thin'},left:{style:'thin'},right:{style:'thin'}}});

  try{
    const res = await fetch(`{{ asset('assets/images/PITA_logo.jpg') }}`);
    const blob = await res.blob();
    const base64 = await new Promise((resolve,reject)=>{
      const fr=new FileReader(); fr.onloadend=()=>resolve(String(fr.result).split(',')[1]); fr.onerror=reject; fr.readAsDataURL(blob);
    });
    const imgId = wb.addImage({ base64:'data:image/jpeg;base64,'+base64, extension:'jpeg' });
    ws.addImage(imgId, { tl:{col:10,row:1}, ext:{width:197,height:196}, editAs:'oneCell' }); // K2
  }catch(_){}

  ws.getRow(8).height = 8;
  for (let c=1; c<=12; c++) {
    ws.getCell(8,c).border = { ...(ws.getCell(8,c).border||{}), bottom:{style:'medium'} };
  }

  const metaRows = [
    [t.customer_name || 'Customer Name', meta.customer],
    [t.address || 'Address',       meta.address],
    [t.job_name || 'Job name',      meta.job],
    ['เลขประจำตัวผู้เสียภาษีอากร', ''],
  ];
  for (let i=0; i<metaRows.length; i++){
    const r = 9+i;
    M(`A${r}`,`B${r}`);  M(`C${r}`,`G${r}`);
    ws.getCell(`A${r}`).value = metaRows[i][0];
    ws.getCell(`C${r}`).value = metaRows[i][1];
  }
  const metaRight = [
    [t.quotation_number || 'Quotation number', meta.qno],
    [t.quotation_date || 'Date (D/M/Y)',     meta.qdate],
    [t.payment_term || 'Payment terms',    meta.pterm],
    [t.credits || 'credit',           meta.credit],
  ];
  for (let i=0; i<metaRight.length; i++){
    const r = 9+i;
    M(`H${r}`,`J${r}`);  M(`K${r}`,`L${r}`);
    ws.getCell(`H${r}`).value = metaRight[i][0];
    ws.getCell(`K${r}`).value = metaRight[i][1];
  }
  styleRange(ws,9,1,12,12,{font:ANG18, alignment:{vertical:'middle'}});

  for (let c=1;c<=12;c++){ 
  ws.getCell(9,c).border  = { ...(ws.getCell(9,c).border||{}),  top:{style:'thin'} };
  }
  for (let r=9;r<=12;r++){
    // outer outline
    ws.getCell(r,1).border  = { ...(ws.getCell(r,1).border||{}),  left:{style:'thin'}  };
    ws.getCell(r,12).border = { ...(ws.getCell(r,12).border||{}), right:{style:'thin'} };

    // keep the B|C divider
    ws.getCell(r,2).border  = { ...(ws.getCell(r,2).border||{}),  right:{style:'thin'} };

    // H/I/J: remove right borders, add LEFT borders instead
    // H (col 8)
    ws.getCell(r,8).border  = { ...(ws.getCell(r,8).border||{}),  left:{style:'thin'}, right:undefined };
    // I (col 9)
    ws.getCell(r,9).border  = { ...(ws.getCell(r,9).border||{}),  left:{style:'thin'}, right:undefined };
    // J (col 10)
    ws.getCell(r,10).border = { ...(ws.getCell(r,10).border||{}), left:{style:'thin'}, right:undefined };
  }

  // ---- table headers 14–15 --------------------------------------------------
  ws.getRow(13).height = 6;
  // add the missing top border over the spacer row 13
  for (let c=1;c<=12;c++) {
    ws.getCell(14,c).border = { ...(ws.getCell(13,c).border||{}), top:{style:'thin'} };
  }
  ws.getCell(13, 2).border = { ...(ws.getCell(13,2).border||{}), right:{style:'thin'} }; 
  ws.getCell(13, 7).border = { ...(ws.getCell(13,7).border||{}), right:{style:'thin'} };

  M('A14','A15'); ws.getCell('A14').value = t.no || 'No';
  M('B14','F15'); ws.getCell('B14').value = t.details || 'Details';
  M('G14','G15'); ws.getCell('G14').value = t.amount || 'Amount';
  M('H14','H15'); ws.getCell('H14').value = t.units || 'Units';
  M('I14','K14'); ws.getCell('I14').value = t.price_amount || 'Price per unit';
  M('L14','L15'); ws.getCell('L14').value = t.total_price || 'Total price';
  ws.getCell('I15').value = t.material_cost || 'Material cost';
  ws.getCell('J15').value = t.labor_cost || 'Labor price';
  ws.getCell('K15').value = t.total || 'Total';
  styleRange(ws,14,1,15,12,{font:{...ANG16, bold:true}, alignment:{horizontal:'center', vertical:'middle'}});
  setRowBorder(ws,14,1,12,{style:'thin'});
  setRowBorder(ws,15,1,12,{style:'thin'});

  // ---- data rows (start 16) -------------------------------------------------
  let r = 16, idx = 1;
  for (const it of rows) {
    ws.getCell(`A${r}`).value = it.no ?? idx;
    M(`B${r}`,`F${r}`); ws.getCell(`B${r}`).value = it.details ?? '';
    ws.getCell(`G${r}`).value = toNum(it.amount);
    ws.getCell(`H${r}`).value = it.unit ?? '';

    const mc = toNum(it.mc_price);
    const lc = toNum(it.lc_price);

    if (mc === 0 && lc === 0) {
      // merge I + J and show "เหมา"
      M(`I${r}`, `J${r}`);
      ws.getCell(`I${r}`).value = 'เหมา';
      ws.getCell(`I${r}`).alignment = { horizontal: 'center', vertical: 'middle' };
    } else {
      // normal numeric cells
      ws.getCell(`I${r}`).value = mc;
      ws.getCell(`J${r}`).value = lc;
      ['I','J'].forEach(col => {
        const cell = ws.getCell(`${col}${r}`);
        cell.alignment = { horizontal:'center', vertical:'middle' };
        cell.numFmt = '#,##0.00';
      });
    }

    ws.getCell(`K${r}`).value = toNum(it.ppu ?? (toNum(it.mc_price)+toNum(it.lc_price)));
    ws.getCell(`L${r}`).value = toNum(it.subtotal);

    styleRange(ws, r, 1, r, 12, { font: ANG16 });
    ws.getCell(`G${r}`).alignment = { horizontal:'center', vertical:'middle' };
    ws.getCell(`A${r}`).alignment = { horizontal:'center', vertical:'middle' };
    ws.getCell(`H${r}`).alignment = { horizontal:'center', vertical:'middle' };
    ws.getCell(`K${r}`).alignment = { horizontal:'center', vertical:'middle' };
    ws.getCell(`L${r}`).alignment = { horizontal:'right',  vertical:'middle' };

    ['G','K','L'].forEach(col => {
      ws.getCell(`${col}${r}`).numFmt = '#,##0.00';
    });

    setRowBorder(ws, r, 1, 12, {style:'thin'});
    ws.getRow(r).height = 22;
    r++; idx++;
  }
  const lastDataRow = r - 1;

  // ---- totals ---------------------------------------------------------------
  const totalRow = lastDataRow + 1;
  const vatRow   = lastDataRow + 2;
  const grandRow = lastDataRow + 3;

  // merge left side A..I for Total & VAT
  M(`A${totalRow}`,`I${totalRow}`);
  M(`A${vatRow}`,  `I${vatRow}`);
  // labels and right block
  M(`J${totalRow}`,`K${totalRow}`); ws.getCell(`J${totalRow}`).value = t.total || 'Total';
  M(`J${vatRow}`,`K${vatRow}`);     ws.getCell(`J${vatRow}`).value   = t.tax || 'Tax 7%';
  M(`J${grandRow}`,`K${grandRow}`); ws.getCell(`J${grandRow}`).value = t.total_price || 'Total price';

  ws.getCell(`L${totalRow}`).value = meta.total;
  ws.getCell(`L${vatRow}`).value   = meta.vat;
  ws.getCell(`L${grandRow}`).value = meta.grand;
  ['L'+totalRow,'L'+vatRow,'L'+grandRow].forEach(a=>ws.getCell(a).numFmt='#,##0.00');

  styleRange(ws,totalRow,10,grandRow,12,{font:ANG16, alignment:{horizontal:'right', vertical:'middle'}});
  // right block borders
  setRowBorder(ws,totalRow,10,12,{style:'thin'});
  setRowBorder(ws,vatRow,  10,12,{style:'thin'});
  setRowBorder(ws,grandRow,1,12,{style:'thin'});
  ws.getCell(`L${grandRow}`).font = {...ANG16, bold:true};

  for (let c = 1; c <= 9; c++) {
    const b = ws.getCell(grandRow, c).border || {};
    b.top = undefined;           
    ws.getCell(grandRow, c).border = b;
  }

  // clear borders on merged A..I for Total & VAT (no lines there)
  for (const rr of [totalRow, vatRow]) {
    for (let c=1; c<=9; c++) ws.getCell(rr, c).border = {};
  }

  // Baht text on same row as grand total (A..I)
  M(`A${grandRow}`,`I${grandRow}`);
  ws.getCell(`A${grandRow}`).value = `ตัวอักษร ( ${bahtTextLocal(meta.grand)} )`;
  styleRange(ws,grandRow,1,grandRow,9,{font:ANG16, alignment:{vertical:'middle'}});

  const firstTableRow = 14;
  for (let rr = firstTableRow; rr <= grandRow; rr++) {
    ws.getCell(rr, 9).border  = { ...(ws.getCell(rr,9).border||{}),  left:{style:'thin'} };   
    ws.getCell(rr,11).border  = { ...(ws.getCell(rr,11).border||{}), right:{style:'thin'} };  
  }

  for (let c=1;c<=12;c++){
    ws.getCell(9,c).border         = { ...(ws.getCell(9,c).border||{}),         top:{style:'medium'} };
    ws.getCell(grandRow,c).border  = { ...(ws.getCell(grandRow,c).border||{}),  bottom:{style:'medium'} };
  }
  for (let rr=9; rr<=grandRow; rr++){
    ws.getCell(rr,1).border  = { ...(ws.getCell(rr,1).border||{}),  left:{style:'medium'} };
    ws.getCell(rr,12).border = { ...(ws.getCell(rr,12).border||{}), right:{style:'medium'} };
  }

  const discRow = grandRow + 2;
  M(`A${discRow}`,`L${discRow}`);
  ws.getCell(`A${discRow}`).value =
    'The above price does not include various expenses related to the condominium juristic person.';
  styleRange(ws,discRow,1,discRow,12,{font:ANG16, alignment:{horizontal:'center'}});

  const sigHead = discRow + 2;
  M(`B${sigHead}`,`D${sigHead}`); ws.getCell(`B${sigHead}`).value = 'จัดทำโดย / Prepared By';
  M(`F${sigHead}`,`H${sigHead}`); ws.getCell(`F${sigHead}`).value = 'สั่งซื้อโดย / Order By';
  M(`J${sigHead}`,`L${sigHead}`); ws.getCell(`J${sigHead}`).value = 'ผู้มีอำนาจผูกพันบริษัท / Authorized Signature';
  styleRange(ws,sigHead,2,sigHead,12,{font:{...ANG16, bold:true}, alignment:{horizontal:'center'}});

  const sigLine = sigHead + 1;
  const dateLine= sigHead + 2;
  let printEndRow = dateLine + 1;
  function makeSig(from,to){
    M(`${from}${sigLine}`,`${to}${sigLine}`);
    ws.getCell(`${from}${sigLine}`).value = '( ........................................ )';
    M(`${from}${dateLine}`,`${to}${dateLine}`);
    ws.getCell(`${from}${dateLine}`).value = 'วันที่ ___ / ___ / ______';
  }
  makeSig('B','D'); makeSig('F','H'); makeSig('J','L');
  styleRange(ws,sigLine,2,dateLine,12,{font:ANG16, alignment:{horizontal:'center'}});

  const getImageMeta = (imagePath) => {
    const lower = String(imagePath || '').toLowerCase();
    if (lower.endsWith('.png')) return { ext: 'png', mime: 'image/png' };
    if (lower.endsWith('.gif')) return { ext: 'gif', mime: 'image/gif' };
    return { ext: 'jpeg', mime: 'image/jpeg' };
  };

  if (photos.length > 0) {
    let photoRow = dateLine + 4;
    M(`A${photoRow}`, `L${photoRow}`);
    ws.getCell(`A${photoRow}`).value = t.photos || 'Project Photos';
    styleRange(ws, photoRow, 1, photoRow, 12, { font: { ...ANG18, bold: true }, alignment: { horizontal: 'left' } });
    photoRow += 1;

    const colPositions = [1, 3.8, 6.6, 9.4];
    const portrait = [];
    const landscape = [];

    for (const imagePath of photos) {
      try {
        const img = new Image();
        img.src = `${storageBaseUrl}/${imagePath}`;
        await new Promise((resolve) => {
          img.onload = resolve;
          img.onerror = resolve;
          setTimeout(resolve, 5000);
        });

        if (img.naturalHeight > img.naturalWidth) portrait.push(imagePath);
        else landscape.push(imagePath);
      } catch (_) {
        landscape.push(imagePath);
      }
    }

    const renderGroup = async (list, width, height, rowStep) => {
      for (let i = 0; i < list.length; i += 4) {
        const chunk = list.slice(i, i + 4);
        for (let j = 0; j < chunk.length; j++) {
          const imagePath = chunk[j];
          try {
            const base64 = await toBase64(`${storageBaseUrl}/${imagePath}`);
            if (!base64) continue;

            const metaImg = getImageMeta(imagePath);
            const imgId = wb.addImage({
              base64: `data:${metaImg.mime};base64,${base64}`,
              extension: metaImg.ext,
            });

            ws.addImage(imgId, {
              tl: { col: colPositions[j], row: photoRow },
              ext: { width, height },
              editAs: 'absolute',
            });
          } catch (_) {
            // Skip image conversion failures
          }
        }
        photoRow += rowStep;
      }
    };

    await renderGroup(portrait, 170, 227, 12);
    if (portrait.length > 0 && landscape.length > 0) photoRow += 1;
    await renderGroup(landscape, 200, 133, 8.5);
    printEndRow = Math.max(printEndRow, Math.ceil(photoRow + 1));
  }

  ws.pageSetup = {
    paperSize: 9, orientation:'portrait',
    fitToPage:true, fitToWidth:1, fitToHeight:0,
    margins:{left:0.3,right:0.3,top:0.5,bottom:0.5,header:0.3,footer:0.3}
  };
  ws.pageSetup.printArea = `A1:M${printEndRow}`;

  // ---- save -----------------------------------------------------------------
  const buf = await wb.xlsx.writeBuffer();
  const blob = new Blob([buf], {type:'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'});
  const a = document.createElement('a');
  a.href = URL.createObjectURL(blob);
  const safe = s => String(s||'').replace(/[\\/:*?"<>|]/g, '').replace(/\s+/g, ' ').trim();
  const job  = safe(meta.job)    || 'Condo';
  const addr = safe(meta.address);
  a.download = addr ? `${job} (${addr}).xlsx` : `${job}.xlsx`;
  document.body.appendChild(a); a.click(); a.remove();
}

async function downloadCondoPdf() {
  Swal.fire({
    icon: 'success',
    title: 'Download',
    text: 'PDF downloaded successfully.',
    timer: 2000,
    showConfirmButton: false
  });
  const { jsPDF } = window.jspdf;

  const pageEl = document.querySelector('.page');
  if (!pageEl) return;
  const tempWrapper = document.createElement('div');
  tempWrapper.style.cssText = 'background:#fff;padding:20px;font-family:Times New Roman, serif;';

  const pageClone = pageEl.cloneNode(true);
  tempWrapper.appendChild(pageClone);

  const photoGallery = document.querySelector('.photo-gallery-wrap');
  if (photoGallery) {
    const photoClone = photoGallery.cloneNode(true);
    photoClone.style.marginTop = '30px';
    
    // Ensure consistent photo sizing in PDF (match preview display)
    photoClone.querySelectorAll('.portrait-photos .photo-item').forEach((item) => {
      item.style.setProperty('width', '2in', 'important');
      item.style.setProperty('height', '2.67in', 'important');
    });
    photoClone.querySelectorAll('.landscape-photos .photo-item').forEach((item) => {
      item.style.setProperty('width', '2.4in', 'important');
      item.style.setProperty('height', '1.6in', 'important');
    });
    photoClone.querySelectorAll('.photo-grid').forEach((grid) => {
      grid.style.setProperty('grid-template-columns', 'repeat(4, minmax(0, 1fr))', 'important');
      grid.style.setProperty('gap', '10px', 'important');
    });
    
    tempWrapper.appendChild(photoClone);
  }

  tempWrapper.style.position = 'absolute';
  tempWrapper.style.left = '-9999px';
  tempWrapper.style.top = '0';
  document.body.appendChild(tempWrapper);

  let pdf = null;
  try {
    const images = tempWrapper.querySelectorAll('img');
    const waits = Array.from(images).map((img) => new Promise((resolve) => {
      if (img.complete) return resolve();
      img.onload = resolve;
      img.onerror = resolve;
      setTimeout(resolve, 10000);
    }));
    await Promise.all(waits);

    const canvas = await html2canvas(tempWrapper, {
      scale: 2,
      useCORS: true,
      allowTaint: true,
      backgroundColor: '#ffffff',
      scrollX: 0,
      scrollY: 0,
      logging: false,
      imageTimeout: 15000
    });

    const imgData = canvas.toDataURL('image/jpeg', 0.8);
    pdf = new jsPDF('portrait', 'mm', 'a4');

    const pageWidth = pdf.internal.pageSize.getWidth();
    const pageHeight = pdf.internal.pageSize.getHeight();
    const renderWidth = pageWidth * 0.95;
    const renderHeight = (canvas.height * renderWidth) / canvas.width;

    let heightLeft = renderHeight;
    let position = 10;

    pdf.addImage(imgData, 'JPEG', (pageWidth - renderWidth) / 2, position, renderWidth, renderHeight, undefined, 'FAST');
    heightLeft -= (pageHeight - 20);

    while (heightLeft > 0) {
      pdf.addPage();
      position = heightLeft - renderHeight + 10;
      pdf.addImage(imgData, 'JPEG', (pageWidth - renderWidth) / 2, position, renderWidth, renderHeight, undefined, 'FAST');
      heightLeft -= (pageHeight - 20);
    }
  } finally {
    document.body.removeChild(tempWrapper);
  }

  const safe = s => String(s || '').replace(/[\\/:*?"<>|]/g, '').replace(/\s+/g, ' ').trim();
  const job  = safe(@json($job_name ?? 'Condo'));
  const addr = safe(@json($address ?? ''));
  const filename = addr ? `${job} (${addr}).pdf` : `${job}.pdf`;

  if (pdf) {
    pdf.save(filename);
  }
}

</script>
@if (!empty($autoDownload))
<script>
  window.addEventListener('load', function () {
      setTimeout(function () {
          @if ($autoDownload === 'pdf')
              downloadCondoPdf();
          @elseif ($autoDownload === 'excel')
              downloadCondoExcel();
          @endif
      }, 300);
  });
</script>
@endif
</body>
</html>
