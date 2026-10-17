@extends('layouts.app')
@section('title', 'Dashboard')

@php
    use App\Models\Customer;
    use App\Models\Challan;
    use App\Models\Invoice;
    use App\Models\MoneyReceipt;

    try {
        $customerCount = Customer::count();
        $challanCount = Challan::count();
        $invoiceCount = Invoice::count();
        $receiptCount = MoneyReceipt::count();
    } catch (\Throwable $e) {
        $customerCount = $challanCount = $invoiceCount = $receiptCount = 0;
    }

    try {
        $recentChallans = Challan::with('customer')->latest()->take(5)->get();
        $recentInvoices = Invoice::with('customer')->latest()->take(5)->get();
    } catch (\Throwable $e) {
        $recentChallans = collect();
        $recentInvoices = collect();
    }
@endphp

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-800">Dashboard</h1>
        <p class="text-sm text-slate-500">Overview of Bahar Trading Operations</p>
    </div>

    {{-- Stat cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">

        <a href="{{ route('customers.index') }}"
            class="bg-gradient-to-br from-blue-500 to-blue-700 text-white rounded-xl shadow-md p-5 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-xs uppercase tracking-wider">Customers</p>
                    <p class="text-4xl font-bold mt-2">{{ $customerCount }}</p>
                </div>
                <svg class="w-12 h-12 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-5.13a4 4 0 11-8 0 4 4 0 018 0zm6 3a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
            </div>
            <p class="text-xs mt-3 text-blue-100">Go to Challan →</p>
        </a>

        <a href="{{ route('challans.index') }}"
            class="bg-gradient-to-br from-amber-500 to-orange-600 text-white rounded-xl shadow-md p-5 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-amber-100 text-xs uppercase tracking-wider">Challans</p>
                    <p class="text-4xl font-bold mt-2">{{ $challanCount }}</p>
                </div>
                <svg class="w-12 h-12 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
            <p class="text-xs mt-3 text-amber-100">Go to Invoice →</p>
        </a>

        <a href="{{ route('invoices.index') }}"
            class="bg-gradient-to-br from-emerald-500 to-green-700 text-white rounded-xl shadow-md p-5 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-emerald-100 text-xs uppercase tracking-wider">Invoices</p>
                    <p class="text-4xl font-bold mt-2">{{ $invoiceCount }}</p>
                </div>
                <svg class="w-12 h-12 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 8h6m-5 4h4m-4 4h4m-9 4V6a2 2 0 012-2h6l4 4v12a2 2 0 01-2 2H7a2 2 0 01-2-2z" />
                </svg>
            </div>
            <p class="text-xs mt-3 text-emerald-100">Go to Receipt →</p>
        </a>

        <a href="{{ route('receipts.index') }}"
            class="bg-gradient-to-br from-red-500 to-red-700 text-white rounded-xl shadow-md p-5 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-red-100 text-xs uppercase tracking-wider">Receipts</p>
                    <p class="text-4xl font-bold mt-2">{{ $receiptCount }}</p>
                </div>
                <svg class="w-12 h-12 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 9v-2m0 5a9 9 0 110-18 9 9 0 010 18z" />
                </svg>
            </div>
            <p class="text-xs mt-3 text-red-100">Back to Dashboard →</p>
        </a>
    </div>

    {{-- Recent activity --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl shadow-md p-5">
            <h2 class="font-bold text-slate-700 mb-3 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-amber-500"></span> Recent Challans
            </h2>
            @forelse($recentChallans as $c)
                <div class="flex justify-between border-b py-2 text-sm last:border-0">
                    <a href="{{ route('challans.show', $c) }}"
                        class="text-blue-600 hover:underline font-mono">{{ $c->challan_no }}</a>
                    <span class="text-slate-600 truncate max-w-[150px]">{{ $c->customer->company_name ?? '—' }}</span>
                    <span class="font-semibold"> {{ number_format($c->total_amount, 2) }}</span>
                </div>
            @empty
                <p class="text-sm text-slate-500 py-3">No challans yet.</p>
            @endforelse
        </div>

        <div class="bg-white rounded-xl shadow-md p-5">
            <h2 class="font-bold text-slate-700 mb-3 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Recent Invoices
            </h2>
            @forelse($recentInvoices as $inv)
                <div class="flex justify-between border-b py-2 text-sm last:border-0">
                    <a href="{{ route('invoices.show', $inv) }}"
                        class="text-blue-600 hover:underline font-mono">{{ $inv->invoice_no }}</a>
                    <span class="text-slate-600 truncate max-w-[150px]">{{ $inv->customer->company_name ?? '—' }}</span>
                    <span class="font-semibold">{{ number_format($inv->total_amount, 2) }}</span>
                </div>
            @empty
                <p class="text-sm text-slate-500 py-3">No invoices yet.</p>
            @endforelse
        </div>
    </div>
@endsection
