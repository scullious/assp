<?php
include_once "authentication/Authentication.php";
ini_set("default_charset","UTF-8");

$admin_groups = ['EM_ADMIN', 'WM_ADMIN', 'Группа автоматизации'];
$user_groups = array_merge(array_map(function ($a) { return 'WM_TUS' . $a; }, range(1,5)), array_map(function ($a) { return 'EM_TUS' . $a; }, range(1,5)));

$auth = new Authentication(); // m_babynin
$auth->setDebugMode('192.168.118.29', false);
//$auth->checkValidGroup('WM_TUS1');

if ($auth->checkAccessUserByGroup($admin_groups)) {
    header("Location: admin_readings.php");   
} 

if ($auth->checkAccessUserByGroup($user_groups)) {
    header("Location: user_readings.php");
} 

//foreach (['a_bahirev', 'v_astapkovich', 'a_gudkov', 'a_salov', 's_scherbakov', 'a_chebotarev', 'bahvalovvn', 'a_bulganin', 'i_smirnov'] as $a) {
//    $auth = new Authentication($a);
//    echo '<pre>';
//    echo '------------' . $a . nl2br("\n");
//    print_r($auth->getGroups());
//    echo '</pre>';
//}

//echo '<pre>';
//print_r($auth->getGroups());
//echo '</pre>';

//if($auth->checkValidGroup('Группа автоматизации')){
//    echo 'Пользователь состоит в группе Автоматизации<br>';
//} else {
//    echo 'Пользователь НЕ СОСТОИТ в группе!!!!!!!<br>';
//}

//$group = $auth->checkAccessUserByGroup(['С_ТУС6','С_ТУС3', 'Группа автоматизации', 'НЕ выключение АРМ']); // returns the first match