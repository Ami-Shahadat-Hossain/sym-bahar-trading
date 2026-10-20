@extends('layouts.app')
@section('title', 'Select Challans for Invoice')

@section('content')
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-bold mb-4">
            Select Challans — {{ $customer->company_name }}
            <span class="text-sm font-normal text-gray-500">
                ({{ \Carbon\Carbon::create($request->year, $request->month)->format('F Y') }})
            </span>
        </h2>

        @if ($challans->isEmpty())
            {{-- Empty-state: could be no challans OR all are already invoiced --}}
            @php
                $invoicedCount = \App\Models\Challan::where('customer_id', $customer->id)
                    ->whereMonth('challan_date', $request->month)
                    ->whereYear('challan_date', $request->year)
                    ->whereHas('invoices')
                    ->count();
            @endphp

            <div class="bg-amber-50 border-l-4 border-amber-500 text-amber-800 text-sm px-4 py-3 rounded mb-4">
                @if ($invoicedCount > 0)
                    <strong>All challans for this month are already invoiced.</strong>
                    ({{ $invoicedCount }} challan{{ $invoicedCount === 1 ? '' : 's' }} already linked to an invoice.)
                    <div class="mt-2">
                        <a href="{{ route('invoices.index') }}" class="text-blue-600 hover:underline">
                            → View existing invoices
                        </a>
                        <span class="mx-2 text-slate-400">|</span>
                        <a href="{{ route('challans.index') }}" class="text-blue-600 hover:underline">
                            → Create a new challan
                        </a>
                    </div>
                @else
                    <strong>No challans found</strong> for this customer in the selected month.
                    <div class="mt-2">
                        <a href="{{ route('challans.create') }}" class="text-blue-600 hover:underline">
                            → Create a new challan
                        </a>
                    </div>
                @endif
            </div>
        @else
            <form method="POST" action="{{ route('invoices.store') }}">
                @csrf
                <input type="hidden" name="customer_id" value="{{ $customer->id }}">

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Customer / Buyer Name</label>
                        <input type="text" name="customer_name" value="{{ $customer->customer_name }}"
                            class="w-full border rounded px-3 py-2 text-sm">
                    </div>

                    {{-- Invoice Date — now user-selectable, defaults to today --}}
                    <div>
                        <label class="block text-sm font-medium mb-1">Invoice Date</label>
                        <input type="date" name="invoice_date" value="{{ date('Y-m-d') }}" required
                            class="w-full border rounded px-3 py-2 text-sm">
                    </div>
                </div>

                <table class="w-full text-sm border mb-4">
                    <thead class="bg-slate-800 text-white">
                        <tr>
                            <th class="px-2 py-1 border w-10">
                                <input type="checkbox" onclick="toggleAll(this)">
                            </th>
                            <th class="px-2 py-1 border">Challan No</th>
                            <th class="px-2 py-1 border">Date</th>
                            <th class="px-2 py-1 border">Items</th>
                            <th class="px-2 py-1 border text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($challans as $ch)
                            @php
                                // qty summed per unit for this challan
                                $qtyByUnit = $ch->items
                                    ->groupBy('unit')
                                    ->map(fn($items) => (int) $items->sum('qty'))
                                    ->toArray();
                            @endphp
                            <tr>
                                <td class="px-2 py-1 border text-center">
                                    <input type="checkbox" name="challan_ids[]" value="{{ $ch->id }}"
                                        class="challan-check" data-amount="{{ $ch->total_amount }}"
                                        data-currency="{{ $ch->currency ?? 'USD' }}"
                                        data-qty-by-unit='@json($qtyByUnit)'>
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
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="flex justify-between items-center mb-4">
                    <div class="text-sm space-y-1">
                        <div>
                            <span class="font-medium">Total Quantity : </span>
                            <span class="text-lg font-bold" id="selectedQty">0</span>
                        </div>
                        <div>
                            <span class="font-medium">Selected Total : </span>
                            <span class="text-lg font-bold" id="selectedTotal">$ 0.00</span>
                        </div>
                    </div>
                    <button type="submit" class="bg-green-700 text-white px-6 py-2 rounded text-sm hover:bg-green-800">
                        Generate Invoice
                    </button>
                </div>
            </form>
        @endif
    </div>

    <script>
        function toggleAll(cb) {
            document.querySelectorAll('.challan-check').forEach(c => {
                c.checked = cb.checked;
            });
            updateTotal();
        }

        document.querySelectorAll('.challan-check').forEach(c => {
            c.addEventListener('change', updateTotal);
        });

        function updateTotal() {
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
    </script>
@endsection
