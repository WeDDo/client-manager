<?php

namespace App\DataTables;

use App\Models\DataTable;
use Illuminate\Pagination\LengthAwarePaginator;

abstract class BaseDataTable
{
    protected ?array $additionalData = null;

    protected array $activeColumns = [];
    protected array $columns = [];
    protected LengthAwarePaginator $items;

    protected int $perPage = 500;

    protected array $fieldMappings = [];

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

    public function getDefaultFilters(array $fieldTypes = [], string $name = null): array
    {
        if(!$name) {
            $name = static::class;
        }

        $columns = array_keys($this->getColumnItemClosures());

        $dataTableFilters = DataTable::query()
            ->where('name', $name ?? static::class)
            ->first()?->filters;

        $filters = json_decode($dataTableFilters, true);

        $defaultFilters = [];
        foreach ($columns as $column) {
            $columnValue = null;

            if (isset($filters[$column])) {
                $columnValue = $filters[$column]['value'] ?? null;
            }

            $defaultFilters[] = [
                'name' => "$column",
                'label' => ucfirst(str_replace('_', ' ', $column)),
                'operator' => $filters[$column]['operator'] ?? '=',
                'value' => $columnValue,
                'field_type' => $fieldTypes[$column] ?? 'text',
            ];
        }

        return $defaultFilters;
    }


    protected function applyFilters($query, string $name = null): void
    {
        if(!$name) {
            $name = static::class;
        }

        $dataTableFilters = DataTable::query()->where('name', $name)->first()?->filters;
        if (request('update_filter')) {
            DataTable::query()->updateOrCreate([
                'name' => $name,
            ], ['filters' => json_encode(request('filters'))]);
        }

        $filters = [];
        if (request('filters')) {
            $filters = request('filters');
        } else if (!$filters || count($filters) === 0) {
            $filters = ($dataTableFilters ? json_decode($dataTableFilters, true) : []);
        }

        if (!is_array($filters)) return;

        foreach ($filters as $key => $filter) {
            $fieldKey = $filter['name'];
            $field = $this->fieldMappings[$fieldKey] ?? $fieldKey;
            $operator = $filter['operator'] ?? '=';
            $value = $filter['value'];

            if (!$value || $value === 'null') continue;

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
                case 'ilike':
                    $query->where($field, 'ilike', "%$value%");
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
