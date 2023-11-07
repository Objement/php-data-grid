<?php


namespace Objement\OmPhpDataGrid\Columns;


use Objement\OmPhpDataGrid\Interfaces\OmDataGridColumnInterface;

/**
 *
 * The value from the data source needs to be an array of the following pattern:
 * [
 *   'src' => 'URL.fileext',
 *   'alt' => 'Alt text'  // <- this is optional.
 *   'state' => 'new' OR 'processing'
 * ]
 *
 * Class DataGridImageThumbnailColumn
 * @package BibliothekRegister\Site\Helpers\OmDataGrid\Columns
 */
class OmDataGridImageThumbnailColumn extends OmDataGridColumnBase implements OmDataGridColumnInterface
{
    public function getFormattedValue($value): string
    {
        return $value ? ($value['src'] ?? $value) : '';
    }

    public function getFormattedValueHtml($value): string
    {
        $attrSrc = $value['src'] ?? '';
        $attrAlt = $value['alt'] ?? '';

        $textState = null;
        if (!empty($value['state'])) {
            if ($value['state'] == 'new') {
                $textState = 'Warte auf Verarbeitung...';
            } elseif ($value['state'] == 'processing') {
                $textState = 'Verarbeitung läuft...';
            }
        }

        $imgTag = '<img src="' . $attrSrc . '" alt="' . $attrAlt . '">';
        $imageHtml = isset($value['srcFullSize'])
            ? sprintf('<a href="%s" target="_blank">%s</a>', $value['srcFullSize'], $imgTag)
            : $imgTag;

        return '<div style="background: black; color: white; aspect-ratio: 4/3; display: flex; align-items: center; justify-items: center; text-align: center;">' .
            ($textState ? '<small>' . $textState . '</small>' : '') .
            (!empty($value['src']) ? $imageHtml : '') .
            '</div>';
    }

    public function getType(): string
    {
        return 'thumbnail';
    }
}

