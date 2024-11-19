<?php

namespace App\DataTables;

use App\Models\DataTable;
use Illuminate\Pagination\LengthAwarePaginator;

abstract class BaseDataTable
{
    protected int $perPage = 500;

    protected ?array $additionalData = null;

    protected array $activeColumns = [];
    protected array $columns = [];
    protected LengthAwarePaginator $items;

    protected array $filterFieldTypes = [];
    protected static string $textFieldType = 'text';
    protected static string $dateFieldType = 'date';
    protected static string $boolFieldType = 'bool';

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

    protected function getSelectableColumns(string $name = null): array
    {
        $selectableColumns = [
            [],
            [],
        ];

        if (!$name) {
            $name = static::class;
        }

        // todo refactor logic to only take names to save of selected and take out all other and move to not selected but available

        if (request('update_selectable_columns')) {
            DataTable::query()?->updateOrCreate([
                'name' => $name,
                'user_id' => auth()->id(),
            ], [
                'selectable_columns' => json_encode($selectableColumns)
            ]);
        } else {
            $dataTableSelectedColumns = DataTable::query()
                ->where('name', $name)
                ->where('user_id', auth()->id())
                ->first()?->selected_columns;

            if (!$dataTableSelectedColumns) {
                $columns = array_keys($this->getColumnItemClosures());

                foreach ($columns as $key => $column) {
                    $selectableColumns[0][] = [
                        'id' => $key,
                        'name' => $column,
                    ];
                }

                DataTable::query()?->updateOrCreate([
                    'name' => $name,
                    'user_id' => auth()->id(),
                ], [
                    'selected_columns' => json_encode($selectableColumns)
                ]);
            }
        }

        return $selectableColumns;
    }

    protected function getSelectedColumns(): array
    {
        return []; //todo implement after saving
    }

    protected function setFilterFieldTypes(): array
    {
        return [];
    }

    public function get(): array
    {
        $this->filterFieldTypes = $this->setFilterFieldTypes();

        return array_merge([
            'name' => static::class,
            'active_columns' => $this->getActiveColumns(),
            'selectable_columns' => $this->getSelectableColumns(),
            'columns' => array_keys($this->getColumnItemClosures()),
            'items' => $this->getItems(),
            'filters' => $this->getDefaultFilters($this->filterFieldTypes),
            'sorting' => $this->getDefaultSorting(),
            'additional_data' => $this->additionalData
        ]);
    }

    public function getDefaultSorting(string $name = null): array
    {
        if (!$name) {
            $name = static::class;
        }

        $dataTableSorting = DataTable::query()
            ->where('name', $name ?? static::class)
            ->where('user_id', auth()->id())
            ->first()?->sorting;

        return $dataTableSorting ? json_decode($dataTableSorting, true) : [];
    }

    public function getDefaultFilters(array $fieldTypes = [], string $name = null): array
    {
        if (!$name) {
            $name = static::class;
        }

        $columns = array_keys($this->getColumnItemClosures());

        $dataTableFilters = DataTable::query()
            ->where('name', $name ?? static::class)
            ->where('user_id', auth()->id())
            ->first()?->filters;

        $filters = json_decode($dataTableFilters, true);

        $defaultFilters = [];
        foreach ($columns as $column) {
            $columnValue = null;

            if (isset($filters[$column])) {
                $columnValue = $filters[$column]['value'] ?? null;
            }

            $defaultFilters[] = [
                'name' => $column,
                'label' => ucfirst(str_replace('_', ' ', $column)),
                'operator' => $filters[$column]['operator'] ?? '=',
                'value' => $columnValue,
                'field_type' => $fieldTypes[$column] ?? self::$textFieldType,
            ];
        }

        return $defaultFilters;
    }

    protected function applySorting($query, string $name = null): void
    {
        if (!$name) {
            $name = static::class;
        }

        if (request('update_sorting')) {
            DataTable::query()?->updateOrCreate([
                'name' => $name,
                'user_id' => auth()->id(),
            ], [
                'sorting' => json_encode([
                    'sort_field' => request('sort_field'),
                    'sort_order' => request('sort_order'),
                ])
            ]);
        }

        $dataTableSorting = json_decode(DataTable::query()->where('name', $name)->first()?->sorting, true);
        if ($dataTableSorting && (($dataTableSorting['sort_field'] ?? null) && ($dataTableSorting['sort_order'] ?? null))) {
            $query->orderBy($dataTableSorting['sort_field'], $dataTableSorting['sort_order']);
        }
    }

    protected function applyFilters($query, string $name = null): void
    {
        if (!$name) {
            $name = static::class;
        }

        $dataTableFilters = DataTable::query()->where('name', $name)->first()?->filters;
        if (request('update_filter')) {
            DataTable::query()?->updateOrCreate([
                'name' => $name,
                'user_id' => auth()->id(),
            ], [
                'filters' => json_encode(request('filters')),
            ]);
        }

        $filters = [];
        if (!$filters || count($filters) === 0) {
            $filters = ($dataTableFilters ? json_decode($dataTableFilters, true) : []);
        } else if (request('filters')) {
            $filters = request('filters');
        }

        if (!is_array($filters)) return;

        foreach ($filters as $key => $filter) {
            $fieldKey = $filter['name'];
            $field = $fieldKey;
            $operator = $filter['operator'] ?? '=';
            $value = $filter['value'];

            if ($value === 'true') {
                $value = true;
            }

            if ($value === 'false') {
                $value = false;
            }

            if ($value === null || $value === 'null') continue;

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
