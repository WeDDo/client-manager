<?php

namespace App\DataTables;

use App\Models\DataTable;
use Illuminate\Pagination\LengthAwarePaginator;

abstract class BaseDataTable
{
    protected int $perPage = 500;

    protected string $name;
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
        $this->name = static::class;
        $this->additionalData = $additionalData;

        $this->activeColumns = $this->getActiveColumns();
        $this->columns = array_keys($this->getColumns());
        $this->items = $this->getItems();
    }

    public abstract function getColumnItemClosures(): array;

    public abstract function getColumns(): array;

    public function getActiveColumns(): array
    {
        $dataTable = DataTable::query()
            ->where('name', $this->name)
            ->where('user_id', auth()->id())
            ->first();

        $selectedColumns = $dataTable ? json_decode($dataTable->selected_columns, true) : null;

        $columns = $this->getColumns();

        if ($selectedColumns) {
            $filteredColumns = [];

            foreach ($selectedColumns[0] as $selectedColumn) {
                foreach ($columns as $column) {
                    if ($column['name'] === ($selectedColumn['name'] ?? null)) {
                        $filteredColumns[] = $column;
                    }
                }
            }

            return $filteredColumns;
        }

        return $columns;
    }

    public abstract function getItems(): LengthAwarePaginator;

    protected function getSelectedColumns(): array
    {
        $selectedColumns = [
            [],
            [],
        ];

        $dataTable = DataTable::query()
            ->where('name', $this->name)
            ->where('user_id', auth()->id())
            ->first();

        if ($dataTable && $dataTable->selected_columns) {
            $selectedColumns = json_decode($dataTable->selected_columns, true);
        } else {
            // Generate default selectable columns
            foreach ($this->getColumns() as $key => $value) {
                $selectedColumns[0][] = [
                    'id' => $value['name'],
                    'name' => $value['name'],
                ];
            }

            DataTable::query()->updateOrCreate(
                [
                    'name' => $this->name,
                    'user_id' => auth()->id(),
                ],
                [
                    'selected_columns' => json_encode($selectedColumns),
                ]
            );
        }

        return $selectedColumns;
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
            'selected_columns' => $this->getSelectedColumns(),
            'columns' => array_keys($this->getColumnItemClosures()),
            'items' => $this->getItems(),
            'filters' => $this->getDefaultFilters($this->filterFieldTypes),
            'sorting' => $this->getDefaultSorting(),
            'additional_data' => $this->additionalData
        ]);
    }

    public function getDefaultSorting(): array
    {
        $dataTableSorting = DataTable::query()
            ->where('name', $this->name ?? static::class)
            ->where('user_id', auth()->id())
            ->first()?->sorting;

        return $dataTableSorting ? json_decode($dataTableSorting, true) : [];
    }

    public function getDefaultFilters(array $fieldTypes = []): array
    {
        $columns = array_keys($this->getColumnItemClosures());

        $dataTableFilters = DataTable::query()
            ->where('name', $this->name ?? static::class)
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

    protected function applySorting($query): void
    {
        if (request('update_sorting')) {
            DataTable::query()?->updateOrCreate([
                'name' => $this->name,
                'user_id' => auth()->id(),
            ], [
                'sorting' => json_encode([
                    'sort_field' => request('sort_field'),
                    'sort_order' => request('sort_order'),
                ])
            ]);
        }

        $dataTableSorting = json_decode(DataTable::query()->where('name', $this->name)->first()?->sorting, true);
        if ($dataTableSorting && (($dataTableSorting['sort_field'] ?? null) && ($dataTableSorting['sort_order'] ?? null))) {
            $query->orderBy($dataTableSorting['sort_field'], $dataTableSorting['sort_order']);
        }
    }

    protected function applyFilters($query): void
    {
        $dataTableFilters = DataTable::query()->where('name', $this->name)->first()?->filters;
        if (request('update_filter')) {
            DataTable::query()?->updateOrCreate([
                'name' => $this->name,
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
