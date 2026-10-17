<?php
namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreMoneyReceiptRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'receipt_date'    => 'required|date',
            'invoice_id'      => 'required|exists:invoices,id',
            'received_from'   => 'required|string|max:255',
            'amount'          => 'required|numeric|min:0.01',
            'against_bill_no' => 'nullable|string|max:255',
            'mode'            => 'required|in:Cash,Cheque,Bank Transfer,Bkash,Nagad,Bangla QR',
        ];
    }
}
