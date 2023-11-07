<?php

namespace Objement\OmPhpDataGrid\Columns;

use Objement\OmPhpDataGrid\Interfaces\OmDataGridColumnInterface;

abstract class OmDataGridColumnBase implements OmDataGridColumnInterface
{
    protected string $caption;
    protected string $name;
    protected bool $isFilterable = false;
    protected bool $isHidden = false;
    /**
     * @var callable
     */
    protected $formatter;
    /**
     * @var callable|null
     */
    private $calculatedValueCallback;

    /**
     * DataGridColumnBase constructor.
     * @param string $caption
     * @param string $name
     */
    public function __construct(string $name, string $caption)
    {
        $this->caption = $caption;
        $this->name = $name;
    }

    public function getCaption(): string
    {
        return $this->caption;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setFormatter(callable $func): self
    {
        $this->formatter = $func;
        return $this;
    }

    public function isFilterable(): bool
    {
        return $this->isFilterable;
    }

    /**
     * @param bool $isFilterable
     * @return OmDataGridColumnBase
     */
    public function setIsFilterable(bool $isFilterable): OmDataGridColumnBase
    {
        $this->isFilterable = $isFilterable;
        return $this;
    }

    public function isHidden(): bool
    {
        return $this->isHidden;
    }

    public function setIsHidden($isHidden): self
    {
        $this->isHidden = $isHidden;
        return $this;
    }

    public function setValueCalculationCallback(?callable $callback): self
    {
        $this->calculatedValueCallback = $callback;
        return $this;
    }

    public function getValueCalculationCallback(): ?callable
    {
        return $this->calculatedValueCallback ?? null;
    }
}
