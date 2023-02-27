<?php


$CODE_EXPIRY_TIME = 60 * 6; // seconds

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

$ATTR_TYPES = [
    'standard' => 1,
    'tree' => 2,
    'entity' => 3,
];

$PERMISSION_TYPES = [
    1 => 'can_view',
    2 => 'can_update',
    3 => 'can_delete',
    4 => 'can_edit_structure',
];

return [
    'VIEW_TYPES' => $VIEW_TYPES,
    'VIEW_TYPE_IDS' => array_flip($VIEW_TYPES),
    'DATA_TYPES' => $DATA_TYPES,
    'DATA_TYPE_IDS' => array_flip($DATA_TYPES),
    'ATTR_TYPES' => $ATTR_TYPES,
    'PERMISSION_TYPES' => $PERMISSION_TYPES,
    'PERMISSION_TYPE_IDS' => array_flip($PERMISSION_TYPES),
    'CODE_EXPIRY_TIME' => $CODE_EXPIRY_TIME,
];
