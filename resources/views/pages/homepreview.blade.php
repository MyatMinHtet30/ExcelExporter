<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>BOQ Preview</title>

    <style>

        .table th{white-space:normal; word-break:break-word; line-height:1.15;}
        .col-no{width:120px}
        .col-list{width:auto}
        .col-amt{width:90px}
        .col-unit{width:70px}
        .col-mc{width:90px}
        .col-mt{width:110px}
        .col-lc{width:90px}
        .col-lt{width:110px}
        .col-total{width:120px}
        .boq-wrap {
            max-width: 1200px;
            margin: 20px auto;
            background: #fff;
            border: 2px solid #000;
            padding: 0;
        }

        .dyn {
            color: red !important;
        }

        .boq-meta{
            width:100%;
            border-collapse:collapse;
            border:none;
        }

        .boq-meta td{
            border:none !important;   /* ← remove all borders */
            padding:6px;
            font-size:14px;
        }

        .thead th {
            border: 1px solid #000;
            text-align: center;
            padding: 6px;
            font-size: 14px
        }

        .table{
            width:100%;
            border-collapse:collapse;
            table-layout:fixed;
        }

        .table th,
        .table td {
            border: 1px solid #000;
            padding: 6px;
            font-size: 13px
        }

        .cat {
            background: #fff78a;
            font-weight: 700
        }

        .right {
            text-align: right
        }

        .center {
            text-align: center
        }

        .badge-box {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 4px;
            background: #e8f5e9;
            border: 1px solid #81c784
        }

        .boq-head{
            width:100%;
            text-align:center;      /* center the text */
            font-weight:700;
            font-size:16px;
            padding:6px 0;
            border-bottom:1px solid #000; /* keeps the table-look header line */
        }

        .btn-footer{
            max-width:1200px;
            margin:20px auto;
            display:flex;
            justify-content:center;
            gap:20px;
        }

        .btn-lg{
            padding:10px 26px;
            font-size:16px;
            font-weight:600;
            border:1px solid #000;
            background:#eee;
            cursor:pointer;
        }

        .btn-lg:hover{
            background:#ddd;
        }

        @media print {
            .no-print {
                display: none !important
            }
        }
    </style>
</head>

<body>

    <div class="boq-wrap">
        <div class="boq-head">Bill of Quantities</div>

        <table class="boq-meta">
            <colgroup>
                <col style="width:120px;"><!-- same as .col-no -->
                <col><!-- auto; aligns with 'List' start -->
                <col style="width:220px;"><!-- same as Date/House column -->
            </colgroup>
            <tr>
                <td class="meta-label">Project Name  :</td>
                <td class="dyn">{{ $project_name }}</td>
                <td class="right">Date: <span class="dyn">{{ $date }}</span></td>
            </tr>
            <tr>
                <td class="meta-label">Dear  :</td>
                <td class="dyn">{{ $dear }}</td>
                <td class="right">House No. <span class="dyn">{{ $house_no }}</span></td>
            </tr>
            <tr>
                <td class="meta-label">Trooper  :</td>
                <td class="dyn">{{ $trooper }}</td>
                <td class="right">&nbsp;</td>
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
                <!-- row 1: yellow headings -->
                <tr class="yellow">
                    <th rowspan="2" style="border:1px solid #000;">No.</th>
                    <!-- DO NOT rowspan here, we need a second-row green cell under it -->
                    <th style="border:1px solid #000; text-align:center;">List</th>
                    <th rowspan="2" style="border:1px solid #000;">Amount</th>
                    <th rowspan="2" style="border:1px solid #000;">unit</th>
                    <th colspan="2" style="border:1px solid #000;">Material Cost</th>
                    <th colspan="2" style="border:1px solid #000;">Labor Cost</th>
                    <th rowspan="2" style="border:1px solid #000;">Total Amount</th>
                </tr>

                <!-- row 2: green list_name under List + yellow second-line labels -->
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
                    <td class="center yellow" style="border:1px solid #000;"></td>
                    <td class="yellow" style="border:1px solid #000; font-weight:700; text-align:center;">
                        Miscellaneous work category
                    </td>
                    <td class="yellow" style="border:1px solid #000;"></td>
                    <td class="yellow" style="border:1px solid #000;"></td>
                    <td class="yellow" style="border:1px solid #000;"></td>
                    <td class="yellow" style="border:1px solid #000;"></td>
                    <td class="yellow" style="border:1px solid #000;"></td>
                    <td class="yellow" style="border:1px solid #000;"></td>
                    <td class="yellow" style="border:1px solid #000;"></td>
                </tr>

                @php $n = 1; @endphp
                @foreach($rows as $r)
                    @if(!empty($r['category_name']))
                        <tr>
                            <td class="center">{{ $n }}</td>
                            <td class="dyn">{{ $r['category_name'] }}</td>
                            <td class="right dyn">{{ number_format((float) $r['amount'], 2) }}</td>
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
                    <!-- No. column -->
                    <td class="center">&nbsp;</td>

                    <!-- Seal spans: List + Amount (2 cols) and 5 rows -->
                    <td class="yellow" colspan="2" rowspan="5" style="text-align:center; vertical-align:middle;">
                        <img src="{{ asset('assets/images/168Home.png') }}" alt="Seal" style="width:240px;height:auto;">
                    </td>

                    <!-- ✅ Labels start at the Unit column: colspan=5 (unit + MC PU + MC TP + LC PU + LC TP) -->
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
                {{-- === /Totals + Seal block === --}}

            </tbody>
        </table>

    </div>

    <div class="no-print btn-footer">
        <button type="button" onclick="window.history.back()" class="btn-lg">
            Cancel
        </button>

        <button onclick="window.print()" class="btn-lg">
            Print
        </button>
    </div>

</body>

</html>
