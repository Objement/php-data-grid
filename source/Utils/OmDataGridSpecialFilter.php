<?php

namespace Objement\OmPhpDataGrid\Utils;

use Closure;
use Objement\OmPhpDataGrid\Interfaces\OmDataGridSpecialFilterInterface;
use Objement\OmPhpUtils\Filters\FilterExpressions\OmFilterExpressionInterface;

class OmDataGridSpecialFilter implements OmDataGridSpecialFilterInterface
{
    /**
     * @param string $caption
     * @param Closure $filterExpressionCallback fn() => OmFilterExpressionInterface
     */
    public function __construct(
        private readonly string $caption,
        private readonly Closure $filterExpressionCallback
    )
    {
    }

    public function getCaption(): string
    {
        return $this->caption;
    }

    public function getFilterExpression(): OmFilterExpressionInterface
    {
        return ($this->filterExpressionCallback)();
    }
}
