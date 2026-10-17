@extends('layouts.app')
@section('title', 'Money Receipts')

@section('content')
    <div class="bg-white rounded-xl shadow-md p-6">
        {{-- Header --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-3 mb-5">
            <div>
                <h2 class="text-xl font-bold text-slate-800">Money Receipts</h2>
                <p class="text-xs text-slate-500 mt-1">All payment receipts issued to customers</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('dashboard') }}"
                    class="bg-slate-700 hover:bg-slate-800 text-white px-4 py-2 rounded-lg text-sm font-medium shadow flex items-center gap-1.5 transition">
                    ← Dashboard
                </a>
                <a href="{{ route('invoices.index') }}"
                    class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-sm font-medium shadow flex items-center gap-1.5 transition">
                    → Go To Invoice Page
                </a>
                <a href="{{ route('receipts.create') }}"
                    class="bg-blue-700 hover:bg-blue-800 text-white px-4 py-2 rounded-lg text-sm font-medium shadow flex items-center gap-1.5 transition">
                    + New Receipt
                </a>
            </div>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto rounded-lg border border-slate-200">
            <table class="w-full text-sm">
                <thead class="bg-slate-800 text-white">
                    <tr>
                        <th class="px-3 py-2.5 text-left w-16">SL</th>
                        <th class="px-3 py-2.5 text-left">Receipt No</th>
                        <th class="px-3 py-2.5 text-left">Date</th>
                        <th class="px-3 py-2.5 text-left">Invoice No</th>
                        <th class="px-3 py-2.5 text-left">Received From</th>
                        <th class="px-3 py-2.5 text-left">Mode</th>
                        <th class="px-3 py-2.5 text-right">Amount ($)</th>
                        <th class="px-3 py-2.5 text-center w-60">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($receipts as $i => $r)
                        <tr class="border-b hover:bg-slate-50 transition">
                            <td class="px-3 py-2.5 text-slate-500">{{ $receipts->firstItem() + $i }}</td>
                            <td class="px-3 py-2.5 font-mono font-medium text-slate-800">{{ $r->receipt_no }}</td>
                            <td class="px-3 py-2.5 text-slate-600">{{ $r->receipt_date->format('d-M-Y') }}</td>
                            <td class="px-3 py-2.5 font-mono text-xs text-slate-600">{{ $r->invoice->invoice_no ?? '—' }}
                            </td>
                            <td class="px-3 py-2.5">{{ $r->received_from }}</td>
                            <td class="px-3 py-2.5">
                                <span
                                    class="text-xs px-2 py-0.5 rounded-full bg-slate-100 text-slate-700">{{ $r->mode }}</span>
                            </td>
                            <td class="px-3 py-2.5 text-right font-semibold text-emerald-700">
                                {{ number_format($r->amount, 2) }}
                            </td>
                            <td class="px-3 py-2.5">
                                <div class="flex justify-center gap-1.5">
                                    <a href="{{ route('receipts.show', $r) }}" data-tip="View"
                                        class="w-8 h-8 flex items-center justify-center rounded-md bg-blue-100 text-blue-700 hover:bg-blue-200 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>
                                    <a href="{{ route('receipts.print-a4', $r) }}" data-tip="Print A4 (2 copies)"
                                        class="w-8 h-8 flex items-center justify-center rounded-md bg-slate-200 text-slate-800 hover:bg-slate-300 text-xs font-bold transition">
                                        A4
                                    </a>
                                    <a href="{{ route('receipts.print-pad', $r) }}" data-tip="Print Pad A5"
                                        class="w-8 h-8 flex items-center justify-center rounded-md bg-red-100 text-red-700 hover:bg-red-200 text-xs font-bold transition">
                                        PAD
                                    </a>
                                    <form method="POST" action="{{ route('receipts.destroy', $r) }}"
                                        onsubmit="return confirm('Delete this receipt?')" class="inline">
                                        @csrf @method('DELETE')
                                        <button data-tip="Delete"
                                            class="w-8 h-8 flex items-center justify-center rounded-md bg-red-100 text-red-700 hover:bg-red-200 transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-3 py-10 text-center text-slate-500">
                                <div class="flex flex-col items-center gap-2">
                                    <svg class="w-12 h-12 text-slate-300" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z" />
                                    </svg>
                                    <span>No receipts yet.</span>
                                    <a href="{{ route('receipts.create') }}" class="text-blue-600 hover:underline text-sm">
                                        Create your first receipt →
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @include('partials.pagination', ['paginator' => $receipts])
    </div>
@endsection
