<?php
namespace App\Http\Controllers;

use App\Http\Requests\StoreInvoiceRequest;
use App\Models\Challan;
use App\Models\Customer;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

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
     * Step 2 — Show all UNINVOICED challans for the selected customer
     * and month/year. Challans already attached to any invoice are hidden.
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
            ->whereDoesntHave('invoices') // ← NEW: hide already-invoiced challans
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
            $challans = Challan::with('items')
                ->whereIn('id', $request->challan_ids)
                ->get();

            $currency = $this->resolveInvoiceCurrency($challans, $request->currency);

            $invoice = Invoice::create([
                'invoice_no'    => Invoice::generateInvoiceNo(),
                'invoice_date'  => $request->invoice_date,
                'customer_id'   => $request->customer_id,
                'customer_name' => $request->customer_name,
                'currency'      => $currency,
            ]);

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

            return $invoice;
        });

        return redirect()
            ->route('invoices.show', $invoice)
            ->with('success', 'Invoice ' . $invoice->invoice_no . ' generated successfully.');
    }

    public function show(Invoice $invoice)
    {
        $invoice->load('customer', 'items', 'challans', 'moneyReceipts');
        return view('invoices.show', compact('invoice'));
    }

    public function edit(Invoice $invoice)
    {
        $invoice->load('customer', 'items', 'challans');
        $customers = Customer::orderBy('company_name')->get();

        return view('invoices.edit', compact('invoice', 'customers'));
    }

    public function update(StoreInvoiceRequest $request, Invoice $invoice)
    {
        DB::transaction(function () use ($request, $invoice) {
            $challans = Challan::with('items')
                ->whereIn('id', $request->challan_ids)
                ->get();

            $currency = $this->resolveInvoiceCurrency(
                $challans,
                $request->currency,
                $invoice->currency
            );

            $invoice->update([
                'invoice_date'  => $request->invoice_date,
                'customer_id'   => $request->customer_id,
                'customer_name' => $request->customer_name,
                'currency'      => $currency,
            ]);

            $invoice->items()->delete();
            $invoice->challans()->detach();

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

    public function destroy(Invoice $invoice)
    {
        $no = $invoice->invoice_no;
        $invoice->delete();

        return redirect()
            ->route('invoices.index')
            ->with('success', 'Invoice ' . $no . ' deleted successfully.');
    }

    public function printA4(Invoice $invoice)
    {
        $invoice->load('customer', 'items', 'challans');
        return view('invoices.print-a4', compact('invoice'));
    }

    public function printPad(Invoice $invoice)
    {
        $invoice->load('customer', 'items', 'challans');
        return view('invoices.print-pad', compact('invoice'));
    }

    public function previewNo()
    {
        return response()->json([
            'invoice_no' => Invoice::generateInvoiceNo(),
        ]);
    }

    /**
     * Decide which currency the invoice should use.
     */
    private function resolveInvoiceCurrency($challans, ?string $requested, ?string $fallback = null): string
    {
        $currencies = $challans->pluck('currency')
            ->filter()
            ->map(fn($c) => strtoupper($c))
            ->unique()
            ->values();

        if ($currencies->count() > 1) {
            throw ValidationException::withMessages([
                'challan_ids' => 'Selected challans use different currencies ('
                . $currencies->implode(', ')
                . '). Please select challans of the same currency.',
            ]);
        }

        if ($currencies->count() === 1) {
            return $currencies->first();
        }

        return strtoupper($requested ?? $fallback ?? 'USD');
    }
}
