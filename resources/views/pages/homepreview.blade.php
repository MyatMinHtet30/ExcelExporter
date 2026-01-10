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

    /* Make columns more compact so List column has more room */
    .col-no { width: 70px }
    .col-list { width: 420px }
    .col-amt { width: 80px }
    .col-unit { width: 60px }
    .col-mc { width: 80px }
    .col-mt { width: 100px }
    .col-lc { width: 80px }
    .col-lt { width: 100px }
    .col-total { width: 110px }

    .boq-wrap {
        /* Center page and ensure all columns stay inside the outer border */
        width: 120%;
        max-width: 1300px;
        margin: 20px auto;
        background: #fff;
        border: 2px solid #000;
        padding: 0;
        box-sizing: border-box;
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
        /* Only inner grid borders; outer frame handled by .boq-wrap */
        border: none;
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
        width: 120%;
        max-width: 1300px;
        margin: 20px auto;
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 20px;
        text-align: center;
        box-sizing: border-box;
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
        
        .photo-gallery-wrap {
            page-break-inside: avoid;
            break-inside: avoid;
        }
        
        .photo-item img {
            max-width: 100% !important;
            height: auto !important;
            object-fit: contain !important;
        }
        
        .photo-grid {
            display: grid !important;
            grid-template-columns: repeat(5, 1fr) !important;
            gap: 8px !important;
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

    {{-- Photo Gallery Section --}}
    @if($photos && count($photos) > 0)
        <div class="photo-gallery-wrap" style="width: 120%; max-width: 1200px; margin: 20px auto; background: #fff; border: 2px solid #000; padding: 20px; position: relative; page-break-inside: avoid;">
            
            @php
                $portraitPhotos = [];
                $landscapePhotos = [];
                
                foreach($photos as $photo) {
                    $imagePath = public_path('storage/' . $photo->image_path);
                    
                    // Debug logging for troubleshooting
                    \Log::info('Processing photo for preview', [
                        'image_path' => $photo->image_path,
                        'full_path' => $imagePath,
                        'file_exists' => file_exists($imagePath),
                        'is_temp' => isset($photo->temp) ? $photo->temp : false
                    ]);
                    
                    if (file_exists($imagePath)) {
                        $imageSize = getimagesize($imagePath);
                        if ($imageSize) {
                            $width = $imageSize[0];
                            $height = $imageSize[1];
                            
                            \Log::info('Photo dimensions', [
                                'path' => $photo->image_path,
                                'width' => $width,
                                'height' => $height,
                                'orientation' => $height > $width ? 'portrait' : 'landscape'
                            ]);
                            
                            if ($height > $width) {
                                $portraitPhotos[] = $photo;
                            } else {
                                $landscapePhotos[] = $photo;
                            }
                        } else {
                            \Log::warning('Could not get image size', [
                                'path' => $imagePath
                            ]);
                        }
                    } else {
                        \Log::warning('Photo file not found', [
                            'path' => $imagePath,
                            'image_path' => $photo->image_path
                        ]);
                        
                        // For temporary photos, still add them to display even if we can't determine orientation
                        // Default to landscape if we can't determine
                        if (isset($photo->temp) && $photo->temp) {
                            \Log::info('Adding temp photo as landscape (fallback)', [
                                'path' => $photo->image_path
                            ]);
                            $landscapePhotos[] = $photo;
                        }
                    }
                }
                
                \Log::info('Photo separation complete', [
                    'total_photos' => count($photos),
                    'portrait_count' => count($portraitPhotos),
                    'landscape_count' => count($landscapePhotos)
                ]);
            @endphp
            
            {{-- Portrait Photos (smaller size - 5 per row) --}}
            @if(count($portraitPhotos) > 0)
                <div class="portrait-photos" style="margin-bottom: 15px;">
                    <div class="photo-grid" style="display: grid; grid-template-columns: repeat(5, 1fr); gap: 8px;">
                        @foreach($portraitPhotos as $photo)
                            <div class="photo-item" style="width: 1.5in; height: 2in; border: 1px solid #000; overflow: hidden; position: relative; z-index: 10;">
                                <img src="{{ asset('storage/' . $photo->image_path) }}" 
                                     alt="Portrait Photo" 
                                     style="width: 100%; height: 100%; object-fit: cover;"
                                     crossorigin="anonymous"
                                     onerror="console.error('Failed to load portrait photo: {{ $photo->image_path }}'); this.style.border='2px solid red'; this.style.background='#ffebee';">
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
            
            {{-- Landscape Photos (smaller size - 5 per row) --}}
            @if(count($landscapePhotos) > 0)
                <div class="landscape-photos">
                    <div class="photo-grid" style="display: grid; grid-template-columns: repeat(5, 1fr); gap: 8px;">
                        @foreach($landscapePhotos as $photo)
                            <div class="photo-item" style="width: 1.8in; height: 1.2in; border: 1px solid #000; overflow: hidden; position: relative; z-index: 10;">
                                <img src="{{ asset('storage/' . $photo->image_path) }}" 
                                     alt="Landscape Photo" 
                                     style="width: 100%; height: 100%; object-fit: cover;"
                                     crossorigin="anonymous"
                                     onerror="console.error('Failed to load landscape photo: {{ $photo->image_path }}'); this.style.border='2px solid red'; this.style.background='#ffebee';">
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    @else
        <div class="no-photos-message" style="text-align: center; padding: 20px; border: 1px dashed #ccc; margin: 20px 0; background: #f9f9f9;">
            <p style="margin: 0; color: #666;">No photos uploaded yet</p>
        </div>
    @endif

    <div class="no-print btn-footer">
        <button type="button" onclick="goBackToForm()" class="btn-lg">Cancel</button>
        <button type="button" onclick="downloadBoqExcel()" class="btn-lg">Download Excel</button>
        <button type="button" onclick="downloadBoqPdf()" class="btn-lg">Download PDF</button>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/exceljs/dist/exceljs.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function goBackToForm() {
            @if(isset($is_preview) && $is_preview)
                // Go back to the form page with data restoration
                const isEdit = {{ request()->filled('home_id') ? 'true' : 'false' }};
                if (isEdit) {
                    window.location.href = '{{ route("home.edit", request("home_id", 0)) }}?restore=1';
                } else {
                    window.location.href = '{{ route("home.create") }}?restore=1';
                }
            @else
                window.history.back();
            @endif
        }

        async function toBase64(url) {
            try {
                const res = await fetch(url);
                if (!res.ok) {
                    throw new Error(`Failed to fetch image: ${res.status}`);
                }
                const blob = await res.blob();
                
                // Validate blob
                if (!blob || blob.size === 0) {
                    throw new Error('Empty or invalid image blob');
                }
                
                // Special handling for HEIC files
                const isHeic = url.toLowerCase().includes('.heic') || url.toLowerCase().includes('.heif');
                
                if (isHeic) {
                    console.log('HEIC file detected, attempting conversion:', url);
                    
                    // First try to find a converted JPEG version
                    const convertedUrl = url.replace(/\.(heic|heif)$/i, '.jpg');
                    if (convertedUrl !== url) {
                        try {
                            console.log('Trying converted JPEG version:', convertedUrl);
                            return await toBase64(convertedUrl);
                        } catch (e) {
                            console.warn('Converted JPEG version not found, trying original HEIC');
                        }
                    }
                    
                    // If no converted version, try to process the HEIC file
                    // But expect it might fail in browsers
                    try {
                        const result = await new Promise((resolve, reject) => {
                            const reader = new FileReader();
                            reader.onloadend = () => {
                                const result = reader.result;
                                if (result && typeof result === 'string' && result.includes(',')) {
                                    const base64Data = result.split(',')[1];
                                    if (base64Data && base64Data.length > 100) {
                                        resolve(base64Data);
                                    } else {
                                        reject(new Error('Invalid HEIC base64 data'));
                                    }
                                } else {
                                    reject(new Error('Invalid HEIC result'));
                                }
                            };
                            reader.onerror = () => reject(new Error('HEIC FileReader error'));
                            reader.readAsDataURL(blob);
                        });
                        
                        console.log('HEIC conversion successful');
                        return result;
                    } catch (heicError) {
                        console.warn('HEIC processing failed (expected in most browsers):', heicError.message);
                        return null; // Return null to skip this image
                    }
                }
                
                // Handle non-HEIC files normally
                return new Promise((resolve, reject) => {
                    const reader = new FileReader();
                    reader.onloadend = () => {
                        const result = reader.result;
                        if (result && typeof result === 'string' && result.includes(',')) {
                            const base64Data = result.split(',')[1];
                            resolve(base64Data);
                        } else {
                            reject(new Error('Invalid base64 result'));
                        }
                    };
                    reader.onerror = () => reject(new Error('FileReader error'));
                    reader.readAsDataURL(blob);
                });
                
            } catch (error) {
                console.error('Error converting image to base64:', error);
                
                // For HEIC files, this is expected - return null to skip
                if (url.toLowerCase().includes('.heic') || url.toLowerCase().includes('.heif')) {
                    console.warn('HEIC file processing failed (expected), skipping photo');
                    return null;
                }
                
                // For other formats, also return null instead of throwing
                console.warn('Image processing failed, skipping photo:', url);
                return null;
            }
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

        function placeInColumn(colIndex, percent, imageWidthPx, columnWidthUnits) {
            const PX_PER_COL_UNIT = 7.5; // Excel approximation
            const imageWidthCol = imageWidthPx / PX_PER_COL_UNIT;

            // position LEFT EDGE so image CENTER aligns at percent
            return colIndex + (columnWidthUnits * percent) - (imageWidthCol / 2);
        }

        async function downloadBoqExcel() {

            // ===== Photo layout constants (MATCH PREVIEW) =====
            const DPI = 96;

            // Portrait (1.5in x 2in)
            const PORTRAIT_W = 1.5 * DPI;   // 144
            const PORTRAIT_H = 2.0 * DPI;   // 192

            // Landscape (1.8in x 1.2in)
            const LANDSCAPE_W = 1.8 * DPI;  // 173
            const LANDSCAPE_H = 1.2 * DPI;  // 115

            const PHOTOS_PER_ROW = 5;
            const GAP_PX = 8;               // same as CSS gap

            try {
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

            // --- columns A..J (1..10) ---
            // We insert an extra column so that B & C can be merged for the "List" column,
            // and shift all numeric columns one step to the right.
            ws.columns = [
                { key: 'colA', width: 13 }, // A: No / labels
                { key: 'colB', width: 35 }, // B: List (merged with C)
                { key: 'colC', width: 20 }, // C: List (merged with B)
                { key: 'colD', width: 12 }, // D: Amount
                { key: 'colE', width: 11 }, // E: Unit
                { key: 'colF', width: 14 }, // F: Mat Price / Unit
                { key: 'colG', width: 14 }, // G: Mat Total
                { key: 'colH', width: 14 }, // H: Lab Price / Unit
                { key: 'colI', width: 14 }, // I: Lab Total
                { key: 'colJ', width: 16 }  // J: Grand Total
            ];

            const mediumB = { style: 'medium' };
            const thinB = { style: 'thin' };
            const logoChoice = @json($logo_choice);

            // helper for borders
            function setRowBorders(rowIndex, left=1, right=10, borderStyle=thinB) {
            for (let c=left; c<=right; c++) {
                ws.getCell(rowIndex, c).border = {
                top: borderStyle, left: borderStyle, bottom: borderStyle, right: borderStyle
                };
            }
            }

            // ----- Row 1: leave blank (A..I) -----
            // row 1 empty (index 1) - keep blank
            ws.getRow(1).height = 15;

            // ----- Row 2: title merged A-J, height 24 -----
            ws.getRow(2).height = 19;
            ws.mergeCells(2,1,2,10); // A2:J2
            ws.getCell(2,1).value = 'Bill of Quantities';
            ws.getCell(2,1).alignment = { horizontal: 'center', vertical: 'middle' };
            ws.getCell(2,1).font = { bold: true };

            // ----- Row 3: spacer with thick bottom border A-J, height 6.75 -----
            ws.getRow(3).height = 5.75;
            for (let c=1;c<=10;c++){
            ws.getCell(3,c).border = { bottom: mediumB };
            }

            // ----- Row 4: project row, height 22.7
            ws.getRow(4).height = 18.7;
            ws.getCell(4,1).value = 'Project Name :';
            ws.getCell(4,1).alignment = { horizontal: 'left', vertical: 'middle' };

            // Merge B:C:D for project name
            ws.mergeCells(4,2,4,4); // B4:D4
            ws.getCell(4,2).value = @json($project_name);
            ws.getCell(4,2).alignment = { horizontal: 'left', vertical: 'middle' };

            // Shift date block one column to the right (now H4:J4)
            ws.mergeCells(4,8,4,10); // H4:J4
            ws.getCell(4,8).value = 'Date: ' + (@json($date) || '1/1/2025');
            ws.getCell(4,8).alignment = { horizontal: 'center', vertical: 'middle' };

            // ----- Row 5: Dear row, height 22.7
            ws.getRow(5).height = 18.7;
            ws.getCell(5,1).value = 'Dear :';
            ws.getCell(5,1).alignment = { horizontal: 'left', vertical: 'middle' };
            // Merge B:C for Dear value
            ws.mergeCells(5,2,5,3); // B5:C5
            ws.getCell(5,2).value = @json($dear);
            ws.getCell(5,2).alignment = { horizontal: 'left', vertical: 'middle' };

            // ----- Row 6: Trooper row, height 22.7, thick bottom border
            ws.getCell(6,1).value = 'Trooper :';
            ws.getCell(6,1).alignment = { horizontal: 'left', vertical: 'middle' };

            // Merge B:C for Trooper value
            ws.mergeCells(6,2,6,3); // B6:C6
            ws.getCell(6,2).value = @json($trooper);
            ws.getCell(6,2).alignment = { horizontal: 'left', vertical: 'middle' };

            // Shift House No. block one column to the right (now H6:I6 and J6)
            ws.mergeCells(6,8,6,9); // H6:I6
            ws.getCell(6,8).value = 'House No.';
            ws.getCell(6,8).alignment = { horizontal: 'center', vertical: 'middle' };
            ws.getCell(6,10).value = @json($house_no);
            ws.getCell(6,10).alignment = { horizontal: 'center', vertical: 'middle' };

            // thick border under row 6 (bottom)
            for (let c=1;c<=10;c++) ws.getCell(6,c).border = { bottom: mediumB };

            // ----- Rows 7 & 8: double header rows -----
            ws.mergeCells(7,1,8,1);   // A7:A8 -> No.
            ws.getCell(7,1).value = 'No';
            ws.getCell(7,1).alignment = { horizontal: 'center', vertical: 'middle' };

            // Amount now in column D
            ws.mergeCells(7,4,8,4);   // D7:D8 -> Amount
            ws.getCell(7,4).value = 'Amount';
            ws.getCell(7,4).alignment = { horizontal: 'center', vertical: 'middle' };

            // Unit now in column E
            ws.mergeCells(7,5,8,5);   // E7:E8 -> Unit
            ws.getCell(7,5).value = 'Unit';
            ws.getCell(7,5).alignment = { horizontal: 'center', vertical: 'middle' };

            // Total Amount now in column J
            ws.mergeCells(7,10,8,10);   // J7:J8 -> Total amount
            ws.getCell(7,10).value = 'Total Amount';
            ws.getCell(7,10).alignment = { horizontal: 'center', vertical: 'middle' };

            // Columns B & C merged for "List"
            ws.mergeCells(7,2,7,3); // B7:C7
            ws.mergeCells(8,2,8,3); // B8:C8
            ws.getCell(7,2).value = 'List';
            ws.getCell(7,2).alignment = { horizontal: 'center', vertical: 'middle' };
            ws.getCell(8,2).value = @json($list_name) || 'List';
            ws.getCell(8,2).alignment = { horizontal: 'center', vertical: 'middle' };

            // Merge F & G in row7 for "Material Cost" and set sub-headers in row8
            ws.mergeCells(7,6,7,7); // F7:G7 Material cost
            ws.getCell(7,6).value = 'Material Cost';
            ws.getCell(7,6).alignment = { horizontal: 'center', vertical: 'middle' };

            // Merge H & I in row7 for "Labor Cost"
            ws.mergeCells(7,8,7,9); // H7:I7 Labor Cost
            ws.getCell(7,8).value = 'Labor Cost';
            ws.getCell(7,8).alignment = { horizontal: 'center', vertical: 'middle' };

            // Row 8 sub-headers for F,G,H,I
            ws.getCell(8,6).value = 'Price/Unit';
            ws.getCell(8,7).value = 'Total Price';
            ws.getCell(8,8).value = 'Price/Unit';
            ws.getCell(8,9).value = 'Total Price';
            for (let c of [6,7,8,9]) ws.getCell(8,c).alignment = { horizontal: 'center', vertical: 'middle' };

            for (let rr of [7,8]) {
                for (let c=1;c<=10;c++){
                    ws.getCell(rr,c).border = {
                    top: { style: 'thin' }, left: { style: 'thin' },
                    bottom: { style: 'thin' }, right: { style: 'thin' }
                    };
                    ws.getCell(rr,c).alignment = ws.getCell(rr,c).alignment || { vertical: 'middle' };
                }
            }
            // make row 8 bottom thick underline: set bottom medium border on row 8
            for (let c=1;c<=10;c++) {
            const existing = ws.getCell(8,c).border || {};
            ws.getCell(8,c).border = { ...existing, bottom: mediumB };
            }

            // set header rows heights
            ws.getRow(7).height = 18.7;
            ws.getRow(8).height = 18.7;

            // ----- Row 9: "Miscellaneous work category" in columns B & C -----
            ws.getRow(9).height = 18.7;
            ws.mergeCells(9,2,9,3); // B9:C9
            ws.getCell(9,2).value = 'Miscellaneous work category';
            ws.getCell(9,2).alignment = { horizontal: 'center', vertical: 'middle' };
            // Set border for row9 cells
            for (let c=1;c<=10;c++) {
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

                // Columns B & C merged for category_name
                ws.mergeCells(r,2,r,3); // B..C
                ws.getCell(r,2).value = row.category_name;
                ws.getCell(r,2).alignment = { vertical: 'middle' };

                // Column D: amount (centered)
                ws.getCell(r,4).value = Number(row.amount) || 0;
                ws.getCell(r,4).numFmt = '#,##0.00';
                ws.getCell(r,4).alignment = { horizontal: 'center', vertical: 'middle' };

                // Column E: unit (center)
                const displayUnit = unitMap[row.unit] ?? row.unit ?? '';
                ws.getCell(r,5).value = displayUnit;
                ws.getCell(r,5).alignment = { horizontal: 'center', vertical: 'middle' };

                // Column F: mc_price, Column G: mat_total
                ws.getCell(r,6).value = Number(row.mc_price) || 0;
                ws.getCell(r,7).value = Number(row.mat_total) || 0;

                // Column H: lc_price, Column I: lab_total
                ws.getCell(r,8).value = Number(row.lc_price) || 0;
                ws.getCell(r,9).value = Number(row.lab_total) || 0;

                // Column J: grand_total
                ws.getCell(r,10).value = Number(row.grand_total) || 0;

                // apply number formats and alignments for money cols
                for (let col of [6,7,8,9,10]) {
                ws.getCell(r,col).numFmt = '_-* #,##0.00_-;\\-* #,##0.00_-;_-* "-"??_-' ;
                ws.getCell(r,col).alignment = { horizontal: 'right', vertical: 'middle' };
                }

                // borders for the data row
                for (let c=1;c<=10;c++){
                ws.getCell(r,c).border = { top: thinB, left: thinB, bottom: thinB, right: thinB };
                }

                ws.getRow(r).height = 18.7;
                r++;
            }

            // ----- After data: one empty row then totals block of 5 rows -----
            // ensure there is an empty row
            const emptyRow = r;
            ws.getRow(emptyRow).height = 18.7;
            // Merge only B:C for this spacer row (keep D separate), keep borders on all cells
            ws.mergeCells(emptyRow,2,emptyRow,3); // B..C
            for (let c=1;c<=10;c++){
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

            // Column B: big logo area with thick box border -> merge B..D rows for five rows
            // (local/logo section, merged B:C:D)
            ws.mergeCells(totalsStart,2, totalsStart+4,4); // B..D across 5 rows
            for (let rr = totalsStart; rr <= totalsStart + 4; rr++) {
                // set thick border on merged region cells individually
                ws.getCell(rr,2).border = { top: mediumB, left: mediumB, bottom: mediumB, right: mediumB };
                ws.getCell(rr,3).border = { top: mediumB, left: mediumB, bottom: mediumB, right: mediumB };
                ws.getCell(rr,4).border = { top: mediumB, left: mediumB, bottom: mediumB, right: mediumB };
            }

            ws.getCell(totalsStart,2).alignment = { horizontal: 'center', vertical: 'middle' };

            // Insert seal/logo into the merged B..D area (visually centered)
            if (logoChoice === '168_home') {
                try {
                    const base64 = await toBase64(`{{ asset('assets/images/168Home.png') }}`);
                    const imgId = wb.addImage({ base64: "data:image/png;base64," + base64, extension: "png" });
                    // place image roughly centered in merged B..D area (columns 2..4)
                    ws.addImage(imgId, {
                        // ExcelJS uses 0-based indices; 1.5, totalsStart-0.5 centers it nicely
                        tl: { col: 1.5, row: totalsStart - 0.5 },
                        ext: { width: 240, height: 140 },
                        editAs: 'absolute'
                    });
                    ws.getCell(totalsStart,2).alignment = { horizontal: 'center', vertical: 'middle' };
                } catch (e) { /* ignore image errors */ }
            }

            // Columns E..H merged per row for the label text (we'll merge E..H for each totals row)
            const labelColsLeft = 5, labelColsRight = 8; // E..H
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

            // Column I in totals block: add thick/bold border as well
            for (let rr = totalsStart; rr <= totalsStart + 4; rr++) {
            ws.getCell(rr,9).border = { top: mediumB, left: mediumB, bottom: mediumB, right: mediumB };
            }

            // Column J: totals values with thick border and right alignment
            // compute totals values (use your blade values)
            const miscTotal = Number(@json($miscTotal)) || 0;
            const operating = Number(@json($operating)) || 0;
            const abTotal = Number(@json($abTotal)) || 0;
            const vat = Number(@json($vat)) || 0;
            const finalTotal = Number(@json($finalTotal)) || 0;

            const vals = [ miscTotal, operating, abTotal, vat, finalTotal ];

            for (let i=0;i<5;i++){
            const rr = totalsStart + i;
            ws.getCell(rr,10).value = vals[i];
            ws.getCell(rr,10).numFmt = '_-* #,##0.00_-;\\-* #,##0.00_-;_-* "-"??_-' ;
            ws.getCell(rr,10).alignment = { horizontal: 'center', vertical: 'middle' };
            ws.getCell(rr,10).border = { top: mediumB, left: mediumB, bottom: mediumB, right: mediumB };
            ws.getRow(rr).height = 18.7;
            }
            // bold the final total (last row)
            ws.getCell(totalsStart + 4, 10);

            const lastRowIndex = ws.rowCount;
            for (let rr = 1; rr <= lastRowIndex; rr++) {
                const cell = ws.getCell(rr, 10);
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


            // ===== PHOTO GALLERY — BIGGER PHOTOS, NARROW GAPS =====
            const photos = @json($photos ?? []);
                if (photos && photos.length > 0) {

                    // ===== PHOTO SETTINGS =====
                        const PORTRAIT_WIDTH_PX = 205;   // Keep bigger size: 1.5 inches
                        const PORTRAIT_HEIGHT_PX = 273;  // Keep bigger size: 2.0 inches
                        const LANDSCAPE_WIDTH_PX = 215;  // Same width for consistency
                        const LANDSCAPE_HEIGHT_PX = 161; // Keep bigger size: 1.125 inches
                        
                        // ===== COLUMN WIDTHS (Excel column units) =====
                        const COL_WIDTHS = {
                            A: 13,   // Column A width
                            B: 55,   // Column B width  
                            C: 12,   // Column C width
                            D: 10,   // Column D width
                            E: 14,   // Column E width
                            F: 14,   // Column F width
                            G: 14,   // Column G width
                            H: 14,   // Column H width
                            I: 16    // Column I width
                        };
                        
                        // ===== ADJUSTABLE PHOTO POSITIONS =====
                        // You can easily change these settings:
                        // Format: { column: 'A', percent: 0.5 } means middle of column A
                        // percent: 0 = left edge, 0.5 = middle, 1 = right edge
                        
                        const PORTRAIT_POSITIONS = [
                            { column: 'A', percent: 0.175 },  // Photo 1: Middle of column A
                            { column: 'B', percent: 0.05 }, // Photo 2: 25% into column B
                            { column: 'C', percent: 0.5 },  // Photo 3: Middle of column C
                            { column: 'E', percent: 0.3 },  // Photo 4: 30% into column E
                            { column: 'G', percent: 0.7 }   // Photo 5: 70% into column G
                        ];
                        
                        const LANDSCAPE_POSITIONS = [
                            { column: 'A', percent: 0.5 },  // Photo 1: Middle of column A
                            { column: 'B', percent: 0.15 }, // Photo 2: 15% into column B
                            { column: 'D', percent: 0.5 },  // Photo 3: Middle of column D
                            { column: 'F', percent: 0.4 },  // Photo 4: 40% into column F
                            { column: 'H', percent: 0.6 }   // Photo 5: 60% into column H
                        ];
                        
                        // Function to convert column letter to index
                        function getColumnIndex(columnLetter) {
                            const columns = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I'];
                            return columns.indexOf(columnLetter);
                        }
                        
                        // Function to calculate exact position within a column
                        function calculatePhotoPosition(columnLetter, percent, imageWidthPx) {
                            const columnIndex = getColumnIndex(columnLetter);
                            const columnWidth = COL_WIDTHS[columnLetter] || 10; // Default width if not found
                            const imageWidthCol = imageWidthPx / 64; // Convert pixels to column units
                            
                            // Calculate position: column start + (percent of column width) - (half image width for centering)
                            return columnIndex + (columnWidth * percent) - (imageWidthCol / 2);
                        }
                        
                        // Convert position configs to actual Excel positions
                        const PHOTO_POSITIONS_PORTRAIT = PORTRAIT_POSITIONS.map(pos => 
                            calculatePhotoPosition(pos.column, pos.percent, PORTRAIT_WIDTH_PX)
                        );
                        
                        const PHOTO_POSITIONS_LANDSCAPE = LANDSCAPE_POSITIONS.map(pos => 
                            calculatePhotoPosition(pos.column, pos.percent, LANDSCAPE_WIDTH_PX)
                        );
                        
                        // Start photos well below the table
                        let currentRow = ws.rowCount + 3;
                        
                        // Classify photos by orientation
                        const classified = await Promise.all(
                            photos.map(async p => {
                                const img = new Image();
                                img.src = `{{ asset('storage/') }}/${p.image_path}`;
                                await new Promise(r => img.onload = r);
                                return { ...p, isPortrait: img.height > img.width };
                            })
                        );

                        const portraitPhotos = classified.filter(p => p.isPortrait);
                        const landscapePhotos = classified.filter(p => !p.isPortrait);

                        // Function to render photos with your positioning logic
                        async function renderPhotoGroup(photoList, photoWidth, photoHeight, positions) {
                            if (photoList.length === 0) return { processed: 0, skipped: 0 };
                            
                            let processedCount = 0;
                            let skippedCount = 0;
                            
                            // Process photos in rows of 5
                            for (let rowStart = 0; rowStart < photoList.length; rowStart += 5) {
                                const rowPhotos = photoList.slice(rowStart, rowStart + 5);
                                
                                // Position each photo in this row
                                for (let i = 0; i < rowPhotos.length && i < positions.length; i++) {
                                    const photo = rowPhotos[i];
                                    const columnPosition = positions[i];
                                    
                                    // Validate position values to prevent Excel corruption
                                    if (typeof columnPosition !== 'number' || isNaN(columnPosition) || columnPosition < 0) {
                                        console.warn(`Invalid column position for photo ${i}:`, columnPosition);
                                        skippedCount++;
                                        continue; // Skip this photo
                                    }
                                    
                                    try {
                                        // Convert to base64 with error handling
                                        const base64 = await toBase64(`{{ asset('storage/') }}/${photo.image_path}`);
                                        
                                        // Validate base64 data
                                        if (!base64 || base64.length < 100) {
                                            console.warn(`Skipping photo due to conversion failure:`, photo.image_path);
                                            skippedCount++;
                                            continue; // Skip this photo
                                        }
                                        
                                        // Determine image format from file extension
                                        const fileName = photo.image_path.toLowerCase();
                                        let imageFormat = 'jpeg';
                                        let mimeType = 'image/jpeg';
                                        
                                        if (fileName.endsWith('.png')) {
                                            imageFormat = 'png';
                                            mimeType = 'image/png';
                                        } else if (fileName.endsWith('.gif')) {
                                            imageFormat = 'gif';
                                            mimeType = 'image/gif';
                                        } else if (fileName.endsWith('.webp')) {
                                            imageFormat = 'webp';
                                            mimeType = 'image/webp';
                                        } else if (fileName.endsWith('.bmp')) {
                                            imageFormat = 'bmp';
                                            mimeType = 'image/bmp';
                                        } else {
                                            // For HEIC, AVIF, TIFF and other formats, convert to JPEG
                                            imageFormat = 'jpeg';
                                            mimeType = 'image/jpeg';
                                        }

                                        const imgId = wb.addImage({
                                            base64: `data:${mimeType};base64,` + base64,
                                            extension: imageFormat
                                        });

                                        // Position the image with validated values
                                        ws.addImage(imgId, {
                                            tl: { 
                                                col: Math.max(0, columnPosition), // Ensure non-negative
                                                row: Math.max(0, currentRow)      // Ensure non-negative
                                            },
                                            ext: { 
                                                width: Math.max(50, photoWidth),   // Minimum width
                                                height: Math.max(50, photoHeight)  // Minimum height
                                            },
                                            editAs: 'absolute'
                                        });
                                        
                                        processedCount++;
                                    } catch (error) {
                                        console.error(`Error processing photo ${i}:`, error);
                                        skippedCount++;
                                        // Continue with next photo instead of breaking
                                    }
                                }
                                
                                // Move to next row with safe integer values
                                const ROW_SPAN_PORTRAIT = 10;   // Safe integer spacing
                                const ROW_SPAN_LANDSCAPE = 8;   // Safe integer spacing

                                currentRow += photoHeight > photoWidth
                                    ? ROW_SPAN_PORTRAIT
                                    : ROW_SPAN_LANDSCAPE;
                            }
                            
                            return { processed: processedCount, skipped: skippedCount };
                        }

                        let totalProcessed = 0;
                        let totalSkipped = 0;

                        // Render portrait photos first
                        if (portraitPhotos.length > 0) {
                            const portraitResult = await renderPhotoGroup(
                        portraitPhotos,
                        PORTRAIT_WIDTH_PX,
                        PORTRAIT_HEIGHT_PX,
                        PHOTO_POSITIONS_PORTRAIT
                    );
                            totalProcessed += portraitResult.processed;
                            totalSkipped += portraitResult.skipped;
                        }

                        // Render landscape photos after portraits
                        if (landscapePhotos.length > 0) {
                            const landscapeResult = await renderPhotoGroup(
                                landscapePhotos,
                                LANDSCAPE_WIDTH_PX,
                                LANDSCAPE_HEIGHT_PX,
                                PHOTO_POSITIONS_LANDSCAPE
                            );
                            totalProcessed += landscapeResult.processed;
                            totalSkipped += landscapeResult.skipped;
                        }
                        
                        // Log photo processing results
                        console.log(`Photo processing complete: ${totalProcessed} processed, ${totalSkipped} skipped`);
                }





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
            
            } catch (error) {
                console.error('Excel generation error:', error);
                
                // Check if error is related to HEIC files
                const errorMessage = error.message || error.toString();
                if (errorMessage.toLowerCase().includes('heic') || errorMessage.toLowerCase().includes('heif')) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Excel Generated with Limitations',
                        html: 'Excel file generated successfully, but some HEIC photos were skipped.<br><br>HEIC photos are not fully supported in Excel. For best results, please use JPG, PNG, or other standard formats.',
                        confirmButtonText: 'OK'
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Download Failed',
                        text: 'Failed to generate Excel file. Please try again or contact support.',
                        confirmButtonText: 'OK'
                    });
                }
            }
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
            
            // Create a temporary wrapper that includes both BOQ and photos
            const tempWrapper = document.createElement('div');
            tempWrapper.style.cssText = `
                background: #fff;
                padding: 20px;
                font-family: 'Times New Roman', serif;
            `;
            
            // Clone the BOQ element
            const boqElement = document.querySelector('.boq-wrap');
            if (!boqElement) return;
            
            const boqClone = boqElement.cloneNode(true);
            boqClone.style.fontFamily = "'AngsanaPDF', 'Times New Roman', serif";
            tempWrapper.appendChild(boqClone);
            
            // Clone the photo gallery if it exists
            const photoGallery = document.querySelector('.photo-gallery-wrap');
            if (photoGallery) {
                const photoClone = photoGallery.cloneNode(true);
                // Add some spacing between BOQ and photos
                photoClone.style.marginTop = '30px';
                tempWrapper.appendChild(photoClone);
            }
            
            // Temporarily add to document for rendering
            tempWrapper.style.position = 'absolute';
            tempWrapper.style.left = '-9999px';
            tempWrapper.style.top = '0';
            document.body.appendChild(tempWrapper);

            try {
                // Wait for all images to load before capturing
                const images = tempWrapper.querySelectorAll('img');
                const imagePromises = Array.from(images).map(img => {
                    return new Promise((resolve) => {
                        if (img.complete) {
                            resolve();
                        } else {
                            img.onload = resolve;
                            img.onerror = resolve; // Continue even if image fails to load
                            // Timeout after 10 seconds
                            setTimeout(resolve, 10000);
                        }
                    });
                });
                
                await Promise.all(imagePromises);
                
                const canvas = await html2canvas(tempWrapper, {
                    scale: 2, // Reduced scale for better performance with photos
                    useCORS: true,
                    allowTaint: true,
                    backgroundColor: '#ffffff',
                    scrollX: 0,
                    scrollY: 0,
                    logging: false,
                    imageTimeout: 15000, // Increased timeout for photo loading
                    onclone: function(clonedDoc) {
                        // Ensure images are loaded in the cloned document
                        const clonedImages = clonedDoc.querySelectorAll('img');
                        clonedImages.forEach(img => {
                            img.style.maxWidth = '100%';
                            img.style.height = 'auto';
                            // Force image to be visible
                            img.style.display = 'block';
                            img.style.visibility = 'visible';
                        });
                    }
                });

                const imgData = canvas.toDataURL('image/jpeg', 0.8);

                const pdf = new jsPDF('landscape', 'mm', 'a4');

                const pageWidth = pdf.internal.pageSize.getWidth();
                const pageHeight = pdf.internal.pageSize.getHeight();

                const imgWidth = pageWidth * 0.95;
                const imgHeight = (canvas.height * imgWidth) / canvas.width;

                let heightLeft = imgHeight;
                let position = 10;

                // First page
                pdf.addImage(imgData, 'JPEG', (pageWidth - imgWidth) / 2, position, imgWidth, imgHeight, undefined, 'FAST');
                heightLeft -= (pageHeight - 20);

                // Remaining pages
                while (heightLeft > 0) {
                    pdf.addPage();
                    position = heightLeft - imgHeight + 10;
                    pdf.addImage(imgData, 'JPEG', (pageWidth - imgWidth) / 2, position, imgWidth, imgHeight, undefined, 'FAST');
                    heightLeft -= (pageHeight - 20);
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
                
            } catch (error) {
                console.error('PDF generation error:', error);
                alert('Error generating PDF. Please try again.');
            } finally {
                // Clean up temporary wrapper
                document.body.removeChild(tempWrapper);
                
                // Restore original font
                if (boqElement) {
                    boqElement.style.fontFamily = "'Times New Roman', serif";
                }
            }
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
