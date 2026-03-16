<?php

declare(strict_types=1);

use Contao\DC_Table;

$GLOBALS['TL_DCA']['dait_cc_record_item'] = [
    'config' => [
        'dataContainer' => DC_Table::class,
        'ptable' => 'dait_cc_record',
        'onsubmit_callback' => [
            ['DalleyIt\\ContaoCatalogue\\Dca\\RecordItemSchemaDca', 'onSubmit'],
        ],
        'onload_callback' => [
            ['DalleyIt\\ContaoCatalogue\\Dca\\RecordItemSchemaDca', 'onLoad'],
        ],
        'sql' => [
            'keys' => [
                'id' => 'primary',
                'pid,sorting' => 'index',
                'parent_id' => 'index',
            ],
        ],
    ],
    'list' => [
        'sorting' => [
            'mode' => 4,
            'fields' => ['sorting'],
            'panelLayout' => 'filter;search,limit',
            'child_record_callback' => ['DalleyIt\\ContaoCatalogue\\Dca\\RecordItemListLabel', 'label'],
        ],
        'operations' => [
            'edit' => ['href' => 'act=edit', 'icon' => 'edit.svg'],
            'copy' => ['href' => 'act=copy', 'icon' => 'copy.svg'],
            'delete' => [
                'href' => 'act=delete',
                'icon' => 'delete.svg',
                'attributes' => 'onclick="if(!confirm(\'DELETE?\'))return false;Backend.getScrollOffset()"',
            ],
            'show' => ['href' => 'act=show', 'icon' => 'show.svg'],
        ],
    ],
    'palettes' => [
        'default' => '{type_legend},type,parent_id;{data_legend},data_json',
    ],
    'fields' => [
        'id' => ['sql' => "int(10) unsigned NOT NULL auto_increment"],
        'pid' => ['sql' => "int(10) unsigned NOT NULL default 0"],
        'tstamp' => ['sql' => "int(10) unsigned NOT NULL default 0"],
        'sorting' => ['sql' => "int(10) unsigned NOT NULL default 0"],
        'parent_id' => [
            'inputType' => 'text',
            'eval' => ['rgxp' => 'digit', 'tl_class' => 'w50'],
            'sql' => "int(10) unsigned NOT NULL default 0",
        ],
        'type' => [
            'inputType' => 'text',
            'eval' => ['mandatory' => true, 'maxlength' => 64, 'tl_class' => 'w50'],
            'sql' => "varchar(64) NOT NULL default ''",
        ],
        'data_json' => [
            'inputType' => 'textarea',
            'eval' => ['decodeEntities' => true, 'tl_class' => 'clr long', 'rows' => 8],
            'sql' => "longtext NULL",
        ],
    ],
];
