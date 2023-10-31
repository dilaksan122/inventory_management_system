<?php

namespace App\Http\Requests;

use App\Models\Invoice;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class InvoiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'date' => ['required'],
            'warehouse_id' => ['required', 'exists:warehouses,id'],
            'due_date' => ['nullable'],
            'customer_id' => ['nullable', 'numeric'],
            'walkin_customer' => ['nullable'],
            'is_delivered' => ['nullable'],
            'billing' => ['nullable', 'array'],
            'shipping' => ['nullable', 'array'],
            'tax' => ['numeric'],
            'discount' => ['numeric'],
            'discount_type' => ['nullable', 'string', Rule::in([Invoice::DISCOUNT_FIXED, Invoice::DISCOUNT_PERCENT])],
            'payment_type' => ['required'],
            'total_paid' => ['nullable', 'numeric', 'between:0,99999999.99'],
            'bank_info' => ['nullable'],
            'notes' => ['nullable', 'max:200'],
            'status' => ['nullable', Rule::in(array_keys(Invoice::INVOICE_ALL_STATUS))],
            'items' => ['array'],
            'items.*.name' => ['required'],
            'items.*.quantity' => ['required'],
            'items.*.price' => ['required']
        ];
    }
}
