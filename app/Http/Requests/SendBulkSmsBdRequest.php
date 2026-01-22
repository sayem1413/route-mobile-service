<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SendBulkSmsBdRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
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
