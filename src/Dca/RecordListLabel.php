<?php

declare(strict_types=1);

namespace DalleyIt\ContaoCatalogue\Dca;

final class RecordListLabel
{
    public function label(array $row): string
    {
        $lang = $row['language'] ?: '-';
        $taxonomy = $row['idx_taxonomy'] ?: '-';
        return sprintf('%s <span style="color:#999">[%s • %s]</span>', $row['title'], $lang, $taxonomy);
    }
}
