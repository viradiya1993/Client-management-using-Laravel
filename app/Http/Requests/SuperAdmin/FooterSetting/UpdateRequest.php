<?php

namespace App\Http\Requests\SuperAdmin\FooterSetting;

use App\Http\Requests\SuperAdmin\SuperAdminBaseRequest;

class UpdateRequest extends SuperAdminBaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            "title" => "required|unique:footer_menu,name,".$this->footer_setting,
//            "slug" => "'required|unique:footer_menu,slug,".$this->footer_setting,
            "description" => "required",
        ];

        return $rules;
    }
}
