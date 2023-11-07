<?php


namespace Objement\OmPhpDataGrid\RowOptions;


use Objement\OmPhpDataGrid\Interfaces\OmDataGridRowOptionConfirmationOptionsInterface;
use Objement\OmPhpDataGrid\Interfaces\OmDataGridRowOptionInterface;
use Closure;

class OmDataGridLinkRowOption implements OmDataGridRowOptionInterface
{
    private string $caption;
    private bool $isVisible = true;
    private bool $isEnabled = true;
    private ?string $link = null;
    private string $icon;
    private ?string $tooltip = null;
    /**
     * @var callable|null
     */
    private $isVisibleCallback;
    /**
     * @var callable|null
     */
    private $isEnabledCallback;
    /**
     * @var callable|null
     */
    private $linkCallback;
    /**
     * @var OmDataGridRowOptionConfirmationOptions|null
     */
    private ?OmDataGridRowOptionConfirmationOptions $confirmationOptions = null;

    /**
     * DataGridLinkRowOption constructor.
     * @param string $caption
     * @param string $icon
     * @param callable|null $linkCallback
     */
    public function __construct(string $caption, string $icon, ?callable $linkCallback)
    {
        $this->caption = $caption;
        $this->linkCallback = $linkCallback;
        $this->icon = $icon;
    }

    /**
     * @return string
     */
    public function getCaption(): string
    {
        return $this->caption;
    }

    /**
     * @param string $caption
     * @return OmDataGridLinkRowOption
     */
    public function setCaption(string $caption): OmDataGridLinkRowOption
    {
        $this->caption = $caption;
        return $this;
    }

    /**
     * @param $row
     * @return bool
     */
    public function isVisible($row): bool
    {
        $callback = $this->isVisibleCallback;
        return $callback !== null
            ? $callback($row)
            : $this->isVisible;
    }

    /**
     * @param callable|null $isVisibleCallback
     * @return OmDataGridLinkRowOption
     */
    public function setIsVisibleCallback(?callable $isVisibleCallback): OmDataGridLinkRowOption
    {
        $this->isVisibleCallback = $isVisibleCallback;
        return $this;
    }

    /**
     * @param $row
     * @return bool
     */
    public function isEnabled($row): bool
    {
        $callback = $this->isEnabledCallback;
        return $callback !== null
            ? $callback($row)
            : $this->isEnabled;
    }

    /**
     * @param callable|null $isEnabledCallback
     * @return OmDataGridLinkRowOption
     */
    public function setIsEnabledCallback(?callable $isEnabledCallback): OmDataGridLinkRowOption
    {
        $this->isEnabledCallback = $isEnabledCallback;
        return $this;
    }

    /**
     * @param $row
     * @return string
     */
    public function getLink($row): string
    {
        $callback = $this->linkCallback;
        return ($callback !== null && $callback($row) !== null)
            ? $callback($row)
            : ($this->link ?? '');
    }

    /**
     * @param string $link
     * @return OmDataGridLinkRowOption
     */
    public function setLink(string $link): OmDataGridLinkRowOption
    {
        $this->link = $link;
        return $this;
    }

    /**
     * @return string
     */
    public function getIcon(): string
    {
        return $this->icon;
    }

    /**
     * @param string $icon
     * @return OmDataGridLinkRowOption
     */
    public function setIcon(string $icon): OmDataGridLinkRowOption
    {
        $this->icon = $icon;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getTooltip(): ?string
    {
        return $this->tooltip ?? $this->caption;
    }

    /**
     * @param string|null $tooltip
     * @return OmDataGridLinkRowOption
     */
    public function setTooltip(?string $tooltip): OmDataGridLinkRowOption
    {
        $this->tooltip = $tooltip;
        return $this;
    }

    /**
     * @return bool
     */
    public function isConfirmationEnabled(): bool
    {
        return isset($this->confirmationOptions);
    }

    /**
     * @param string $confirmationText
     * @return $this
     */
    public function setDefaultConfirmation(string $confirmationText): self
    {
        $this->confirmationOptions = new OmDataGridRowOptionConfirmationOptions(OmDataGridLinkRowOptionConfirmationTypes::Default, $confirmationText);
        return $this;
    }

    /**
     * @param string $confirmationText
     * @param string|Closure $criticalMatchingText
     * @return $this
     */
    public function setCriticalConfirmation(string $confirmationText, string|Closure $criticalMatchingText): self
    {
        $this->confirmationOptions = new OmDataGridRowOptionConfirmationOptions(OmDataGridLinkRowOptionConfirmationTypes::Critical, $confirmationText, $criticalMatchingText);
        return $this;
    }

    public function getConfirmationOptions(): ?OmDataGridRowOptionConfirmationOptionsInterface
    {
        return $this->confirmationOptions;
    }
}
