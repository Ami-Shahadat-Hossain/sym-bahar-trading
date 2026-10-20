<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Money Receipt (3 Copies) {{ $receipt->receipt_no }}</title>
    @include('partials.print-styles')
    <style>
        /* ===== A4 PAGE — 3 COPIES ===== */
        @media print {
            @page {
                size: A4 portrait;
                margin: 0;
            }
        }

        body.format-pad {
            background: #d1d5db;
            margin: 0;
            padding: 0;
        }

        body.format-pad .page {
            width: 210mm;
            height: 297mm;
            padding: 5mm 6mm;
            margin: 6mm auto;
            box-sizing: border-box;
            background: #fff;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
            display: flex;
            flex-direction: column;
        }

        body.format-pad .page .doc-meta .meta-item {
            min-width: auto;
            max-width: none;
            font-size: 9px;
            font-weight: 700;
            border-bottom: 1px solid #000;
            padding-bottom: 1.5px;
            white-space: nowrap;
        }

        .receipt-block {
            flex: 1;
            border: 1px solid #000;
            padding: 2.5mm 4mm;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            background: #fff;
            overflow: hidden;
            margin-bottom: 2mm;
            position: relative;
        }

        .receipt-block:last-child {
            margin-bottom: 0;
        }

        .receipt-block .doc-header {
            transform: scale(0.62);
            transform-origin: top left;
            width: 161%;
            margin-bottom: -12mm;
        }

        .receipt-block .doc-header .separator {
            margin: 1mm 0 1.5mm 0;
            height: 3px;
        }

        .receipt-block .doc-header .sep-thick {
            height: 3px;
        }

        .receipt-block .doc-meta {
            flex-shrink: 0;
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            margin-bottom: 2.5mm;
            font-family: 'Times New Roman', serif;
        }

        .receipt-block .fields-zone {
            flex-shrink: 0;
        }

        /* ===== SHARED GRID COLUMNS ===== */
        :root {
            --label-col: 40mm;
            --col-gap: 2.5mm;
        }

        .receipt-block .field-row {
            display: grid;
            grid-template-columns: var(--label-col) 1fr;
            align-items: baseline;
            column-gap: var(--col-gap);
            margin-bottom: 2.5mm;
            font-size: 8.5px;
            font-family: 'Times New Roman', serif;
            line-height: 1.25;
        }

        .receipt-block .field-row .label {
            white-space: nowrap;
        }

        .receipt-block .field-row .value {
            min-height: 10px;
            word-break: break-word;
            border-bottom: 1px dotted #999;
            padding: 0 2px 1px 2px;
        }

        /* ===== AMOUNT + MODE ROW =====
       Uses the SAME grid columns as field rows.
       Amount box → label column (col 1)
       Mode line  → value column (col 2), aligned with ABC/Thirty Thousand above */
        .amount-mode-wrap {
            display: grid;
            grid-template-columns: var(--label-col) 1fr;
            column-gap: var(--col-gap);
            align-items: center;
            margin-top: 3mm;
        }

        .amount-box {
            grid-column: 1;
            justify-self: start;
            /* ← NEW: shrinks box to content width, no stretch */
            border: 1px solid #000;
            padding: 1.2mm 2mm;
            /* ← was 2mm 3mm */
            font-weight: 700;
            font-size: 12px;
            /* ← was 9px */
            font-family: 'Times New Roman', serif;
            line-height: 1.1;
            text-align: center;
            white-space: nowrap;
            min-width: 24mm;
            /* ← was 30mm */
        }

        /* Mode line — starts exactly where the value column starts */
        .mode-line {
            grid-column: 2;
            display: flex;
            align-items: center;
            gap: 3mm;
            font-size: 7.5px;
            font-family: 'Times New Roman', serif;
            white-space: nowrap;
            min-width: 0;
            line-height: 1;
        }

        .mode-line .mode-label {
            white-space: nowrap;
            flex-shrink: 0;
        }

        .mode-line .mode-item {
            display: inline-flex;
            align-items: center;
            gap: 3px;
            white-space: nowrap;
            flex-shrink: 0;
        }

        .mode-line .cb {
            display: inline-block;
            width: 8px;
            height: 8px;
            border: 1.2px solid #000;
            flex-shrink: 0;
        }

        /* Signature */
        .receipt-block .sig-zone {
            flex-shrink: 0;
            margin-top: auto;
            text-align: right;
            padding-bottom: 1.5mm;
        }

        .receipt-block .sig-zone .line {
            display: inline-block;
            border-top: 1px solid #000;
            padding-top: 2px;
            min-width: 34mm;
            font-size: 8.5px;
            font-style: italic;
            font-family: 'Times New Roman', serif;
            text-align: center;
        }

        /* Footer */
        .receipt-block .local-footer {
            flex-shrink: 0;
            text-align: center;
            font-size: 7px;
            font-family: 'Times New Roman', serif;
            color: #333;
            padding-bottom: 1mm;
            text-shadow: 0.4px 0.4px 0 #d0d0d0, 1px 1px 0 #e0e0e0;
        }

        .receipt-block .local-stripe {
            flex-shrink: 0;
            height: 2mm;
            display: flex;
            margin: 0 -4mm -2.5mm -4mm;
        }

        .receipt-block .local-stripe>div {
            height: 100%;
        }
    </style>
</head>

<body class="format-pad">

    <div class="page">
        @for ($copy = 0; $copy < 3; $copy++)
            <div class="receipt-block">
                @include('partials.print-header', ['docTitle' => 'Money Receipt'])

                <div class="doc-meta">
                    <div class="meta-item">Receipt No : {{ $receipt->receipt_no }}</div>
                    <div class="meta-item right">Date : {{ $receipt->receipt_date->format('d-M-Y') }}</div>
                </div>

                <div class="fields-zone">
                    <div class="field-row">
                        <span class="label">Received with thanks from :</span>
                        <span class="value">{{ $receipt->received_from }}</span>
                    </div>

                    <div class="field-row">
                        <span class="label">The Sum of Amount (In Words) :</span>
                        <span class="value">{{ $receipt->amount_in_words }}</span>
                    </div>

                    <div class="field-row">
                        <span class="label">Against Bill No / Purpose :</span>
                        <span class="value">{{ $receipt->against_bill_no ?? '' }}</span>
                    </div>

                    {{-- ===== Amount box in label col, Mode in value col ===== --}}
                    <div class="amount-mode-wrap">
                        <div class="amount-box">
                            {{ $receipt->currency_symbol }} {{ number_format($receipt->amount, 2) }}
                        </div>

                        <div class="mode-line">
                            <span class="mode-label"><strong>Mode :</strong></span>
                            <span class="mode-item"><span class="cb"></span>Cash</span>
                            <span class="mode-item"><span class="cb"></span>Cheque</span>
                            <span class="mode-item"><span class="cb"></span>Bank Transfer</span>
                            <span class="mode-item"><span class="cb"></span>Bkash / Nagad / Bangla QR</span>
                        </div>
                    </div>
                </div>

                <div class="sig-zone">
                    <div class="line">Authorise Signature</div>
                </div>

                <div class="local-footer">Supplier . Wholesaler . Import &amp; Export . Commission Agent</div>

                <div class="local-stripe">
                    <div style="background:#C8102E; flex: 1.0;"></div>
                    <div style="background:#B00D28; flex: 1.0;"></div>
                    <div style="background:#A00D25; flex: 1.8;"></div>
                    <div style="background:#8B0A1E; flex: 1.4;"></div>
                    <div style="background:#E31E24; flex: 1.6;"></div>
                    <div style="background:#6B0817; flex: 1.8;"></div>
                    <div style="background:#C8102E; flex: 1.2;"></div>
                    <div style="background:#5A0614; flex: 1.0;"></div>
                </div>
            </div>
        @endfor
    </div>

    <div class="no-print" style="text-align:center; padding:20px;">
        <button onclick="window.print()"
            style="background:#C8102E; color:#fff; padding:10px 28px; border:none; border-radius:6px; font-size:14px; cursor:pointer;">
            Print (3 Copies on A4)
        </button>
        <a href="{{ route('receipts.print-a4', $receipt) }}"
            style="margin-left:10px; background:#1e293b; color:#fff; padding:10px 28px; border-radius:6px; font-size:14px; text-decoration:none;">
            Print (A4 — 2 Copies)
        </a>
        <a href="{{ route('receipts.index') }}"
            style="margin-left:10px; background:#e2e8f0; color:#1e293b; padding:10px 28px; border-radius:6px; font-size:14px; text-decoration:none;">
            ← Back
        </a>
    </div>

</body>

</html>
