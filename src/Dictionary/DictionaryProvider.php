<?php

declare(strict_types=1);

namespace DalleyIt\ContaoCatalogue\Dictionary;

use Contao\Database;

final class DictionaryProvider
{
    /**
     * @return array<string,string> code => label (language-aware if available)
     */
    public function getOptions(string $dictKey, string $language = ''): array
    {
        $db = Database::getInstance();

        $dict = $db->prepare('SELECT id FROM dait_cc_dictionary WHERE dict_key=?')->execute($dictKey)->fetchAssoc();
        if (!$dict) {
            return [];
        }

        $dictId = (int)$dict['id'];

        // Prefer current language, fallback to entries without language, then anything.
        $items = [];
        if ($language !== '') {
            $res = $db->prepare('SELECT code,label FROM dait_cc_dictionary_item WHERE pid=? AND language=? ORDER BY sorting')
                ->execute($dictId, $language);
            while ($res->next()) {
                $items[$res->code] = $res->label;
            }
        }

        if (!$items) {
            $res = $db->prepare('SELECT code,label FROM dait_cc_dictionary_item WHERE pid=? AND (language="" OR language IS NULL) ORDER BY sorting')
                ->execute($dictId);
            while ($res->next()) {
                $items[$res->code] = $res->label;
            }
        }

        if (!$items) {
            $res = $db->prepare('SELECT code,label FROM dait_cc_dictionary_item WHERE pid=? ORDER BY sorting')->execute($dictId);
            while ($res->next()) {
                $items[$res->code] = $res->label;
            }
        }

        return $items;
    }
}
