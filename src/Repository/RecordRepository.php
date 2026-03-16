<?php

declare(strict_types=1);

namespace DalleyIt\ContaoCatalogue\Repository;

use Contao\Database;

final class RecordRepository
{
    public function getCatalogJumpToPageId(int $catalogId): int
    {
        $row = Database::getInstance()
            ->prepare('SELECT jumpTo FROM dait_cc_catalogue WHERE id=?')
            ->execute($catalogId)
            ->fetchAssoc();

        return $row ? (int)$row['jumpTo'] : 0;
    }

    public function getCatalogDictionaryKey(int $catalogId): string
    {
        $row = Database::getInstance()
            ->prepare('SELECT dictionaryKey FROM dait_cc_catalogue WHERE id=?')
            ->execute($catalogId)
            ->fetchAssoc();

        return $row ? (string) $row['dictionaryKey'] : '';
    }

    /**
     * @return array{items: array<int,array>, total:int}
     */
    public function findList(int $catalogId, string $language, string $taxonomy, int $page, int $perPage, string $sortMode): array
    {
        $db = Database::getInstance();

        $where = 'pid=? AND published='1'';
        $params = [$catalogId];

        if ($language !== '') {
            $where .= ' AND language=?';
            $params[] = $language;
        }

        if ($taxonomy !== '') {
            $where .= ' AND idx_taxonomy=?';
            $params[] = $taxonomy;
        }

        $orderBy = $this->orderBy($sortMode);

        $total = (int)$db->prepare('SELECT COUNT(*) AS c FROM dait_cc_record WHERE '.$where)->execute(...$params)->c;

        $limit = $perPage > 0 ? $perPage : 0;
        $offset = ($limit > 0) ? ($page - 1) * $limit : 0;

        $sql = 'SELECT id,title,alias,language,idx_taxonomy,data_json FROM dait_cc_record WHERE '.$where.' ORDER BY '.$orderBy;
        if ($limit > 0) {
            $sql .= ' LIMIT '.$offset.','.$limit;
        }

        $stmt = $db->prepare($sql)->execute(...$params);
        $items = [];

        while ($stmt->next()) {
            $items[] = [
                'id' => (int)$stmt->id,
                'title' => (string)$stmt->title,
                'alias' => (string)$stmt->alias,
                'language' => (string)$stmt->language,
                'taxonomy' => (string)$stmt->idx_taxonomy,
                'data' => $this->decodeJson((string)$stmt->data_json),
            ];
        }

        return ['items' => $items, 'total' => $total];
    }

    public function findReaderRecord(int $catalogId, string $language, string $item): ?array
    {
        $db = Database::getInstance();

        // Allow numeric id
        if (ctype_digit($item)) {
            $row = $db->prepare('SELECT * FROM dait_cc_record WHERE id=? AND pid=? AND published='1'')
                ->execute((int)$item, $catalogId)
                ->fetchAssoc();
        } else {
            $row = $db->prepare('SELECT * FROM dait_cc_record WHERE alias=? AND pid=? AND published='1'')
                ->execute($item, $catalogId)
                ->fetchAssoc();
        }

        if (!$row) {
            return null;
        }

        // Language match: if record language differs, attempt to resolve translation
        if ($language !== '' && (string)$row['language'] !== $language) {
            $translated = $this->findTranslationOf((int)$row['id'], $language);
            if ($translated) {
                $row = $translated;
            }
        }

        $row['data'] = $this->decodeJson((string)($row['data_json'] ?? ''));

        return $row;
    }

    /**
     * @return array{prev:?array,next:?array}
     */
    public function getPrevNext(int $catalogId, string $language, string $taxonomy, string $sortMode, int $currentId): array
    {
        $db = Database::getInstance();

        $where = 'pid=? AND published='1'';
        $params = [$catalogId];

        if ($language !== '') {
            $where .= ' AND language=?';
            $params[] = $language;
        }
        if ($taxonomy !== '') {
            $where .= ' AND idx_taxonomy=?';
            $params[] = $taxonomy;
        }

        $orderBy = $this->orderBy($sortMode);

        $ids = [];
        $stmt = $db->prepare('SELECT id,title,alias FROM dait_cc_record WHERE '.$where.' ORDER BY '.$orderBy)->execute(...$params);
        while ($stmt->next()) {
            $ids[] = ['id'=>(int)$stmt->id,'title'=>(string)$stmt->title,'alias'=>(string)$stmt->alias];
        }

        $pos = null;
        foreach ($ids as $i => $row) {
            if ($row['id'] === $currentId) { $pos = $i; break; }
        }

        if ($pos === null) {
            return ['prev' => null, 'next' => null];
        }

        return [
            'prev' => $ids[$pos-1] ?? null,
            'next' => $ids[$pos+1] ?? null,
        ];
    }

    /**
     * @return array<string,int> language => recordId
     */
    public function getTranslations(int $recordId): array
    {
        $db = Database::getInstance();
        $row = $db->prepare('SELECT id,language,languageMain FROM dait_cc_record WHERE id=?')->execute($recordId)->fetchAssoc();
        if (!$row) {
            return [];
        }

        $id = (int)$row['id'];
        $main = (int)$row['languageMain'];

        $masterId = $main > 0 ? $main : $id;

        $result = [];
        $stmt = $db->prepare('SELECT id,language FROM dait_cc_record WHERE id=? OR languageMain=?')->execute($masterId, $masterId);
        while ($stmt->next()) {
            $lang = (string)$stmt->language;
            if ($lang !== '') {
                $result[$lang] = (int)$stmt->id;
            }
        }

        return $result;
    }

    /**
     * Returns records that reference a parent entity via idx_relation_id.
     * This is useful for "child" entities (e.g. jobs) that belong to a "parent" (e.g. organisation).
     */
    public function findByRelationId(int $catalogId, int $relationId, string $language, string $taxonomy, string $sortMode = 'title_asc'): array
    {
        $db = Database::getInstance();

        $where = 'pid=? AND published='1' AND idx_relation_id=?';
        $params = [$catalogId, $relationId];

        if ($language !== '') {
            $where .= ' AND language=?';
            $params[] = $language;
        }
        if ($taxonomy !== '') {
            $where .= ' AND idx_taxonomy=?';
            $params[] = $taxonomy;
        }

        $orderBy = $this->orderBy($sortMode);
        $stmt = $db->prepare('SELECT id,title,alias,idx_taxonomy,data_json FROM dait_cc_record WHERE '.$where.' ORDER BY '.$orderBy)->execute(...$params);
        $items = [];
        while ($stmt->next()) {
            $items[] = [
                'id' => (int)$stmt->id,
                'title' => (string)$stmt->title,
                'alias' => (string)$stmt->alias,
                'taxonomy' => (string)$stmt->idx_taxonomy,
                'data' => $this->decodeJson((string)$stmt->data_json),
            ];
        }
        return $items;
    }

    private function findTranslationOf(int $recordId, string $language): ?array
    {
        $db = Database::getInstance();
        $row = $db->prepare('SELECT id,languageMain FROM dait_cc_record WHERE id=?')->execute($recordId)->fetchAssoc();
        if (!$row) {
            return null;
        }

        $main = (int)$row['languageMain'];
        $masterId = $main > 0 ? $main : $recordId;

        $translated = $db->prepare('SELECT * FROM dait_cc_record WHERE (id=? OR languageMain=?) AND language=? AND published='1'')
            ->execute($masterId, $masterId, $language)
            ->fetchAssoc();

        return $translated ?: null;
    }

    private function orderBy(string $sortMode): string
    {
        return match ($sortMode) {
            'title_desc' => 'title DESC',
            'sorting_asc' => 'sorting ASC',
            'sorting_desc' => 'sorting DESC',
            default => 'title ASC',
        };
    }

    private function decodeJson(string $json): array
    {
        if ($json === '') return [];
        try {
            $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
            return is_array($data) ? $data : [];
        } catch (\Throwable) {
            return [];
        }
    }
}
