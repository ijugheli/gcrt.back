<?php
$VIEW_TYPES = [
    1 => 'input',
    2 => 'textarea',
    3 => 'editable-textarea',
    4 => 'checkbox',
    5 => 'toggle',
    6 => 'select',
    7 => 'searchable-select',
    8 => 'multiselect',
    9 => 'searchable-multiselect',
    10 => 'datepicker',
    11 => 'timepicker',
    12 => 'datetimepicker',
    13 => 'treeselect',
    14 => 'tableselect',
];

$DATA_TYPES =  [
    1 => 'string',
    2 => 'int',
    3 => 'double',
    4 => 'date',
    5 => 'datetime',
    6 => 'boolean',
];

return [
    'VIEW_TYPES' => $VIEW_TYPES,
    'VIEW_TYPE_IDS' => array_flip($VIEW_TYPES),
    'DATA_TYPES' => $DATA_TYPES,
    'DATA_TYPE_IDS' => array_flip($DATA_TYPES)

];
