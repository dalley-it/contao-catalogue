<?php

declare(strict_types=1);

namespace DalleyIt\ContaoCatalogue\Dca;

use Contao\DataContainer;
use Contao\Database;
use DalleyIt\ContaoCatalogue\Schema\SchemaRegistry;

final class RecordIndexer
{
    public function __construct(
        private readonly SchemaRegistry $schemaRegistry,
    ) {
    }

    public function onSubmit(DataContainer $dc): void
    {
        if (!$dc->id) {
            return;
        }

        $db = Database::getInstance();
        $row = $db->prepare('SELECT data_json, pid FROM dait_cc_record WHERE id=?')->execute($dc->id)->fetchAssoc();
        if (!$row) {
            return;
        }

        $catalog = $db->prepare('SELECT schema_key FROM dait_cc_catalogue WHERE id=?')->execute((int) $row['pid'])->fetchAssoc();
        $schemaKey = (string) ($catalog['schema_key'] ?? '');

        $data = [];
        if (!empty($row['data_json'])) {
            try {
                $data = json_decode($row['data_json'], true, 512, JSON_THROW_ON_ERROR);
            } catch (\Throwable) {
                $data = [];
            }
        }

        $schema = $this->schemaRegistry->getRecordSchema($schemaKey);
        $indexes = $schema['indexes'] ?? [
            'idx_taxonomy' => 'taxonomy',
            'idx_relation_id' => 'relation_id',
        ];

        $idxTaxonomy = (string) $this->getValueByPath($data, (string) ($indexes['idx_taxonomy'] ?? 'taxonomy'));
        $idxRelationId = (int) $this->getValueByPath($data, (string) ($indexes['idx_relation_id'] ?? 'relation_id'));

        $db->prepare('UPDATE dait_cc_record SET idx_taxonomy=?, idx_relation_id=? WHERE id=?')
            ->execute($idxTaxonomy, $idxRelationId, $dc->id);
    }

    /**
     * Returns a value from a nested array using dot notation (e.g. "company.address.city").
     */
    private function getValueByPath(array $data, string $path): mixed
    {
        if ($path === '') {
            return null;
        }

        $segments = explode('.', $path);
        $value = $data;

        foreach ($segments as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return null;
            }
            $value = $value[$segment];
        }

        return $value;
    }
}
