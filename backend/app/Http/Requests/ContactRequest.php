<?php

namespace App\Http\Requests;

use App\Http\Requests\Base\BaseRequest;
use App\Models\Contact;

class ContactRequest extends BaseRequest
{
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

    protected function getModelInstance(): Contact
    {
        return new Contact();
    }
}
