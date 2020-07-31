<?php

namespace App\Http\Requests\Invoices;

use App\Http\Requests\CoreRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreInvoice extends CoreRequest
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
        $rules = [
            'issue_date' => 'required',
            'due_date' => 'required',
            'sub_total' => 'required',
            'total' => 'required',
            'currency_id' => 'required'
        ];

        if($this->project_id == '')
        {
            $rules['client_id'] = 'required';
        }

        if($this->project_id > 0){
            $rules['company_name'] = 'required';
        }


        if($this->recurring_payment == 'yes')
        {
            $rules['billing_frequency'] = 'required';
            $rules['billing_interval'] = 'required|integer';
            $rules['billing_cycle'] = 'required|integer';
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'company_name.required' => 'Client not assigned to selected Project.'
        ];
    }
}
