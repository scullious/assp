<?php

// USER'S INTERFACE FOR WRITING METER READINGS

include_once "authentication/Authentication.php";
include_once "function.php";
ini_set("default_charset","UTF-8");

$user_groups = array_merge(array_map(function ($a) { return 'WM_TUS' . $a; }, range(1,5)), array_map(function ($a) { return 'EM_TUS' . $a; }, range(1,5)));
$auth = new Authentication(); // 'n_sergeev'

if ($auth->checkAccessUserByGroup($user_groups) != false) { 
    $db = getConnect();
    foreach ($auth->getGroups() as $g) {
        preg_match('/^(WM|EM)_TUS[0-9]$/', $g) ? preg_match('/^(WM|EM)_TUS[0-9]$/', $g, $group) : false;
    }

    $user_res = substr($group[0], 0, 1); // access depending on specific resource
    $user_tus = substr($group[0], -1); // and TUS #
    $username = $auth->getFIO();
    $last_month = reduce_date(date('m'), date('Y')); // previous month for the main users' table

    //-------------------------------------------------------------
    //---------------------ELECTRICITY METERS----------------------
    //-------------------------------------------------------------

    if ($user_res == 'E') {
        // find out if a table for current month for electricity meters values exists..
        $query = mysqli_query($db, "SHOW TABLES LIKE 'em_" . date('m_Y') ."'");
        // ...and if it doesn't - create it...
        if ($query->num_rows == 0) { // num of rows in mysql response == 0
            mysqli_query($db, "CREATE TABLE em_" . date('m_Y') . " LIKE em_" . $last_month);
        } else {
            // ...otherwise get em_nums(in order to check already filled in fields)
            $query2 = mysqli_query($db, "SELECT em_num, value, notes, editor FROM em_" . date('m_Y'));
            while ($q2 = mysqli_fetch_assoc($query2)) {
                $last_values[] = array(
                                        'em_num' => $q2['em_num'],
                                        'last_val' => $q2['value'],
                                        'notes' => $q2['notes'],
                                        'editor' => $q2['editor']  );
            }
        }

        //--------------------
        // output db -> screen
        //--------------------

        // get stuff from DB
        $query2 = mysqli_query($db, 
                "SELECT 
                    st.type as st_type, 
                    struct_num, 
                    mt.type as mt_type, 
                    em_val.value as value, 
                    em.em_num as em_num, 
                    em_val.notes as notes, 
                    em_val.editor as editor
                        FROM electricity_meters as em
                            RIGHT JOIN tuses as tus on tus.tus_id = em.tus_id
                            RIGHT JOIN struct_types as st on st.st_id = em.st_id
                            RIGHT JOIN meter_types as mt on mt.mt_id = em.mt_id
                            RIGHT JOIN em_" . $last_month . " as em_val on em_val.em_num = em.em_num
                        WHERE status = 1 AND em.tus_id = " . $user_tus . "
                        ORDER BY st.st_id, struct_num");

        // fetch from query stuff
        $i = 0;
        while ($q = mysqli_fetch_assoc($query2)) {
            $data[] = array (   'str_type'    => $q['st_type'],
                                'str_num'     => $q['struct_num'],
                                'em_num'       => $q['em_num'],
                                'meter_type'  => $q['mt_type'],
                                'meter_value' => $q['value'],
                                'last_val'    => '',
                                'notes'       => '',
                                'editor'      => ''
                            );
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

        //
        if (!empty($last_values)) {
            foreach ($data as $key => $value) {
                foreach ($last_values as $lv) {
                    if ($lv['em_num'] == $value['em_num']) {
                        $data[$key]['last_val'] = $lv['last_val'];
                        $data[$key]['notes'] = $lv['notes'];
                        $data[$key]['editor'] = $lv['editor'];
                    }
                }
            }
        }

        $st_tmp[] = array($st => $i); // last entry str_type/amount
        // unfolding 2d-array
        $st_arr = call_user_func_array('array_merge', $st_tmp); // same as $st_arr = array_merge($tmp[0], $tmp[1], ..., $tmp[sizeof($tmp)-1]);

        //-------------------
        // input screen -> db
        //-------------------

        // pressing button Сохранить
        if (isset($_POST['userAdd'])) {    
            // querying to write data from table fields to db
            for ($j = 0; $j < count($_POST['lastVal']); ++$j) {
                if (!empty($_POST['lastVal'][$j])) { // value input field is NOT empty
                    empty($_POST['notes'][$j]) ? $_POST['notes'][$j] = ' ' : null;
                    mysqli_query($db, "REPLACE INTO em_" . date('m_Y') . "
                                            (em_num,
                                            date,
                                            value,
                                            consumption,
                                            notes,
                                            editor
                                        ) VALUES ('" . $data[$j]['em_num'] . "', 
                                             '" . date('Y-m-d') . "', 
                                             " . $_POST['lastVal'][$j] . ", 
                                             " . ($_POST['lastVal'][$j] - $data[$j]['meter_value']) . ", 
                                             '" . $_POST['notes'][$j] . "', 
                                             '" . $username . "')");
                }
            }
            header("Location: user_readings.php");
        }
    }

    //-------------------------------------------------------------
    //------------------------WATER METERS-------------------------
    //-------------------------------------------------------------

    if ($user_res == 'W') {
        // find out if a table for current month for water meters values exists..
        $query = mysqli_query($db, "SHOW TABLES LIKE 'wm_" . date('m_Y') ."'");
        // ...and if it doesn't - create it...
        if ($query->num_rows == 0) { // num of rows in mysql response == 0
            mysqli_query($db, "CREATE TABLE wm_" . date('m_Y') . " LIKE wm_" . $last_month);
        } else {
            // ...otherwise get wm_nums(in order to check already filled in fields)
            $query2 = mysqli_query($db, "SELECT wm_num, value, notes, editor FROM wm_" . date('m_Y'));
            while ($q2 = mysqli_fetch_assoc($query2)) {
                $last_values[] = array(
                                        'wm_num' => $q2['wm_num'],
                                        'last_val' => $q2['value'],
                                        'notes' => $q2['notes'],
                                        'editor' => $q2['editor'] );
            }
        }

        //--------------------
        // output db -> screen
        //--------------------

        // get stuff from DB
        $query2 = mysqli_query($db, 
                "SELECT 
                    st.type as st_type, 
                    struct_num, 
                    mt.type as mt_type, 
                    wm_val.value as value, 
                    wm.wm_num as wm_num, 
                    wm_val.notes as notes, 
                    wm_val.editor as editor
                        FROM water_meters as wm
                            RIGHT JOIN tuses as tus on tus.tus_id = wm.tus_id
                            RIGHT JOIN struct_types as st on st.st_id = wm.st_id
                            RIGHT JOIN meter_types as mt on mt.mt_id = wm.mt_id
                            RIGHT JOIN wm_" . $last_month . " as wm_val on wm_val.wm_num = wm.wm_num
                        WHERE status = 1 AND wm.tus_id = " . $user_tus . "
                        ORDER BY st.st_id, struct_num");

        // fetch from query stuff
        $i = 0;
        while ($q = mysqli_fetch_assoc($query2)) {
            $data[] = array (   'str_type'    => $q['st_type'],
                                'str_num'     => $q['struct_num'],
                                'wm_num'       => $q['wm_num'],
                                'meter_type'  => $q['mt_type'],
                                'meter_value' => $q['value'],
                                'last_val'    => '',
                                'notes'       => '',
                                'editor'      => ''
                            );
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

        //
        if (!empty($last_values)) {
            foreach ($data as $key => $value) {
                foreach ($last_values as $lv) {
                    if ($lv['wm_num'] == $value['wm_num']) {
                        $data[$key]['last_val'] = $lv['last_val'];
                        $data[$key]['notes'] = $lv['notes'];
                        $data[$key]['editor'] = $lv['editor'];
                    }
                }
            }
        }

        !empty($st) ? $st_tmp[] = array($st => $i) : false; // last entry str_type/amount
        // unfolding 2d-array
        $st_arr = call_user_func_array('array_merge', $st_tmp); // same as $st_arr = array_merge($tmp[0], $tmp[1], ..., $tmp[sizeof($tmp)-1]);

        //-------------------
        // input screen -> db
        //-------------------

        // pressing button Сохранить
        if (isset($_POST['userAdd'])) {
            // querying to write data from table fields to db
            for ($j = 0; $j < count($_POST['lastVal']); ++$j) {
                if (!empty($_POST['lastVal'][$j])) { // value input field is NOT empty
                    empty($_POST['notes'][$j]) ? $_POST['notes'][$j] = ' ' : null;
                    mysqli_query($db, "REPLACE INTO wm_" . date('m_Y') . "
                                            (wm_num,
                                            date,
                                            value,
                                            consumption,
                                            notes,
                                            editor
                                        ) VALUES ('" . $data[$j]['wm_num'] . "', 
                                             '" . date('Y-m-d') . "', 
                                             " . $_POST['lastVal'][$j] . ",  
                                             " . ($_POST['lastVal'][$j] - $data[$j]['meter_value']) . ", 
                                             '" . $_POST['notes'][$j] . "', 
                                             '" . $username . "')");
                }
            }
            header("Location: user_readings.php");
        }
    }

    include_once('view/header.html');
    include_once('view/user.html');
    mysqli_close($db);
    
} else { $auth->checkAccessUserByGroup(null); }
