<?php

namespace App\Services;

use App\Models\Contact;

class ContactService
{
    public function show(Contact $contact): Contact
    {
        return $contact;
    }

    public function store(array $data): Contact
    {
        return Contact::create($data);
    }

    public function update(array $data, Contact $contact): void
    {
        $contact->update($data);
    }
}
