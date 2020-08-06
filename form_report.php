<?php
include_once "function.php";
include_once "Reports.php";
ini_set("default_charset","UTF-8");

require_once 'function.php';
require_once 'SimpleXLS/SimpleXLS.php';
require_once 'SimpleXLS/SimpleXLSX.php';

include_once('view/header.html');
include_once('view/menu.html');
include_once('view/report.html');

$uploaddir = 'output/';

//upload a file to reports dir
if (@move_uploaded_file($_FILES['userfile']['tmp_name'], $uploaddir . $_FILES['userfile']['name'])) {
    //print "File is valid, and was successfully uploaded.";
    //$row = 1;
    $filename = 'output/' . @$_FILES['userfile']['name']; // 'output/'
}

//open the file and parse
if ($xlsx = @SimpleXLSX::parse($filename)) {
    $rows = $xlsx->rows();
} elseif ($xls = @SimpleXLS::parse($filename)) {
    $rows = $xls->rows();
} else {
    SimpleXLS::parseError();
}

if (!empty($rows)) {
    switch (true) {
        case mb_strstr($rows[0][4], 'Дата заключения'):
            echo "<h5 style='text-align: center'>Отчет для ТЭК</h5>";
            $report = new TEK($rows, $filename);
            $report->getReport();
            echo "<div class='text-center'><a class='btn btn-danger' href='output/Отчет для ТЭК.xlsx'>Экспортировать</a></div></br></br>";
            break;
        
        case mb_strstr($rows[0][2], 'Код точки учета'):
            echo "<h5 style='text-align: center'>Отчет для ТГК</h5>";
            $report = new TGK($rows, $filename);
            $report->getReport();
            echo "<div class='text-center'><a class='btn btn-danger' href='output/Отчет для ТГК.xlsx'>Экспортировать</a></div></br></br>";
            break;
    
        case mb_strstr($rows[0][5], 'Тип прибора учета'):
            echo "<h5 style='text-align: center'>Отчет для Водоканала</h5>";
            $report = new VodokanalPartial($rows, $filename);
            $report->getReport();
            echo "<div class='text-center'><a class='btn btn-danger' href='output/Отчет для Водоканала.xlsx'>Экспортировать</a></div></br></br>";
            break;
        
        default:
            echo 'Неверный шаблон отчета';
            break;
    }
}

if (!empty($_POST['vodokanal_full'])) {
    if ($xlsx = @SimpleXLSX::parse('output/vodokanal_full_template.xlsx')) {
        $rows = $xlsx->rows();
    }
    echo "<h5 style='text-align: center'>Полный отчет для Водоканала</h5>";
    $report = new VodokanalFull($rows, null);
    $report->getReport();
    echo "<div class='text-center'><a class='btn btn-primary' href='output/Отчет для Водоканала полный.xlsx'>Экспортировать</a></div></br></br>";
}
