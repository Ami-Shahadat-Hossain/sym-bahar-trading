@extends('layouts.app')
@section('title', 'Add Customer')

@section('content')
    <div class="bg-white rounded-lg shadow p-6 max-w-xl">
        <h2 class="text-xl font-bold mb-4">Add Customer / Delivery Address</h2>

        <form method="POST" action="{{ route('customers.store') }}">
            @csrf

            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Company Name</label>
                <input type="text" name="company_name" required value="{{ old('company_name') }}"
                    class="w-full border rounded px-3 py-2 text-sm">
                @error('company_name')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Address</label>
                <textarea name="address" required rows="3" class="w-full border rounded px-3 py-2 text-sm">{{ old('address') }}</textarea>
                @error('address')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Customer Name (optional)</label>
                <input type="text" name="customer_name" value="{{ old('customer_name') }}"
                    class="w-full border rounded px-3 py-2 text-sm">
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium mb-1">Attention (optional)</label>
                <input type="text" name="attention" value="{{ old('attention') }}"
                    class="w-full border rounded px-3 py-2 text-sm">
            </div>

            <div class="flex gap-3">
                <button type="submit" class="bg-green-700 text-white px-6 py-2 rounded text-sm hover:bg-green-800">
                    Save Customer
                </button>
                <a href="{{ route('customers.index') }}"
                    class="bg-gray-300 text-gray-800 px-6 py-2 rounded text-sm">Cancel</a>
            </div>
        </form>
    </div>
@endsection
