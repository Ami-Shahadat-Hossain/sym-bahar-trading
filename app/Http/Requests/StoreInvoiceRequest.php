<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {return true;}

    public function rules(): array
    {
        return [
            'invoice_date'  => 'required|date',
            'customer_id'   => 'required|exists:customers,id',
            'customer_name' => 'nullable|string|max:255',
            'challan_ids'   => 'required|array|min:1',
            'challan_ids.*' => 'exists:challans,id',
        ];
    }

    public function messages(): array
    {
        return [
            'challan_ids.required' => 'Please select at least one challan to generate the invoice.',
            'challan_ids.min'      => 'Please select at least one challan.',
        ];
    }
}
