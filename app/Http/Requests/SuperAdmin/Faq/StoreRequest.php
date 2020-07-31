<?php

namespace App\Http\Requests\SuperAdmin\Faq;

use App\Http\Requests\SuperAdmin\SuperAdminBaseRequest;

class StoreRequest extends SuperAdminBaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $data = [
            'title' => 'required',
            'description' => 'required',
        ];

        return $data;
    }
}
