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
                <td class="meta-label">{{ __('Project Name') }}:</td>
                <td class="dyn">{{ $project_name }}</td>
                <td class="right">Date: <span class="dyn">{{ $date }}</span></td>
            </tr>
            <tr>
                <td class="meta-label">{{ __('Dear') }}:</td>
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
                            <td class="center dyn">{{ __($r['unit']) }}</td>
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
                        @php
                            $logoPath = null;
                            if (!empty($logo_choice) && $logo_choice === '168_home') {
                                $logoPath = asset('assets/images/168Home.png');
                            } elseif (!empty($logo_choice) && $logo_choice === 'pi_kaew') {
                                // currently wanted pi_kaew to hide the logo; if later you want to show its own:
                                // $logoPath = asset('assets/images/piKaew.png'); // add this image file if you want
                            }
                        @endphp

                        @if($logoPath)
                            <img src="{{ $logoPath }}" alt="Seal" style="width:240px;height:auto;">
                        @endif
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

        const unitMap = {
            'sq.m': '{{ __("sq.m") }}',
            'm': '{{ __("m") }}',
            'lump sum': '{{ __("lump sum") }}',
            'leaf': '{{ __("leaf") }}',
            'trip': '{{ __("trip") }}',
            'set': '{{ __("set") }}',
            'sheet': '{{ __("sheet") }}',
            'unit': '{{ __("unit") }}',
            'point': '{{ __("point") }}',
            'day': '{{ __("day") }}',
            'piece': '{{ __("piece") }}',
            'floor': '{{ __("floor") }}',
        };

        async function downloadBoqExcel() {
            Swal.fire({
                icon: 'success',
                title: 'Download',
                text: 'Excel downloaded successfully.',
                timer: 2000,
                showConfirmButton: false
            });

            const wb = new ExcelJS.Workbook();
            const ws = wb.addWorksheet('BOQ', {
            properties: { defaultRowHeight: 18.7 }
            });

            // --- columns A..I (1..9) ---
            ws.columns = [
                { key: 'colA', width: 13 }, // col A (No / labels)
                { key: 'colB', width: 42 }, // List / descriptions
                { key: 'colC', width: 12 }, // Amount
                { key: 'colD', width: 10 }, // Unit
                { key: 'colE', width: 14 }, // Mat Price / Unit
                { key: 'colF', width: 14 }, // Mat Total
                { key: 'colG', width: 14 }, // Lab Price / Unit
                { key: 'colH', width: 14 }, // Lab Total
                { key: 'colI', width: 16 }  // Grand Total
            ];

            const mediumB = { style: 'medium' };
            const thinB = { style: 'thin' };
            const logoChoice = @json($logo_choice);

            // helper for borders
            function setRowBorders(rowIndex, left=1, right=9, borderStyle=thinB) {
            for (let c=left; c<=right; c++) {
                ws.getCell(rowIndex, c).border = {
                top: borderStyle, left: borderStyle, bottom: borderStyle, right: borderStyle
                };
            }
            }

            // ----- Row 1: leave blank (A..I) -----
            // row 1 empty (index 1) - keep blank
            ws.getRow(1).height = 15;

            // ----- Row 2: title merged A-I, height 24 -----
            ws.getRow(2).height = 19;
            ws.mergeCells(2,1,2,9); // A2:I2
            ws.getCell(2,1).value = 'Bill of Quantities';
            ws.getCell(2,1).alignment = { horizontal: 'center', vertical: 'middle' };
            ws.getCell(2,1).font = { bold: true };

            // ----- Row 3: spacer with thick bottom border A-I, height 6.75 -----
            ws.getRow(3).height = 5.75;
            for (let c=1;c<=9;c++){
            ws.getCell(3,c).border = { bottom: mediumB };
            }

            // ----- Row 4: project row, height 22.7
            ws.getRow(4).height = 18.7;
            ws.getCell(4,1).value = 'Project Name :';
            ws.getCell(4,1).alignment = { horizontal: 'left', vertical: 'middle' };

            ws.mergeCells(4,2,4,3); // B4:C4 for project name data
            ws.getCell(4,2).value = @json($project_name);
            ws.getCell(4,2).alignment = { horizontal: 'left', vertical: 'middle' };

            ws.mergeCells(4,7,4,9); // G4:I4
            ws.getCell(4,7).value = 'Date: ' + (@json($date) || '1/1/2025');
            ws.getCell(4,7).alignment = { horizontal: 'center', vertical: 'middle' };

            // ----- Row 5: Dear row, height 22.7
            ws.getRow(5).height = 18.7;
            ws.getCell(5,1).value = 'Dear :';
            ws.getCell(5,1).alignment = { horizontal: 'left', vertical: 'middle' };
            ws.getCell(5,2).value = @json($dear);
            ws.getCell(5,2).alignment = { horizontal: 'left', vertical: 'middle' };

            // ----- Row 6: Trooper row, height 22.7, thick bottom border
           ws.getCell(6,1).value = 'Trooper :';
            ws.getCell(6,1).alignment = { horizontal: 'left', vertical: 'middle' };

            // Do NOT merge B..F — put Trooper text in column B and center it
            ws.getCell(6,2).value = @json($trooper);
            ws.getCell(6,2).alignment = { horizontal: 'left', vertical: 'middle' };

            ws.mergeCells(6,7,6,8); // G6:H6
            ws.getCell(6,7).value = 'House No.';
            ws.getCell(6,7).alignment = { horizontal: 'center', vertical: 'middle' };
            ws.getCell(6,9).value = @json($house_no);
            ws.getCell(6,9).alignment = { horizontal: 'center', vertical: 'middle' };

            // thick border under row 6 (bottom)
            for (let c=1;c<=9;c++) ws.getCell(6,c).border = { bottom: mediumB };

            // ----- Rows 7 & 8: double header rows -----
            ws.mergeCells(7,1,8,1);   // A7:A8 -> No.
            ws.getCell(7,1).value = 'No';
            ws.getCell(7,1).alignment = { horizontal: 'center', vertical: 'middle' };

            ws.mergeCells(7,3,8,3);   // C7:C8 -> Amount
            ws.getCell(7,3).value = 'Amount';
            ws.getCell(7,3).alignment = { horizontal: 'center', vertical: 'middle' };

            ws.mergeCells(7,4,8,4);   // D7:D8 -> Unit
            ws.getCell(7,4).value = 'Unit';
            ws.getCell(7,4).alignment = { horizontal: 'center', vertical: 'middle' };

            ws.mergeCells(7,9,8,9);   // I7:I8 -> Total amount
            ws.getCell(7,9).value = 'Total Amount';
            ws.getCell(7,9).alignment = { horizontal: 'center', vertical: 'middle' };

            // Column B for row7-row8: top row (7) show "List" centered; row8 B will show the sub-header text
            ws.getCell(7,2).value = 'List';
            ws.getCell(7,2).alignment = { horizontal: 'center', vertical: 'middle' };
            ws.getCell(8,2).value = @json($list_name) || 'List';
            ws.getCell(8,2).alignment = { horizontal: 'center', vertical: 'middle' };

            // Merge E & F in row7 for "Material price" and set sub-headers in row8
            ws.mergeCells(7,5,7,6); // E7:F7 Material price
            ws.getCell(7,5).value = 'Material Cost';
            ws.getCell(7,5).alignment = { horizontal: 'center', vertical: 'middle' };

            // Merge G & H in row7 for "Labor Cost"
            ws.mergeCells(7,7,7,8); // G7:H7 Labor Cost
            ws.getCell(7,7).value = 'Labor Cost';
            ws.getCell(7,7).alignment = { horizontal: 'center', vertical: 'middle' };

            // Row 8 sub-headers for E,F,G,H
            ws.getCell(8,5).value = 'Price/Unit';
            ws.getCell(8,6).value = 'Total Price';
            ws.getCell(8,7).value = 'Price/Unit';
            ws.getCell(8,8).value = 'Total Price';
            for (let c of [5,6,7,8]) ws.getCell(8,c).alignment = { horizontal: 'center', vertical: 'middle' };

            for (let rr of [7,8]) {
                for (let c=1;c<=9;c++){
                    ws.getCell(rr,c).border = {
                    top: { style: 'thin' }, left: { style: 'thin' },
                    bottom: { style: 'thin' }, right: { style: 'thin' }
                    };
                    ws.getCell(rr,c).alignment = ws.getCell(rr,c).alignment || { vertical: 'middle' };
                }
            }
            // make row 8 bottom thick underline: set bottom medium border on row 8
            for (let c=1;c<=9;c++) {
            const existing = ws.getCell(8,c).border || {};
            ws.getCell(8,c).border = { ...existing, bottom: mediumB };
            }

            // set header rows heights
            ws.getRow(7).height = 18.7;
            ws.getRow(8).height = 18.7;

            // ----- Row 9: "Miscellaneous work category" in column B -----
            ws.getRow(9).height = 18.7;
            ws.getCell(9,2).value = 'Miscellaneous work category';
            ws.getCell(9,2).alignment = { horizontal: 'center', vertical: 'middle' };
            // Set border for row9 cells
            for (let c=1;c<=9;c++) {
            ws.getCell(9,c).border = { top: thinB, left: thinB, bottom: thinB, right: thinB };
            ws.getCell(9,c).alignment = { vertical: 'middle' };
            }

            // ----- Data rows start at row 10 (dynamic) -----
            let r = 10;
            let no = 1;
            const rows = @json($rows);

            for (const row of rows) {
                if (!row.category_name) continue; // keep same logic as your blade template

                // Column A: No.
                ws.getCell(r,1).value = no++;
                ws.getCell(r,1).alignment = { horizontal: 'center', vertical: 'middle' };

                // Column B: category_name
                ws.getCell(r,2).value = row.category_name;
                ws.getCell(r,2).alignment = { vertical: 'middle' };

                // Column C: amount (centered)
                ws.getCell(r,3).value = Number(row.amount) || 0;
                ws.getCell(r,3).numFmt = '#,##0.00';
                ws.getCell(r,3).alignment = { horizontal: 'center', vertical: 'middle' };

                // Column D: unit (center)
                const displayUnit = unitMap[row.unit] ?? row.unit ?? '';
                ws.getCell(r,4).value = displayUnit;
                ws.getCell(r,4).alignment = { horizontal: 'center', vertical: 'middle' };

                // Column E: mc_price, Column F: mat_total
                ws.getCell(r,5).value = Number(row.mc_price) || 0;
                ws.getCell(r,6).value = Number(row.mat_total) || 0;

                // Column G: lc_price, Column H: lab_total
                ws.getCell(r,7).value = Number(row.lc_price) || 0;
                ws.getCell(r,8).value = Number(row.lab_total) || 0;

                // Column I: grand_total
                ws.getCell(r,9).value = Number(row.grand_total) || 0;

                // apply number formats and alignments for money cols
                for (let col of [5,6,7,8,9]) {
                ws.getCell(r,col).numFmt = '_-* #,##0.00_-;\\-* #,##0.00_-;_-* "-"??_-' ;
                ws.getCell(r,col).alignment = { horizontal: 'right', vertical: 'middle' };
                }

                // borders for the data row
                for (let c=1;c<=9;c++){
                ws.getCell(r,c).border = { top: thinB, left: thinB, bottom: thinB, right: thinB };
                }

                ws.getRow(r).height = 18.7;
                r++;
            }

            // ----- After data: one empty row then totals block of 5 rows -----
            // ensure there is an empty row
            const emptyRow = r;
            ws.getRow(emptyRow).height = 18.7;
            for (let c=1;c<=9;c++){
                ws.getCell(emptyRow, c).border = { top: thinB, left: thinB, bottom: thinB, right: thinB };
            }
            r = emptyRow + 1; // totals start here

            const totalsStart = r; // totalsStart .. totalsStart+4 (five rows)
            // Column A: "thick border" and display A and B on first two rows (as you requested)
            // We'll put 'A' on first totals row and 'B' on second totals row in column A
            ws.getCell(totalsStart, 1).value = 'A';
            ws.getCell(totalsStart+1, 1).value = 'B';
            // apply medium border on column A for the 5 totals rows
            for (let rr = totalsStart; rr <= totalsStart + 4; rr++) {
                ws.getCell(rr,1).border = { top: mediumB, left: mediumB, bottom: mediumB, right: mediumB };
                ws.getCell(rr,1).alignment = { horizontal: 'center', vertical: 'middle' };
                ws.getRow(rr).height = 18.7;
            }

            // Column B: big logo area with thick box border -> merge B..C rows for five rows
            // You requested "inside the 5 row we gonna display logo photo and make itThick Box border"
            ws.mergeCells(totalsStart,2, totalsStart+4,3); // B..C across 5 rows
            for (let rr = totalsStart; rr <= totalsStart + 4; rr++) {
                // set thick border on merged region cells individually
                ws.getCell(rr,2).border = { top: mediumB, left: mediumB, bottom: mediumB, right: mediumB };
                ws.getCell(rr,3).border = { top: mediumB, left: mediumB, bottom: mediumB, right: mediumB };
            }

            ws.getCell(totalsStart,2).alignment = { horizontal: 'center', vertical: 'middle' };

            // Insert seal/logo into the merged B..C area (approx position)
            if (logoChoice === '168_home') {
                try {
                    const base64 = await toBase64(`{{ asset('assets/images/168Home.png') }}`);
                    const imgId = wb.addImage({ base64: "data:image/png;base64," + base64, extension: "png" });
                    // place image anchored to B cell (col index 2), row totalsStart-1 (ExcelJS uses zero-based row for ext coords)
                    ws.addImage(imgId, {
                        tl: { col: 1.15, row: totalsStart - 1 + 0.2 },
                        ext: { width: 240, height: 140 },
                        editAs: 'oneCell'
                    });
                    ws.getCell(totalsStart,2).alignment = { horizontal: 'center', vertical: 'middle' };
                } catch (e) { /* ignore image errors */ }
            }

            // Column C we already included in merged logo region, but you also asked "for the column C make it all thick border"
            for (let rr = totalsStart; rr <= totalsStart + 4; rr++) {
                ws.getCell(rr,3).border = { top: mediumB, left: mediumB, bottom: mediumB, right: mediumB };
            }

            // Columns D..G merged per row for the label text (we'll merge D..G for each totals row)
            const labelColsLeft = 4, labelColsRight = 7; // D..G
            for (let i=0; i<5; i++) {
            const rr = totalsStart + i;
            ws.mergeCells(rr, labelColsLeft, rr, labelColsRight); // D..G
            // labels as requested:
            const labelMap = [
                'Total price for miscellaneous work category',
                'Operating expenses and profit 15%',
                'Total price of work category A,B',
                'Value Added Tax 7%',
                'Total price'
            ];
            ws.getCell(rr, labelColsLeft).value = labelMap[i];
            ws.getCell(rr, labelColsLeft).alignment = { horizontal: 'center', vertical: 'middle' };
            // set border on merged D..G cells
            for (let c = labelColsLeft; c <= labelColsRight; c++) {
                ws.getCell(rr,c).border = { top: mediumB, left: mediumB, bottom: mediumB, right: mediumB };
            }
            }

            // Column H: make it all thick border
            for (let rr = totalsStart; rr <= totalsStart + 4; rr++) {
            ws.getCell(rr,8).border = { top: mediumB, left: mediumB, bottom: mediumB, right: mediumB };
            }

            // Column I: totals values with thick border and right alignment
            // compute totals values (use your blade values)
            const miscTotal = Number(@json($miscTotal)) || 0;
            const operating = Number(@json($operating)) || 0;
            const abTotal = Number(@json($abTotal)) || 0;
            const vat = Number(@json($vat)) || 0;
            const finalTotal = Number(@json($finalTotal)) || 0;

            const vals = [ miscTotal, operating, abTotal, vat, finalTotal ];

            for (let i=0;i<5;i++){
            const rr = totalsStart + i;
            ws.getCell(rr,9).value = vals[i];
            ws.getCell(rr,9).numFmt = '_-* #,##0.00_-;\\-* #,##0.00_-;_-* "-"??_-' ;
            ws.getCell(rr,9).alignment = { horizontal: 'center', vertical: 'middle' };
            ws.getCell(rr,9).border = { top: mediumB, left: mediumB, bottom: mediumB, right: mediumB };
            ws.getRow(rr).height = 18.7;
            }
            // bold the final total (last row)
            ws.getCell(totalsStart + 4, 9);

            const lastRowIndex = ws.rowCount;
            for (let rr = 1; rr <= lastRowIndex; rr++) {
            const cell = ws.getCell(rr, 9);
            const prev = cell.border || null;
            // preserve prev entirely and only replace the right side
            cell.border = {
                ...(prev || {}),
                right: { style: 'medium' }
            };
            }
            
            // ----- Force Angsana New font size 16 for all cells -----
            ws.eachRow({ includeEmpty: true }, (row) => {
            row.eachCell((cell) => {
                const prev = cell.font || {};
                cell.font = { ...prev, name: 'Angsana New', size: 16 };
            });
            });

            // page setup & print area (optional)
            ws.pageSetup = {
            paperSize: 9, orientation: 'landscape',
            fitToPage: true, fitToWidth: 1, fitToHeight: 0,
            margins: { left: 0.3, right: 0.3, top: 0.5, bottom: 0.5, header: 0.3, footer: 0.3 }
            };

            // Save & download
            const buf = await wb.xlsx.writeBuffer();
            const blob = new Blob([buf], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
            const a = document.createElement('a');
            a.href = URL.createObjectURL(blob);
            const listName = String(@json($list_name) || 'BOQ').trim();
            let houseNo  = String(@json($house_no) || '').trim();

            houseNo = houseNo.replace(/\//g, '-');
            let fileName = houseNo
                ? `${listName} (${houseNo})`
                : listName;

            // clean illegal filename characters
            fileName = fileName.replace(/[\\/:*?"<>|]/g, '');

            a.download = `${fileName}.xlsx`;
            document.body.appendChild(a);
            a.click();
            a.remove();
        }


        async function downloadBoqPdf() {
            Swal.fire({
                icon: 'success',
                title: 'Download',
                text: 'PDF downloaded successfully.',
                timer: 2000,
                showConfirmButton: false
            });

            const { jsPDF } = window.jspdf;
            const boqElement = document.querySelector('.boq-wrap');
            if (!boqElement) return;

            boqElement.style.fontFamily = "'AngsanaPDF', 'Times New Roman', serif";

            const canvas = await html2canvas(boqElement, {
                scale: 3,
                useCORS: true,
                backgroundColor: '#ffffff',
                scrollX: 0,
                scrollY: -window.scrollY
            });

            boqElement.style.fontFamily = "'Times New Roman', serif";

            const imgData = canvas.toDataURL('image/jpeg', 0.75);

            const pdf = new jsPDF('landscape', 'mm', 'a4');

            const pageWidth  = pdf.internal.pageSize.getWidth();
            const pageHeight = pdf.internal.pageSize.getHeight();

            const imgWidth  = pageWidth * 0.98;
            const imgHeight = (canvas.height * imgWidth) / canvas.width;

            let heightLeft = imgHeight;
            let position = 10;

            // first page
            pdf.addImage(imgData, 'JPEG', 5, position, imgWidth, imgHeight, undefined, 'FAST');
            heightLeft -= pageHeight;

            // remaining pages
            while (heightLeft > 0) {
                pdf.addPage();
                position = heightLeft - imgHeight + 10;
                pdf.addImage(imgData, 'JPEG', 5, position, imgWidth, imgHeight, undefined, 'FAST');
                heightLeft -= pageHeight;
            }

            const listName = String(@json($list_name) || 'BOQ').trim();
            let houseNo = String(@json($house_no) || '').trim();

            // Convert slash to dash
            houseNo = houseNo.replace(/\//g, '-');

            let fileName = houseNo
                ? `${listName} (${houseNo})`
                : listName;

            fileName = fileName.replace(/[\\:*?"<>|]/g, '');

            pdf.save(fileName + '.pdf');
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
