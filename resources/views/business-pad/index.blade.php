<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Business Pad</title>
    @include('partials.print-styles')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <style>
        @media print {
            @page {
                size: A4 portrait;
                margin: 0;
            }

            .no-print {
                display: none !important;
            }
        }

        @media screen {
            .page {
                width: 210mm;
                height: 297mm;
                overflow: hidden;
                box-sizing: border-box;
                margin: 0 auto 20px auto;
                background: #ffffff;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            }
        }

        /* Flex column so the signature can pin to the page bottom */
        .page {
            display: flex;
            flex-direction: column;
        }

        /* Free-text paragraph (replaces the items table) */
        .pad-paragraph {
            margin-top: 6mm;
            padding: 0 4mm;
            font-size: 12pt;
            line-height: 1.8;
            white-space: pre-wrap;
            word-break: break-word;
            min-height: 20mm;
        }

        /* Signature — right-aligned, pinned above page-number / footer */
        .signature {
            display: flex;
            justify-content: flex-end;
            align-items: flex-end;
            margin-top: 4mm;
            padding: 0 4mm;
        }

        .signature .line {
            border-top: 1px solid #000;
            padding-top: 4px;
            min-width: 60mm;
            text-align: center;
            font-size: 11pt;
        }

        /* Always-present spacer: pushes signature (or footer in blank mode) to bottom */
        .flex-spacer {
            flex: 1;
        }

        /* ---------- Screen-only control panel ---------- */
        .pad-controls {
            max-width: 210mm;
            margin: 20px auto;
            padding: 20px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            font-family: system-ui, -apple-system, "Segoe UI", sans-serif;
        }

        .pad-controls h1 {
            font-size: 20px;
            margin: 0 0 12px;
            color: #1e293b;
        }

        .pad-controls label {
            display: block;
            font-size: 13px;
            color: #475569;
            margin: 10px 0 4px;
            font-weight: 600;
        }

        .pad-controls input[type="date"],
        .pad-controls textarea {
            width: 100%;
            padding: 8px 10px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            font-size: 13px;
            box-sizing: border-box;
            font-family: inherit;
        }

        .pad-controls textarea {
            min-height: 140px;
            resize: vertical;
        }

        .pad-controls .row {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-top: 16px;
        }

        .pad-controls button,
        .pad-controls a.btn {
            padding: 9px 18px;
            border: none;
            border-radius: 6px;
            font-size: 13px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-print {
            background: #C8102E;
            color: #fff;
        }

        .btn-pdf {
            background: #059669;
            color: #fff;
        }

        .btn-blank {
            background: #1e293b;
            color: #fff;
        }

        .btn-back {
            background: #e2e8f0;
            color: #1e293b;
        }
    </style>
</head>

<body class="format-a4">

    {{-- Controls (never printed) --}}
    <div class="pad-controls no-print">
        <h1>Business Pad</h1>

        <label for="padDateInput">Date</label>
        <input type="date" id="padDateInput" value="{{ now()->format('Y-m-d') }}">

        <label for="padTextInput">Pad Content <span style="font-weight:400;color:#94a3b8;">(leave empty for a blank
                pad)</span></label>
        <textarea id="padTextInput" placeholder="Enter the text that should appear on the pad…"></textarea>

        <div class="row">
            <button class="btn-print" onclick="printPad(true)">Print (with content)</button>
            <button class="btn-blank" onclick="printPad(false)">Print (blank pad)</button>
            <button class="btn-pdf" onclick="downloadPdf(true)">Download PDF (with content)</button>
            <button class="btn-blank" onclick="downloadPdf(false)">Download PDF (blank)</button>
            <a class="btn-back" href="/">← Back</a>
        </div>
    </div>

    {{-- The Pad --}}
    <div class="page" id="padPage">

        {{-- Header — same partial as print-a4, no doc-title --}}
        @include('partials.print-header', ['docTitle' => null])

        {{-- Content block: doc-meta (Date only, right-aligned) + paragraph --}}
        <div id="padContent">
            <div class="doc-meta">
                <div class="meta-item right">Date : <span id="padDateDisplay">{{ now()->format('d-M-Y') }}</span></div>
            </div>

            <div class="pad-paragraph" id="padParagraphDisplay"></div>
        </div>

        {{-- Spacer: always present so footer (and signature) sits at the bottom --}}
        <div class="flex-spacer"></div>

        {{-- Signature block --}}
        <div id="padSignature" class="signature">
            <div class="line">Authorise Signature</div>
        </div>

        <div class="page-number">Page 1 of 1</div>

        @include('partials.print-footer')
    </div>

    <script>
        const dateInput = document.getElementById('padDateInput');
        const textInput = document.getElementById('padTextInput');
        const dateDisplay = document.getElementById('padDateDisplay');
        const paraDisplay = document.getElementById('padParagraphDisplay');
        const padContent = document.getElementById('padContent');
        const padSignature = document.getElementById('padSignature');

        function formatDate(value) {
            if (!value) return '';
            const d = new Date(value + 'T00:00:00');
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
                'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'
            ];
            return String(d.getDate()).padStart(2, '0') + '-' +
                months[d.getMonth()] + '-' + d.getFullYear();
        }

        function refresh() {
            dateDisplay.textContent = formatDate(dateInput.value) || '—';
            paraDisplay.textContent = textInput.value || '';
        }

        dateInput.addEventListener('input', refresh);
        textInput.addEventListener('input', refresh);
        refresh();

        // Toggle content + signature blocks together (header / footer stay put)
        function setMode(showContent) {
            const disp = showContent ? '' : 'none';
            padContent.style.display = disp;
            padSignature.style.display = disp;
        }

        function printPad(showContent) {
            const prevContent = padContent.style.display;
            const prevSig = padSignature.style.display;
            setMode(showContent);
            setTimeout(() => {
                window.print();
                setTimeout(() => {
                    padContent.style.display = prevContent;
                    padSignature.style.display = prevSig;
                }, 200);
            }, 50);
        }

        async function downloadPdf(showContent) {
            const prevContent = padContent.style.display;
            const prevSig = padSignature.style.display;
            setMode(showContent);

            try {
                const {
                    jsPDF
                } = window.jspdf;
                const pdf = new jsPDF({
                    unit: 'mm',
                    format: 'a4',
                    orientation: 'portrait'
                });
                const pdfW = pdf.internal.pageSize.getWidth();
                const pdfH = pdf.internal.pageSize.getHeight();

                const canvas = await html2canvas(document.getElementById('padPage'), {
                    scale: 2,
                    useCORS: true,
                    backgroundColor: '#ffffff',
                    logging: false,
                    scrollX: 0,
                    scrollY: 0,
                });
                const imgData = canvas.toDataURL('image/jpeg', 0.98);
                pdf.addImage(imgData, 'JPEG', 0, 0, pdfW, pdfH);

                const fname = (showContent ? 'business-pad-' : 'business-pad-blank-') +
                    new Date().toISOString().slice(0, 10);
                pdf.save(fname + '.pdf');
            } finally {
                padContent.style.display = prevContent;
                padSignature.style.display = prevSig;
            }
        }
    </script>
</body>

</html>
