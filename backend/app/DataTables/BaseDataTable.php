<?php

namespace App\DataTables;

abstract class BaseDataTable
{
    protected ?array $additionalData = null;

    protected array $activeColumns = [];
    protected array $columns = [];
    protected $items = [];

    protected int $perPage = 5;

    public function __construct(?array $additionalData = null)
    {
        $this->additionalData = $additionalData;

        $this->activeColumns = $this->getActiveColumns();
        $this->columns = array_keys($this->getColumnItemClosures());
        $this->items = $this->getItems();
    }

    public abstract function getColumnItemClosures(): array;

    public abstract function getActiveColumns(): array;

    public abstract function getItems();
}
