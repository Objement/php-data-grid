<?php

namespace Objement\OmPhpDataGrid\Interfaces;

use Objement\OmPhpDataGrid\RowOptions\OmDataGridLinkRowOptionConfirmationTypes;

interface OmDataGridRowOptionConfirmationOptionsInterface
{
    function getText(): string;
    function getType(): OmDataGridLinkRowOptionConfirmationTypes;
    function getCriticalMatchingText($row): ?string;
}
