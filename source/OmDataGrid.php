<?php

namespace Objement\OmPhpDataGrid;


use Objement\OmPhpDataGrid\Interfaces\OmDataGridDataSourceInterface;
use Objement\OmPhpDataGrid\Interfaces\OmDataGridDefinitionInterface;
use Objement\OmPhpDataGrid\Interfaces\OmDataGridFilterAdapterInterface;
use Objement\OmPhpDataGrid\Interfaces\OmDataGridRendererInterface;
use Objement\OmPhpDataGrid\Utils\OmDataGridHttpGetToOmDataGridFilterAdapter;

class OmDataGrid
{
    private static $lastDataGridId = 0;

    private OmDataGridDataSourceInterface $dataSource;
    private OmDataGridRendererInterface $renderer;
    private OmDataGridDefinitionInterface $gridDefinition;
    private OmDataGridFilterAdapterInterface $filterAdapter;
    private int $dataGridId;

    /**
     * OmDataGrid constructor.
     */
    public function __construct(
        OmDataGridDataSourceInterface $dataSource,
        OmDataGridRendererInterface   $renderer,
        OmDataGridDefinitionInterface $gridDefinition
    )
    {
        $this->dataGridId = self::$lastDataGridId++;
        $this->dataSource = $dataSource;
        $this->renderer = $renderer;
        $this->gridDefinition = $gridDefinition;

        $this->setFilterAdapter(new OmDataGridHttpGetToOmDataGridFilterAdapter($this->dataGridId, $this->gridDefinition));
    }

    public function render(): string
    {
        return $this->renderer->render($this->dataGridId, $this->gridDefinition, $this->dataSource, $this->filterAdapter->getFilterQuery());
    }

    /**
     * @param OmDataGridFilterAdapterInterface $filterAdapter
     * @return OmDataGrid
     */
    public function setFilterAdapter(OmDataGridFilterAdapterInterface $filterAdapter): OmDataGrid
    {
        $this->filterAdapter = $filterAdapter;
        return $this;
    }
}
