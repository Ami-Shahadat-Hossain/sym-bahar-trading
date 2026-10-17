<?php
namespace App\Http\Controllers;

use App\Http\Requests\StoreCustomerRequest;
use App\Models\Customer;

class CustomerController extends Controller
{
    /**
     * List all customers with pagination.
     */
    public function index()
    {
        $customers = Customer::latest()->paginate(15);
        return view('customers.index', compact('customers'));
    }

    /**
     * Show the create form.
     */
    public function create()
    {
        return view('customers.create');
    }

    /**
     * Store a new customer.
     */
    public function store(StoreCustomerRequest $request)
    {
        $data                = $request->validated();
        $data['customer_id'] = Customer::generateCustomerId();

        $customer = Customer::create($data);

        return redirect()
            ->route('customers.index')
            ->with('success', 'Customer "' . $customer->company_name . '" (' . $customer->customer_id . ') created successfully.');
    }

    /**
     * Show a customer with related challans and invoices.
     */
    public function show(Customer $customer)
    {
        $customer->load(['challans' => fn($q) => $q->latest()->take(10), 'invoices' => fn($q) => $q->latest()->take(10)]);
        return view('customers.show', compact('customer'));
    }

    /**
     * Show the edit form.
     */
    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    /**
     * Update the customer.
     */
    public function update(StoreCustomerRequest $request, Customer $customer)
    {
        $customer->update($request->validated());

        return redirect()
            ->route('customers.index')
            ->with('success', 'Customer "' . $customer->company_name . '" Customerupdated successfully.');
    }

    /**
     * Delete the customer.
     */
    public function destroy(Customer $customer)
    {
        $name = $customer->company_name;
        $customer->delete();

        return redirect()
            ->route('customers.index')
            ->with('success', 'Customer "' . $name . '" deleted successfully.');
    }
}
