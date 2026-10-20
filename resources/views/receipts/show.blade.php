@extends('layouts.app')
@section('title', 'Receipt ' . $receipt->receipt_no)

@section('content')
    <div class="bg-white rounded-xl shadow-md p-6 max-w-3xl">
        {{-- Header --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-3 mb-6">
            <div>
                <h2 class="text-xl font-bold text-slate-800">
                    Money Receipt
                    <span class="ml-2 text-xs font-medium px-2 py-0.5 rounded bg-slate-100 text-slate-700">
                        {{ $receipt->currency ?? 'USD' }}
                    </span>
                </h2>
                <p class="text-sm text-slate-500 font-mono mt-1">{{ $receipt->receipt_no }}</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('receipts.edit', $receipt) }}"
                    class="bg-amber-600 hover:bg-amber-700 text-white px-4 py-2 rounded-lg text-sm font-medium shadow flex items-center gap-1.5 transition">
                    ✎ Edit
                </a>
                <a href="{{ route('receipts.print-a4', $receipt) }}"
                    class="bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded-lg text-sm font-medium shadow flex items-center gap-1.5 transition"
                    data-tip="Print 2 copies on A4">
                    🖨 Print (A4)
                </a>
                <a href="{{ route('receipts.print-pad', $receipt) }}"
                    class="bg-red-700 hover:bg-red-800 text-white px-4 py-2 rounded-lg text-sm font-medium shadow flex items-center gap-1.5 transition"
                    data-tip="Print on Pad / A5">
                    🖨 Print (Pad)
                </a>
                <a href="{{ route('receipts.index') }}"
                    class="bg-slate-200 hover:bg-slate-300 text-slate-800 px-4 py-2 rounded-lg text-sm font-medium transition">
                    ← Back
                </a>
            </div>
        </div>

        {{-- Meta --}}
        <div class="grid grid-cols-2 gap-4 text-sm mb-6 p-4 bg-slate-50 rounded-lg">
            <div>
                <span class="text-slate-500 text-xs block mb-0.5">Invoice No</span>
                <a href="{{ route('invoices.show', $receipt->invoice) }}"
                    class="font-mono text-blue-600 hover:underline">{{ $receipt->invoice->invoice_no }}</a>
            </div>
            <div>
                <span class="text-slate-500 text-xs block mb-0.5">Date</span>
                <span class="font-medium">{{ $receipt->receipt_date->format('d-M-Y') }}</span>
            </div>
            <div>
                <span class="text-slate-500 text-xs block mb-0.5">Mode</span>
                <span class="inline-block text-xs px-2 py-0.5 rounded-full bg-blue-100 text-blue-700 font-medium">
                    {{ $receipt->mode }}
                </span>
            </div>
            <div>
                <span class="text-slate-500 text-xs block mb-0.5">Company</span>
                <span class="font-medium">{{ $receipt->invoice->customer->company_name ?? '—' }}</span>
            </div>
        </div>

        {{-- Received from --}}
        <div class="mb-5">
            <p class="text-xs text-slate-500 uppercase tracking-wider mb-1">Received With Thanks From</p>
            <p class="text-lg font-semibold text-slate-800">{{ $receipt->received_from }}</p>
        </div>

        {{-- Amount --}}
        <div class="text-center border-2 border-slate-200 rounded-xl py-6 mb-5 bg-gradient-to-br from-slate-50 to-white">
            <p class="text-xs text-slate-500 uppercase tracking-widest mb-2">Amount Received</p>
            <p class="text-4xl font-bold text-emerald-700">
                <span class="text-xl font-medium text-slate-600">{{ $receipt->currency_symbol }}</span>
                {{ number_format($receipt->amount, 2) }}
            </p>
        </div>

        {{-- Amount in words --}}
        <div class="bg-slate-50 border-l-4 border-slate-700 p-4 rounded-r-lg mb-5">
            <p class="text-xs text-slate-500 uppercase tracking-wider mb-1">In Words</p>
            <p class="text-sm italic font-medium text-slate-800">{{ $receipt->amount_in_words }}</p>
        </div>

        {{-- Against bill --}}
        @if ($receipt->against_bill_no)
            <div class="text-sm">
                <span class="text-slate-500">Against Bill No / Purpose :</span>
                <span class="font-medium text-slate-800">{{ $receipt->against_bill_no }}</span>
            </div>
        @endif
    </div>
@endsection
