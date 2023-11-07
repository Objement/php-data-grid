<?php

namespace Objement\OmPhpDataGrid\Interfaces;

use Objement\OmPhpUtils\Filters\FilterExpressions\OmFilterExpressionInterface;

interface OmDataGridSpecialFilterInterface
{
    function getCaption(): string;
    function getFilterExpression(): OmFilterExpressionInterface;
}
