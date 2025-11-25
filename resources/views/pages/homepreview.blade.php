<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>BOQ Preview</title>

    <style>
    @font-face {
        font-family: 'AngsanaPDF';
        src: url('{{ asset("assets/fonts/angsana-new.ttf") }}') format("truetype");
    }

    html, body {
    margin: 0;
    background: #fff;
    font-family: 'Times New Roman', Times, serif;
    color: #000;
    }

    /* Make sure table also uses it */
    table,
    table td,
    table th,
    .boq-wrap,
    .boq-wrap * {
        font-family: 'Times New Roman', Times, serif;
    }

    .table th {
        white-space: normal;
        word-break: break-word;
        line-height: 1.15;
    }

    .col-no { width: 120px }
    .col-list { width: auto }
    .col-amt { width: 90px }
    .col-unit { width: 70px }
    .col-mc { width: 90px }
    .col-mt { width: 110px }
    .col-lc { width: 90px }
    .col-lt { width: 110px }
    .col-total { width: 120px }

    .boq-wrap {
        max-width: 1200px;
        margin: 20px auto;
        background: #fff;
        border: 2px solid #000;
        padding: 0;
    }

    .dyn { color: red !important; }

    .boq-meta {
        width: 100%;
        border-collapse: collapse;
        border: none;
    }

    .boq-meta td {
        border: none !important;
        padding: 6px;
        font-size: 14px;
    }

    .thead th {
        border: 1px solid #000;
        text-align: center;
        padding: 6px;
        font-size: 14px;
    }

    .table {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
    }

    .table th,
    .table td {
        border: 1px solid #000;
        padding: 6px;
        font-size: 13px;
    }

    .cat { background: #fff78a; font-weight: 700; }
    .right { text-align: right; }
    .center { text-align: center; }

    .badge-box {
        display: inline-block;
        padding: 2px 8px;
        border-radius: 4px;
        background: #e8f5e9;
        border: 1px solid #81c784;
    }

    .boq-head {
        width: 100%;
        text-align: center;
        font-weight: 700;
        font-size: 16px;
        padding: 6px 0;
        border-bottom: 1px solid #000;
    }

    .btn-footer {
        max-width: 1200px;
        margin: 20px auto;
        display: flex;
        justify-content: center;
        gap: 20px;
    }

    .btn-lg {
        padding: 10px 26px;
        font-size: 16px;
        font-weight: 600;
        border: 1px solid #000;
        background: #eee;
        cursor: pointer;
    }

    .btn-lg:hover {
        background: #ddd;
    }

    @media print {
        .no-print {
            display: none !important;
        }
    }
</style>
</head>

<body>

    <div class="boq-wrap">
        <div class="boq-head">Bill of Quantities</div>

        <table class="boq-meta">
            <colgroup>
                <col style="width:120px;">
                <col>
                <col style="width:220px;">
            </colgroup>
            <tr>
                <td class="meta-label">Project Name :</td>
                <td class="dyn">{{ $project_name }}</td>
                <td class="right">Date: <span class="dyn">{{ $date }}</span></td>
            </tr>
            <tr>
                <td class="meta-label">Dear :</td>
                <td class="dyn" colspan="2">{{ $dear }}</td>
            </tr>
            <tr>
                <td class="meta-label">Trooper :</td>
                <td class="dyn">{{ $trooper }}</td>
                <td class="right">
                    House No. <span class="dyn">{{ $house_no }}</span>
                </td>
            </tr>
        </table>

        <table class="table">
            <colgroup>
                <col class="col-no">
                <col class="col-list">
                <col class="col-amt">
                <col class="col-unit">
                <col class="col-mc">
                <col class="col-mt">
                <col class="col-lc">
                <col class="col-lt">
                <col class="col-total">
            </colgroup>
            <thead>
                <tr class="yellow">
                    <th rowspan="2" style="border:1px solid #000;">No.</th>
                    <th style="border:1px solid #000; text-align:center;">List</th>
                    <th rowspan="2" style="border:1px solid #000;">Amount</th>
                    <th rowspan="2" style="border:1px solid #000;">unit</th>
                    <th colspan="2" style="border:1px solid #000;">Material Cost</th>
                    <th colspan="2" style="border:1px solid #000;">Labor Cost</th>
                    <th rowspan="2" style="border:1px solid #000;">Total Amount</th>
                </tr>

                <tr>
                    <th class="green dyn" style="border:1px solid #000; text-align:center; font-weight:700;">
                        {{ $list_name }}
                    </th>
                    <th class="yellow" style="border:1px solid #000;">Price/Unit</th>
                    <th class="yellow" style="border:1px solid #000;">Total Price</th>
                    <th class="yellow" style="border:1px solid #000;">Price/Unit</th>
                    <th class="yellow" style="border:1px solid #000;">Total Price</th>
                </tr>
            </thead>
            <tbody>

                <tr>
                    <td class="center yellow"></td>
                    <td class="yellow" style="font-weight:700; text-align:center;">
                        Miscellaneous work category
                    </td>
                    <td class="yellow"></td>
                    <td class="yellow"></td>
                    <td class="yellow"></td>
                    <td class="yellow"></td>
                    <td class="yellow"></td>
                    <td class="yellow"></td>
                    <td class="yellow"></td>
                </tr>

                @php $n = 1; @endphp
                @foreach($rows as $r)
                    @if(!empty($r['category_name']))
                        <tr>
                            <td class="center">{{ $n }}</td>
                            <td class="dyn">{{ $r['category_name'] }}</td>
                            <!-- Amount CENTER to match Excel -->
                            <td class="center dyn">{{ number_format((float) $r['amount'], 2) }}</td>
                            <td class="center dyn">{{ $r['unit'] }}</td>
                            <td class="right dyn">{{ number_format((float) $r['mc_price'], 2) }}</td>
                            <td class="right pink dyn">{{ number_format((float) $r['mat_total'], 2) }}</td>
                            <td class="right dyn">{{ number_format((float) $r['lc_price'], 2) }}</td>
                            <td class="right pink dyn">{{ number_format((float) $r['lab_total'], 2) }}</td>
                            <td class="right yellow dyn">{{ number_format((float) $r['grand_total'], 2) }}</td>
                        </tr>
                        @php $n++; @endphp
                    @endif
                @endforeach

                {{-- === Totals + Seal block === --}}
                <tr>
                    <td class="center">&nbsp;</td>

                    <td class="yellow" colspan="2" rowspan="5" style="text-align:center; vertical-align:middle;">
                        <img src="{{ asset('assets/images/168Home.png') }}" alt="Seal" style="width:240px;height:auto;">
                    </td>

                    <td class="right sum-row" colspan="5">Total price for miscellaneous work category</td>
                    <td class="right pink dyn"><strong>{{ number_format($miscTotal, 2) }}</strong></td>
                </tr>

                <tr>
                    <td class="center">&nbsp;</td>
                    <td class="right sum-row" colspan="5">Operating expenses and profit 15%</td>
                    <td class="right pink dyn"><strong>{{ number_format($operating, 2) }}</strong></td>
                </tr>

                <tr>
                    <td class="center">&nbsp;</td>
                    <td class="right sum-row" colspan="5">Total price of work category A,B</td>
                    <td class="right dyn"><strong>{{ number_format($abTotal, 2) }}</strong></td>
                </tr>

                <tr>
                    <td class="center">&nbsp;</td>
                    <td class="right sum-row" colspan="5">Value Added Tax 7%</td>
                    <td class="right pink dyn"><strong>{{ number_format($vat, 2) }}</strong></td>
                </tr>

                <tr>
                    <td class="center">&nbsp;</td>
                    <td class="right sum-row" colspan="5">Total price</td>
                    <td class="right yellow dyn"><strong>{{ number_format($finalTotal, 2) }}</strong></td>
                </tr>

            </tbody>
        </table>

    </div>

    <div class="no-print btn-footer">
        <button type="button" onclick="window.history.back()" class="btn-lg">Cancel</button>
        <button type="button" onclick="downloadBoqExcel()" class="btn-lg">Download Excel</button>
        <button type="button" onclick="downloadBoqPdf()" class="btn-lg">Download PDF</button>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/exceljs/dist/exceljs.min.js"></script>
    <script>
        async function toBase64(url) {
            const res = await fetch(url);
            const blob = await res.blob();
            return new Promise((resolve, reject) => {
                const reader = new FileReader();
                reader.onloadend = () => resolve(reader.result.split(',')[1]);
                reader.onerror = reject;
                reader.readAsDataURL(blob);
            });
        }

        async function downloadBoqExcel() {
            const wb = new ExcelJS.Workbook();
            const ws = wb.addWorksheet('BOQ');

            ws.columns = [
                { header: 'No.', width: 14 },
                { header: 'List', width: 42 },
                { header: 'Amount', width: 12 },
                { header: 'unit', width: 10 },
                { header: 'Price/Unit', width: 14 },
                { header: 'Total Price', width: 16 },
                { header: 'Price/Unit', width: 14 },
                { header: 'Total Price', width: 16 },
                { header: 'Total Amount', width: 16 },
            ];

            const FIXED_HEIGHT = 30;
            let r = 1;

            /* =======================
               TITLE
            ======================= */
            ws.mergeCells(r, 1, r, 9);
            ws.getCell(r, 1).value = "Bill of Quantities";
            ws.getCell(r, 1).alignment = { horizontal: "center", vertical: "middle" };
            ws.getCell(r, 1).font = { bold: true };
            ws.getRow(r).height = FIXED_HEIGHT;
            r += 2;

            /* =======================
               META FIELDS
            ======================= */

            // Row 1: Project Name + Date
            ws.getCell(r, 1).value = "Project Name  :";
            ws.mergeCells(r, 2, r, 6);
            ws.getCell(r, 2).value = @json($project_name);

            ws.mergeCells(r, 7, r, 9);
            ws.getCell(r, 7).value = "Date: " + @json($date);
            ws.getCell(r, 7).alignment = {
                horizontal: "center",
                vertical: "middle"
            };

            ws.getRow(r).height = FIXED_HEIGHT;
            r++;

            // Row 2: Dear
            ws.getCell(r, 1).value = "Dear  :";
            ws.mergeCells(r, 2, r, 6);
            ws.getCell(r, 2).value = @json($dear);

            ws.getRow(r).height = FIXED_HEIGHT;
            r++;

            // Row 3: Trooper + House No.
            ws.getCell(r, 1).value = "Trooper  :";
            ws.mergeCells(r, 2, r, 6);
            ws.getCell(r, 2).value = @json($trooper);

            ws.mergeCells(r, 7, r, 8);
            ws.getCell(r, 7).value = "House No.";
            ws.getCell(r, 7).alignment = {
                horizontal: "center",
                vertical: "middle"
            };

            ws.getCell(r, 9).value = @json($house_no);
            ws.getCell(r, 9).alignment = {
                horizontal: "center",
                vertical: "middle"
            };

            ws.getRow(r).height = FIXED_HEIGHT;
            r += 2;

            /* =======================
               HEADER 2-ROW
            ======================= */
            const hdr1 = r, hdr2 = r + 1;

            ws.mergeCells(hdr1, 1, hdr2, 1);
            ws.getCell(hdr1, 1).value = 'No.';

            ws.getCell(hdr1, 2).value = 'List';

            ws.mergeCells(hdr1, 3, hdr2, 3);
            ws.getCell(hdr1, 3).value = 'Amount';

            ws.mergeCells(hdr1, 4, hdr2, 4);
            ws.getCell(hdr1, 4).value = 'unit';

            ws.mergeCells(hdr1, 5, hdr1, 6);
            ws.getCell(hdr1, 5).value = 'Material Cost';

            ws.mergeCells(hdr1, 7, hdr1, 8);
            ws.getCell(hdr1, 7).value = 'Labor Cost';

            ws.mergeCells(hdr1, 9, hdr2, 9);
            ws.getCell(hdr1, 9).value = 'Total Amount';

            ws.getCell(hdr2, 2).value = @json($list_name);
            ws.getCell(hdr2, 5).value = "Price/Unit";
            ws.getCell(hdr2, 6).value = "Total Price";
            ws.getCell(hdr2, 7).value = "Price/Unit";
            ws.getCell(hdr2, 8).value = "Total Price";

            for (let c = 1; c <= 9; c++) {
                ws.getCell(hdr1, c).font = { bold: true };
                ws.getCell(hdr2, c).font = { bold: true };

                ws.getCell(hdr1, c).alignment =
                    ws.getCell(hdr2, c).alignment =
                    { horizontal: "center", vertical: "middle", wrapText: true };

                ws.getCell(hdr1, c).border =
                    ws.getCell(hdr2, c).border =
                    { top: { style: 'thin' }, left: { style: 'thin' }, bottom: { style: 'thin' }, right: { style: 'thin' } };
            }

            ws.getRow(hdr1).height = FIXED_HEIGHT;
            ws.getRow(hdr2).height = FIXED_HEIGHT;

            /* =======================
               CATEGORY BAND
            ======================= */
            r = hdr2 + 1;
            ws.getCell(r, 2).value = "Miscellaneous work category";
            ws.getCell(r, 2).font = { bold: true };
            ws.getCell(r, 2).alignment = { horizontal: "center", vertical: "middle" };

            for (let c = 1; c <= 9; c++) {
                ws.getCell(r, c).border = {
                    top: { style: 'thin' },
                    left: { style: 'thin' },
                    bottom: { style: 'thin' },
                    right: { style: 'thin' },
                };
                ws.getCell(r, c).alignment = { vertical: "middle", wrapText: true };
            }

            ws.getRow(r).height = FIXED_HEIGHT;
            r++;

            /* =======================
               DATA ROWS
            ======================= */
            let n = 1;
            const rows = @json($rows);

            for (const row of rows) {
                if (!row.category_name) continue;

                ws.getCell(r, 1).value = n++;                      // No.
                ws.getCell(r, 2).value = row.category_name;        // List
                ws.getCell(r, 3).value = Number(row.amount) || 0;  // Amount
                ws.getCell(r, 4).value = row.unit;                 // unit
                ws.getCell(r, 5).value = Number(row.mc_price) || 0;
                ws.getCell(r, 6).value = Number(row.mat_total) || 0;
                ws.getCell(r, 7).value = Number(row.lc_price) || 0;
                ws.getCell(r, 8).value = Number(row.lab_total) || 0;
                ws.getCell(r, 9).value = Number(row.grand_total) || 0;

                // No. center
                ws.getCell(r, 1).alignment = { horizontal: "center", vertical: "middle" };

                // Amount center with number format
                ws.getCell(r, 3).numFmt = "#,##0.00";
                ws.getCell(r, 3).alignment = { horizontal: "center", vertical: "middle" };

                // unit center
                ws.getCell(r, 4).alignment = { horizontal: "center", vertical: "middle" };

                // Money columns right
                for (let c of [5, 6, 7, 8, 9]) {
                    ws.getCell(r, c).numFmt = "#,##0.00";
                    ws.getCell(r, c).alignment = { horizontal: "right", vertical: "middle" };
                }

                // Borders
                for (let c = 1; c <= 9; c++) {
                    ws.getCell(r, c).border = {
                        top: { style: 'thin' },
                        left: { style: 'thin' },
                        bottom: { style: 'thin' },
                        right: { style: 'thin' }
                    };
                    if (!ws.getCell(r, c).alignment) {
                        ws.getCell(r, c).alignment = {};
                    }
                    ws.getCell(r, c).alignment.vertical = "middle";
                }

                ws.getRow(r).height = FIXED_HEIGHT;
                r++;
            }

            /* =======================
               TOTALS + SEAL
            ======================= */
            const startTotals = r;
            ws.mergeCells(startTotals, 2, startTotals + 4, 3);

            const totals = {
                miscTotal: Number(@json($miscTotal)) || 0,
                operating: Number(@json($operating)) || 0,
                abTotal: Number(@json($abTotal)) || 0,
                vat: Number(@json($vat)) || 0,
                finalTotal: Number(@json($finalTotal)) || 0,
            };

            const labels = [
                "Total price for miscellaneous work category",
                "Operating expenses and profit 15%",
                "Total price of work category A,B",
                "Value Added Tax 7%",
                "Total price"
            ];

            const vals = [
                totals.miscTotal,
                totals.operating,
                totals.abTotal,
                totals.vat,
                totals.finalTotal
            ];

            for (let i = 0; i < 5; i++) {
                let rr = startTotals + i;

                ws.mergeCells(rr, 4, rr, 8);
                ws.getCell(rr, 4).value = labels[i];
                ws.getCell(rr, 9).value = vals[i];
                ws.getCell(rr, 9).numFmt = "#,##0.00";

                ws.getCell(rr, 4).alignment = { horizontal: "right", vertical: "middle" };
                ws.getCell(rr, 9).alignment = { horizontal: "right", vertical: "middle" };

                for (let c = 1; c <= 9; c++) {
                    ws.getCell(rr, c).border =
                        { top: { style: 'thin' }, left: { style: 'thin' }, bottom: { style: 'thin' }, right: { style: 'thin' } };

                    if (!ws.getCell(rr, c).alignment) {
                        ws.getCell(rr, c).alignment = {};
                    }
                    ws.getCell(rr, c).alignment.vertical = "middle";
                }

                ws.getRow(rr).height = FIXED_HEIGHT;
            }

            ws.getCell(startTotals + 4, 9).font = { bold: true };

            /* =======================
               SEAL IMAGE
            ======================= */
            try {
                const base64 = await toBase64(`{{ asset('assets/images/168Home.png') }}`);
                const imgId = wb.addImage({ base64: "data:image/png;base64," + base64, extension: "png" });

                ws.addImage(imgId, {
                    tl: { col: 1.1, row: startTotals - 1 + 0.2 },
                    ext: { width: 240, height: 140 },
                    editAs: "oneCell"
                });
            } catch (e) { }

            /* =======================
               APPLY ANGSANA NEW FONT
            ======================= */
            ws.eachRow({ includeEmpty: true }, row => {
                row.eachCell(cell => {
                    const prev = cell.font || {};
                    cell.font = {
                        ...prev,
                        name: "Angsana New",
                        size: 16
                    };
                });
            });

            /* =======================
               SAVE
            ======================= */
            const buf = await wb.xlsx.writeBuffer();
            const blob = new Blob([buf], { type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" });
            const a = document.createElement("a");
            a.href = URL.createObjectURL(blob);
            const project = @json($project_name) ?? "";
            a.download = `${project} Home.xlsx`;
            document.body.appendChild(a);
            a.click();
            a.remove();
        }

        async function downloadBoqPdf() {
            const { jsPDF } = window.jspdf;
            const boqElement = document.querySelector('.boq-wrap');
            if (!boqElement) return;

            // Use Angsana for PDF capture
            boqElement.style.fontFamily = "'AngsanaPDF', 'Times New Roman', serif";

            // Higher canvas resolution, but we will use JPEG to keep file size small
            const canvas = await html2canvas(boqElement, {
                scale: 3,                 // ↑ better resolution than 2
                useCORS: true,
                backgroundColor: '#ffffff',
                scrollX: 0,
                scrollY: -window.scrollY
            });

            // Reset font back for screen preview
            boqElement.style.fontFamily = "'Times New Roman', serif";

            // JPEG with quality instead of heavy PNG
            const imgData = canvas.toDataURL('image/jpeg', 0.7);  // 0.7 = good quality, smaller size

            const pdf = new jsPDF('landscape', 'mm', 'a4');
            const pageWidth  = pdf.internal.pageSize.getWidth();
            const pageHeight = pdf.internal.pageSize.getHeight();

            const imgWidth  = canvas.width;
            const imgHeight = canvas.height;

            const targetWidth  = pageWidth * 0.98;
            const ratio        = targetWidth / imgWidth;
            const targetHeight = imgHeight * ratio;

            const x = (pageWidth - targetWidth) / 2;
            const y = 10;

            // Use JPEG + FAST compression in jsPDF
            pdf.addImage(imgData, 'JPEG', x, y, targetWidth, targetHeight, undefined, 'FAST');

            const project = @json($project_name) ?? '';
            pdf.save((project || 'BOQ') + ' Home.pdf');
        }
    </script>

    @if(!empty($autoDownload))
    <script>
        window.addEventListener('load', function () {
            setTimeout(function () {
                @if($autoDownload === 'pdf')
                    downloadBoqPdf();
                @elseif($autoDownload === 'excel')
                    downloadBoqExcel();
                @endif
            }, 300);
        });
    </script>
    @endif

</body>

</html>
