<?php

declare(strict_types=1);

use Contao\DC_Table;

$GLOBALS['TL_DCA']['dait_cc_dictionary_item'] = [
    'config' => [
        'dataContainer' => DC_Table::class,
        'ptable' => 'dait_cc_dictionary',
        'sql' => ['keys' => ['id' => 'primary', 'pid,sorting' => 'index', 'code' => 'index']],
    ],
    'list' => [
        'sorting' => ['mode' => 4, 'fields' => ['sorting'], 'panelLayout' => 'filter;search,limit'],
        'label' => ['fields' => ['code', 'label', 'language'], 'format' => '%s – %s [%s]'],
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
        'default' => '{title_legend},code,label,language',
    ],
    'fields' => [
        'id' => ['sql' => "int(10) unsigned NOT NULL auto_increment"],
        'pid' => ['sql' => "int(10) unsigned NOT NULL default 0"],
        'tstamp' => ['sql' => "int(10) unsigned NOT NULL default 0"],
        'sorting' => ['sql' => "int(10) unsigned NOT NULL default 0"],
        'code' => [
            'inputType' => 'text',
            'eval' => ['mandatory' => true, 'maxlength' => 32, 'tl_class' => 'w50'],
            'sql' => "varchar(32) NOT NULL default ''",
        ],
        'label' => [
            'inputType' => 'text',
            'eval' => ['mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50'],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'language' => [
            'inputType' => 'text',
            'eval' => ['maxlength' => 16, 'tl_class' => 'w50'],
            'sql' => "varchar(16) NOT NULL default ''",
        ],
    ],
];
