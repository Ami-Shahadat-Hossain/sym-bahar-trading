<?php
namespace App\Http\Controllers;

use App\Helpers\NumberToWords;
use App\Http\Requests\StoreMoneyReceiptRequest;
use App\Models\Invoice;
use App\Models\MoneyReceipt;

class MoneyReceiptController extends Controller
{
    public function index()
    {
        $receipts = MoneyReceipt::with('invoice.customer')->latest()->paginate(15);
        return view('receipts.index', compact('receipts'));
    }

    public function create()
    {
        $invoices = Invoice::with('customer')->latest()->get();
        return view('receipts.create', compact('invoices'));
    }

    public function store(StoreMoneyReceiptRequest $request)
    {
        $data                    = $request->validated();
        $data['receipt_no']      = MoneyReceipt::generateReceiptNo();
        $data['amount_in_words'] = NumberToWords::convert($data['amount']);

        $receipt = MoneyReceipt::create($data);

        return redirect()
            ->route('receipts.show', $receipt)
            ->with('success', 'Receipt ' . $receipt->receipt_no . ' created successfully.');
    }

    public function show(MoneyReceipt $receipt)
    {
        $receipt->load('invoice.customer');
        return view('receipts.show', compact('receipt'));
    }

    public function printA4(MoneyReceipt $receipt)
    {
        $receipt->load('invoice.customer');
        return view('receipts.print-a4', compact('receipt'));
    }

    public function printPad(MoneyReceipt $receipt)
    {
        $receipt->load('invoice.customer');
        return view('receipts.print-pad', compact('receipt'));
    }

    public function destroy(MoneyReceipt $receipt)
    {
        $receipt->delete();
        return redirect()
            ->route('receipts.index')
            ->with('success', 'Receipt deleted successfully.');
    }
}
