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
//        $this->columns = $this->getSelectedColumns();
        $this->items = $this->getItems();
    }

    public abstract function getColumnItemClosures(): array;

    public abstract function getColumns(): array;
//    public abstract function getActiveColumns(): array;

    public function getActiveColumns(): array
    {
        // Fetch the DataTable record for the current name and user
        $dataTable = DataTable::query()
            ->where('name', $this->name)
            ->where('user_id', auth()->id())
            ->first();

        // Decode the selected_columns or use the default active columns as fallback
        $selectedColumns = $dataTable ? json_decode($dataTable->selected_columns, true) : null;

        // Default columns structure
        $columns = $this->getColumns();

//        dd($columns, $selectedColumns);

        // Filter the default columns by the selected columns
        if ($selectedColumns) {
            $filteredColumns = [];

            foreach ($selectedColumns[0] as $selectedColumn) { // Accessing the first array in $selectedColumns
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

    protected function getSelectableColumns(): array
    {
        $selectableColumns = [
            [],
            [],
        ];

        $dataTable = DataTable::query()
            ->where('name', $this->name)
            ->where('user_id', auth()->id())
            ->first();

        if ($dataTable && $dataTable->selected_columns) {
            $selectableColumns = json_decode($dataTable->selected_columns, true);
        } else {
            // Generate default selectable columns
            foreach ($this->getColumnItemClosures() as $key => $closure) {
                $selectableColumns[0][] = [
                    'id' => $key,
                    'name' => $key,
                ];
            }

            DataTable::query()->updateOrCreate(
                [
                    'name' => $this->name,
                    'user_id' => auth()->id(),
                ],
                [
                    'selected_columns' => json_encode($selectableColumns),
                ]
            );
        }

        return $selectableColumns;
    }

//    protected function getSelectedColumns(): array
//    {
//        $dataTable = DataTable::where([
//            'name' => $this->name,
//            'user_id' => auth()->id(),
//        ])->first();
//
//        $selectedColumns = json_decode($dataTable->selected_columns, true);
//        if (!$selectedColumns) return;
//
//        foreach ($this->getColumnItemClosures() as $key => $value) {
//
//        }
//
//        return []; //todo implement after saving
//    }
//    protected function getSelectedColumns(): array
//    {
//        // Fetch the DataTable record for the current user and DataTable name
//        $dataTable = DataTable::where([
//            'name' => $this->name,
//            'user_id' => auth()->id(),
//        ])->first();
//
//        // Decode the saved selected columns if available
//        $selectedColumns = $dataTable ? json_decode($dataTable->selected_columns, true) : null;
//
//        // If no selected columns are saved, return the default active columns
//        if (!$selectedColumns || empty($selectedColumns)) {
//            return $this->getActiveColumns();
//        }
//
//        // Fetch closures for all defined columns
//        $columnClosures = $this->getColumnItemClosures();
//
//        // Filter the column closures to only include those saved as selected
//        $filteredColumns = [];
//        foreach ($selectedColumns as $selectedColumn) {
//            $columnKey = $selectedColumn['id'] ?? $selectedColumn['name'];
//
//            if (isset($columnClosures[$columnKey])) {
//                $filteredColumns[$columnKey] = $columnClosures[$columnKey];
//            }
//        }
//
//        // If no valid columns are selected, return the default active columns as a failsafe
//        if (empty($filteredColumns)) {
//            return $this->getActiveColumns();
//        }
//
//        return $filteredColumns;
//    }
////

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
