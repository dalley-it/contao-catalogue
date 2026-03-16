<?php

declare(strict_types=1);

use Contao\DC_Table;

$GLOBALS['TL_DCA']['dait_cc_record'] = [
    'config' => [
        'dataContainer' => DC_Table::class,
        'ptable' => 'dait_cc_catalogue',
        'ctable' => ['dait_cc_record_item'],
        'onsubmit_callback' => [
            ['DalleyIt\\ContaoCatalogue\\Dca\\RecordIndexer', 'onSubmit'],
        ],
        'onload_callback' => [
            ['DalleyIt\\ContaoCatalogue\\Dca\\RecordSchemaDca', 'onLoad'],
        ],
        'sql' => [
            'keys' => [
                'id' => 'primary',
                'pid,published' => 'index',
                'pid,sorting' => 'index',
                'alias' => 'index',
                'languageMain' => 'index',
                'idx_taxonomy' => 'index',
                'idx_relation_id' => 'index',
            ],
        ],
    ],
    'list' => [
        'sorting' => [
            'mode' => 4,
            'fields' => ['sorting'],
            'headerFields' => ['title', 'schema_key'],
            'panelLayout' => 'filter;search,limit',
            'child_record_callback' => ['DalleyIt\\ContaoCatalogue\\Dca\\RecordListLabel', 'label'],
        ],
        'global_operations' => [
            'all' => [
                'href' => 'act=select',
                'class' => 'header_edit_all',
                'attributes' => 'onclick="Backend.getScrollOffset()" accesskey="e"',
            ],
        ],
        'operations' => [
            'edit' => [
                'href' => 'act=edit',
                'icon' => 'edit.svg',
            ],
            'children' => [
                'href' => 'table=dait_cc_record_item',
                'icon' => 'children.svg',
            ],
            'copy' => [
                'href' => 'act=copy',
                'icon' => 'copy.svg',
            ],
            'delete' => [
                'href' => 'act=delete',
                'icon' => 'delete.svg',
                'attributes' => 'onclick="if(!confirm(\'DELETE?\'))return false;Backend.getScrollOffset()"',
            ],
            'toggle' => [
                'icon' => 'visible.svg',
                'button_callback' => ['DalleyIt\\ContaoCatalogue\\Dca\\ToggleHelper', 'toggleIcon'],
            ],
            'show' => [
                'href' => 'act=show',
                'icon' => 'show.svg',
            ],
        ],
    ],
    'palettes' => [
        // Base palette; schema fields are injected by RecordSchemaDca::onLoad into a palette named 'default'
        'default' => '{title_legend},title,alias,language,languageMain,published;{index_legend},idx_taxonomy,idx_relation_id;{data_legend},data_json;{publish_legend},start,stop',
    ],
    'fields' => [
        'id' => ['sql' => "int(10) unsigned NOT NULL auto_increment"],
        'pid' => [
            'foreignKey' => 'dait_cc_catalogue.title',
            'sql' => "int(10) unsigned NOT NULL default 0",
            'relation' => ['type' => 'belongsTo', 'load' => 'lazy'],
        ],
        'tstamp' => ['sql' => "int(10) unsigned NOT NULL default 0"],
        'sorting' => ['sql' => "int(10) unsigned NOT NULL default 0"],
        'title' => [
            'inputType' => 'text',
            'search' => true,
            'eval' => ['mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50'],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'alias' => [
            'inputType' => 'text',
            'eval' => ['rgxp' => 'alias', 'maxlength' => 128, 'tl_class' => 'w50'],
            'sql' => "varchar(128) NOT NULL default ''",
        ],
        'language' => [
            'inputType' => 'text',
            'eval' => ['maxlength' => 16, 'tl_class' => 'w50'],
            'sql' => "varchar(16) NOT NULL default ''",
        ],
        'languageMain' => [
            'inputType' => 'text',
            'eval' => ['rgxp' => 'digit', 'tl_class' => 'w50'],
            'sql' => "int(10) unsigned NOT NULL default 0",
        ],
        'data_json' => [
            'inputType' => 'textarea',
            'eval' => ['decodeEntities' => true, 'tl_class' => 'clr long', 'rows' => 6],
            'sql' => "longtext NULL",
        ],
        'idx_taxonomy' => [
            'inputType' => 'text',
            'filter' => true,
            'eval' => ['maxlength' => 16, 'tl_class' => 'w50'],
            'sql' => "varchar(16) NOT NULL default ''",
        ],
        'idx_relation_id' => [
            'inputType' => 'text',
            'filter' => true,
            'eval' => ['rgxp' => 'digit', 'tl_class' => 'w50'],
            'sql' => "int(10) unsigned NOT NULL default 0",
        ],
        'start' => [
            'inputType' => 'text',
            'eval' => ['rgxp' => 'datim', 'datepicker' => true, 'tl_class' => 'w50 wizard'],
            'sql' => "int(10) unsigned NOT NULL default 0",
        ],
        'stop' => [
            'inputType' => 'text',
            'eval' => ['rgxp' => 'datim', 'datepicker' => true, 'tl_class' => 'w50 wizard'],
            'sql' => "int(10) unsigned NOT NULL default 0",
        ],
        'published' => [
            'inputType' => 'checkbox',
            'eval' => ['isBoolean' => true, 'tl_class' => 'w50 m12'],
            'sql' => "char(1) NOT NULL default ''",
        ],
    ],
];
