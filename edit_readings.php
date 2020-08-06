<?php

// THIS SCRIPT RUNS AFTER ACTIVATING 'SAVE' BUTTON IN MODAL RELATED TO 'ADMIN READINGS' TABLE

session_start();
include_once "function.php";
$db = getConnect();
ini_set("default_charset","UTF-8");

$last_month = $_SESSION['last_month'];
$penult_month = $_SESSION['penult_month'];
$username = $_SESSION['username'];

// pressing the save button in the edit modal form
// querying writing to db
if (isset($_POST['meter_num'])) {
    
    //-------------------------------------------------------------
    //---------------------ELECTRICITY METERS----------------------
    //-------------------------------------------------------------
    
    if ($_SESSION['admin_res'] == 'E') {
        $l = mysqli_query($db, 
                "SELECT value 
                    FROM em_". $last_month . 
                    " WHERE em_num = '" . $_POST['meter_num'] . "'");

        $last_readings = mysqli_fetch_assoc($l)['value'];

        mysqli_query($db, 
                "REPLACE INTO `em_" . date('m_Y') . "` (
                    id,
                    em_num,
                    date,
                    value,
                    consumption, 
                    notes, 
                    editor
                ) VALUES ("
                    . $_POST['reading_id'] . ", '"
                    . $_POST['meter_num'] . "', '"
                    . date('Y-m-d') . "', "
                    . $_POST['value'] . ", "
                    . ((int)$_POST['value'] - (int)$last_readings) . ", '"
                    . $_POST['notes'] . "', '"
                    . $username . "')");
    }
    
    //-------------------------------------------------------------
    //-------------------------WATER METERS------------------------
    //-------------------------------------------------------------
    
    if ($_SESSION['admin_res'] == 'W') {
        $l = mysqli_query($db, 
                "SELECT value 
                    FROM wm_". $last_month . 
                    " WHERE wm_num = '" . $_POST['meter_num'] . "'");

        $last_readings = mysqli_fetch_assoc($l)['value'];

        mysqli_query($db, 
                "REPLACE INTO `wm_" . date('m_Y') . "` (
                    id,
                    wm_num,
                    date,
                    value,
                    consumption, 
                    notes, 
                    editor
                ) VALUES ("
                    . $_POST['reading_id'] . ", '"
                    . $_POST['meter_num'] . "', '"
                    . date('Y-m-d') . "', "
                    . $_POST['value'] . ", "
                    . ((int)$_POST['value'] - (int)$last_readings) . ", '"
                    . $_POST['notes'] . "', '"
                    . $username . "')");
    }
}

mysqli_close($db);
