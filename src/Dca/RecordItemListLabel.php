<?php

declare(strict_types=1);

namespace DalleyIt\ContaoCatalogue\Dca;

final class RecordItemListLabel
{
    public function label(array $row): string
    {
        $type = $row['type'] ?: '-';
        $parent = (int)($row['parent_id'] ?? 0);
        return sprintf('%s <span style="color:#999">(#%d)</span>', $type, $parent);
    }
}
