<?php


namespace Objement\OmPhpDataGrid;


use Objement\OmPhpDataGrid\Interfaces\OmDataGridColumnInterface;
use Objement\OmPhpDataGrid\Interfaces\OmDataGridDataSourceInterface;
use Objement\OmPhpUtils\Filters\OmFilterQueryInterface;

class OmDataGridArrayDataSource implements OmDataGridDataSourceInterface
{
    protected array $data;

    /**
     * DataGridArrayDataSource constructor.
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function getColumnValue(int $rowNumber, OmDataGridColumnInterface $column)
    {
        $row = $this->data[$rowNumber] ?? null;
        if (!$row) {
            return null;
        }

        $columnName = $column->getName();

        if ($column->getValueCalculationCallback() !== null) {
            return $column->getValueCalculationCallback()($row);
        }

        if (is_object($row)) {

            $columnName = strtoupper($columnName[0]) . substr($columnName, 1);

            $getterName = 'get' . $columnName;
            if (method_exists($row, $getterName) && is_callable([$row, $getterName])) {
                return call_user_func([$row, $getterName]);
            } else {
                trigger_error('Getter with name "' . $getterName . '" doesnt exist.', E_USER_ERROR);
            }
        }

        if (!is_array($row)) {
            trigger_error('Row must be an associative array or an object.', E_USER_ERROR);
        }

        return $row[$columnName] ?? null;
    }

    public function getRows(?OmFilterQueryInterface $filterQuery): array
    {
        // @todo use filterquery
        return $this->data;
    }

    public function getTotalCount(): int
    {
        return count($this->data);
    }
}
