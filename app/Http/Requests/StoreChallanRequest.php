<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreChallanRequest extends FormRequest
{
    public function authorize(): bool
    {return true;}

    public function rules(): array
    {
        return [
            'challan_date'             => 'required|date',
            'customer_id'              => 'required|exists:customers,id',
            'buyer_name'               => 'nullable|string|max:255',
            'items'                    => 'required|array|min:1',
            'items.*.item_description' => 'required|string|max:255',
            'items.*.style_name'       => 'nullable|string|max:255',
            'items.*.qty'              => 'required|integer|min:1',
            'items.*.unit'             => 'required|string|max:20',
            'items.*.rate'             => 'required|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'items.required'                    => 'Please add at least one item.',
            'items.*.item_description.required' => 'Item description is required on every row.',
            'items.*.qty.min'                   => 'Quantity must be at least 1.',
            'items.*.rate.min'                  => 'Rate cannot be negative.',
        ];
    }
}
