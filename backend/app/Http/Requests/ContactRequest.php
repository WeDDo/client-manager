<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContactRequest extends FormRequest
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
            'name' => 'required',
            'company_name' => 'nullable',
            'position' => 'nullable',
            'phone1' => 'nullable',
            'phone2' => 'nullable',
            'email1' => 'nullable',
            'email2' => 'nullable',
            'birthday' => 'nullable',
            'notes' => 'nullable',
            'address1' => 'nullable',
            'address2' => 'nullable',
            'city' => 'nullable',
            'state' => 'nullable',
            'postal_code' => 'nullable',
            'country' => 'nullable',
            'website' => 'nullable',
            'preferred_contact_method' => 'nullable',
            'status' => 'nullable',
            'last_contacted_at' => 'nullable',
            'partner_id' => 'nullable',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge($this->extractAutocompleteIds([
            'partner_id',
        ]));
    }

    protected function extractAutocompleteIds(array $fields): array
    {
        $data = $this->all();
        $formattedData = [];

        foreach ($fields as $field) {
            if (isset($data[$field]['id']) && is_array($data[$field])) {
                $formattedData[$field] = $data[$field]['id'];
            }
        }

        return array_merge($data, $formattedData);
    }
}
