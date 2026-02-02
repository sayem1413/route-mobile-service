<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FileToBase64Request extends FormRequest
{
    public function rules(): array
    {
        return [
            'documemt' => 'nullable|file|mimes:pdf,jpg,jpeg,png|min:1|max:4096',
        ];
    }
}
