<?php

namespace Objement\OmPhpDataGrid\RowOptions;

use Closure;
use Objement\OmPhpDataGrid\Interfaces\OmDataGridRowOptionConfirmationOptionsInterface;

class OmDataGridRowOptionConfirmationOptions implements OmDataGridRowOptionConfirmationOptionsInterface
{
    private string $text;
    private OmDataGridLinkRowOptionConfirmationTypes $type;
    private string|null|Closure $criticalMatchingText;

    /**
     * @param OmDataGridLinkRowOptionConfirmationTypes $type
     * @param string $text
     * @param string|Closure|null $criticalMatchingText
     */
    public function __construct(OmDataGridLinkRowOptionConfirmationTypes $type, string $text, string|null|Closure $criticalMatchingText = null)
    {
        $this->text = $text;
        $this->type = $type;
        $this->criticalMatchingText = $criticalMatchingText;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function getType(): OmDataGridLinkRowOptionConfirmationTypes
    {
        return $this->type;
    }

    /**
     * @param $row
     * @return string|null
     */
    public function getCriticalMatchingText($row): ?string
    {
        return is_callable($this->criticalMatchingText)
            ? ($this->criticalMatchingText)($row)
            : $this->criticalMatchingText;
    }
}
