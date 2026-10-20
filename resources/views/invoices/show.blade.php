@extends('layouts.app')
@section('title', 'Invoice ' . $invoice->invoice_no)

@section('content')
    @php
        // Distinct units across all invoice items (case-insensitive)
        $uniqueUnits = $invoice->items
            ->pluck('unit')
            ->filter(fn($u) => $u !== null && $u !== '')
            ->map(fn($u) => trim((string) $u))
            ->filter()
            ->unique(fn($u) => strtoupper($u))
            ->values();
        $unitLabel = $uniqueUnits->isEmpty() ? '' : $uniqueUnits->implode(' / ');

        // Sum of all qty (matches print views)
        $totalQty = $invoice->items->sum('qty');

        // Per-unit breakdown, used as a tooltip when units are mixed
        $qtyByUnit = $invoice->items->groupBy('unit')->map(fn($g, $u) => (int) $g->sum('qty') . ' ' . $u);
    @endphp

    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-xl font-bold">
                    Invoice {{ $invoice->invoice_no }}
                    <span class="ml-2 text-xs font-medium px-2 py-0.5 rounded bg-slate-100 text-slate-700">
                        {{ $invoice->currency ?? 'USD' }}
                    </span>
                </h2>
                <p class="text-sm text-gray-500">{{ $invoice->invoice_date->format('d-M-Y') }}</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('invoices.print-a4', $invoice) }}"
                    class="bg-green-700 text-white px-4 py-2 rounded text-sm hover:bg-green-800">
                    Print A4
                </a>
                <a href="{{ route('receipts.create') }}"
                    class="bg-blue-700 text-white px-4 py-2 rounded text-sm hover:bg-blue-800">
                    Create Receipt
                </a>
                <a href="{{ route('invoices.index') }}" class="bg-gray-300 text-gray-800 px-4 py-2 rounded text-sm">
                    Back
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6 text-sm">
            <div>
                <h3 class="font-semibold text-gray-700 mb-1">Company</h3>
                <p class="font-medium">{{ $invoice->customer->company_name }}</p>
                <p class="text-gray-600">{{ $invoice->customer->address }}</p>
                @if ($invoice->customer->attention)
                    <p class="text-gray-600">Attention {{ $invoice->customer->attention }}</p>
                @endif
            </div>
            <div>
                @if ($invoice->customer_name)
                    <h3 class="font-semibold text-gray-700 mb-1">Buyer Name</h3>
                    <p>{{ $invoice->customer_name }}</p>
                @endif
                <h3 class="font-semibold text-gray-700 mt-2 mb-1">Challans Included</h3>
                <p class="font-mono text-xs">
                    {{ $invoice->challans->pluck('challan_no')->implode(', ') }}
                </p>
            </div>
        </div>

        <div class="overflow-x-auto mb-6">
            <table class="w-full text-sm border">
                <thead class="bg-slate-800 text-white">
                    <tr>
                        <th class="px-2 py-2 border text-center w-12">SL</th>
                        <th class="px-2 py-2 border text-left">Item Description</th>
                        <th class="px-2 py-2 border text-left">Style Name</th>
                        <th class="px-2 py-2 border text-center">Challan No</th>
                        <th class="px-2 py-2 border text-center">Qty</th>
                        <th class="px-2 py-2 border text-center">Unit</th>
                        <th class="px-2 py-2 border text-right">Rate</th>
                        <th class="px-2 py-2 border text-right">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($invoice->items as $i => $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-2 py-2 border text-center">{{ $i + 1 }}</td>
                            <td class="px-2 py-2 border">{{ $item->item_description }}</td>
                            <td class="px-2 py-2 border">{{ $item->style_name ?? '—' }}</td>
                            <td class="px-2 py-2 border text-center font-mono text-xs">
                                {{ $item->challan_no ?? '—' }}
                            </td>
                            <td class="px-2 py-2 border text-center">{{ $item->qty }}</td>
                            <td class="px-2 py-2 border text-center">{{ $item->unit }}</td>
                            <td class="px-2 py-2 border text-right">
                                {{ rtrim(rtrim(number_format($item->rate, 9, '.', ''), '0'), '.') ?: '0' }}</td>
                            <td class="px-2 py-2 border text-right">{{ number_format($item->amount, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="bg-gray-100 font-semibold">
                        <td colspan="4" class="px-2 py-2 border text-right">Grand Total :</td>
                        {{-- Total Qty, aligned under the Qty column --}}
                        <td class="px-2 py-2 border text-center" title="{{ $qtyByUnit->implode(' + ') }}">
                            {{ $totalQty }}</td>
                        {{-- Unit label, aligned under the Unit column --}}
                        <td class="px-2 py-2 border text-center" title="{{ $uniqueUnits->implode(', ') }}">
                            {{ $unitLabel }}</td>
                        {{-- Rate cell stays empty --}}
                        <td class="px-2 py-2 border"></td>
                        {{-- Grand Total amount --}}
                        <td class="px-2 py-2 border text-right">
                            {{ $invoice->currency_symbol }} {{ number_format($invoice->total_amount, 2) }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="bg-slate-50 border-l-4 border-slate-700 p-3 text-sm italic">
            <strong>Amount In Words :</strong> {{ $invoice->amount_in_words }}
        </div>
    </div>
@endsection
