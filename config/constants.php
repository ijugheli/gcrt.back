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

$surveys = [
    1 => 'SCL90',
    2 => 'ERQ',
    3 => 'GAD7',
    4 => 'LEC5',
    5 => 'PHQ9',
    6 => 'PHQ15',
    7 => 'ITQ',
    8 => 'CAPS5',
];

// საინტერპრეტაციო ცხრილი შფოთვის  კითხვარისთვის
$GAD7Ranges = [
    ['from' => 7, 'to' => 15, 'title' => 'არ არის/უმნიშვნელოდაა გამოხატული'],
    ['from' => 16, 'to' => 19, 'title' => 'სუსტად გამოხატული'],
    ['from' => 20, 'to' => 27, 'title' => 'საშუალო დონე'],
    ['from' => 28,  'to' => 32, 'title' => 'მწვავედ გამოხატული'],
];

$PHQ9Ranges = [
    ['from' => 0, 'to' => 4, 'title' => 'არანაირი'],
    ['from' => 5, 'to' => 9, 'title' => 'სუსტი დეპრესია'],
    ['from' => 10, 'to' => 14, 'title' => 'საშუალო დეპრესია'],
    ['from' => 15,  'to' => 19, 'title' => 'საშუალოდ მწვავე დეპრესია '],
    ['from' => 20,  'to' => 27, 'title' => 'მწვავე დეპრესია'],
];

// საინტერპრეტაციო ცხრილი PHQ-15 -თვის
$PHQ15Ranges = [
    ['from' => 0, 'to' => 4, 'title' => 'არ არის გამოხატული'],
    ['from' => 5, 'to' => 9, 'title' => 'სუსტად გამოხატული'],
    ['from' => 10, 'to' => 14, 'title' => 'საშუალოდ გამოხატული'],
    ['from' => 15,  'to' => 30, 'title' => 'მწვავედ გამოხატული'],
];

$ITQ = [
    'P1_2' => ['group_title' => 'აწმყოში ხელახალი განცდის (Re-experiencing) კრიტერიუმები (RE_DX)', 'sum_title' => 'აწმყოში ხელახალი განცდის (Re- experiencing) ქულა (Re)'],
    'P3_4' => ['group_title' => 'თავის არიდების კრიტერიუმები (AV_DX)', 'sum_title' => 'თავის არიდების (Avoidance) ქულა (AV)'],
    'P5_6' => ['group_title' => 'მიმდინარე საფრთხის შეგრძნების კრიტერიუმები (TH_DX)', 'sum_title' => 'მიმდინარე საფრთხის შეგრძნების (Sense of current threat) ქულა (Th)'],
    'P7_9' => ['group_title' => 'პტსა-თი გამოწვეული ფუნქციური მოშლის (PTSDFI) კრიტერიუმები'],
    'C1_2' => ['group_title' => 'აფექტური დისრეგულაციის (Affective dysregulation) კრიტერიუმები (AD_dx)', 'sum_title' => 'აფექტური დისრეგულაციის (Affective dysregulation) ქულა (AD)'],
    'C3_4' => ['group_title' => 'ნეგატიური თვითაღქმის (Negative self-concept) კრიტერიუმები (NSC_dx)', 'sum_title' => 'ნეგატიური თვითაღქმის (Negative self-concept) ქულა (NSC)'],
    'C5_6' => ['group_title' => 'ურთიერთობის სირთულეების (Disturbances in relationships) კრიტერიუმები (DR_dx)', 'sum_title' => 'ურთიერთობის სირთულეების (Disturbances in relationships) ქულა (DR)'],
    'C7_9' => ['group_title' => 'თოდ-ით გამოწვეული ფუნქციური მოშლის (DSOFI) კრიტერიუმები.'],
];

$meetsAllITQCriterias = function ($item) {
    return $item['result'] == true;
};

$meetsITQCriterias = function (int $value, int $key) {
    return $value >= 2;
};

// tree select ids for case and cliemnt 43 icd10
$treeselectIDs = [45, 30, 44, 42, 27];
$lazyTreeselectIDs = [43];


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
    'GAD7Ranges' => $GAD7Ranges,
    'PHQ15Ranges' => $PHQ15Ranges,
    'PHQ9Ranges' => $PHQ9Ranges,
    'surveyIDS' => array_flip($surveys),
    'surveys' => $surveys,
    'ITQ' => $ITQ,
    'meetsAllITQCriterias' => $meetsAllITQCriterias,
    'meetsITQCriterias' => $meetsITQCriterias,
    'treeselectIDs' => $treeselectIDs,
    'lazyTreeselectIDs' => $lazyTreeselectIDs
];
