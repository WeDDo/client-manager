<?php

namespace App\Services;

use App\Models\EmailInboxSetting;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AutocompleteService
{
    public function search()
    {
        $table = request('table');
        $searchQuery = request('query');
        $searchFields = request('search_fields', ['id', 'name']);

        $query = $this->buildSearchQuery($table, $searchFields, $searchQuery);

        return $query->get();
    }

    public function searchById()
    {
        $table = request('table');
        $id = request('id');
        $searchFields = request('search_fields', ['id', 'name']);

        $query = $this->buildSearchQuery($table, $searchFields);

        return $query->where('id', $id)->first();
    }

    protected function buildSearchQuery(
        string $table,
        array $searchFields,
        ?string $query = null
    ): Builder
    {
        $concatenatedFields = implode(", ' ', ", array_filter($searchFields, fn($field) => $field !== 'id'));
        $labelField = DB::raw("CONCAT_WS(' ', $concatenatedFields) AS label");

        $searchQuery = DB::table($table)->select($labelField, 'id', ...$searchFields);

        if ($query) {
            $searchQuery->where(function ($q) use ($searchFields, $query) {
                foreach ($searchFields as $field) {
                    if ($field === 'id') {
                        $q->orWhere($field, $query);
                    } else {
                        $q->orWhere($field, 'LIKE', "%$query%");
                    }
                }
            });
        }

        return $searchQuery;
    }
}


