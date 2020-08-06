<?php
include_once "authentication/Authentication.php";
include_once "function.php";
$db = getConnect();
ini_set("default_charset","UTF-8");

//$cur_month = date('m_Y'); // current month for the main admin's table

//-------------------------------------------------------------
//----------------------ADMIN'S INTERFACE----------------------
//-------------------------------------------------------------

//
if (isset($_POST['form_table'])) {
    $_SESSION = [   'month' => $month_sel[$_POST['month']],
                    'year' => $_POST['year'] ];
    
    $query = mysqli_query($db, 
                "SELECT tus.name as tus, st.type as st_type, struct_num, mt.type as mt_type, wm_val.value as value, wm.wm_id as wm_id, wm_val.id as reading_id
                    FROM water_meters as wm
                        RIGHT JOIN tuses as tus on tus.tus_id = wm.tus_id
                        RIGHT JOIN struct_types as st on st.st_id = wm.st_id
                        RIGHT JOIN meter_types as mt on mt.mt_id = wm.mt_id
                        RIGHT JOIN wm_" . $month_sel[$_POST['month']] . "_" . $_POST['year'] . " as wm_val on wm_val.wm_id = wm.wm_id
                    WHERE wm.tus_id = 1
                    ORDER BY st.st_id, struct_num");
    
    // fetch from query stuff
    $i = 0;
    while ($q = mysqli_fetch_assoc($query)) {
        $data[] = array (   'tus' => $q['tus'],
                            'str_type' => $q['st_type'],
                            'str_num' => $q['struct_num'],
                            'wm_id' => $q['wm_id'],
                            'meter_type' => $q['mt_type'],
                            'meter_value' => $q['value'],
                            'reading_id' => $q['reading_id']);
        
        // if structure type is different next step, then write str_type/amount to an array and set counter to zero
        // this is done for rowspan for Объект column
        if (isset($st)) {
            if ($q['st_type'] != $st) {
                $st_tmp[] = array($st => $i); // the named array contains str_type/amount matching
                $i = 0;
            }
        }  
        $st = $q['st_type'];
        $i++;
    }
    
    if (!empty($st)) {
        $st_tmp[] = array($st => $i); // last entry str_type/amount
        // unfold 2d-array (to make it 1d-array)
        $st_arr = call_user_func_array('array_merge', $st_tmp);
    }
}

include_once('view/header.html');
include_once('view/menu.html');
include_once('view/edit_readings.html');


mysqli_close($db);
