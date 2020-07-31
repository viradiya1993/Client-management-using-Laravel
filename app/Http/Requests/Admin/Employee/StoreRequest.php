<?php

namespace App\Http\Requests\Admin\Employee;

use App\Http\Requests\CoreRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRequest extends CoreRequest
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
//            "employee_id" => "required|unique:employee_details",
            'employee_id' => [
                'required',
                Rule::unique('employee_details')->where(function($query) {
                    $query->where(['employee_id' => $this->request->get('employee_id'), 'company_id' => company()->id]);
                })
            ],
            "name" => "required",
            "email" => "required|email|unique:users",
            "password" => "required|min:6",
            'slack_username' => 'nullable|unique:employee_details,slack_username',
            'hourly_rate' => 'nullable|numeric',
            'joining_date' => 'required',
            'department' => 'required',
            'designation' => 'required',
        ];
    }
}
