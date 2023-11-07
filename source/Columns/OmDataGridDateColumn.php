<?php


namespace Objement\OmPhpDataGrid\Columns;


use Objement\OmPhpDataGrid\Interfaces\OmDataGridColumnInterface;
use DateTime;

class OmDataGridDateColumn extends OmDataGridColumnBase implements OmDataGridColumnInterface
{
    public function getFormattedValue($value): string
    {
        if ($this->formatter != null) {
            $value = ($this->formatter)($value);
        }

        /**
         * @var $value DateTime
         */
        return $value ? $value->format('d.m.Y') : '';
    }

    public function getFormattedValueHtml($value): string
    {
        return $this->getFormattedValue($value);
    }

    public function getType(): string
    {
        return 'date';
    }
}

