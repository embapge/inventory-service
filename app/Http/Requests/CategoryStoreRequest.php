<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CategoryStoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            "name" => "required"
        ];
    }
}
