@extends('layouts.app')
@section('title', 'Challans')

@section('content')
    <div class="bg-white rounded-xl shadow-md p-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-3 mb-5">
            <h2 class="text-xl font-bold text-slate-800">Challans</h2>
            <div class="flex gap-2">
                <a href="{{ route('invoices.index') }}"
                    class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-sm font-medium shadow flex items-center gap-1">
                    → Go To Invoice Page
                </a>
                <a href="{{ route('challans.create') }}"
                    class="bg-blue-700 hover:bg-blue-800 text-white px-4 py-2 rounded-lg text-sm font-medium shadow flex items-center gap-1">
                    + New Challan
                </a>
            </div>
        </div>

        <div class="overflow-x-auto rounded-lg border border-slate-200">
            <table class="w-full text-sm">
                <thead class="bg-slate-800 text-white">
                    <tr>
                        <th class="px-3 py-2 text-left w-16">SL</th>
                        <th class="px-3 py-2 text-left">Challan No</th>
                        <th class="px-3 py-2 text-left">Date</th>
                        <th class="px-3 py-2 text-left">Company</th>
                        <th class="px-3 py-2 text-left">Buyer</th>
                        <th class="px-3 py-2 text-right">Total</th>
                        <th class="px-3 py-2 text-center w-64">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($challans as $i => $ch)
                        <tr class="border-b hover:bg-slate-50">
                            <td class="px-3 py-2">{{ $challans->firstItem() + $i }}</td>
                            <td class="px-3 py-2 font-mono">{{ $ch->challan_no }}</td>
                            <td class="px-3 py-2">{{ $ch->challan_date->format('d-M-Y') }}</td>
                            <td class="px-3 py-2">{{ $ch->customer->company_name }}</td>
                            <td class="px-3 py-2 text-xs">{{ $ch->buyer_name ?? '—' }}</td>
                            <td class="px-3 py-2 text-right font-semibold">{{ number_format($ch->total_amount, 2) }}</td>
                            <td class="px-3 py-2">
                                <div class="flex justify-center gap-1.5">
                                    <a href="{{ route('challans.show', $ch) }}" data-tip="View"
                                        class="w-8 h-8 flex items-center justify-center rounded-md bg-blue-100 text-blue-700 hover:bg-blue-200">👁</a>
                                    <a href="{{ route('challans.edit', $ch) }}" data-tip="Edit"
                                        class="w-8 h-8 flex items-center justify-center rounded-md bg-amber-100 text-amber-700 hover:bg-amber-200">✎</a>
                                    <a href="{{ route('challans.print-a4', $ch) }}" data-tip="Print A4"
                                        class="w-8 h-8 flex items-center justify-center rounded-md bg-slate-200 text-slate-800 hover:bg-slate-300 text-xs font-bold">A4</a>
                                    <a href="{{ route('challans.print-pad', $ch) }}" data-tip="Print Pad"
                                        class="w-8 h-8 flex items-center justify-center rounded-md bg-red-100 text-red-700 hover:bg-red-200 text-xs font-bold">PAD</a>
                                    <form method="POST" action="{{ route('challans.destroy', $ch) }}"
                                        onsubmit="return confirm('Delete this challan?')" class="inline">
                                        @csrf @method('DELETE')
                                        <button data-tip="Delete"
                                            class="w-8 h-8 flex items-center justify-center rounded-md bg-red-100 text-red-700 hover:bg-red-200">🗑</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-3 py-6 text-center text-slate-500">No challans yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @include('partials.pagination', ['paginator' => $challans])
    </div>
@endsection
