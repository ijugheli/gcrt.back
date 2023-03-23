<?php

$alphabet = [
    'ა' => 'a',
    'ბ' => 'b',
    'გ' => 'g',
    'დ' => 'd',
    'ე' => 'e',
    'ვ' => 'v',
    'ზ' => 'z',
    'თ' => 'T',
    'ი' => 'i',
    'კ' => 'k',
    'ლ' => 'l',
    'მ' => 'm',
    'ნ' => 'n',
    'ო' => 'o',
    'პ' => 'p',
    'ჟ' => 'J',
    'რ' => 'r',
    'ს' => 's',
    'ტ' => 't',
    'უ' => 'u',
    'ფ' => 'f',
    'ქ' => 'q',
    'ღ' => 'gh',
    'ყ' => 'y',
    'შ' => 'sh',
    'ჩ' => 'ch',
    'ც' => 'ts',
    'ძ' => 'dz',
    'წ' => 'w',
    'ჭ' => 'CH',
    'ხ' => 'x',
    'ჯ' => 'j',
    'ჰ' => 'h',
    '0' => '0',
    '1' => '1',
    '2' => '2',
    '3' => '3',
    '4' => '4',
    '5' => '5',
    '6' => '6',
    '7' => '7',
    '8' => '8',
    '9' => '9',
];


$surveyQuestionTypes = [
    0 => 'matrix',
    1 => 'boolean',
    2 => 'checkbox',
    3 => 'radiogroup',
    4 => 'text',
];

$surveySectionValueTypes = [
    0 => 'question',
    1 => 'choice',
];

$actionTypes = [
    1 => 'recover_password',
    2 => 'otp',
];

$validationTypes = [
    1 => 'email',
    2 => 'phone',
];

$userActionTypes = [
    1 => 'addAttr',
    2 => 'editAttr',
    3 => 'deleteAttr',
    4 => 'addProperty',
    5 => 'editProperty',
    6 => 'deleteProperty',
    7 => 'addRecord',
    8 => 'editRecord',
    9 => 'deleteRecord',
    10 => 'login',
    11 => 'logout',
    12 => 'activateOTP',
    13 => 'deactivateOTP',
];

$userActionTypeTitles = [
    1 => 'ატრიბუტის დამატება',
    2 => 'ატრიბუტის რედაქტირება',
    3 => 'ატრიბუტის წაშლა',
    4 => 'პარამეტრის დამატება',
    5 => 'პარამეტრის რედაქტირება',
    6 => 'პარამეტრის წაშლა',
    7 => 'ჩანაწერის დამატება',
    8 => 'ჩანაწერის რედაქტირება',
    9 => 'ჩანაწერის წაშლა',
    10 => 'სისტემაში შესვლა',
    11 => 'სისტემიდან გასვლა',
    12 => 'OTP ჩართვა',
    13 => 'OTP გამორთვა',
];

$SCL90Ranges = [
    ['from' => 0.1, 'to' => 0.4, 'title' => 'ძალიან დაბალი დონე'],
    ['from' => 0.5, 'to' => 1.4, 'title' => 'დაბალი დონე'],
    ['from' => 1.5, 'to' => 2.4, 'title' => 'საშუალო დონე'],
    ['from' => 2.5,  'to' => 3.4, 'title' => 'აწეული დონე'],
    ['from' => 3.5, 'to' => 4.0, 'title' => 'მაღალი დონე']
];

return [
    'userActionTypes' => $userActionTypes,
    'userActionTypeTitles' => $userActionTypeTitles,
    'userActionTypesIDS' => array_flip($userActionTypes),
    'actionTypes' => $actionTypes,
    'actionTypeIDS' => array_flip($actionTypes),
    'validationTypes' => $validationTypes,
    'validationTypeIDS' => array_flip($validationTypes),
    'surveyQuestionTypes' => $surveyQuestionTypes,
    'surveyQuestionTypeIDS' => array_flip($surveyQuestionTypes),
    'surveySectionValueTypeIDS' => array_flip($surveySectionValueTypes),
    'surveySectionValueTypes' => $surveySectionValueTypes,
    'alphabet' => $alphabet,
    'SCL90Ranges' => $SCL90Ranges,
];
