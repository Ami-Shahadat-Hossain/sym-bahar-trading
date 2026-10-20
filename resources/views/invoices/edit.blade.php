@extends('layouts.app')
@section('title', 'Edit Invoice ' . $invoice->invoice_no)

@section('content')
    <div class="bg-white rounded-lg shadow p-6" x-data="invoiceForm()">
        <form method="POST" action="{{ route('invoices.update', $invoice) }}">
            @csrf
            @method('PUT')

            {{-- Header --}}
            <div class="flex flex-wrap justify-between items-center mb-6 gap-3">
                <div>
                    <h2 class="text-xl font-bold">
                        Edit Invoice {{ $invoice->invoice_no }}
                        <span class="ml-2 text-xs font-medium px-2 py-0.5 rounded bg-slate-100 text-slate-700"
                            x-text="currency"></span>
                    </h2>
                    <p class="text-sm text-gray-500">
                        Created {{ $invoice->created_at->format('d-M-Y') }}
                    </p>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('invoices.show', $invoice) }}"
                        class="bg-gray-500 text-white px-4 py-2 rounded text-sm hover:bg-gray-600">
                        View
                    </a>
                    <a href="{{ route('invoices.index') }}" class="bg-gray-300 text-gray-800 px-4 py-2 rounded text-sm">
                        Cancel
                    </a>
                </div>
            </div>

            {{-- Basic info --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div>
                    <label class="block text-sm font-medium mb-1">Invoice No</label>
                    <input type="text" value="{{ $invoice->invoice_no }}" readonly
                        class="w-full border rounded px-3 py-2 bg-gray-100 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Invoice Date</label>
                    <input type="date" name="invoice_date" value="{{ $invoice->invoice_date->format('Y-m-d') }}" required
                        class="w-full border rounded px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Customer / Company</label>
                    <select name="customer_id" required class="w-full border rounded px-3 py-2 text-sm">
                        @foreach ($customers as $c)
                            <option value="{{ $c->id }}" @selected($c->id == $invoice->customer_id)>
                                {{ $c->company_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Customer / Buyer Name</label>
                    <input type="text" name="customer_name" value="{{ $invoice->customer_name }}"
                        class="w-full border rounded px-3 py-2 text-sm">
                </div>
            </div>

            {{-- Currency --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div>
                    <label class="block text-sm font-medium mb-1">Currency</label>
                    <select name="currency" x-model="currency" class="w-full border rounded px-3 py-2 text-sm">
                        <option value="USD">Dollar ($)</option>
                        <option value="BDT">BDT (৳)</option>
                    </select>
                    <p class="text-xs text-gray-500 mt-1">
                        All selected challans must share the same currency.
                    </p>
                </div>
            </div>

            {{-- Challan selection --}}
            <div class="mb-3">
                <h3 class="text-sm font-semibold text-slate-700">Challans included in this invoice</h3>
                <p class="text-xs text-gray-500">
                    Checked challans (green) are currently included. Uncheck to remove their items.
                    Check other challans of the same customer to add them.
                </p>
            </div>

            @php
                $attachedIds = $invoice->challans->pluck('id')->all();

                $allChallans = \App\Models\Challan::with('items')
                    ->where('customer_id', $invoice->customer_id)
                    ->orderBy('challan_date', 'desc')
                    ->orderBy('id', 'desc')
                    ->get();
            @endphp

            <div class="overflow-x-auto mb-6">
                <table class="w-full text-sm border">
                    <thead class="bg-slate-800 text-white">
                        <tr>
                            <th class="px-2 py-1 border w-10">
                                <input type="checkbox" onclick="toggleAllChallans(this)" title="Select all">
                            </th>
                            <th class="px-2 py-1 border">Challan No</th>
                            <th class="px-2 py-1 border">Date</th>
                            <th class="px-2 py-1 border">Items</th>
                            <th class="px-2 py-1 border text-right">Amount</th>
                            <th class="px-2 py-1 border text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($allChallans as $ch)
                            @php
                                $isIncluded = in_array($ch->id, $attachedIds);
                                $qtyByUnit = $ch->items
                                    ->groupBy('unit')
                                    ->map(fn($items) => (int) $items->sum('qty'))
                                    ->toArray();
                            @endphp
                            <tr class="{{ $isIncluded ? 'bg-emerald-50' : '' }}">
                                <td class="px-2 py-1 border text-center">
                                    <input type="checkbox" name="challan_ids[]" value="{{ $ch->id }}"
                                        class="challan-check" data-amount="{{ $ch->total_amount }}"
                                        data-currency="{{ $ch->currency ?? 'USD' }}"
                                        data-qty-by-unit='@json($qtyByUnit)' @checked($isIncluded)>
                                </td>
                                <td class="px-2 py-1 border font-mono">
                                    {{ $ch->challan_no }}
                                    <span class="ml-1 text-[10px] px-1 rounded bg-slate-100 text-slate-700">
                                        {{ $ch->currency ?? 'USD' }}
                                    </span>
                                </td>
                                <td class="px-2 py-1 border">{{ $ch->challan_date->format('d-M-Y') }}</td>
                                <td class="px-2 py-1 border text-xs">
                                    @foreach ($ch->items as $it)
                                        {{ $it->item_description }} ({{ $it->qty }} {{ $it->unit }})@if (!$loop->last)
                                            ,
                                        @endif
                                    @endforeach
                                </td>
                                <td class="px-2 py-1 border text-right">
                                    {{ $ch->currency_symbol }} {{ number_format($ch->total_amount, 2) }}
                                </td>
                                <td class="px-2 py-1 border text-center text-xs">
                                    @if ($isIncluded)
                                        <span class="text-emerald-700 font-semibold">Included</span>
                                    @else
                                        <span class="text-gray-500">Available</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-2 py-4 text-center text-gray-500">
                                    No challans for this customer yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Selected Total + Total Qty (live) --}}
            <div class="flex justify-end mb-6">
                <div class="text-sm space-y-1 text-right">
                    <div>
                        <span class="font-medium">Total Quantity : </span>
                        <span class="text-lg font-bold" id="selectedQty">0</span>
                    </div>
                    <div>
                        <span class="font-medium">Selected Total : </span>
                        <span class="text-lg font-bold" id="selectedTotal"></span>
                    </div>
                </div>
            </div>

            {{-- Items Preview (read-only) --}}
            <div class="mb-3">
                <h3 class="text-sm font-semibold text-slate-700">Current Items</h3>
                <p class="text-xs text-gray-500">
                    Items will be regenerated from the checked challans when you save.
                </p>
            </div>

            <div class="overflow-x-auto mb-6">
                <table class="w-full text-sm border">
                    <thead class="bg-slate-700 text-white">
                        <tr>
                            <th class="px-2 py-1 border text-center w-12">SL</th>
                            <th class="px-2 py-1 border text-left">Item Description</th>
                            <th class="px-2 py-1 border text-left">Style</th>
                            <th class="px-2 py-1 border text-left">Challan</th>
                            <th class="px-2 py-1 border text-center">Qty</th>
                            <th class="px-2 py-1 border text-center">Unit</th>
                            <th class="px-2 py-1 border text-right">Rate</th>
                            <th class="px-2 py-1 border text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($invoice->items as $i => $item)
                            <tr class="hover:bg-gray-50">
                                <td class="px-2 py-1 border text-center">{{ $i + 1 }}</td>
                                <td class="px-2 py-1 border">{{ $item->item_description }}</td>
                                <td class="px-2 py-1 border">{{ $item->style_name ?? '—' }}</td>
                                <td class="px-2 py-1 border font-mono text-xs">{{ $item->challan_no ?? '—' }}</td>
                                <td class="px-2 py-1 border text-center">{{ $item->qty }}</td>
                                <td class="px-2 py-1 border text-center">{{ $item->unit }}</td>
                                <td class="px-2 py-1 border text-right">
                                    {{ rtrim(rtrim(number_format($item->rate, 9, '.', ''), '0'), '.') ?: '0' }}
                                </td>
                                <td class="px-2 py-1 border text-right">{{ number_format($item->amount, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-2 py-4 text-center text-gray-500">No items yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="bg-gray-100 font-semibold">
                            <td colspan="7" class="px-2 py-2 border text-right">Grand Total :</td>
                            <td class="px-2 py-2 border text-right">
                                {{ $invoice->currency_symbol }} {{ number_format($invoice->total_amount, 2) }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            {{-- Amount in words (current) --}}
            <div class="bg-slate-50 border-l-4 border-slate-700 p-3 text-sm italic mb-6">
                <strong>Amount In Words :</strong> {{ $invoice->amount_in_words }}
            </div>

            {{-- Submit --}}
            <div class="flex gap-3">
                <button type="submit" class="bg-green-700 text-white px-6 py-2 rounded text-sm hover:bg-green-800">
                    Update Invoice
                </button>
                <a href="{{ route('invoices.show', $invoice) }}"
                    class="bg-gray-300 text-gray-800 px-6 py-2 rounded text-sm">
                    Cancel
                </a>
            </div>
        </form>
    </div>

    <script>
        function invoiceForm() {
            return {
                currency: '{{ $invoice->currency ?? 'USD' }}'
            };
        }

        function toggleAllChallans(cb) {
            document.querySelectorAll('.challan-check').forEach(c => {
                c.checked = cb.checked;
            });
            updateInvoiceTotal();
        }

        document.querySelectorAll('.challan-check').forEach(c => {
            c.addEventListener('change', updateInvoiceTotal);
        });

        function updateInvoiceTotal() {
            let amount = 0;
            const currencies = new Set();
            const qtyByUnit = {};

            document.querySelectorAll('.challan-check:checked').forEach(c => {
                amount += parseFloat(c.dataset.amount) || 0;
                currencies.add(c.dataset.currency || 'USD');

                let map = {};
                try {
                    map = JSON.parse(c.dataset.qtyByUnit || '{}');
                } catch (e) {
                    map = {};
                }

                for (const [unit, qty] of Object.entries(map)) {
                    qtyByUnit[unit] = (qtyByUnit[unit] || 0) + (parseFloat(qty) || 0);
                }
            });

            // ---- Amount display ----
            const amountEl = document.getElementById('selectedTotal');
            if (currencies.size === 0) {
                amountEl.textContent = '$ 0.00';
                amountEl.classList.remove('text-red-600');
            } else if (currencies.size === 1) {
                const cur = currencies.values().next().value;
                const symbol = cur === 'BDT' ? '৳' : '$';
                amountEl.textContent = symbol + ' ' + amount.toFixed(2);
                amountEl.classList.remove('text-red-600');
            } else {
                amountEl.textContent = 'Mixed currencies — pick one';
                amountEl.classList.add('text-red-600');
            }

            // ---- Qty display ----
            const qtyEl = document.getElementById('selectedQty');
            const entries = Object.entries(qtyByUnit);
            const totalQty = entries.reduce((s, [, q]) => s + q, 0);

            if (entries.length === 0 || totalQty === 0) {
                qtyEl.textContent = '0';
            } else if (entries.length === 1) {
                qtyEl.textContent = totalQty + ' ' + entries[0][0];
            } else {
                const breakdown = entries.map(([u, q]) => q + ' ' + u).join(' + ');
                qtyEl.textContent = totalQty + ' (' + breakdown + ')';
            }
        }

        document.addEventListener('DOMContentLoaded', updateInvoiceTotal);
    </script>
@endsection
