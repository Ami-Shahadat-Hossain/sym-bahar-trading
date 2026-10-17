<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        background: #d1d5db;
        font-family: 'Times New Roman', Times, serif;
        color: #000;
    }

    /* ===== PAGE — FLEX COLUMN WITH FIXED HEIGHT ===== */
    .page {
        background: #fff;
        margin: 8mm auto;
        position: relative;
        box-sizing: border-box;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    /* A4: fixed height so absolute/flex children anchor to real bottom */
    body.format-a4 .page {
        width: 210mm;
        height: 297mm;
        padding: 6mm 10mm 0 10mm;
        font-size: 11.5px;
    }

    /* Pad: fixed height too */
    body.format-pad .page {
        width: 148mm;
        height: 210mm;
        padding: 5mm 6mm 0 6mm;
        font-size: 8.5px;
    }

    /* Footer text — flex child, pushed near the bottom */
    .footer-text {
        flex-shrink: 0;
        margin-top: auto;
        /* pushes everything below it to the bottom */
        text-align: center;
        font-size: 0.9em;
        color: #333;
        padding: 3mm 0 2mm 0;
        font-family: 'Times New Roman', Times, serif;
        text-shadow:
            0.5px 0.5px 0 #d0d0d0,
            1px 1px 0 #e0e0e0;
        position: static;
        /* no more absolute */
    }

    /* Bottom stripe — flex child at the very bottom, bleeds to page edges */
    .bottom-stripe {
        flex-shrink: 0;
        height: 5mm;
        display: flex;
        margin: 0 -10mm;
        /* cancel left/right page padding so it goes edge-to-edge */
        position: static;
        /* no more absolute */
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }

    .bottom-stripe>div {
        height: 100%;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }

    /* Page number — stays absolute, sits above the stripe */
    .page-number {
        position: absolute;
        bottom: 12mm;
        right: 12mm;
        font-size: 0.82em;
        color: #666;
        font-family: Arial, sans-serif;
    }

    /* ===== FORMAT: A4 ===== */
    body.format-a4 .page {
        width: 210mm;
        min-height: 297mm;
        padding: 8mm 10mm 20mm 10mm;
        font-size: 11.5px;
    }

    /* ===== FORMAT: PAD (A5) ===== */
    body.format-pad .page {
        width: 148mm;
        min-height: 210mm;
        padding: 5mm 6mm 14mm 6mm;
        font-size: 8.5px;
    }

    /* ===== HEADER ===== */
    .doc-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        padding-bottom: 2mm;
    }

    .header-left {
        display: flex;
        gap: 10px;
        align-items: flex-start;
    }

    .header-logo {
        flex-shrink: 0;
    }

    .header-brand {
        display: flex;
        flex-direction: column;
    }

    .brand-name {
        font-family: 'Arial Black', Arial, Helvetica, sans-serif;
        font-weight: 900;
        font-size: 2.4em;
        letter-spacing: -0.5px;
        color: #3a3a3a;
        line-height: 1;
        margin-top: 2px;
    }

    .brand-tag {
        font-family: Arial, Helvetica, sans-serif;
        font-size: 0.52em;
        color: #b0b0b0;
        letter-spacing: 1.5px;
        margin-top: 4px;
        font-weight: 600;
        text-shadow:
            0.5px 0.5px 0 #d8d8d8,
            1px 1px 0 #e5e5e5,
            1.5px 1.5px 0 #f0f0f0;
    }

    .header-right {
        text-align: right;
        max-width: 55%;
    }

    .doc-title {
        font-family: Arial, Helvetica, sans-serif;
        font-weight: 700;
        font-size: 1.7em;
        color: #C8102E;
        margin-bottom: 4px;
        line-height: 1.1;
        letter-spacing: 0.3px;
    }

    .address {
        line-height: 1.4;
        font-size: 0.95em;
        font-family: Arial, Helvetica, sans-serif;
        color: #111;
    }

    .contact {
        font-size: 0.95em;
        font-family: Arial, Helvetica, sans-serif;
        margin-top: 2px;
        color: #111;
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: 4px;
    }

    .contact .icon {
        flex-shrink: 0;
    }

    /* ===== SEPARATOR (red thick + black thin) ===== */
    .separator {
        display: flex;
        align-items: center;
        margin: 2mm 0 3mm 0;
        height: 4px;
    }

    .sep-thick {
        width: 28%;
        height: 4px;
        background: #C8102E;
    }

    .sep-thin {
        flex: 1;
        height: 1px;
        background: #000;
        align-self: center;
    }

    /* ===== META ROW ===== */
    .doc-meta {
        display: flex;
        justify-content: space-between;
        font-size: 1.15em;
        margin-bottom: 3mm;
        font-weight: 700;
        font-family: 'Times New Roman', Times, serif;
    }

    .doc-meta .meta-item {
        border-bottom: 1px solid #000;
        padding-bottom: 2px;
        min-width: 65mm;
    }

    .doc-meta .meta-item.right {
        text-align: right;
    }

    /* ===== BUYER BLOCK ===== */
    .buyer-block {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 3mm;
        font-size: 1em;
        line-height: 1.5;
        font-family: 'Times New Roman', Times, serif;
    }

    .buyer-left {
        max-width: 68%;
    }

    .buyer-left .company {
        font-weight: 400;
        text-transform: uppercase;
    }

    .buyer-left .attention {
        font-weight: 700;
    }

    .buyer-right {
        text-align: right;
        font-weight: 700;
        font-style: italic;
        font-size: 1.1em;
        padding-top: 6mm;
    }

    /* ===== ITEMS TABLE ===== */
    table.items {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
    }

    table.items th,
    table.items td {
        border: 1px solid #000;
        padding: 4px 5px;
        vertical-align: middle;
        word-wrap: break-word;
        line-height: 1.3;
    }

    table.items th {
        font-weight: 700;
        text-align: center;
        background: #fff;
        font-size: 0.95em;
        font-family: 'Times New Roman', Times, serif;
    }

    table.items td {
        font-size: 0.95em;
    }

    table.items td.center {
        text-align: center;
    }

    table.items td.right {
        text-align: right;
    }

    table.items tbody tr {
        height: 24px;
    }

    /* ===== AMOUNT IN WORDS ===== */
    .amount-words {
        margin-top: 6mm;
        font-size: 1.15em;
        font-weight: 700;
        font-family: 'Times New Roman', Times, serif;
    }

    /* ===== SIGNATURE ===== */
    .signature {
        margin-top: 30mm;
        text-align: right;
    }

    .signature .line {
        display: inline-block;
        border-top: 1px solid #000;
        padding-top: 4px;
        min-width: 50mm;
        font-size: 1em;
        text-align: center;
        font-style: italic;
        font-family: 'Times New Roman', Times, serif;
    }

    body.format-pad .signature {
        margin-top: 16mm;
    }

    /* ===== FOOTER TEXT ===== */
    .footer-text {
        position: absolute;
        bottom: 8mm;
        left: 0;
        right: 0;
        text-align: center;
        font-size: 0.95em;
        color: #333;
        font-family: 'Times New Roman', Times, serif;
        text-shadow:
            0.5px 0.5px 0 #d0d0d0,
            1px 1px 0 #e0e0e0;
    }

    /* ===== BOTTOM STRIPE ===== */
    .bottom-stripe {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 5mm;
        display: flex;
    }

    .bottom-stripe>div {
        height: 100%;
    }

    /* ===== PAGE NUMBER ===== */
    .page-number {
        position: absolute;
        bottom: 9mm;
        right: 12mm;
        font-size: 0.82em;
        color: #666;
        font-family: Arial, sans-serif;
    }

    /* ===== PAGE BREAK / PRINT ===== */
    .page-break {
        page-break-after: always;
        break-after: page;
    }

    .no-print {
        display: block;
    }

    @media print {
        body {
            background: #fff;
        }

        .page {
            margin: 0;
            box-shadow: none;
        }

        .no-print {
            display: none !important;
        }
    }
</style>
