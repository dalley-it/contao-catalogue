<?php

declare(strict_types=1);

namespace DalleyIt\ContaoCatalogue\Dca;

use Contao\DataContainer;
use Contao\Database;
use DalleyIt\ContaoCatalogue\Schema\SchemaRegistry;

final class RecordItemSchemaDca
{
    public function __construct(private readonly SchemaRegistry $schemas) {}

    public function onLoad(?DataContainer $dc = null): void
    {
        if (!$dc || !$dc->id) {
            return;
        }

        $db = Database::getInstance();
        $item = $db->prepare('SELECT pid,type,data_json FROM dait_cc_record_item WHERE id=?')->execute($dc->id)->fetchAssoc();
        if (!$item) {
            return;
        }

        $record = $db->prepare('SELECT pid FROM dait_cc_record WHERE id=?')->execute((int)$item['pid'])->fetchAssoc();
        if (!$record) {
            return;
        }

        $catalog = $db->prepare('SELECT schema_key FROM dait_cc_catalogue WHERE id=?')->execute((int)$record['pid'])->fetchAssoc();
        if (!$catalog) {
            return;
        }

        $schema = $this->schemas->getSchema((string)$catalog['schema_key']);
        $itemTypes = $schema['itemTypes'] ?? [];
        if (!is_array($itemTypes)) {
            return;
        }

        $type = (string)($item['type'] ?? '');
        $typeSchema = $itemTypes[$type] ?? null;
        if (!is_array($typeSchema)) {
            return;
        }

        $fields = $typeSchema['fields'] ?? [];
        if (!is_array($fields)) {
            return;
        }

        foreach ($fields as $name => $def) {
            if (!is_string($name) || !is_array($def)) {
                continue;
            }

            $GLOBALS['TL_DCA']['dait_cc_record_item']['fields'][$name] = array_merge(
                [
                    'eval' => ['tl_class' => 'w50'],
                    'load_callback' => [[self::class, 'loadJsonField']],
                    'save_callback' => [[self::class, 'saveJsonField']],
                ],
                $def,
                [
                    'sql' => null,
                ],
            );
        }

        $palette = '{type_legend},type,parent_id;{data_legend},'.implode(',', array_keys($fields)).',data_json';
        $GLOBALS['TL_DCA']['dait_cc_record_item']['palettes']['default'] = $palette;
    }

    public function onSubmit(DataContainer $dc): void
    {
        // no-op in MVP v2; placeholder for future indexing
    }

    public static function loadJsonField(mixed $value, DataContainer $dc): mixed
    {
        $db = Database::getInstance();
        $row = $db->prepare('SELECT data_json FROM dait_cc_record_item WHERE id=?')->execute($dc->id)->fetchAssoc();
        if (!$row || empty($row['data_json'])) {
            return null;
        }

        try {
            $data = json_decode((string)$row['data_json'], true, 512, JSON_THROW_ON_ERROR);
        } catch (\Throwable) {
            return null;
        }

        return $data[$dc->field] ?? null;
    }

    public static function saveJsonField(mixed $value, DataContainer $dc): mixed
    {
        $db = Database::getInstance();
        $row = $db->prepare('SELECT data_json FROM dait_cc_record_item WHERE id=?')->execute($dc->id)->fetchAssoc();
        $data = [];

        if ($row && !empty($row['data_json'])) {
            try {
                $data = json_decode((string)$row['data_json'], true, 512, JSON_THROW_ON_ERROR);
            } catch (\Throwable) {
                $data = [];
            }
        }

        $data[$dc->field] = $value;

        $db->prepare('UPDATE dait_cc_record_item SET data_json=? WHERE id=?')->execute(json_encode($data, JSON_UNESCAPED_UNICODE), $dc->id);

        return $value;
    }
}
