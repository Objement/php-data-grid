<?php


namespace Objement\OmPhpDataGrid\Columns;


use Objement\OmPhpDataGrid\Interfaces\OmDataGridColumnInterface;

class OmDataGridTextColumn extends OmDataGridColumnBase implements OmDataGridColumnInterface
{
    public function getFormattedValue($value): string
    {
        if ($this->formatter != null) {
            $value = ($this->formatter)($value);
        }

        return $value ?? '';
    }

    public function getFormattedValueHtml($value): string
    {
        return $this->getFormattedValue($value);
    }

    public function getType(): string
    {
        return 'text';
    }
}
