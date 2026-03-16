<?php

declare(strict_types=1);

use Contao\System;

$GLOBALS['TL_DCA']['tl_module']['palettes']['dait_catalogue_list'] = '{title_legend},name,headline,type;{config_legend},cc_catalogue,cc_perPage,cc_sortMode;{filter_legend},cc_enableTaxonomyFilter;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space';
$GLOBALS['TL_DCA']['tl_module']['palettes']['dait_catalogue_reader'] = '{title_legend},name,headline,type;{config_legend},cc_catalogue;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space';

$GLOBALS['TL_DCA']['tl_module']['fields']['cc_catalogue'] = [
    'inputType' => 'select',
    'foreignKey' => 'dait_cc_catalogue.title',
    'eval' => ['mandatory' => true, 'chosen' => true, 'tl_class' => 'w50'],
    'sql' => "int(10) unsigned NOT NULL default 0",
    'relation' => ['type' => 'hasOne', 'load' => 'lazy'],
];

$GLOBALS['TL_DCA']['tl_module']['fields']['cc_perPage'] = [
    'inputType' => 'text',
    'eval' => ['rgxp' => 'digit', 'tl_class' => 'w50'],
    'sql' => "smallint(5) unsigned NOT NULL default 0",
];

$GLOBALS['TL_DCA']['tl_module']['fields']['cc_sortMode'] = [
    'inputType' => 'select',
    'options' => ['title_asc', 'title_desc', 'sorting_asc', 'sorting_desc'],
    'eval' => ['tl_class' => 'w50'],
    'sql' => "varchar(32) NOT NULL default 'title_asc'",
];

$GLOBALS['TL_DCA']['tl_module']['fields']['cc_enableTaxonomyFilter'] = [
    'inputType' => 'checkbox',
    'eval' => ['isBoolean' => true, 'tl_class' => 'w50 m12'],
    'sql' => "char(1) NOT NULL default '1'",
];
