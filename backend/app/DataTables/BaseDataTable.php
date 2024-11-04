<?php

namespace App\DataTables;

use Illuminate\Database\Query\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

abstract class BaseDataTable
{
    protected ?array $additionalData = null;

    protected array $activeColumns = [];
    protected array $columns = [];
    protected LengthAwarePaginator $items;

    protected int $perPage = 500;

    public function __construct(?array $additionalData = null)
    {
        $this->additionalData = $additionalData;

        $this->activeColumns = $this->getActiveColumns();
        $this->columns = array_keys($this->getColumnItemClosures());
        $this->items = $this->getItems();
    }

    public abstract function getColumnItemClosures(): array;

    public abstract function getActiveColumns(): array;

    public abstract function getItems(): LengthAwarePaginator;

    public function applyDefaultOrderBy($query): void
    {
        if ((request('sort_field') !== 'null' && request('sort_field')) && (request('sort_order') !== 'null' && request('sort_order'))) {
            $query->orderBy(request('sort_field'), request('sort_order'));
        }
    }
}
