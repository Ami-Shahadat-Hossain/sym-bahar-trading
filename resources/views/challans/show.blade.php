@extends('layouts.app')
@section('title', 'Challan ' . $challan->challan_no)

@section('content')
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-xl font-bold">
                    Challan {{ $challan->challan_no }}
                    <span class="ml-2 text-xs font-medium px-2 py-0.5 rounded bg-slate-100 text-slate-700">
                        {{ $challan->currency ?? 'USD' }}
                    </span>
                </h2>
                <p class="text-sm text-gray-500">{{ $challan->challan_date->format('d-M-Y') }}</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('challans.print-a4', $challan) }}"
                    class="bg-green-700 text-white px-4 py-2 rounded text-sm hover:bg-green-800">
                    Print A4
                </a>
                <a href="{{ route('challans.edit', $challan) }}"
                    class="bg-gray-500 text-dark px-4 py-2 rounded text-sm hover:bg-gray-600">
                    Edit
                </a>
                <a href="{{ route('challans.index') }}" class="bg-gray-300 text-gray-800 px-4 py-2 rounded text-sm">
                    Back
                </a>
            </div>
        </div>

        {{-- Party info --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6 text-sm">
            <div>
                <h3 class="font-semibold text-gray-700 mb-1">Company</h3>
                <p class="font-medium">{{ $challan->customer->company_name }}</p>
                <p class="text-gray-600">{{ $challan->customer->address }}</p>
                @if ($challan->customer->attention)
                    <p class="text-gray-600">Attention {{ $challan->customer->attention }}</p>
                @endif
            </div>
            <div>
                @if ($challan->buyer_name)
                    <h3 class="font-semibold text-gray-700 mb-1">Buyer Name</h3>
                    <p>{{ $challan->buyer_name }}</p>
                @endif
            </div>
        </div>

        {{-- Items table --}}
        <div class="overflow-x-auto mb-6">
            <table class="w-full text-sm border">
                <thead class="bg-slate-800 text-white">
                    <tr>
                        <th class="px-2 py-2 border text-center w-12">SL</th>
                        <th class="px-2 py-2 border text-left">Items Description</th>
                        <th class="px-2 py-2 border text-left">Style Name</th>
                        <th class="px-2 py-2 border text-center">Qty</th>
                        <th class="px-2 py-2 border text-center">Unit</th>
                        <th class="px-2 py-2 border text-right">Rate</th>
                        <th class="px-2 py-2 border text-right">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($challan->items as $i => $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-2 py-2 border text-center">{{ $i + 1 }}</td>
                            <td class="px-2 py-2 border">{{ $item->item_description }}</td>
                            <td class="px-2 py-2 border">{{ $item->style_name ?? '—' }}</td>
                            <td class="px-2 py-2 border text-center">{{ $item->qty }}</td>
                            <td class="px-2 py-2 border text-center">{{ $item->unit }}</td>
                            {{-- Rate: up to 9 decimals, trailing zeros trimmed --}}
                            <td class="px-2 py-2 border text-right">
                                {{ rtrim(rtrim(number_format($item->rate, 9, '.', ''), '0'), '.') ?: '0' }}
                            </td>
                            <td class="px-2 py-2 border text-right">{{ number_format($item->amount, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="bg-gray-100 font-semibold">
                        <td colspan="6" class="px-2 py-2 border text-right">Grand Total :</td>
                        <td class="px-2 py-2 border text-right">
                            {{-- Currency-aware symbol ($ or ৳) --}}
                            {{ $challan->currency_symbol }} {{ number_format($challan->total_amount, 2) }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="bg-slate-50 border-l-4 border-slate-700 p-3 text-sm italic">
            <strong>Amount In Words :</strong> {{ $challan->amount_in_words }}
        </div>
    </div>
@endsection
