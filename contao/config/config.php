<?php

declare(strict_types=1);

// Backend modules
$GLOBALS['BE_MOD']['content']['dait_catalogue'] = [
    'tables' => ['dait_cc_catalogue', 'dait_cc_record', 'dait_cc_record_item'],
];

$GLOBALS['BE_MOD']['system']['dait_dictionary'] = [
    'tables' => ['dait_cc_dictionary', 'dait_cc_dictionary_item'],
];
