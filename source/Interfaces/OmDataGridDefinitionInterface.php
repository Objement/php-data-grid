<?php


namespace Objement\OmPhpDataGrid\Interfaces;

interface OmDataGridDefinitionInterface
{
    /**
     * @return OmDataGridColumnInterface[]
     */
    public function getColumns(): array;

    /**
     * Returns only visible columns
     * @return OmDataGridColumnInterface[]
     */
    public function getVisibleColumns(): array;

    /**
     * @param OmDataGridColumnInterface $column
     * @return OmDataGridDefinitionInterface
     */
    public function addColumn(OmDataGridColumnInterface $column): OmDataGridDefinitionInterface;

    /**
     * @param OmDataGridColumnInterface[] $columns
     * @return OmDataGridDefinitionInterface
     */
    public function setColumns(array $columns): OmDataGridDefinitionInterface;

    /**
     * @param OmDataGridRowOptionInterface $rowOption
     * @return OmDataGridDefinitionInterface
     */
    public function addRowOption(OmDataGridRowOptionInterface $rowOption): OmDataGridDefinitionInterface;

    /**
     * @return array|null
     */
    public function getRowOptions(): ?array;

    /**
     * @param callable $rowAttributeHtmlCallback
     * @return OmDataGridDefinitionInterface
     */
    public function setRowAttributeHtmlCallback(callable $rowAttributeHtmlCallback): OmDataGridDefinitionInterface;

    /**
     * @param array|object $row
     * @return string
     */
    public function getRowAttributeHtml(array|object $row): string;

    /**
     * @param callable $detailContentCallback
     * @return OmDataGridDefinitionInterface
     */
    public function setDetailContentCallback(callable $detailContentCallback): OmDataGridDefinitionInterface;

    /**
     * @param array|object $row
     * @return string|null
     */
    public function getDetailContent(array|object $row): ?string;

    /**
     * @param callable $isDetailContentExpandedCallback
     * @return OmDataGridDefinitionInterface
     */
    public function setIsDetailContentExpandedCallback(callable $isDetailContentExpandedCallback): OmDataGridDefinitionInterface;

    /**
     * @param array|object $row
     * @return bool
     */
    public function isDetailContentExpanded(array|object $row): bool;

    /**
     * @return bool
     */
    public function isSearchEnabled(): bool;

    /**
     * @param OmDataGridSpecialFilterInterface[] $specialFilters
     * @return OmDataGridDefinitionInterface
     */
    public function setSpecialFilters(array $specialFilters): OmDataGridDefinitionInterface;

    /**
     * @return OmDataGridSpecialFilterInterface[]|null
     */
    public function getSpecialFilters(): ?array;

    /**
     * @param bool $isPagingEnabled
     * @return OmDataGridDefinitionInterface
     */
    public function setIsPagingEnabled(bool $isPagingEnabled): OmDataGridDefinitionInterface;

    /**
     * @return bool
     */
    public function isPagingEnabled(): bool;

    /**
     * @param array $options
     * @return OmDataGridDefinitionInterface
     */
    public function setPagingEntriesPerPageOptions(array $options): OmDataGridDefinitionInterface;

    /**
     * @return array
     */
    public function getPagingEntriesPerPageOptions(): array;

    /**
     * Sets the default selected option index for the "entries per page" dropdown.
     * @param int $index
     * @return OmDataGridDefinitionInterface
     */
    public function setPagingDefaultEntriesPerPageOptionIndex(int $index): OmDataGridDefinitionInterface;

    /**
     * Gets the default selected option index for the "entries per page" dropdown.
     * @return int
     */
    public function getPagingDefaultEntriesPerPageOptionIndex(): int;
}
