<?php

declare(strict_types=1);

namespace DalleyIt\ContaoCatalogue\Schema;

use Contao\System;

final class SchemaRegistry
{
    /**
     * Returns the schema definition for records.
     *
     * The schema file must return an array, typically containing:
     * - fields: associative array of virtual fields stored in data_json
     * - indexes: mapping of database index columns to JSON paths
     * - itemTypes: optional nested item type schemas for dait_cc_record_item
     */
    public function getRecordSchema(string $schemaKey): array
    {
        return $this->getSchema($schemaKey);
    }

    /**
     * @return array<string, array>
     */
    public function getSchema(string $schemaKey): array
    {
        $projectDir = System::getContainer()->getParameter('kernel.project_dir');
        $path = $projectDir.'/contao/catalog_schemas/'.$schemaKey.'.php';

        if (!is_file($path)) {
            return [];
        }

        $schema = include $path;

        return is_array($schema) ? $schema : [];
    }
}
