<?php

namespace App\DataTables;

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
        if ((request('sort_field') !== 'null' && request('sort_field') !== 'undefined' && request('sort_field')) && (request('sort_order') !== 'null' && request('sort_order') !== 'undefined' && request('sort_order'))) {
            $query->orderBy(request('sort_field'), request('sort_order'));
        }
    }

    public function getDefaultFilters(array $fieldTypes = []): array
    {
        $columns = array_keys($this->getColumnItemClosures());

        $defaultFilters = [];
        foreach ($columns as $column) {
            $defaultFilters[] = [
                'name' => $column,
                'label' => ucfirst(str_replace('_', ' ', $column)),
                'operator' => '=',
                'value' => null,
                'field_type' => $fieldTypes[$column] ?? 'text',
            ];
        }

        return $defaultFilters;
    }

    protected function applyFilters($query): void
    {
        $filters = request('filters') ?? [];

        foreach ($filters as $filter) {
            $field = $filter['name'];
            $operator = $filter['operator'] ?? '=';
            $value = $filter['value'];

            if(!$value || $value === 'null') continue;

            switch ($operator) {
                case '=':
                    $query->where($field, '=', $value);
                    break;
                case '<':
                    $query->where($field, '<', $value);
                    break;
                case '>':
                    $query->where($field, '>', $value);
                    break;
                case 'like':
                    $query->where($field, 'like', "%$value%");
                    break;
                case '<=':
                    $query->where($field, '<=', $value);
                    break;
                case '>=':
                    $query->where($field, '>=', $value);
                    break;
                default:
                    throw new \InvalidArgumentException("Unsupported operator: {$operator}");
            }
        }
    }
}
