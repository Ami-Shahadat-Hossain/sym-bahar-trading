@extends('layouts.app')
@section('title', 'Edit Challan')

@section('content')
    <div class="bg-white rounded-lg shadow p-6" x-data="challanForm()">
        <form method="POST" action="{{ route('challans.update', $challan) }}">
            @csrf
            @method('PUT')

            {{-- Title bar: heading + currency selector --}}
            <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
                <h2 class="text-xl font-bold">Edit Challan {{ $challan->challan_no }}</h2>

                <div class="flex items-center gap-2">
                    <label class="text-sm font-medium">Currency</label>
                    <select name="currency" x-model="currency" class="border rounded px-3 py-1.5 text-sm">
                        <option value="USD">Dollar ($)</option>
                        <option value="BDT">BDT (৳)</option>
                    </select>
                </div>
            </div>

            {{-- Row 1: Challan No, Date, Company, Buyer Name --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div>
                    <label class="block text-sm font-medium mb-1">Challan No</label>
                    <input type="text" value="{{ $challan->challan_no }}" readonly
                        class="w-full border rounded px-3 py-2 bg-gray-100 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Date</label>
                    <input type="date" name="challan_date" value="{{ $challan->challan_date->format('Y-m-d') }}" required
                        class="w-full border rounded px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Company Name</label>
                    <select name="customer_id" required class="w-full border rounded px-3 py-2 text-sm">
                        @foreach ($customers as $c)
                            <option value="{{ $c->id }}" @selected($c->id == $challan->customer_id)>
                                {{ $c->company_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Buyer Name</label>
                    <input type="text" name="buyer_name" value="{{ $challan->buyer_name }}"
                        class="w-full border rounded px-3 py-2 text-sm">
                </div>
            </div>

            {{-- Items table --}}
            <div class="overflow-x-auto mb-4">
                <table class="w-full text-sm border">
                    <thead class="bg-slate-800 text-white">
                        <tr>
                            <th class="px-2 py-1 border">SL</th>
                            <th class="px-2 py-1 border">Item Description</th>
                            <th class="px-2 py-1 border">Style Name</th>
                            <th class="px-2 py-1 border">Qty</th>
                            <th class="px-2 py-1 border">Unit</th>
                            <th class="px-2 py-1 border">
                                Rate (<span x-text="currencySymbol"></span>)
                            </th>
                            <th class="px-2 py-1 border">Amount</th>
                            <th class="px-2 py-1 border"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(item, index) in items" :key="index">
                            <tr>
                                <td class="px-2 py-1 border text-center" x-text="index + 1"></td>
                                <td class="px-2 py-1 border">
                                    <input type="text" :name="`items[${index}][item_description]`"
                                        x-model="item.item_description" required
                                        class="w-full border rounded px-2 py-1 text-xs">
                                </td>
                                <td class="px-2 py-1 border">
                                    <input type="text" :name="`items[${index}][style_name]`" x-model="item.style_name"
                                        class="w-full border rounded px-2 py-1 text-xs">
                                </td>
                                <td class="px-2 py-1 border">
                                    <input type="number" :name="`items[${index}][qty]`" x-model.number="item.qty"
                                        @input="calcAmount(item)" required min="1" step="1"
                                        class="w-20 border rounded px-2 py-1 text-xs">
                                </td>
                                <td class="px-2 py-1 border">
                                    <select :name="`items[${index}][unit]`" x-model="item.unit"
                                        class="w-24 border rounded px-2 py-1 text-xs">
                                        <option>PCS</option>
                                        <option>KG</option>
                                        <option>MTR</option>
                                        <option>YDS</option>
                                        <option>SET</option>
                                        <option>BOX</option>
                                        <option>CTN</option>
                                        <option>DOZEN</option>
                                        <option>GROSS</option>
                                        <option>ROLL</option>
                                        <option>CONE</option>
                                        <option>TON</option>
                                        <option>LTR</option>
                                        <option>BAG</option>
                                        <option>PAIR</option>
                                    </select>
                                </td>
                                <td class="px-2 py-1 border">
                                    {{-- 9 decimal places allowed in Rate --}}
                                    <input type="number" :name="`items[${index}][rate]`" x-model.number="item.rate"
                                        @input="calcAmount(item)" required step="0.000000001"
                                        class="w-32 border rounded px-2 py-1 text-xs">
                                </td>
                                <td class="px-2 py-1 border">
                                    {{-- Editable: auto-filled from qty × rate, or type your own --}}
                                    <div class="flex items-center gap-1">
                                        <input type="number" :name="`items[${index}][amount]`" x-model.number="item.amount"
                                            @input="item.manual = true; item.amount = round2(item.amount)" step="0.01"
                                            min="0" required
                                            class="w-24 border rounded px-2 py-1 text-xs text-right">
                                        <button type="button" x-show="item.manual" @click="resetAuto(item)"
                                            title="Back to auto amount" x-cloak
                                            class="text-blue-600 text-[10px] underline">auto</button>
                                    </div>
                                </td>
                                <td class="px-2 py-1 border text-center">
                                    <button type="button" @click="removeItem(index)"
                                        class="text-red-600 text-xs">✕</button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <div class="flex flex-wrap items-center justify-between gap-4 mb-4">
                <button type="button" @click="addItem()"
                    class="bg-blue-600 text-white px-4 py-2 rounded text-sm hover:bg-blue-700">
                    + Add Item
                </button>

                <div class="text-right space-y-1">
                    <div class="text-sm font-medium">
                        Total Quantity : <span class="font-bold" x-text="totalQty"></span>
                    </div>
                    <div class="text-sm font-medium">
                        Grand Total : <span class="text-lg font-bold"
                            x-text="currencySymbol + ' ' + grandTotal.toFixed(2)"></span>
                    </div>
                </div>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="bg-green-700 text-white px-6 py-2 rounded text-sm hover:bg-green-800">
                    Update Challan
                </button>
                <a href="{{ route('challans.show', $challan) }}"
                    class="bg-gray-300 text-gray-800 px-6 py-2 rounded text-sm">Cancel</a>
            </div>
        </form>
    </div>

    @php
        $itemsForJs = $challan->items
            ->map(function ($i) {
                $calculated = round((float) $i->qty * (float) $i->rate, 2);
                $stored = round((float) $i->amount, 2);
                return [
                    'item_description' => $i->item_description,
                    'style_name' => $i->style_name,
                    'qty' => (int) $i->qty,
                    'unit' => $i->unit,
                    'rate' => (float) $i->rate,
                    'amount' => $stored,
                    // If stored amount differs from qty × rate, it was manual
                    'manual' => abs($calculated - $stored) > 0.005,
                ];
            })
            ->values()
            ->toArray();
    @endphp

    <script>
        function challanForm() {
            return {
                currency: '{{ $challan->currency ?? 'USD' }}',
                items: @json($itemsForJs),
                addItem() {
                    this.items.push({
                        item_description: '',
                        style_name: '',
                        qty: 1,
                        unit: 'PCS',
                        rate: 0,
                        amount: 0,
                        manual: false
                    });
                },
                removeItem(i) {
                    if (this.items.length > 1) this.items.splice(i, 1);
                },
                round2(v) {
                    v = parseFloat(v) || 0;
                    return Math.round(v * 100) / 100;
                },
                // Auto qty × rate -> 2 decimals. Skipped when the user typed a manual Amount.
                calcAmount(item) {
                    if (item.manual) return;
                    const qty = parseFloat(item.qty) || 0;
                    const rate = parseFloat(item.rate) || 0;
                    item.amount = this.round2(qty * rate);
                },
                // "auto" link: drop the manual value, go back to qty × rate
                resetAuto(item) {
                    item.manual = false;
                    this.calcAmount(item);
                },
                get totalQty() {
                    return this.items.reduce((s, i) => s + (parseFloat(i.qty) || 0), 0);
                },
                get grandTotal() {
                    return this.items.reduce((s, i) => s + (parseFloat(i.amount) || 0), 0);
                },
                get currencySymbol() {
                    return this.currency === 'BDT' ? '৳' : '$';
                }
            };
        }
    </script>
@endsection
