<?php

namespace Objement\OmPhpDataGrid\Utils;

use DateTime;
use Exception;
use Objement\OmPhpDataGrid\Exceptions\OmDataGridOperationNotFoundException;
use Objement\OmPhpDataGrid\Interfaces\OmDataGridColumnInterface;
use Objement\OmPhpDataGrid\Interfaces\OmDataGridDefinitionInterface;
use Objement\OmPhpDataGrid\Interfaces\OmDataGridFilterAdapterInterface;
use Objement\OmPhpUtils\Exceptions\OmRequestHandlerOutOfDesiredLengthException;
use Objement\OmPhpUtils\Exceptions\OmRequestHandlerValueNotInWhiteListException;
use Objement\OmPhpUtils\Filters\FilterExpressions\OmAndFilterExpressionGroup;
use Objement\OmPhpUtils\Filters\FilterExpressions\OmFilterExpressionConditionInterface;
use Objement\OmPhpUtils\Filters\FilterExpressions\OmFilterExpressionInterface;
use Objement\OmPhpUtils\Filters\FilterExpressions\OmOrFilterExpressionGroup;
use Objement\OmPhpUtils\Filters\OmFilterExpressionFactory;
use Objement\OmPhpUtils\Filters\OmFilterQuery;
use Objement\OmPhpUtils\Filters\OmFilterQueryInterface;
use Objement\OmPhpUtils\Filters\OmQueryLimit;
use Objement\OmPhpUtils\HttpRequests\OmRequestHandler;
use Objement\OmPhpUtils\HttpRequests\OmRequestHandlerInterface;

class OmDataGridHttpGetToOmDataGridFilterAdapter implements OmDataGridFilterAdapterInterface
{
    const OPERATION_CONTAINS = 'contains';
    const OPERATION_EQUALS = 'equals';
    const OPERATION_STARTS = 'starts';
    const OPERATION_ENDS = 'ends';

    const POSSIBLE_OPERATIONS_STRING = [self::OPERATION_CONTAINS, self::OPERATION_EQUALS, self::OPERATION_STARTS, self::OPERATION_ENDS];
    const POSSIBLE_OPERATIONS_DATE = [self::OPERATION_CONTAINS];

    private readonly OmRequestHandlerInterface $requestHandler;
    private OmDataGridHttpGetParameterNames $parameterNames;

    public function __construct(
        private readonly int                         $dataGridId,
        private readonly OmDataGridDefinitionInterface $dataGridDefinition
    )
    {
        $this->requestHandler = new OmRequestHandler($_GET);
        $this->parameterNames = new OmDataGridHttpGetParameterNames($this->dataGridId);
    }

    public function getFilterQuery(): ?OmFilterQueryInterface
    {
        $limit = $this->processLimit();
        $expressionsSearch = $this->processExpressionsSearch();
        $expressionsSpecialFilter = $this->processExpressionsSpecialFilter();
        $expressions = $this->processExpressions();

        return $this->buildFilterQuery($limit, $expressions, $expressionsSearch, $expressionsSpecialFilter);
    }

    /**
     * @param string|null $operation
     * @param string $fieldName
     * @param mixed $criteria
     * @return OmFilterExpressionConditionInterface|null
     * @throws OmDataGridOperationNotFoundException
     */
    private function operationToExpression(?string $operation, string $fieldName, mixed $criteria): ?OmFilterExpressionConditionInterface
    {
        return match ($operation) {
            self::OPERATION_CONTAINS => OmFilterExpressionFactory::like($fieldName, $criteria),
            self::OPERATION_EQUALS => OmFilterExpressionFactory::equals($fieldName, $criteria),
            self::OPERATION_STARTS => OmFilterExpressionFactory::startsWith($fieldName, $criteria),
            self::OPERATION_ENDS => OmFilterExpressionFactory::endsWith($fieldName, $criteria),
            default => throw new OmDataGridOperationNotFoundException('Unexpected value. Add to self::POSSIBLE_OPERATIONS.'),
        };
    }

    private function buildFilterQuery(OmQueryLimit $limit, array $expressions, array $expressionsSearch, ?OmFilterExpressionInterface $expressionSpecialFilter): ?OmFilterQueryInterface
    {
        $filterExpression = null;
        if (!empty($expressions) && !empty($expressionsSearch)) {
            $filterExpression = new OmAndFilterExpressionGroup([
                new OmOrFilterExpressionGroup($expressionsSearch),
                new OmAndFilterExpressionGroup($expressions),
            ]);
        } elseif (!empty($expressions)) {
            $filterExpression = new OmAndFilterExpressionGroup($expressions);
        } elseif (!empty($expressionsSearch)) {
            $filterExpression = new OmOrFilterExpressionGroup($expressionsSearch);
        }

        if ($expressionSpecialFilter !== null) {
            $filterExpression = new OmAndFilterExpressionGroup(array_values(array_filter([
                $expressionSpecialFilter,
                $filterExpression
            ])));
        }

        $filterQuery = new OmFilterQuery();

        if ($filterExpression) {
            $filterQuery->setExpressions([$filterExpression]);
        }

        $filterQuery->setLimit($limit);

        return $filterQuery;
    }

    /**
     * @return array
     */
    private function processExpressionsSearch(): array
    {
        $expressionsSearch = [];

        if ($this->requestHandler->isSet($this->parameterNames->searchQuery())) {
            foreach ($this->getFilterableColumns() as $column) {
                try {
                    $input = trim($this->requestHandler->getString($this->parameterNames->searchQuery()));
                    if ($column->getType() == 'date') {
                        if (!empty($input)) {
                            $parsedDate = DateTime::createFromFormat('d.m.Y', $input);
                            if ($parsedDate !== false) {
                                $mysqlDateFormat = $parsedDate->format('Y-m-d');
                                $expressionsSearch[] = OmFilterExpressionFactory::equals($column->getName(), $mysqlDateFormat);
                            }
                        }
                    } else {
                        $expressionsSearch[] = OmFilterExpressionFactory::like($column->getName(), $input);
                    }
                } catch (Exception) {
                    // just don't add the field if there is something wrong with the field.
                }
            }
        }

        return $expressionsSearch;
    }

    private function processExpressionsSpecialFilter(): ?OmFilterExpressionInterface
    {
        $expressionsSpecialFilter = null;

        // If special filter is set
        if ($this->requestHandler->isSet($this->parameterNames->specialFilter())) {
            try {
                if (isset($this->dataGridDefinition->getSpecialFilters()[(int)($this->requestHandler->getInt($this->parameterNames->specialFilter()) - 1)])) {
                    $expressionsSpecialFilter = $this->dataGridDefinition->getSpecialFilters()[(int)($this->requestHandler->getInt($this->parameterNames->specialFilter()) - 1)]->getFilterExpression();
                }
            } catch (Exception) {
                // just don't add the field if there is something wrong with the field.
            }
        }

        return $expressionsSpecialFilter;
    }

    private function processExpressions(): array
    {
        $expressions = [];

        foreach ($this->getFilterableColumns() as $columnIndex => $column) {
            try {
                $operation = $this->getColumnFilterOperation($columnIndex, $column);
                $criteria = $this->getColumnFilterCriteria($columnIndex, $column);
            } catch (Exception) {
                continue;
            }

            try {
                $expressions[] = $this->operationToExpression($operation, $column->getName(), $criteria);
            } catch (OmDataGridOperationNotFoundException) {
                continue;
            }
        }

        return $expressions;
    }

    /**
     * @throws OmRequestHandlerOutOfDesiredLengthException
     * @throws OmRequestHandlerValueNotInWhiteListException
     */
    private function getColumnFilterOperation(int $columnIndex, OmDataGridColumnInterface $column): ?string
    {
        if ($column->getType() === 'date') {
            return $this->requestHandler->getStringForWhiteList($this->parameterNames->columnFilterOperation($columnIndex), self::POSSIBLE_OPERATIONS_DATE);
        }

        return $this->requestHandler->getStringForWhiteList($this->parameterNames->columnFilterOperation($columnIndex), self::POSSIBLE_OPERATIONS_STRING);
    }

    /**
     * @throws OmRequestHandlerOutOfDesiredLengthException
     */
    private function getColumnFilterCriteria(int $columnIndex, OmDataGridColumnInterface $column): string
    {
        $input = trim($this->requestHandler->getString($this->parameterNames->columnFilterCriteria($columnIndex)));

        if ($column->getType() === 'date') {
            $parsedDate = DateTime::createFromFormat('d.m.Y', $input);
            if ($parsedDate !== false) {
                return $parsedDate->format('Y-m-d');
            }
        }

        return $input;
    }

    private function getFilterableColumns(): array
    {
        // array filter preserves indexed which is important in the cases above (for column filters)
        return array_filter($this->dataGridDefinition->getColumns(), fn($column) => $column->isFilterable());
    }

    private function processLimit(): OmQueryLimit
    {
        $defaultEntriesPerPage = $this->dataGridDefinition->getPagingEntriesPerPageOptions()[$this->dataGridDefinition->getPagingDefaultEntriesPerPageOptionIndex()];

        try {
            $currentPage = $this->requestHandler->isSet($this->parameterNames->pagingCurrentPage())
                ? $this->requestHandler->getInt($this->parameterNames->pagingCurrentPage())
                : 1;
        } catch (Exception) {
            $currentPage = 1;
        }

        try {
            $entriesPerPage = $this->requestHandler->isSet($this->parameterNames->pagingEntriesPerPage())
                ? $this->dataGridDefinition->getPagingEntriesPerPageOptions()[$this->requestHandler->getInt($this->parameterNames->pagingEntriesPerPage())]
                : $defaultEntriesPerPage;
        } catch (Exception) {
            $entriesPerPage = $defaultEntriesPerPage;
        }

        return OmQueryLimit::startFromForLength(
            $currentPage > 1
                ? ($currentPage - 1) * $entriesPerPage
                : 0,
            $entriesPerPage
        );
    }
}
