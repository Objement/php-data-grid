<?php


namespace Objement\OmPhpDataGrid\Interfaces;


interface OmDataGridColumnInterface
{
    function getCaption(): string;
    function getName(): string;
    function getType(): string;
    function isFilterable(): bool;
    function isHidden(): bool;

    /**
     * @param $value
     * @return string
     */
    function getFormattedValue($value): string;

    /**
     * @param $value
     * @return string
     */
    function getFormattedValueHtml($value): string;

    /**
     * @param callable|null $callback ($row: array) => mixed
     * @return OmDataGridColumnInterface
     */
    function setValueCalculationCallback(?callable $callback): self;

    /**
     * @return callable|null
     */
    function getValueCalculationCallback(): ?callable;
}
