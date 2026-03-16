<?php

/**
 * Example schema.
 *
 * Copy this file to <project>/contao/catalog_schemas/<your-key>.php and adjust it.
 */

return [
    // Virtual fields stored in dait_cc_record.data_json
    'fields' => [
        'taxonomy' => [
            'label' => ['Taxonomy', 'Generic classification value (e.g. region, category).'],
            'inputType' => 'dictionarySelect',
            'dictionaryKey' => 'example_taxonomy',
            'eval' => ['tl_class' => 'w50'],
        ],
        'relation_id' => [
            'label' => ['Relation ID', 'Numeric relation to another record (optional).'],
            'inputType' => 'text',
            'eval' => ['rgxp' => 'digit', 'tl_class' => 'w50'],
        ],
        'description' => [
            'label' => ['Description', 'Free text description.'],
            'inputType' => 'textarea',
            'eval' => ['tl_class' => 'clr long', 'rows' => 6],
        ],
    ],

    // Mapping of database index columns to JSON paths.
    // These columns are used for filtering and relations.
    'indexes' => [
        'idx_taxonomy' => 'taxonomy',
        'idx_relation_id' => 'relation_id',
    ],

    // Optional nested item types for dait_cc_record_item.
    'itemTypes' => [
        'block' => [
            'label' => 'Block',
            'fields' => [
                'headline' => [
                    'label' => ['Headline', 'Block headline.'],
                    'inputType' => 'text',
                    'eval' => ['maxlength' => 255, 'tl_class' => 'w50'],
                ],
                'text' => [
                    'label' => ['Text', 'Block text.'],
                    'inputType' => 'textarea',
                    'eval' => ['tl_class' => 'clr long', 'rows' => 6],
                ],
            ],
        ],
    ],
];
