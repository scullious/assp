<?php
include_once "function.php";
$db = getConnect();
ini_set("default_charset","UTF-8");

//$ab = ['Адмиралтейский'];
//
//foreach($ab as $ba) {
//    $query = mysqli_query($db, "
//        insert into regions (name)
//        values ('" . $ba . "');
//    ");
//}
//
//for ($i = 2017; $i < 2020; $i++) {
//    if ($i == 2017) {
//        for ($j = 8; $j < 13; $j++) {
//            if ($j < 10) {
//                $query = mysqli_query($db, "
//                    create table wm_0" . $j . "_" . $i . "
//                        (id int not null auto_increment,
//                        wm_id int not null,
//                        date date,
//                        value int,
//                        consumption int,
//                        primary key (id));
//                    ");
//            }
//            else {
//                $query = mysqli_query($db, "
//                    create table wm_" . $j . "_" . $i . "
//                        (id int not null auto_increment,
//                        wm_id int not null,
//                        date date,
//                        value int,
//                        consumption int,
//                        primary key (id));
//                    ");
//            }
//            
//        }
//    }
//    
//    elseif ($i == 2019) {
//        for ($j = 1; $j < 6; $j++) {
//            if ($j < 10) {
//                $query = mysqli_query($db, "
//                    create table wm_0" . $j . "_" . $i . "
//                        (id int not null auto_increment,
//                        wm_id int not null,
//                        date date,
//                        value int,
//                        consumption int,
//                        primary key (id));
//                    ");
//            }
//            else {
//                $query = mysqli_query($db, "
//                    create table wm_" . $j . "_" . $i . "
//                        (id int not null auto_increment,
//                        wm_id int not null,
//                        date date,
//                        value int,
//                        consumption int,
//                        primary key (id));
//                    ");
//            }
//            
//        }
//    }
//    
//    else {
//        for ($j = 1; $j < 13; $j++) {
//            if ($j < 10) {
//                $query = mysqli_query($db, "
//                    create table wm_0" . $j . "_" . $i . "
//                        (id int not null auto_increment,
//                        wm_id int not null,
//                        date date,
//                        value int,
//                        consumption int,
//                        primary key (id));
//                    ");
//            }
//            else {
//                $query = mysqli_query($db, "
//                    create table wm_" . $j . "_" . $i . "
//                        (id int not null auto_increment,
//                        wm_id int not null,
//                        date date,
//                        value int,
//                        consumption int,
//                        primary key (id));
//                    ");
//            }
//            
//        }
//    }
//    
//}    


//put info from the file to the DB
require_once 'SimpleXLS/SimpleXLSX.php';

$filename = 'main.xlsx';

//open the file and parse
if ($xlsx = SimpleXLSX::parse($filename)) {
    $rows = $xlsx->rows();
} else {
    echo SimpleXLSX::parseError();
}

for ($k = 2, $l = 0; $k < sizeof($rows); $k++, $l++) {
    $regs[$l] = $rows[$k][0]; 
    $struct[$l] = $rows[$k][1]; 
    $address[$l] = $rows[$k][2]; 
    $agreem[$l] = $rows[$k][3]; 
    $wt_name[$l] = $rows[$k][4]; 
    $wm_name[$l] = $rows[$k][5];
    $tus[$l] = $rows[$k][49];
    $own[$l] = $rows[$k][50];
}

for ($i = 0; $i < sizeof($reg); $i++) {
    $query = mysqli_query($db, "
            insert into water_meters (
                                        name,
                                        reg_id,
                                        tus_id,
                                        struct,
                                        address,
                                        wt_id,
                                        own_id
                                        )
                            values (
                                        
                                        )
        ");
}

var_dump($own);
