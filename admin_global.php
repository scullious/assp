<?php
session_start();
include_once "Meters.php";
include_once "authentication/Authentication.php";
ini_set("default_charset","UTF-8");

$admin_groups = ['EM_ADMIN', 'WM_ADMIN', 'Группа автоматизации'];
$auth = new Authentication(); // n_sergeev

if ($auth->checkAccessUserByGroup($admin_groups) != false) { 
    if ($auth->checkValidGroup('WM_ADMIN')) {
        $meter = new WaterMeters();
    }

    if ($auth->checkValidGroup('EM_ADMIN')) {
        $meter = new ElectricityMeters();
    }

    if ($auth->checkValidGroup('Группа автоматизации')) {
        $meter = new AllMeters();
    }

    $_SESSION = [   'meter_class'   => get_class($meter),
                    'meter_obj'     => $meter ];

    include_once('view/header.html');
    include_once('view/menu.html');
    include_once('view/edit_global.html');
    
} else { $auth->checkAccessUserByGroup(null); } 