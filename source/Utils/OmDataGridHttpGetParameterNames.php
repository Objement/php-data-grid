<?php

namespace Objement\OmPhpDataGrid\Utils;

class OmDataGridHttpGetParameterNames
{
    private int $dataGridId;

    public function __construct(int $dataGridId)
    {
        $this->dataGridId = $dataGridId;
    }

    public function pagingCurrentPage(): string
    {
        return $this->buildParameterName('cp');
    }

    public function pagingEntriesPerPage(): string
    {
        return $this->buildParameterName('epp');
    }

    public function searchQuery(): string
    {
        return $this->buildParameterName('q');
    }

    public function specialFilter(): string
    {
        return $this->buildParameterName('sf');
    }

    public function columnFilterOperation(int $columnIndex): string
    {
        return $this->buildParameterName('c' . $columnIndex . '_o');
    }

    public function columnFilterCriteria(int $columnIndex): string
    {
        return $this->buildParameterName('c' . $columnIndex . '_c');
    }

    private function buildParameterName(string $parameter): string
    {
        return 'd' . $this->dataGridId . $parameter;
    }
}
