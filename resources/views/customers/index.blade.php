@extends('layouts.app')
@section('title', 'Customers')

@section('content')
    <div class="bg-white rounded-xl shadow-md p-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-3 mb-5">
            <h2 class="text-xl font-bold text-slate-800">Delivery Addresses / Customers</h2>
            <div class="flex gap-2">
                <a href="{{ route('challans.index') }}"
                    class="bg-amber-500 hover:bg-amber-600 text-white px-4 py-2 rounded-lg text-sm font-medium shadow flex items-center gap-1">
                    → Go To Challan Page
                </a>
                <a href="{{ route('customers.create') }}"
                    class="bg-blue-700 hover:bg-blue-800 text-white px-4 py-2 rounded-lg text-sm font-medium shadow flex items-center gap-1">
                    + New Customer
                </a>
            </div>
        </div>

        <div class="overflow-x-auto rounded-lg border border-slate-200">
            <table class="w-full text-sm">
                <thead class="bg-slate-800 text-white">
                    <tr>
                        <th class="px-3 py-2 text-left w-16">SL</th>
                        <th class="px-3 py-2 text-left">Customer ID</th>
                        <th class="px-3 py-2 text-left">Company Name</th>
                        <th class="px-3 py-2 text-left">Address</th>
                        <th class="px-3 py-2 text-left">Customer Name</th>
                        <th class="px-3 py-2 text-center w-56">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customers as $i => $c)
                        <tr class="border-b hover:bg-slate-50">
                            <td class="px-3 py-2">{{ $customers->firstItem() + $i }}</td>
                            <td class="px-3 py-2 font-mono">{{ $c->customer_id }}</td>
                            <td class="px-3 py-2 font-medium">{{ $c->company_name }}</td>
                            <td class="px-3 py-2 text-xs text-slate-600">
                                {{ \Illuminate\Support\Str::limit($c->address, 60) }}</td>
                            <td class="px-3 py-2">{{ $c->customer_name ?? '—' }}</td>
                            <td class="px-3 py-2">
                                <div class="flex justify-center gap-1.5">
                                    <a href="{{ route('customers.show', $c) }}" data-tip="View"
                                        class="w-8 h-8 flex items-center justify-center rounded-md bg-blue-100 text-blue-700 hover:bg-blue-200">
                                        👁
                                    </a>
                                    <a href="{{ route('customers.edit', $c) }}" data-tip="Edit"
                                        class="w-8 h-8 flex items-center justify-center rounded-md bg-amber-100 text-amber-700 hover:bg-amber-200">
                                        ✎
                                    </a>
                                    <a href="{{ route('challans.create') }}" data-tip="New Challan"
                                        class="w-8 h-8 flex items-center justify-center rounded-md bg-emerald-100 text-emerald-700 hover:bg-emerald-200">
                                        ＋
                                    </a>
                                    <form method="POST" action="{{ route('customers.destroy', $c) }}"
                                        onsubmit="return confirm('Delete this customer?')" class="inline">
                                        @csrf @method('DELETE')
                                        <button data-tip="Delete"
                                            class="w-8 h-8 flex items-center justify-center rounded-md bg-red-100 text-red-700 hover:bg-red-200">
                                            🗑
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-3 py-6 text-center text-slate-500">No customers yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @include('partials.pagination', ['paginator' => $customers])
    </div>
@endsection
