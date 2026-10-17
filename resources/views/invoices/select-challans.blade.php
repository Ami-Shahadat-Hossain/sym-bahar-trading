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
            <p class="text-red-600">No challans found for this customer in the selected month.</p>
        @else
            <form method="POST" action="{{ route('invoices.store') }}">
                @csrf
                <input type="hidden" name="customer_id" value="{{ $customer->id }}">
                <input type="hidden" name="invoice_date" value="{{ date('Y-m-d') }}">

                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Customer / Buyer Name</label>
                    <input type="text" name="customer_name" value="{{ $customer->customer_name }}"
                        class="w-full md:w-1/3 border rounded px-3 py-2 text-sm">
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
                            <tr>
                                <td class="px-2 py-1 border text-center">
                                    <input type="checkbox" name="challan_ids[]" value="{{ $ch->id }}"
                                        class="challan-check" data-amount="{{ $ch->total_amount }}">
                                </td>
                                <td class="px-2 py-1 border font-mono">{{ $ch->challan_no }}</td>
                                <td class="px-2 py-1 border">{{ $ch->challan_date->format('d-M-Y') }}</td>
                                <td class="px-2 py-1 border text-xs">
                                    @foreach ($ch->items as $it)
                                        {{ $it->item_description }} ({{ $it->qty }} {{ $it->unit }})@if (!$loop->last)
                                            ,
                                        @endif
                                    @endforeach
                                </td>
                                <td class="px-2 py-1 border text-right">
                                    {{ number_format($ch->total_amount, 2) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="flex justify-between items-center mb-4">
                    <div class="text-sm">
                        <span class="font-medium">Selected Total : </span>
                        <span class="text-lg font-bold" id="selectedTotal">$ 0.00</span>
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
                updateTotal();
            });
        }
        document.querySelectorAll('.challan-check').forEach(c => {
            c.addEventListener('change', updateTotal);
        });

        function updateTotal() {
            let total = 0;
            document.querySelectorAll('.challan-check:checked').forEach(c => {
                total += parseFloat(c.dataset.amount);
            });
            document.getElementById('selectedTotal').textContent = '$ ' + total.toFixed(2);
        }
    </script>
@endsection
