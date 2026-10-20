<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Invoice (Pad) {{ $invoice->invoice_no }}</title>
    @include('partials.print-styles')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <style>
        @media print {
            @page {
                size: 148mm 210mm;
                margin: 0;
            }
        }

        /* Screen-only A5 box so html2canvas captures each .page at exactly
           A5 proportions. Doesn't affect print output. */
        @media screen {
            .page {
                width: 148mm;
                height: 210mm;
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

<body class="format-pad">

    @php
        $allItems = $invoice->items;
        $groups = [];
        foreach ($allItems as $item) {
            $ch = $item->challan_no ?? '';
            if (empty($groups) || end($groups)['challan_no'] !== $ch) {
                $groups[] = ['challan_no' => $ch, 'items' => [$item]];
            } else {
                $groups[count($groups) - 1]['items'][] = $item;
            }
        }

        $flatRows = [];
        $serial = 0;
        foreach ($groups as $g) {
            $count = count($g['items']);
            foreach ($g['items'] as $j => $item) {
                $serial++;
                $flatRows[] = [
                    'serial' => $serial,
                    'item' => $item,
                    'group_challan' => $g['challan_no'],
                    'is_first_of_group' => $j === 0,
                    'group_size' => $count,
                ];
            }
        }

        $perPage = 15;
        $chunks = collect($flatRows)->chunk($perPage);
        $pages = $chunks->isEmpty() ? [collect()] : $chunks->values()->all();
        $totalPages = count($pages);
        $grandQty = $allItems->sum('qty');

        // Distinct units across all items (case-insensitive) for the Grand Total cell
        $uniqueUnits = $allItems
            ->pluck('unit')
            ->filter(fn($u) => $u !== null && $u !== '')
            ->map(fn($u) => trim((string) $u))
            ->filter()
            ->unique(fn($u) => strtoupper($u))
            ->values();
        $unitLabel = $uniqueUnits->isEmpty() ? '' : $uniqueUnits->implode(' / ');

        // Per-unit breakdown as a tooltip when units are mixed
        $qtyByUnit = $allItems->groupBy('unit')->map(fn($g, $u) => (int) $g->sum('qty') . ' ' . $u);
    @endphp

    @foreach ($pages as $pageIdx => $pageItems)
        @php $isLastPage = ($pageIdx === $totalPages - 1); @endphp

        <div class="page">
            @include('partials.print-header', ['docTitle' => 'BILL'])

            <div class="doc-meta">
                <div class="meta-item">Invoice No : {{ $invoice->invoice_no }}</div>
                <div class="meta-item right">Date : {{ $invoice->invoice_date->format('d-M-Y') }}</div>
            </div>

            <div class="buyer-block">
                <div class="buyer-left">
                    <div class="company">{{ $invoice->customer->company_name }}</div>
                    <div>{{ $invoice->customer->address }}</div>
                    @if ($invoice->customer->attention)
                        <div class="attention">Attention {{ $invoice->customer->attention }}</div>
                    @endif
                </div>
                <div class="buyer-right">
                    @if ($invoice->customer_name)
                        Buyer Name: {{ $invoice->customer_name }}
                    @endif
                </div>
            </div>

            <table class="items">
                <colgroup>
                    <col style="width: 5.5%;">
                    <col style="width: 21%;">
                    <col style="width: 17%;">
                    <col style="width: 12%;">
                    <col style="width: 7%;">
                    <col style="width: 7%;">
                    <col style="width: 12%;">
                    <col style="width: 18.5%;">
                </colgroup>
                <thead>
                    <tr>
                        <th>SL</th>
                        <th>Item Description</th>
                        <th>Style Name</th>
                        <th>Challan No</th>
                        <th>Qty</th>
                        <th>Unit</th>
                        <th>Rate</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pageItems as $row)
                        <tr>
                            <td class="center">{{ $row['serial'] }}</td>
                            <td>{{ $row['item']->item_description }}</td>
                            <td>{{ $row['item']->style_name ?? '' }}</td>

                            @if ($row['is_first_of_group'])
                                <td class="center" rowspan="{{ $row['group_size'] }}">
                                    {{ $row['group_challan'] }}
                                </td>
                            @endif

                            <td class="center">{{ $row['item']->qty }}</td>
                            <td class="center">{{ $row['item']->unit }}</td>
                            {{-- Rate: up to 9 decimals, trailing zeros trimmed --}}
                            <td class="right">
                                {{ rtrim(rtrim(number_format($row['item']->rate, 9, '.', ''), '0'), '.') ?: '0' }}
                            </td>
                            <td class="right">{{ number_format($row['item']->amount, 2) }}</td>
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
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                    @endfor

                    @if ($isLastPage)
                        <tr>
                            <td colspan="4" class="center" style="font-weight:400;">Grand Total</td>
                            {{-- Qty --}}
                            <td class="center" title="{{ $qtyByUnit->implode(' + ') }}">
                                {{ $grandQty }}
                            </td>
                            {{-- Unit label (deduped, slash-joined if mixed) --}}
                            <td class="center" title="{{ $uniqueUnits->implode(', ') }}">
                                {{ $unitLabel }}
                            </td>
                            {{-- Rate cell stays empty --}}
                            <td></td>
                            {{-- Amount with currency symbol --}}
                            <td class="right">
                                {{ $invoice->currency_symbol }} {{ number_format($invoice->total_amount, 2) }}
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>

            @if ($isLastPage)
                <div class="amount-words">
                    Amount In Words : {{ $invoice->amount_in_words }}
                </div>

                {{-- Payment Note --}}
                <div class="amount-words" style="margin-top:3px; font-style:normal;">
                    <strong>Payment Note :</strong> Payment of the invoice value must be made in Bangladeshi Taka Cash
                    or Cheque.
                </div>

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
    @endforeach

    <div class="no-print" style="text-align:center; padding:20px;">
        <button onclick="window.print()"
            style="background:#C8102E; color:#fff; padding:10px 28px; border:none; border-radius:6px; font-size:14px; cursor:pointer;">
            Print (Pad A5)
        </button>
        <button onclick="downloadPadPdf()"
            style="margin-left:10px; background:#059669; color:#fff; padding:10px 28px; border:none; border-radius:6px; font-size:14px; cursor:pointer;">
            Download PDF
        </button>
        <a href="{{ route('invoices.print-a4', $invoice) }}"
            style="margin-left:10px; background:#1e293b; color:#fff; padding:10px 28px; border-radius:6px; font-size:14px; text-decoration:none;">
            Print (A4)
        </a>
        <a href="{{ route('invoices.index') }}"
            style="margin-left:10px; background:#e2e8f0; color:#1e293b; padding:10px 28px; border-radius:6px; font-size:14px; text-decoration:none; display:inline-block;">
            ← Back to Invoice List
        </a>
    </div>

    <script>
        async function downloadPadPdf() {
            const noPrintEls = document.querySelectorAll('.no-print');
            noPrintEls.forEach(el => el.style.display = 'none');

            try {
                const pages = document.querySelectorAll('.page');
                const {
                    jsPDF
                } = window.jspdf;

                // A5 portrait: 148mm x 210mm
                const pdf = new jsPDF({
                    unit: 'mm',
                    format: [148, 210],
                    orientation: 'portrait'
                });
                const pdfW = pdf.internal.pageSize.getWidth(); // 148
                const pdfH = pdf.internal.pageSize.getHeight(); // 210

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
                    if (i > 0) pdf.addPage([148, 210], 'portrait');
                    pdf.addImage(imgData, 'JPEG', 0, 0, pdfW, pdfH);
                }

                pdf.save('{{ $invoice->invoice_no }}-pad.pdf');
            } finally {
                noPrintEls.forEach(el => el.style.display = '');
            }
        }
    </script>

</body>

</html>
