<?php

// ADMIN'S INTERFACE FOR EDITING METER READINGS 

session_start();
include_once "authentication/Authentication.php";
include_once "function.php";
ini_set("default_charset","UTF-8");

$admin_groups = ['EM_ADMIN', 'WM_ADMIN', 'Группа автоматизации'];
$auth = new Authentication(); // 'm_babynin'

if ($auth->checkAccessUserByGroup($admin_groups) != false) { 
    $db = getConnect();
    foreach ($auth->getGroups() as $g) {
        preg_match('/^(WM|EM)_ADMIN/', $g) ? preg_match('/^(WM|EM)_ADMIN/', $g, $group) : false;
    }

    $last_month = reduce_date(date('m'), date('Y')); // previous month for the main users' table
    $penult_month = reduce_date(date('m') - 1, date('Y')); // month before the previous month for the main users' table
    $admin_res = !empty($group) ? substr($group[0], 0, 1) : null;
    $tuses = selectQuery('SELECT tus_id, name FROM tuses');

    //
    if (isset($_POST['form_table'])) {
        $_SESSION = [ 'last_month'   => $last_month,
                      'penult_month' => $penult_month,
                      'admin_res'    => $admin_res,
                      'username'     => $auth->getFIO()];

        //-------------------------------------------------------------
        //---------------------ELECTRICITY METERS----------------------
        //-------------------------------------------------------------

        if ($admin_res == 'E') {
            $query = mysqli_query($db, 
                        "SELECT 
                            st.type as st_type, 
                            struct_num, 
                            mt.type as mt_type, 
                            em_val.value as value, 
                            em.em_num as em_num, 
                            em_val.id as reading_id, 
                            em_val.notes as notes, 
                            em_val.editor as editor 
                                FROM electricity_meters as em
                                    RIGHT JOIN tuses as tus on tus.tus_id = em.tus_id
                                    RIGHT JOIN struct_types as st on st.st_id = em.st_id
                                    RIGHT JOIN meter_types as mt on mt.mt_id = em.mt_id
                                    RIGHT JOIN em_" . date('m_Y') . " as em_val on em_val.em_num = em.em_num
                                WHERE status = 1 AND em.tus_id = " . $_POST['tus'] . "
                                ORDER BY st.st_id, struct_num");

            // fetch from query stuff
            $i = 0;
            while ($q = mysqli_fetch_assoc($query)) {
                $data[] = array (   'em_num'      => $q['em_num'],
                                    'str_type'    => $q['st_type'],
                                    'str_num'     => $q['struct_num'],
                                    'meter_type'  => $q['mt_type'],
                                    'meter_value' => $q['value'],
                                    'reading_id'  => $q['reading_id'],
                                    'notes'       => $q['notes'],
                                    'editor'      => $q['editor']);

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

        }

        //-------------------------------------------------------------
        //-------------------------WATER METERS------------------------
        //-------------------------------------------------------------

        if ($admin_res == 'W') {
            $query = mysqli_query($db, 
                        "SELECT 
                            st.type as st_type, 
                            struct_num, 
                            mt.type as mt_type, 
                            wm_val.value as value, 
                            wm.wm_num as wm_num, 
                            wm_val.id as reading_id, 
                            wm_val.notes as notes, 
                            wm_val.editor as editor 
                                FROM water_meters as wm
                                    RIGHT JOIN tuses as tus on tus.tus_id = wm.tus_id
                                    RIGHT JOIN struct_types as st on st.st_id = wm.st_id
                                    RIGHT JOIN meter_types as mt on mt.mt_id = wm.mt_id
                                    RIGHT JOIN wm_" . date('m_Y') . " as wm_val on wm_val.wm_num = wm.wm_num
                                WHERE status = 1 AND wm.tus_id = " . $_POST['tus'] . " 
                                ORDER BY st.st_id, struct_num");

            // fetch from query stuff
            $i = 0;
            while ($q = mysqli_fetch_assoc($query)) {
                $data[] = array (   'wm_num'      => $q['wm_num'],
                                    'str_type'    => $q['st_type'],
                                    'str_num'     => $q['struct_num'],
                                    'meter_type'  => $q['mt_type'],
                                    'meter_value' => $q['value'],
                                    'reading_id'  => $q['reading_id'],
                                    'notes'       => $q['notes'],
                                    'editor'      => $q['editor']);

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
    
} else { $auth->checkAccessUserByGroup(null); }
