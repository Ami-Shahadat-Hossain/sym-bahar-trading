<?php
namespace App\Http\Controllers;

use App\Http\Requests\StoreInvoiceRequest;
use App\Models\Challan;
use App\Models\Customer;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    /**
     * List all invoices with pagination.
     */
    public function index()
    {
        $invoices = Invoice::with('customer')->latest()->paginate(15);
        return view('invoices.index', compact('invoices'));
    }

    /**
     * Step 1 — Select customer + month + year.
     */
    public function create()
    {
        $customers = Customer::orderBy('company_name')->get();
        return view('invoices.create', compact('customers'));
    }

    /**
     * Step 2 — Show all challans for the selected customer and month/year.
     */
    public function selectChallans(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'month'       => 'required|integer|between:1,12',
            'year'        => 'required|integer|min:2020|max:2100',
        ]);

        $customer = Customer::findOrFail($request->customer_id);

        $challans = Challan::with('items')
            ->where('customer_id', $request->customer_id)
            ->whereMonth('challan_date', $request->month)
            ->whereYear('challan_date', $request->year)
            ->orderBy('challan_date')
            ->orderBy('id')
            ->get();

        $invoiceNo = Invoice::generateInvoiceNo();

        return view('invoices.select-challans', compact(
            'customer',
            'challans',
            'invoiceNo',
            'request'
        ));
    }

    /**
     * Step 3 — Generate the invoice from selected challans.
     */
    public function store(StoreInvoiceRequest $request)
    {
        $invoice = DB::transaction(function () use ($request) {
            $invoice = Invoice::create([
                'invoice_no'    => Invoice::generateInvoiceNo(),
                'invoice_date'  => $request->invoice_date,
                'customer_id'   => $request->customer_id,
                'customer_name' => $request->customer_name,
            ]);

            $challans = Challan::with('items')
                ->whereIn('id', $request->challan_ids)
                ->get();

            // Attach pivot
            $invoice->challans()->attach($challans->pluck('id'));

            // Copy items from each challan into the invoice
            foreach ($challans as $challan) {
                foreach ($challan->items as $item) {
                    $invoice->items()->create([
                        'item_description' => $item->item_description,
                        'style_name'       => $item->style_name,
                        'challan_no'       => $challan->challan_no,
                        'qty'              => $item->qty,
                        'unit'             => $item->unit,
                        'rate'             => $item->rate,
                        'amount'           => $item->amount,
                    ]);
                }
            }

            $invoice->recalculateTotal();

            return $invoice;
        });

        return redirect()
            ->route('invoices.show', $invoice)
            ->with('success', 'Invoice ' . $invoice->invoice_no . ' generated successfully.');
    }

    /**
     * Show a single invoice.
     */
    public function show(Invoice $invoice)
    {
        $invoice->load('customer', 'items', 'challans', 'moneyReceipts');
        return view('invoices.show', compact('invoice'));
    }

    /**
     * Show the edit form (invoice items are editable).
     */
    public function edit(Invoice $invoice)
    {
        $invoice->load('customer', 'items', 'challans');
        $customers = Customer::orderBy('company_name')->get();

        return view('invoices.edit', compact('invoice', 'customers'));
    }

    /**
     * Update invoice basic info + regenerate items.
     */
    public function update(StoreInvoiceRequest $request, Invoice $invoice)
    {
        DB::transaction(function () use ($request, $invoice) {
            $invoice->update([
                'invoice_date'  => $request->invoice_date,
                'customer_id'   => $request->customer_id,
                'customer_name' => $request->customer_name,
            ]);

            $invoice->items()->delete();
            $invoice->challans()->detach();

            $challans = Challan::with('items')
                ->whereIn('id', $request->challan_ids)
                ->get();

            $invoice->challans()->attach($challans->pluck('id'));

            foreach ($challans as $challan) {
                foreach ($challan->items as $item) {
                    $invoice->items()->create([
                        'item_description' => $item->item_description,
                        'style_name'       => $item->style_name,
                        'challan_no'       => $challan->challan_no,
                        'qty'              => $item->qty,
                        'unit'             => $item->unit,
                        'rate'             => $item->rate,
                        'amount'           => $item->amount,
                    ]);
                }
            }

            $invoice->recalculateTotal();
        });

        return redirect()
            ->route('invoices.show', $invoice)
            ->with('success', 'Invoice ' . $invoice->invoice_no . ' updated successfully.');
    }

    /**
     * Delete the invoice.
     */
    public function destroy(Invoice $invoice)
    {
        $no = $invoice->invoice_no;
        $invoice->delete();

        return redirect()
            ->route('invoices.index')
            ->with('success', 'Invoice ' . $no . ' deleted successfully.');
    }

    /**
     * Print A4 view — 14 rows per page, multi-page with page numbers,
     * challan grouping via rowspan.
     */
    public function printA4(Invoice $invoice)
    {
        $invoice->load('customer', 'items', 'challans');
        return view('invoices.print-a4', compact('invoice'));
    }

    /**
     * Print Pad view — A5 / real pad size.
     */
    public function printPad(Invoice $invoice)
    {
        $invoice->load('customer', 'items', 'challans');
        return view('invoices.print-pad', compact('invoice'));
    }

    /**
     * Optional: generate invoice number without saving (for preview).
     */
    public function previewNo()
    {
        return response()->json([
            'invoice_no' => Invoice::generateInvoiceNo(),
        ]);
    }
}
