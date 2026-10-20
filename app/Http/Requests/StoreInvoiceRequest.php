<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'invoice_date'  => 'required|date',
            'customer_id'   => 'required|exists:customers,id',
            'customer_name' => 'nullable|string|max:255',
            'currency'      => 'nullable|in:USD,BDT',  // ← new
            'challan_ids'   => 'required|array|min:1', // ← from form
            'challan_ids.*' => 'integer|exists:challans,id',
        ];
    }

    public function messages(): array
    {
        return [
            'challan_ids.required' => 'Please select at least one challan.',
            'challan_ids.min'      => 'Please select at least one challan.',
            'currency.in'          => 'Currency must be USD or BDT.',
        ];
    }
}
