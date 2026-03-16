<?php

declare(strict_types=1);

namespace DalleyIt\ContaoCatalogue\Dca;

use Contao\Backend;
use Contao\DataContainer;
use Contao\Image;
use Contao\StringUtil;
use Contao\Versions;

final class ToggleHelper
{
    public function toggleIcon(array $row, string $href, string $label, string $title, string $icon, string $attributes, string $table): string
    {
        $published = ($row['published'] ?? '') === '1';

        $icon = $published ? 'visible.svg' : 'invisible.svg';
        $href .= '&id='.$row['id'].'&state='.($published ? '' : '1');

        return '<a href="'.Backend::addToUrl($href).'" title="'.StringUtil::specialchars($title).'"'.$attributes.'>'
            .Image::getHtml($icon, $label).'</a> ';
    }
}
