<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Money Receipt {{ $receipt->receipt_no }}</title>
    @include('partials.print-styles')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <style>
        @media print {
            @page {
                size: A4 portrait;
                margin: 0;
            }
        }

        /* Screen-only: pin .page width to A4 so html2canvas captures at
           print-accurate proportions. Height stays auto — the PDF script
           aspect-fits the result into A4, so no clipping. */
        @media screen {
            .page {
                width: 210mm;
                margin: 0 auto 20px auto;
                box-sizing: border-box;
                background: #fff;
            }
        }

        /* Each receipt block: fixed height, flex column, no overlap */
        .receipt-block {
            height: 138mm;
            border: 1.5px solid #000;
            padding: 5mm 8mm;
            box-sizing: border-box;
            margin-bottom: 6mm;
            display: flex;
            flex-direction: column;
            background: #fff;
            position: relative;
        }

        .receipt-block:last-child {
            margin-bottom: 0;
        }

        /* Header zone */
        .receipt-block .doc-header {
            flex-shrink: 0;
        }

        /* Meta row: receipt no + date */
        .receipt-block .doc-meta {
            flex-shrink: 0;
            margin-bottom: 4mm;
        }

        /* Fields zone — auto-flex to fill remaining space above signature */
        .receipt-block .fields {
            flex: 1;
            padding-top: 2mm;
        }

        /* ===== SHARED GRID COLUMNS ===== */
        :root {
            --label-col: 55mm;
            --col-gap: 4mm;
        }

        /* Each field: label | value on same baseline */
        .receipt-block .field-row {
            display: grid;
            grid-template-columns: var(--label-col) 1fr;
            align-items: baseline;
            column-gap: var(--col-gap);
            margin-bottom: 4mm;
            font-size: 11px;
            font-family: 'Times New Roman', serif;
        }

        .receipt-block .field-row .label {
            white-space: nowrap;
            font-weight: 400;
        }

        .receipt-block .field-row .value {
            padding-left: 4px;
            min-height: 14px;
            line-height: 1.35;
            word-break: break-word;
        }

        /* ===== AMOUNT + MODE ROW =====
           Uses the SAME grid columns as field rows.
           Amount box → label column (col 1)
           Mode line  → value column (col 2), aligned with ABC above */
        .amount-mode-wrap {
            display: grid;
            grid-template-columns: var(--label-col) 1fr;
            column-gap: var(--col-gap);
            align-items: center;
            margin-top: 4mm;
        }

        /* Amount box — slightly smaller, hugs content, sits in label column */
        .amount-box {
            grid-column: 1;
            justify-self: start;
            border: 1.5px solid #000;
            padding: 2mm 3mm;
            font-weight: 700;
            font-size: 12px;
            font-family: 'Times New Roman', serif;
            line-height: 1.15;
            text-align: center;
            white-space: nowrap;
            min-width: 32mm;
        }

        /* Mode line — items grouped, starting at value column x */
        .mode-line {
            grid-column: 2;
            display: flex;
            align-items: center;
            gap: 5mm;
            font-size: 10.5px;
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
            width: 10px;
            height: 10px;
            border: 1.3px solid #000;
            flex-shrink: 0;
        }

        /* Signature */
        .receipt-block .sig-line {
            flex-shrink: 0;
            text-align: right;
            margin-top: 2mm;
            margin-bottom: 4mm;
            padding-right: 2mm;
        }

        .receipt-block .sig-line .line {
            display: inline-block;
            border-top: 1px solid #000;
            padding-top: 3px;
            min-width: 48mm;
            font-size: 11px;
            font-style: italic;
            font-family: 'Times New Roman', serif;
            text-align: center;
        }

        /* Footer */
        .receipt-block .local-footer {
            flex-shrink: 0;
            text-align: center;
            font-size: 10.5px;
            font-family: 'Times New Roman', serif;
            color: #333;
            margin-bottom: 2mm;
            text-shadow: 0.5px 0.5px 0 #d0d0d0, 1px 1px 0 #e0e0e0;
        }

        .receipt-block .local-stripe {
            flex-shrink: 0;
            height: 3.5mm;
            display: flex;
            margin: 0 -8mm -5mm -8mm;
        }

        .receipt-block .local-stripe>div {
            height: 100%;
        }
    </style>
</head>

<body class="format-a4">

    <div class="page" style="padding: 8mm 10mm;">
        @for ($copy = 0; $copy < 2; $copy++)
            <div class="receipt-block">
                @include('partials.print-header', ['docTitle' => 'Money Receipt'])

                <div class="doc-meta">
                    <div class="meta-item">Receipt No : {{ $receipt->receipt_no }}</div>
                    <div class="meta-item right">Date : {{ $receipt->receipt_date->format('d-M-Y') }}</div>
                </div>

                <div class="fields">
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

                <div class="sig-line">
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
            Print (A4 — 2 Copies)
        </button>
        <button onclick="downloadPdf()"
            style="margin-left:10px; background:#059669; color:#fff; padding:10px 28px; border:none; border-radius:6px; font-size:14px; cursor:pointer;">
            Download PDF
        </button>
        <a href="{{ route('receipts.print-pad', $receipt) }}"
            style="margin-left:10px; background:#1e293b; color:#fff; padding:10px 28px; border-radius:6px; font-size:14px; text-decoration:none;">
            Print (Pad A5)
        </a>
        <a href="{{ route('receipts.index') }}"
            style="margin-left:10px; background:#e2e8f0; color:#1e293b; padding:10px 28px; border-radius:6px; font-size:14px; text-decoration:none;">
            ← Back
        </a>
    </div>

    <script>
        async function downloadPdf() {
            const noPrintEls = document.querySelectorAll('.no-print');
            noPrintEls.forEach(el => el.style.display = 'none');

            try {
                const pages = document.querySelectorAll('.page');
                const {
                    jsPDF
                } = window.jspdf;
                const pdf = new jsPDF({
                    unit: 'mm',
                    format: 'a4',
                    orientation: 'portrait'
                });
                const pdfW = pdf.internal.pageSize.getWidth(); // 210
                const pdfH = pdf.internal.pageSize.getHeight(); // 297

                for (let i = 0; i < pages.length; i++) {
                    const canvas = await html2canvas(pages[i], {
                        scale: 2,
                        useCORS: true,
                        backgroundColor: '#ffffff',
                        logging: false,
                        scrollX: 0,
                        scrollY: 0
                    });
                    const imgData = canvas.toDataURL('image/jpeg', 0.98);

                    // Aspect-fit: scale the captured canvas into A4 without distortion.
                    const imgAspect = canvas.width / canvas.height;
                    const pdfAspect = pdfW / pdfH;
                    let drawW = pdfW,
                        drawH = pdfH,
                        x = 0,
                        y = 0;
                    if (imgAspect > pdfAspect) {
                        drawH = pdfW / imgAspect;
                        y = (pdfH - drawH) / 2;
                    } else {
                        drawW = pdfH * imgAspect;
                        x = (pdfW - drawW) / 2;
                    }

                    if (i > 0) pdf.addPage();
                    pdf.addImage(imgData, 'JPEG', x, y, drawW, drawH);
                }

                pdf.save('{{ $receipt->receipt_no }}.pdf');
            } finally {
                noPrintEls.forEach(el => el.style.display = '');
            }
        }
    </script>

</body>

</html>
