@extends('layouts.app')
@section('title', 'Create Invoice')

@section('content')
    <div class="bg-white rounded-lg shadow p-6 max-w-xl">
        <h2 class="text-xl font-bold mb-4">Create Invoice</h2>
        <p class="text-sm text-gray-600 mb-4">Select a customer and month/year to view available challans.</p>

        <form method="POST" action="{{ route('invoices.select-challans') }}">
            @csrf

            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Customer / Company</label>
                <select name="customer_id" required class="w-full border rounded px-3 py-2 text-sm">
                    <option value="">— Select Customer —</option>
                    @foreach ($customers as $c)
                        <option value="{{ $c->id }}">{{ $c->company_name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-6">
                <div>
                    <label class="block text-sm font-medium mb-1">Month</label>
                    <select name="month" required class="w-full border rounded px-3 py-2 text-sm">
                        @for ($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" @selected($m == date('n'))>
                                {{ \Carbon\Carbon::create(null, $m)->format('F') }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Year</label>
                    <select name="year" required class="w-full border rounded px-3 py-2 text-sm">
                        @for ($y = date('Y'); $y >= 2020; $y--)
                            <option value="{{ $y }}">{{ $y }}</option>
                        @endfor
                    </select>
                </div>
            </div>

            <button type="submit" class="bg-blue-700 text-white px-6 py-2 rounded text-sm hover:bg-blue-800">
                Show Challans →
            </button>
        </form>
    </div>
@endsection
