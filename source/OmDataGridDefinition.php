<?php


namespace Objement\OmPhpDataGrid;


use Objement\OmPhpDataGrid\Interfaces\OmDataGridColumnInterface;
use Objement\OmPhpDataGrid\Interfaces\OmDataGridDefinitionInterface;
use Objement\OmPhpDataGrid\Interfaces\OmDataGridRowOptionInterface;
use Objement\OmPhpDataGrid\Interfaces\OmDataGridSpecialFilterInterface;

class OmDataGridDefinition implements OmDataGridDefinitionInterface
{
    /**
     * @var OmDataGridColumnInterface[]
     */
    private array $columns;

    /**
     * @var OmDataGridDefinitionInterface[]|null
     */
    private ?array $rowOptions;
    /**
     * @var callable|null
     */
    private mixed $rowAttributeHtmlCallback = null;
    /**
     * @var callable|null
     */
    private $detailContentCallback = null;
    /**
     * @var callable
     */
    private $isDetailContentExpandedCallback;
    private bool $isSearchEnabled = false;
    /**
     * @var array|OmDataGridSpecialFilterInterface[]
     */
    private ?array $specialFilters = null;
    private bool $isPagingEnabled = false;
    private array $pagingEntriesPerPageOptions = [10, 25, 50, 100, 10000];
    private int $pagingDefaultEntriesPerPageOptionIndex = 1;

    /**
     * DataGridDefinitionInterface constructor.
     * @param OmDataGridColumnInterface[] $columns
     */
    public function __construct(array $columns = [], ?array $rowOptions = null)
    {
        $this->columns = $columns;
        $this->rowOptions = $rowOptions;
    }

    /**
     * @inheritDoc
     */
    public function setColumns(array $columns): OmDataGridDefinitionInterface
    {
        $this->columns = $columns;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addColumn(OmDataGridColumnInterface $column): OmDataGridDefinitionInterface
    {
        $this->columns[] = $column;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addRowOption(OmDataGridRowOptionInterface $rowOption): OmDataGridDefinitionInterface
    {
        $this->rowOptions[] = $rowOption;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    /**
     * @inheritDoc
     */
    public function getRowOptions(): ?array
    {
        return $this->rowOptions;
    }

    /**
     * @inheritDoc
     */
    public function setRowAttributeHtmlCallback(callable $rowAttributeHtmlCallback): OmDataGridDefinitionInterface
    {
        $this->rowAttributeHtmlCallback = $rowAttributeHtmlCallback;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getRowAttributeHtml(array|object $row): string
    {
        if ($this->rowAttributeHtmlCallback != null && is_callable($this->rowAttributeHtmlCallback)) {
            $attributeHtml = ($this->rowAttributeHtmlCallback)($row);
            return !empty($attributeHtml) ? ' ' . $attributeHtml : '';
        }

        return '';
    }

    /**
     * @inheritDoc
     */
    public function setDetailContentCallback(callable $detailContentCallback): OmDataGridDefinitionInterface
    {
        $this->detailContentCallback = $detailContentCallback;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getDetailContent(array|object $row): ?string
    {
        if ($this->detailContentCallback != null && is_callable($this->detailContentCallback)) {
            $detailContent = ($this->detailContentCallback)($row);
            return !empty($detailContent) ? $detailContent : null;
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public function setIsDetailContentExpandedCallback(callable $isDetailContentExpandedCallback): OmDataGridDefinitionInterface
    {
        $this->isDetailContentExpandedCallback = $isDetailContentExpandedCallback;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isDetailContentExpanded(array|object $row): bool
    {
        if ($this->isDetailContentExpandedCallback == null || ($this->isDetailContentExpandedCallback)($row) == null) {
            return false;
        }

        return ($this->isDetailContentExpandedCallback)($row);
    }

    /**
     * @return bool
     */
    public function isSearchEnabled(): bool
    {
        return $this->isSearchEnabled;
    }

    /**
     * @param bool $isSearchEnabled
     * @return OmDataGridDefinition
     */
    public function setIsSearchEnabled(bool $isSearchEnabled): OmDataGridDefinition
    {
        $this->isSearchEnabled = $isSearchEnabled;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getVisibleColumns(): array
    {
        return array_filter($this->getColumns(), fn(OmDataGridColumnInterface $column) => !$column->isHidden());
    }

    /**
     * @inheritDoc
     */
    public function setSpecialFilters(array $specialFilters): OmDataGridDefinitionInterface
    {
        $this->specialFilters = $specialFilters;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getSpecialFilters(): ?array
    {
        return $this->specialFilters;
    }

    /**
     * @inheritDoc
     */
    public function setIsPagingEnabled(bool $isPagingEnabled): OmDataGridDefinitionInterface
    {
        $this->isPagingEnabled = $isPagingEnabled;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isPagingEnabled(): bool
    {
        return $this->isPagingEnabled;
    }

    /**
     * @inheritDoc
     */
    public function setPagingEntriesPerPageOptions(array $options): OmDataGridDefinitionInterface
    {
        if (!$this->isPagingEnabled) {
            trigger_error('You probably should enable paging first else the page options will have no effect.', E_USER_WARNING);
        }

        if (empty($options)) {
            trigger_error('You need at least one entry.', E_USER_WARNING);
        }

        $this->pagingEntriesPerPageOptions = $options;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getPagingEntriesPerPageOptions(): array
    {
        return $this->pagingEntriesPerPageOptions;
    }

    /**
     * @inheritDoc
     */
    public function setPagingDefaultEntriesPerPageOptionIndex(int $index): OmDataGridDefinitionInterface
    {
        if (!$this->isPagingEnabled) {
            trigger_error('You probably should enable paging first, else the page options will have no effect.', E_USER_WARNING);
        }

        $this->pagingDefaultEntriesPerPageOptionIndex = isset($this->pagingEntriesPerPageOptions[$index]) ? $index : 0;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getPagingDefaultEntriesPerPageOptionIndex(): int
    {
        return $this->pagingDefaultEntriesPerPageOptionIndex;
    }
}
