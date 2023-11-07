<?php


namespace Objement\OmPhpDataGrid\Interfaces;

use Objement\OmPhpUtils\Filters\OmFilterQueryInterface;

interface OmDataGridRendererInterface
{
    public function render(int $dataGridId, OmDataGridDefinitionInterface $dataGridDefinition, OmDataGridDataSourceInterface $dataSource, ?OmFilterQueryInterface $filterQuery): string;
}
