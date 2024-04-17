<?php


namespace Objement\OmPhpDataGrid;

use Objement\OmPhpDataGrid\Interfaces\OmDataGridDataSourceInterface;
use Objement\OmPhpDataGrid\Interfaces\OmDataGridDefinitionInterface;
use Objement\OmPhpDataGrid\Interfaces\OmDataGridRendererInterface;
use Objement\OmPhpDataGrid\Utils\OmDataGridHttpGetParameterNames;
use Objement\OmPhpUtils\Filters\OmFilterQueryInterface;
use Objement\OmPhpUtils\HttpRequests\OmRequestHandler;
use Objement\OmPhpUtils\OmUrlUtility;
use Objement\OmPhpUtils\Paging\OmPagingHelper;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFunction;

class OmDataGridRendererTwig implements OmDataGridRendererInterface
{
    private Environment $twig;

    /**
     * DataGridTwigRenderer constructor.
     */
    public function __construct(?string $customTemplatePath=null)
    {
        $this->twig = new Environment(new FilesystemLoader($customTemplatePath ?? __DIR__.DIRECTORY_SEPARATOR.'Templates'), [
            'debug' => false,
            'strict_variables' => true
        ]);
        $this->twig->addFunction(new TwigFunction('getCurrentUrlWithAdditionalParameters', fn($additionalQueryString) => OmUrlUtility::getCurrentUrlWithAdditionalParameters($additionalQueryString)));
    }

    public function render(int $dataGridId, OmDataGridDefinitionInterface $dataGridDefinition, OmDataGridDataSourceInterface $dataSource, ?OmFilterQueryInterface $filterQuery): string
    {
        $requestHandler = new OmRequestHandler($_GET);

        $parameterNames = new OmDataGridHttpGetParameterNames($dataGridId);

        $viewData = [
            'dataGridId' => $dataGridId,
            'gridDefinition' => $dataGridDefinition,
            'dataSource' => $dataSource,
            'rows' => $dataSource->getRows($filterQuery),
            'isFilterActive' => !empty($filterQuery->getExpressions()),
            'pagingEntriesPerPageOptions' => $dataGridDefinition->getPagingEntriesPerPageOptions(),
            'pagingDefaultEntriesPerPageOptionIndex' => $dataGridDefinition->getPagingDefaultEntriesPerPageOptionIndex(),
            'paging' => $this->createPaging($parameterNames, $requestHandler, $dataGridDefinition->getPagingEntriesPerPageOptions(), $dataSource->getTotalCount()),
            'httpGetRequestHandler' => $requestHandler
        ];

        return $this->twig->render('OmDataGrid.twig', $viewData);
    }

    private function createPaging(OmDataGridHttpGetParameterNames $parameterNames, OmRequestHandler $requestHandler, array $pagingEntriesPerPageOptions, int $totalCount): OmPagingHelper
    {
        $pagingEntriesPerPageOptionIndex = $requestHandler->isSet($parameterNames->pagingEntriesPerPage()) ? $requestHandler->getInt($parameterNames->pagingEntriesPerPage()) : 1;
        $pagingEntriesPerPage = $pagingEntriesPerPageOptions[$pagingEntriesPerPageOptionIndex] ?? $pagingEntriesPerPageOptions[1];

        $paging = new OmPagingHelper($totalCount, $pagingEntriesPerPage);

        if ($requestHandler->isSet($parameterNames->pagingCurrentPage())) {
            $paging->setRequestedPageNumber($requestHandler->getInt($parameterNames->pagingCurrentPage()));
        }

        return $paging;
    }
}
