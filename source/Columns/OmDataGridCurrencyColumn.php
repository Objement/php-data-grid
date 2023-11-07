<?php

namespace Objement\OmPhpDataGrid\Columns;

use Objement\OmPhpDataGrid\Interfaces\OmDataGridColumnInterface;

class OmDataGridCurrencyColumn extends OmDataGridColumnBase implements OmDataGridColumnInterface
{
    private string $currencySign;

    public function __construct(string $name, string $caption, $currencySign='€')
    {
        parent::__construct($name, $caption);
        $this->currencySign = $currencySign;
    }

    public function getFormattedValue($value): string
    {
        /**
         * @var $value float
         */
        return number_format($value ?? 0, 2, ',', '').' '.$this->currencySign;
    }

    public function getFormattedValueHtml($value): string
    {
        return $this->getFormattedValue($value);
    }

    public function getType(): string
    {
        return 'currency';
    }
}

