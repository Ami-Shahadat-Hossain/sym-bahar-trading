@extends('layouts.app')
@section('title', 'Create Money Receipt')

@section('content')
    <div class="bg-white rounded-xl shadow-md p-6 max-w-3xl">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-xl font-bold text-slate-800">Create Money Receipt</h2>
                <p class="text-xs text-slate-500 mt-1">Select an invoice and enter the received amount</p>
            </div>
            <a href="{{ route('receipts.index') }}" class="text-sm text-slate-500 hover:text-slate-800 transition">← Back to
                list</a>
        </div>

        @if ($errors->any())
            <div class="bg-red-50 border-l-4 border-red-600 text-red-800 text-sm px-4 py-3 rounded mb-5">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('receipts.store') }}">
            @csrf

            {{-- Row 1 --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Receipt No</label>
                    <input type="text" value="{{ \App\Models\MoneyReceipt::generateReceiptNo() }}" readonly
                        class="w-full border border-slate-300 rounded-lg px-3 py-2.5 bg-slate-100 text-slate-600 font-mono text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Date <span
                            class="text-red-600">*</span></label>
                    <input type="date" name="receipt_date" value="{{ old('receipt_date', date('Y-m-d')) }}" required
                        class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none transition">
                </div>
            </div>

            {{-- Invoice --}}
            <div class="mb-5">
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Invoice No <span
                        class="text-red-600">*</span></label>
                <select name="invoice_id" required id="invoiceSelect"
                    class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none transition">
                    <option value="">— Select Invoice —</option>
                    @foreach ($invoices as $inv)
                        <option value="{{ $inv->id }}" data-customer="{{ $inv->customer->company_name }}"
                            data-customer-name="{{ $inv->customer_name }}" data-amount="{{ $inv->total_amount }}"
                            data-invoice-no="{{ $inv->invoice_no }}" @selected(old('invoice_id') == $inv->id)>
                            {{ $inv->invoice_no }} — {{ $inv->customer->company_name }}
                            ($ {{ number_format($inv->total_amount, 2) }})
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Received from --}}
            <div class="mb-5">
                <label class="block text-sm font-medium text-slate-700 mb-1.5">
                    Received From (Customer Name) <span class="text-red-600">*</span>
                </label>
                <input type="text" name="received_from" id="receivedFrom" required value="{{ old('received_from') }}"
                    placeholder="Auto-filled when you select an invoice"
                    class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none transition">
            </div>

            {{-- Amount + Against bill --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">
                        Amount ($) <span class="text-red-600">*</span>
                    </label>
                    <input type="number" name="amount" id="amountInput" step="0.01" min="0.01" required
                        value="{{ old('amount') }}"
                        class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none transition">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Against Bill No / Purpose</label>
                    <input type="text" name="against_bill_no" id="againstBillInput" value="{{ old('against_bill_no') }}"
                        class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none transition">
                </div>
            </div>

            {{-- Mode --}}
            <div class="mb-6">
                <label class="block text-sm font-medium text-slate-700 mb-2">
                    Mode <span class="text-red-600">*</span>
                </label>
                <div class="flex flex-wrap gap-2">
                    @foreach (['Cash', 'Cheque', 'Bank Transfer', 'Bkash', 'Nagad', 'Bangla QR'] as $m)
                        <label class="cursor-pointer">
                            <input type="radio" name="mode" value="{{ $m }}" class="peer hidden"
                                @checked(old('mode', 'Cash') === $m)>
                            <div
                                class="px-4 py-2 rounded-lg border-2 border-slate-200 text-sm font-medium text-slate-600
                                    peer-checked:border-red-600 peer-checked:bg-red-50 peer-checked:text-red-700
                                    hover:border-slate-300 transition">
                                {{ $m }}
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- Buttons --}}
            <div class="flex gap-3 pt-4 border-t border-slate-200">
                <button type="submit"
                    class="bg-gradient-to-r from-red-600 to-red-800 hover:from-red-700 hover:to-red-900 text-white px-6 py-2.5 rounded-lg text-sm font-semibold shadow-md hover:shadow-lg transition-all duration-200 transform hover:-translate-y-0.5 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Generate Receipt
                </button>
                <a href="{{ route('receipts.index') }}"
                    class="bg-slate-200 hover:bg-slate-300 text-slate-800 px-6 py-2.5 rounded-lg text-sm font-medium transition">
                    Cancel
                </a>
            </div>
        </form>
    </div>

    <script>
        document.getElementById('invoiceSelect').addEventListener('change', function() {
            const opt = this.options[this.selectedIndex];

            document.getElementById('receivedFrom').value = opt.dataset.customer || '';
            document.getElementById('amountInput').value = opt.dataset.amount || '';
            document.getElementById('againstBillInput').value = opt.dataset.invoiceNo || '';
        });
    </script>
@endsection
