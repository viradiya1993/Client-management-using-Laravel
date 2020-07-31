<?php

namespace App\Http\Requests\Front\Register;

use App\GlobalSetting;
use App\Http\Requests\CoreRequest;
use Illuminate\Foundation\Http\FormRequest;

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
        $global = GlobalSetting::first();

        $rules = [
            'company_name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required||confirmed',
            'password_confirmation' => 'required'
        ];

        if(!is_null($global->google_recaptcha_key))
        {
            $rules['g-recaptcha-response'] = 'required';
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'g-recaptcha-response.required' => 'Please select google recaptcha'
        ];
    }
}
