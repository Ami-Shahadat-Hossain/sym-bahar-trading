<?php
namespace App\Http\Controllers;

use App\Http\Requests\StoreChallanRequest;
use App\Models\Challan;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChallanController extends Controller
{
    /**
     * List all challans with pagination.
     */
    public function index()
    {
        $challans = Challan::with('customer')->latest()->paginate(15);
        return view('challans.index', compact('challans'));
    }

    /**
     * Show the create form.
     */
    public function create()
    {
        $customers = Customer::orderBy('company_name')->get();
        $challanNo = Challan::generateChallanNo();

        return view('challans.create', compact('customers', 'challanNo'));
    }

    /**
     * Store a new challan with items.
     */
    public function store(StoreChallanRequest $request)
    {
        $challan = DB::transaction(function () use ($request) {
            $challan = Challan::create([
                'challan_no'   => Challan::generateChallanNo(),
                'challan_date' => $request->challan_date,
                'customer_id'  => $request->customer_id,
                'buyer_name'   => $request->buyer_name,
            ]);

            foreach ($request->items as $item) {
                $challan->items()->create([
                    'item_description' => $item['item_description'],
                    'style_name'       => $item['style_name'] ?? null,
                    'qty'              => (int) $item['qty'],
                    'unit'             => $item['unit'],
                    'rate'             => (float) $item['rate'],
                    'amount'           => (float) $item['qty'] * (float) $item['rate'],
                ]);
            }

            $challan->recalculateTotal();

            return $challan;
        });

        return redirect()
            ->route('challans.show', $challan)
            ->with('success', 'Challan ' . $challan->challan_no . ' created successfully.');
    }

    /**
     * Show a single challan.
     */
    public function show(Challan $challan)
    {
        $challan->load('customer', 'items');
        return view('challans.show', compact('challan'));
    }

    /**
     * Show the edit form.
     */
    public function edit(Challan $challan)
    {
        $challan->load('items');
        $customers = Customer::orderBy('company_name')->get();

        return view('challans.edit', compact('challan', 'customers'));
    }

    /**
     * Update an existing challan and its items.
     */
    public function update(StoreChallanRequest $request, Challan $challan)
    {
        DB::transaction(function () use ($request, $challan) {
            $challan->update([
                'challan_date' => $request->challan_date,
                'customer_id'  => $request->customer_id,
                'buyer_name'   => $request->buyer_name,
            ]);

            // Delete old items and recreate
            $challan->items()->delete();

            foreach ($request->items as $item) {
                $challan->items()->create([
                    'item_description' => $item['item_description'],
                    'style_name'       => $item['style_name'] ?? null,
                    'qty'              => (int) $item['qty'],
                    'unit'             => $item['unit'],
                    'rate'             => (float) $item['rate'],
                    'amount'           => (float) $item['qty'] * (float) $item['rate'],
                ]);
            }

            $challan->recalculateTotal();
        });

        return redirect()
            ->route('challans.show', $challan)
            ->with('success', 'Challan ' . $challan->challan_no . ' updated successfully.');
    }

    /**
     * Delete a challan (cascades to items via DB foreign key).
     */
    public function destroy(Challan $challan)
    {
        $no = $challan->challan_no;
        $challan->delete();

        return redirect()
            ->route('challans.index')
            ->with('success', 'Challan ' . $no . ' deleted successfully.');
    }

    /**
     * Print A4 view — 15 rows per page, multi-page with page numbers.
     */
    public function printA4(Challan $challan)
    {
        $challan->load('customer', 'items');
        return view('challans.print-a4', compact('challan'));
    }

    /**
     * Print Pad view — A5 / real pad size.
     */
    public function printPad(Challan $challan)
    {
        $challan->load('customer', 'items');
        return view('challans.print-pad', compact('challan'));
    }

    /**
     * Optional: bulk delete.
     */
    public function bulkDestroy(Request $request)
    {
        $request->validate(['ids' => 'required|array']);

        $count = Challan::whereIn('id', $request->ids)->delete();

        return redirect()
            ->route('challans.index')
            ->with('success', $count . ' challans deleted.');
    }
}
