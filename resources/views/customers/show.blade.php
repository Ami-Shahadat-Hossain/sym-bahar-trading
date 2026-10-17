@extends('layouts.app')
@section('title', 'Customer ' . $customer->customer_id)

@section('content')
    <div class="bg-white rounded-lg shadow p-6 max-w-2xl">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-bold">{{ $customer->company_name }}</h2>
            <div class="flex gap-2">
                <a href="{{ route('customers.edit', $customer) }}"
                    class="bg-gray-500 text-dark px-4 py-2 rounded text-sm hover:bg-gray-600">
                    Edit
                </a>
                <a href="{{ route('customers.index') }}" class="bg-gray-300 text-gray-800 px-4 py-2 rounded text-sm">Back</a>
            </div>
        </div>

        <dl class="grid grid-cols-3 gap-3 text-sm">
            <dt class="text-gray-500">Customer ID</dt>
            <dd class="col-span-2 font-mono">{{ $customer->customer_id }}</dd>

            <dt class="text-gray-500">Company Name</dt>
            <dd class="col-span-2">{{ $customer->company_name }}</dd>

            <dt class="text-gray-500">Address</dt>
            <dd class="col-span-2">{{ $customer->address }}</dd>

            <dt class="text-gray-500">Customer Name</dt>
            <dd class="col-span-2">{{ $customer->customer_name ?? '—' }}</dd>

            <dt class="text-gray-500">Attention</dt>
            <dd class="col-span-2">{{ $customer->attention ?? '—' }}</dd>

            <dt class="text-gray-500">Challans</dt>
            <dd class="col-span-2">{{ $customer->challans->count() }}</dd>

            <dt class="text-gray-500">Invoices</dt>
            <dd class="col-span-2">{{ $customer->invoices->count() }}</dd>
        </dl>
    </div>
@endsection
