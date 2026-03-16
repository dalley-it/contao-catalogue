<?php

declare(strict_types=1);

use Contao\DC_Table;

$GLOBALS['TL_DCA']['dait_cc_catalogue'] = [
    'config' => [
        'dataContainer' => DC_Table::class,
        'ctable' => ['dait_cc_record'],
        'sql' => [
            'keys' => [
                'id' => 'primary',
            ],
        ],
    ],
    'list' => [
        'sorting' => [
            'mode' => 1,
            'fields' => ['title'],
            'flag' => 1,
            'panelLayout' => 'filter;search,limit',
        ],
        'label' => [
            'fields' => ['title', 'schema_key'],
            'format' => '%s (%s)',
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
                'href' => 'table=dait_cc_record',
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
        'default' => '{title_legend},title,schema_key,published;{fe_legend},jumpTo,perPage,sortMode;{options_legend},dictionaryKey',
    ],
    'fields' => [
        'id' => ['sql' => "int(10) unsigned NOT NULL auto_increment"],
        'tstamp' => ['sql' => "int(10) unsigned NOT NULL default 0"],
        'title' => [
            'inputType' => 'text',
            'eval' => ['mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50'],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'schema_key' => [
            'inputType' => 'text',
            'eval' => ['mandatory' => true, 'maxlength' => 64, 'tl_class' => 'w50'],
            'sql' => "varchar(64) NOT NULL default ''",
        ],
        'jumpTo' => [
            'inputType' => 'pageTree',
            'eval' => ['fieldType' => 'radio', 'tl_class' => 'w50'],
            'sql' => "int(10) unsigned NOT NULL default 0",
        ],
        'perPage' => [
            'inputType' => 'text',
            'eval' => ['rgxp' => 'digit', 'tl_class' => 'w50'],
            'sql' => "smallint(5) unsigned NOT NULL default 0",
        ],
        'sortMode' => [
            'inputType' => 'select',
            'options' => ['title_asc', 'title_desc', 'sorting_asc', 'sorting_desc'],
            'eval' => ['tl_class' => 'w50'],
            'sql' => "varchar(32) NOT NULL default 'title_asc'",
        ],
        'dictionaryKey' => [
            'inputType' => 'text',
            'eval' => ['maxlength' => 64, 'tl_class' => 'w50'],
            'sql' => "varchar(64) NOT NULL default ''",
        ],
        'published' => [
            'inputType' => 'checkbox',
            'eval' => ['isBoolean' => true, 'tl_class' => 'w50 m12'],
            'sql' => "char(1) NOT NULL default ''",
        ],
    ],
];
