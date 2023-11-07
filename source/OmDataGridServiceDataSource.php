<?php


namespace Objement\OmPhpDataGrid;


use Objement\OmPhpDataGrid\Interfaces\OmDataGridDataSourceInterface;
use Objement\OmPhpUtils\Filters\OmFilterQueryInterface;
use Objement\OmPhpUtils\Filters\OmFilterQueryResultInterface;

class OmDataGridServiceDataSource extends OmDataGridArrayDataSource implements OmDataGridDataSourceInterface
{
    private int $totalCount = 0;
    /**
     * @var callable (?OmFilterQueryInterface $filterQuery) => OmFilterQueryResultInterface
     */
    private $callbackGetRows;

    public function __construct(callable $callbackGetRows)
    {
        parent::__construct([]);
        $this->callbackGetRows = $callbackGetRows;
    }

    public function getTotalCount(): int
    {
        return $this->totalCount;
    }

    public function getRows(?OmFilterQueryInterface $filterQuery): array
    {
        /**
         * @var $filterResult OmFilterQueryResultInterface
         */
        $filterResult = ($this->callbackGetRows)($filterQuery);

        $this->data = $filterResult->getResults();
        $this->totalCount = $filterResult->getTotalCount();

        return $this->data;
    }
}
