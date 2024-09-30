<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PartnerRequest extends FormRequest
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
        $partnerId = $this->route('partner');

        return [
            'id_name' => ['required'], //todo add unique
            'name' => 'required',
            'name2' => 'nullable',
            'legal_status' => 'nullable',
            'email' => ['nullable', 'email'],
            'phone' => 'nullable',
        ];
    }
}
