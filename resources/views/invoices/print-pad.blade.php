<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Invoice (Pad) {{ $invoice->invoice_no }}</title>
    @include('partials.print-styles')
    <style>
        @media print {
            @page {
                size: 148mm 210mm;
                margin: 0;
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
    @endphp

    @foreach ($pages as $pageIdx => $pageItems)
        @php $isLastPage = ($pageIdx === $totalPages - 1); @endphp

        <div class="page">
            @include('partials.print-header', ['docTitle' => 'INVOICE'])

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
                            <td class="right">{{ number_format($row['item']->rate, 2) }}</td>
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
                            <td class="center">{{ $grandQty }}</td>
                            <td></td>
                            <td></td>
                            <td class="right">{{ number_format($invoice->total_amount, 2) }}</td>
                        </tr>
                    @endif
                </tbody>
            </table>

            @if ($isLastPage)
                <div class="amount-words">
                    Amount In Words : {{ $invoice->amount_in_words }}
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
        <a href="{{ route('invoices.print-a4', $invoice) }}"
            style="margin-left:10px; background:#1e293b; color:#fff; padding:10px 28px; border-radius:6px; font-size:14px; text-decoration:none;">
            Print (A4)
        </a>
        <a href="{{ route('invoices.index') }}"
            style="margin-left:10px; background:#e2e8f0; color:#1e293b; padding:10px 28px; border-radius:6px; font-size:14px; text-decoration:none; display:inline-block;">
            ← Back to Invoice List
        </a>
    </div>

</body>

</html>
