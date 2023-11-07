<?php


namespace Objement\OmPhpDataGrid\Interfaces;


interface OmDataGridRowOptionInterface
{
    public function isVisible($row): bool;
    public function isEnabled($row): bool;
    public function getLink($row): ?string;
    public function getCaption(): string;
    public function getIcon(): ?string;
    public function getTooltip(): ?string;
    public function isConfirmationEnabled(): bool;
    public function getConfirmationOptions(): ?OmDataGridRowOptionConfirmationOptionsInterface;
}
