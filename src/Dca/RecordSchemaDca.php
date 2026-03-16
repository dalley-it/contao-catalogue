<?php

declare(strict_types=1);

namespace DalleyIt\ContaoCatalogue\Dca;

use Contao\DataContainer;
use Contao\Database;
use DalleyIt\ContaoCatalogue\Dictionary\DictionaryProvider;
use DalleyIt\ContaoCatalogue\Schema\SchemaRegistry;

final class RecordSchemaDca
{
    public function __construct(
        private readonly SchemaRegistry $schemas,
        private readonly DictionaryProvider $dictionaryProvider,
    ) {}

    public function onLoad(?DataContainer $dc = null): void
    {
        if (!$dc || !$dc->id) {
            return;
        }

        $db = Database::getInstance();
        $row = $db->prepare('SELECT pid, data_json, language FROM dait_cc_record WHERE id=?')->execute($dc->id)->fetchAssoc();
        if (!$row) {
            return;
        }

        $catalog = $db->prepare('SELECT schema_key, dictionaryKey FROM dait_cc_catalogue WHERE id=?')->execute((int) $row['pid'])->fetchAssoc();
        if (!$catalog) {
            return;
        }

        $schemaKey = (string)$catalog['schema_key'];
        $schema = $this->schemas->getSchema($schemaKey);

        if (!$schema) {
            return;
        }

        $language = (string)($row['language'] ?? '');
        $defaultDictionaryKey = (string) ($catalog['dictionaryKey'] ?? '');

        $fields = $schema['fields'] ?? [];
        if (!is_array($fields)) {
            return;
        }

        // Inject fields into DCA as virtual fields stored in data_json
        foreach ($fields as $name => $def) {
            if (!is_string($name) || !is_array($def)) {
                continue;
            }

            // Helper: dictionarySelect uses a dictionary table as option source.
            if (($def['inputType'] ?? '') === 'dictionarySelect') {
                $def['inputType'] = 'select';
                $dictKey = (string) ($def['dictionaryKey'] ?? $defaultDictionaryKey);
                $options = $this->dictionaryProvider->getOptions($dictKey, $language);
                $def['options'] = array_keys($options);
                $def['reference'] = $options;
                $def['eval'] = array_merge(['includeBlankOption' => true, 'chosen' => true], $def['eval'] ?? []);
            }

            $GLOBALS['TL_DCA']['dait_cc_record']['fields'][$name] = array_merge(
                [
                    'eval' => ['tl_class' => 'w50'],
                    'load_callback' => [[self::class, 'loadJsonField']],
                    'save_callback' => [[self::class, 'saveJsonField']],
                ],
                $def,
                [
                    'sql' => null, // virtual
                ],
            );
        }

        // Build palette: base + schema fields in data_legend
        $palette = '{title_legend},title,alias,language,languageMain,published;';

        $palette .= '{data_legend},';
        $palette .= implode(',', array_keys($fields));
        $palette .= ',data_json;';

        $palette .= '{index_legend},idx_taxonomy,idx_relation_id;{publish_legend},start,stop';

        $GLOBALS['TL_DCA']['dait_cc_record']['palettes']['default'] = $palette;
    }

    public static function loadJsonField(mixed $value, DataContainer $dc): mixed
    {
        $db = Database::getInstance();
        $row = $db->prepare('SELECT data_json FROM dait_cc_record WHERE id=?')->execute($dc->id)->fetchAssoc();
        if (!$row || empty($row['data_json'])) {
            return null;
        }

        try {
            $data = json_decode((string)$row['data_json'], true, 512, JSON_THROW_ON_ERROR);
        } catch (\Throwable) {
            return null;
        }

        $field = $dc->field;
        return $data[$field] ?? null;
    }

    public static function saveJsonField(mixed $value, DataContainer $dc): mixed
    {
        $db = Database::getInstance();
        $row = $db->prepare('SELECT data_json FROM dait_cc_record WHERE id=?')->execute($dc->id)->fetchAssoc();
        $data = [];

        if ($row && !empty($row['data_json'])) {
            try {
                $data = json_decode((string)$row['data_json'], true, 512, JSON_THROW_ON_ERROR);
            } catch (\Throwable) {
                $data = [];
            }
        }

        $data[$dc->field] = $value;

        $db->prepare('UPDATE dait_cc_record SET data_json=? WHERE id=?')->execute(json_encode($data, JSON_UNESCAPED_UNICODE), $dc->id);

        return $value;
    }
}
