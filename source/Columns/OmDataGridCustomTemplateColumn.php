<?php

namespace Objement\OmPhpDataGrid\Columns;

use Objement\OmPhpDataGrid\Interfaces\OmDataGridColumnInterface;

class OmDataGridCustomTemplateColumn extends OmDataGridColumnBase implements OmDataGridColumnInterface
{
    private string $customTemplateUrl;

    public function __construct(string $name, string $caption, string $customTemplateUrl)
    {
        parent::__construct($name, $caption);

        $this->customTemplateUrl = $customTemplateUrl;
    }

    /**
     * @return string
     */
    public function getCustomTemplateUrl(): string
    {
        return $this->customTemplateUrl;
    }

    public function getFormattedValue($value): string
    {
        $value = self::getFormattedValue($value);

        return $value ?? '';
    }

    public function getFormattedValueHtml($value): string
    {
        return $this->getFormattedValue($value);
    }

    public function getType(): string
    {
        return 'custom_template';
    }
}
