<?php

namespace Objement\OmPhpDataGrid\Interfaces;

use Objement\OmPhpUtils\Filters\OmFilterQueryInterface;

interface OmDataGridFilterAdapterInterface
{
    public function getFilterQuery(): ?OmFilterQueryInterface;
}
