<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Delivery Challan {{ $challan->challan_no }}</title>
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

        /* Ensure .page is exactly A4 on screen too, so html2canvas captures the
           correct aspect ratio. Prevents the "PDF looks stretched" issue. */
        @media screen {
            .page {
                width: 210mm;
                height: 297mm;
                overflow: hidden;
                box-sizing: border-box;
                margin: 0 auto 20px auto;
                background: #ffffff;
            }

            .page-break {
                display: none;
            }
        }
    </style>
</head>

<body class="format-a4">

    @php
        $perPage = 20;
        $items = $challan->items;
        $chunks = $items->chunk($perPage);
        $pages = $chunks->isEmpty() ? [collect()] : $chunks->values()->all();
        $totalPages = count($pages);
        $serialStart = 0;
        $grandQty = $items->sum('qty');
    @endphp

    @foreach ($pages as $pageIdx => $pageItems)
        @php $isLastPage = ($pageIdx === $totalPages - 1); @endphp

        <div class="page">
            @include('partials.print-header', ['docTitle' => 'Delivery Challan'])

            <div class="doc-meta">
                <div class="meta-item">Challan No : {{ $challan->challan_no }}</div>
                <div class="meta-item right">Date : {{ $challan->challan_date->format('d-M-Y') }}</div>
            </div>

            <div class="buyer-block">
                <div class="buyer-left">
                    <div class="company">{{ $challan->customer->company_name }}</div>
                    <div>{{ $challan->customer->address }}</div>
                    @if ($challan->customer->attention)
                        <div class="attention">Attention {{ $challan->customer->attention }}</div>
                    @endif
                </div>
                <div class="buyer-right">
                    @if ($challan->buyer_name)
                        Buyer Name: {{ $challan->buyer_name }}
                    @endif
                </div>
            </div>

            <table class="items">
                <colgroup>
                    <col style="width: 8%;">
                    <col style="width: 40%;">
                    <col style="width: 32%;">
                    <col style="width: 10%;">
                    <col style="width: 10%;">
                </colgroup>
                <thead>
                    <tr>
                        <th>SL</th>
                        <th>Items</th>
                        <th>Style Name</th>
                        <th>Qty</th>
                        <th>Unit</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pageItems as $i => $item)
                        <tr>
                            <td class="center">{{ $serialStart + $i + 1 }}</td>
                            <td class="center">{{ $item->item_description }}</td>
                            <td class="center">{{ $item->style_name ?? '' }}</td>
                            <td class="center">{{ $item->qty }}</td>
                            <td class="center">{{ $item->unit }}</td>
                        </tr>
                    @empty
                    @endforelse

                    @php $emptyRows = max(0, $perPage - $pageItems->count()); @endphp
                    @for ($r = 0; $r < $emptyRows; $r++)
                        <tr>
                            <td class="center">&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                    @endfor

                    @if ($isLastPage)
                        <tr>
                            <td colspan="3" class="center" style="font-weight:700;">Grand Total</td>
                            <td class="center" style="font-weight: 700;">{{ $grandQty }}</td>
                            <td></td>
                        </tr>
                    @endif
                </tbody>
            </table>

            @if ($isLastPage)
                <div class="signature">
                    <div class="line">Authorise Signature</div>
                </div>
            @endif

            <div class="page-number">Page {{ $pageIdx + 1 }} of {{ $totalPages }}</div>

            @include('partials.print-footer')
        </div>

        @if (!$isLastPage)
            <div class="page-break"></div>
        @endif

        @php $serialStart += $pageItems->count(); @endphp
    @endforeach

    <div class="no-print" style="text-align:center; padding:20px;">
        <button onclick="window.print()"
            style="background:#C8102E; color:#fff; padding:10px 28px; border:none; border-radius:6px; font-size:14px; cursor:pointer;">
            Print (A4)
        </button>
        <button onclick="downloadPdf()"
            style="margin-left:10px; background:#059669; color:#fff; padding:10px 28px; border:none; border-radius:6px; font-size:14px; cursor:pointer;">
            Download PDF
        </button>
        <a href="{{ route('challans.print-pad', $challan) }}"
            style="margin-left:10px; background:#1e293b; color:#fff; padding:10px 28px; border-radius:6px; font-size:14px; text-decoration:none;">
            Print (Pad A5)
        </a>
        <a href="{{ route('challans.index') }}"
            style="margin-left:10px; background:#e2e8f0; color:#1e293b; padding:10px 28px; border-radius:6px; font-size:14px; text-decoration:none; display:inline-block;">
            ← Back to Challan List
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
                    if (i > 0) pdf.addPage();
                    pdf.addImage(imgData, 'JPEG', 0, 0, pdfW, pdfH);
                }

                pdf.save('{{ $challan->challan_no }}.pdf');
            } finally {
                noPrintEls.forEach(el => el.style.display = '');
            }
        }
    </script>

</body>

</html>
