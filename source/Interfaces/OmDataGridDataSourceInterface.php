<?php


namespace Objement\OmPhpDataGrid\Interfaces;

use Objement\OmPhpUtils\Filters\OmFilterQueryInterface;

interface OmDataGridDataSourceInterface
{
    public function getColumnValue(int $rowNumber, OmDataGridColumnInterface $column);
    public function getRows(?OmFilterQueryInterface $filterQuery): array;
    public function getTotalCount(): int;
}
