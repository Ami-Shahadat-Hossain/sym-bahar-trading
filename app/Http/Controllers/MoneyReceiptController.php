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

    /**
     * Only invoices that don't have any receipt yet are selectable.
     */
    public function create()
    {
        $invoices = Invoice::with('customer')
            ->whereDoesntHave('moneyReceipts') // ← filter added
            ->latest()
            ->get();

        return view('receipts.create', compact('invoices'));
    }

    public function store(StoreMoneyReceiptRequest $request)
    {
        $data    = $request->validated();
        $invoice = Invoice::findOrFail($data['invoice_id']);

        $data['receipt_no']      = MoneyReceipt::generateReceiptNo();
        $data['currency']        = $invoice->currency ?? 'USD';
        $data['amount_in_words'] = NumberToWords::convert(
            (float) $data['amount'],
            $data['currency']
        );

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

    /**
     * Edit form. Invoices shown = those without receipts + the receipt's
     * current invoice (so it stays selectable even though it now has one).
     */
    public function edit(MoneyReceipt $receipt)
    {
        $receipt->load('invoice.customer');

        $invoices = Invoice::with('customer')
            ->where(function ($q) use ($receipt) {
                $q->whereDoesntHave('moneyReceipts')
                    ->orWhere('id', $receipt->invoice_id);
            })
            ->latest()
            ->get();

        return view('receipts.edit', compact('receipt', 'invoices'));
    }

    public function update(StoreMoneyReceiptRequest $request, MoneyReceipt $receipt)
    {
        $data    = $request->validated();
        $invoice = Invoice::findOrFail($data['invoice_id']);

        $data['currency']        = $invoice->currency ?? 'USD';
        $data['amount_in_words'] = NumberToWords::convert(
            (float) $data['amount'],
            $data['currency']
        );

        $receipt->update($data);

        return redirect()
            ->route('receipts.show', $receipt)
            ->with('success', 'Receipt ' . $receipt->receipt_no . ' updated successfully.');
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
