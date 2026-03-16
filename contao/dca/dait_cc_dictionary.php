<?php

declare(strict_types=1);

use Contao\DC_Table;

$GLOBALS['TL_DCA']['dait_cc_dictionary'] = [
    'config' => [
        'dataContainer' => DC_Table::class,
        'ctable' => ['dait_cc_dictionary_item'],
        'sql' => ['keys' => ['id' => 'primary', 'dict_key' => 'unique']],
    ],
    'list' => [
        'sorting' => ['mode' => 1, 'fields' => ['title'], 'panelLayout' => 'filter;search,limit'],
        'label' => ['fields' => ['title', 'dict_key'], 'format' => '%s (%s)'],
        'operations' => [
            'edit' => ['href' => 'act=edit', 'icon' => 'edit.svg'],
            'children' => ['href' => 'table=dait_cc_dictionary_item', 'icon' => 'children.svg'],
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
        'default' => '{title_legend},title,dict_key',
    ],
    'fields' => [
        'id' => ['sql' => "int(10) unsigned NOT NULL auto_increment"],
        'tstamp' => ['sql' => "int(10) unsigned NOT NULL default 0"],
        'title' => [
            'inputType' => 'text',
            'eval' => ['mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50'],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'dict_key' => [
            'inputType' => 'text',
            'eval' => ['mandatory' => true, 'maxlength' => 64, 'tl_class' => 'w50'],
            'sql' => "varchar(64) NOT NULL default ''",
        ],
    ],
];
