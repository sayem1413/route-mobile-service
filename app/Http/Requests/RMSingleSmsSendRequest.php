<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RMSingleSmsSendRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'destination' => [
                'required',
                'string',
                'regex:/^01[3-9]\d{8}$/',
            ],
            'message' => [
                'required',
                'string',
                'max:255',
            ],
            'country' => [
                'nullable', 
                'string', 
                'size:2'
            ],
        ];
    }

    protected function prepareForValidation(): void
    {
        $mobile = $this->destination;

        $mobile = preg_replace('/\D/', '', $mobile);

        if (str_starts_with($mobile, '880')) {
            $mobile = substr($mobile, 3);
        }

        $this->merge([
            'destination' => $mobile,
        ]);
    }
}
