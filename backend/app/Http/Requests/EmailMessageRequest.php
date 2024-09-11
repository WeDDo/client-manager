<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmailMessageRequest extends FormRequest
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
            'subject' => 'nullable',
            'is_seen' => 'nullable',
            'is_flagged' => 'nullable',
            'is_answered' => 'nullable',
            'body_text' => 'nullable',
            'body_html' => 'nullable',
        ];
    }
}
