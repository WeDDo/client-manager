<?php

namespace App\Services;

use App\Models\Partner;

class PartnerService
{
    public function show(Partner $partner): Partner
    {
        return $partner;
    }

    public function store(array $data): Partner
    {
        return Partner::create($data);
    }

    public function update(array $data, Partner $partner): void
    {
        $partner->update($data);
    }
}


