<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Delivery Challan (Pad) {{ $challan->challan_no }}</title>
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
        $perPage = 15;
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
                    <col style="width: 6%;">
                    <col style="width: 36%;">
                    <col style="width: 32%;">
                    <col style="width: 13%;">
                    <col style="width: 13%;">
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
            Print (Pad A5)
        </button>
        <a href="{{ route('challans.print-a4', $challan) }}"
            style="margin-left:10px; background:#1e293b; color:#fff; padding:10px 28px; border-radius:6px; font-size:14px; text-decoration:none;">
            Print (A4)
        </a>
        <a href="{{ route('challans.index') }}"
            style="margin-left:10px; background:#e2e8f0; color:#1e293b; padding:10px 28px; border-radius:6px; font-size:14px; text-decoration:none; display:inline-block;">
            ← Back to Challan List
        </a>
    </div>

</body>

</html>
