<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreatePDFFromFilesRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'files' => 'required|array',
            'files.*' => 'required|file|mimes:pdf,jpg,jpeg,png|max:4096',
        ];
    }
}
