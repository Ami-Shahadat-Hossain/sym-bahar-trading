@extends('layouts.app')
@section('title', 'Invoice ' . $invoice->invoice_no)

@section('content')
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-xl font-bold">Invoice {{ $invoice->invoice_no }}</h2>
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
                            <td class="px-2 py-2 border text-right">{{ number_format($item->rate, 2) }}</td>
                            <td class="px-2 py-2 border text-right">{{ number_format($item->amount, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="bg-gray-100 font-semibold">
                        <td colspan="7" class="px-2 py-2 border text-right">Grand Total :</td>
                        <td class="px-2 py-2 border text-right">
                            $ {{ number_format($invoice->total_amount, 2) }}
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
