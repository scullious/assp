<?php

// *** ACHTUNG!!! THIS SCRIPT BEGINS TO WORK RIGHT AFTER 'ADD', 'EDIT' OR 'DEL' BUTTON IS CLICKED

include_once "Meters.php"; // must be before session_start(), cause getVars() method wouldn't work
session_start();
include_once "Modals.php";
ini_set("default_charset","UTF-8");

$water_del_query = "UPDATE water_meters SET status = 0 WHERE wm_num = ";
$electr_del_query = "UPDATE electricity_meters SET status = 0 WHERE em_num = ";

switch (true) {
    
    //---------------------------------
    //-----------Add buttons-----------
    //----------inside modals----------
    //---------------------------------
    
    // '+region' button
    case isset($_POST['new_reg']):
        // create instances for both 'regions' and 'meter info' modals
        $modal_regions = new ModalRegions($_POST['new_reg']); 
        $modal_meters = new ModalMeters(null, $_POST['r_type'], $_POST['r_id']);
        // it's needed for updating 2 tables simultaneously(or to be exact, to update selector in the 2nd table)
        echo json_encode( [$modal_regions->drawAfterAdd(), $modal_meters->modalSample()] );
        break;
        
    // '+structure type' button
    case isset($_POST['new_str']):
        // create instances for both 'structure types' and 'meter info' modals
        $modal_structs = new ModalStructures($_POST['new_str']);
        $modal_meters = new ModalMeters(null, $_POST['r_type'], $_POST['r_id']);
        // it's needed for updating 2 tables simultaneously
        echo json_encode( [$modal_structs->drawAfterAdd(), $modal_meters->modalSample()] );
        break;

    // '+tus' button
    case isset($_POST['new_tus']):
        // create instances for both 'tuses' and 'meter info' modals
        $modal_tuses = new ModalTuses($_POST['new_tus']);
        $modal_meters = new ModalMeters(null, $_POST['r_type'], $_POST['r_id']);
        // it's needed for updating 2 tables simultaneously
        echo json_encode( [$modal_tuses->drawAfterAdd(), $modal_meters->modalSample()] );
        break;

    // '+owner' button
    case isset($_POST['new_own']):
        // create instances for both 'owners' and 'meter info' modals
        $modal_owners = new ModalOwners($_POST['new_own']);
        $modal_meters = new ModalMeters(null, $_POST['r_type'], $_POST['r_id']);
        // it's needed for updating 2 tables simultaneously
        echo json_encode( [$modal_owners->drawAfterAdd(), $modal_meters->modalSample()] );
        break;

    // '+meter type' button
    case isset($_POST['new_meter_type']):
        // create instances for both 'meter types' and 'meter info' modals
        $modal_meter_types = new ModalMeterTypes($_POST['new_meter_type']);
        $modal_meters = new ModalMeters(null, $_POST['r_type'], $_POST['r_id']);
        // it's needed for updating 2 tables simultaneously
        echo json_encode( [$modal_meter_types->drawAfterAdd(), $modal_meters->modalSample()] );
        break;
    
    // '+meter num' button
    case isset($_POST['new_meter_num']):
        // create instances for 'meter nums', 'meter info' and 'relations' modals
        $modal_meter_nums = new ModalMeterNums($_POST['new_meter_num'], $_POST['r_type'], $_POST['r_id']);
        $modal_meters = new ModalMeters(null, $_POST['r_type'], $_POST['r_id']);
        $modal_rels = new ModalAgrRels(null, $_POST['r_type'], $_POST['r_id']);
        // it's needed for updating 3 tables simultaneously
        echo json_encode( [$modal_meter_nums->drawAfterAdd(), $modal_meters->modalSample(), $modal_rels->modalSample()] );
        break;

    // '+meter's full info' button
    case isset($_POST['new_meter_info']):
        $water_add_query[0] = "INSERT INTO water_meters (
                                    wm_num,
                                    id,
                                    mt_id,
                                    reg_id,
                                    tus_id,
                                    st_id,
                                    struct_num,
                                    address,
                                    res_id,
                                    status"; // own_id,
        
        $water_add_query[1] = "INSERT INTO wm_" . reduce_date(date('m'), date('Y')) . " (
                                    wm_num,
                                    value,
                                    consumption";
        
        $electr_add_query[0] = "INSERT INTO electricity_meters (
                                    em_num,
                                    id,
                                    mt_id,
                                    reg_id,
                                    tus_id,
                                    st_id,
                                    struct_num,
                                    address,
                                    res_id, 
                                    status"; // own_id,
        
        $electr_add_query[1] = "INSERT INTO em_" . reduce_date(date('m'), date('Y')) . " (
                                    em_num,
                                    value,
                                    consumption";
        
        $modal_meters = new ModalMeters($_POST['new_meter_info'], $_POST['r_type'], $_POST['r_id']);
        $modal_meter_dates = new ModalMeterDates(null, $_POST['r_id']);
        // check meter type to use exact piece of the mysql query
        switch ($_SESSION['meter_class']) {
            case 'WaterMeters':
                echo json_encode( [$modal_meters->drawAfterAdd($water_add_query), $modal_meter_dates->modalSample()] );
                break;
            
            case 'ElectricityMeters':
                echo json_encode( [$modal_meters->drawAfterAdd($electr_add_query), $modal_meter_dates->modalSample()] );
                break;
            
            case 'AllMeters':
                // 'Electricity' = 1
                echo $_POST['new_meter_info']['res_name'] == '1' ? 
                    json_encode( [$modal_meters->drawAfterAdd($electr_add_query), $modal_meter_dates->modalSample()] ) : 
                    json_encode( [$modal_meters->drawAfterAdd($water_add_query), $modal_meter_dates->modalSample()] );
                break;
        }
        break;
    
    // '+meter's date/valid info' button
    case isset($_POST['new_meter_date']):
        $water_table = ["water_meters", "wm_num"];
        $electr_table = ["electricity_meters", "em_num"];
        
        $modal_meter_dates = new ModalMeterDates($_POST['new_meter_date'], $_POST['r_id']);
        // again, check meter type to use exact piece of the mysql query
        switch ($_SESSION['meter_class']) {
            case 'WaterMeters':
                echo $modal_meter_dates->drawAfterAdd($water_table);
                break;
            
            case 'ElectricityMeters':
                echo $modal_meter_dates->drawAfterAdd($electr_table);
                break;
            
            // 'Electricity' = 1
            case 'AllMeters':
                echo $_POST['new_meter_date']['res_name'] == '1' ? $modal_meter_dates->drawAfterAdd($electr_table) : $modal_meter_dates->drawAfterAdd($water_table);
                break;
        }
        break;

    // '+agreement' button
    case isset($_POST['new_agr']):
        // create instances for both 'agreements' and 'relations' modals
        $modal_agrs = new ModalAgrs($_POST['new_agr'], $_POST['r_type'], $_POST['r_id']);
        $modal_rels = new ModalAgrRels(null, $_POST['r_type'], $_POST['r_id']);
        // it's needed for updating 2 tables simultaneously
        echo json_encode( [$modal_agrs->drawAfterAdd(), $modal_rels->modalSample()] );
        break;

    // '+relation meter_num <-> agreement' button
    case isset($_POST['new_rel']):
        $modal_rels = new ModalAgrRels($_POST['new_rel'], $_POST['r_type'], $_POST['r_id']);
        echo $modal_rels->drawAfterAdd();
        break;

    //---------------------------------
    //-----------Del buttons-----------
    //----------inside modals----------
    //---------------------------------

    // 'X meter num' button
    case isset($_POST['del_meter_num']):
        // create instances
        $modal_meter_nums = new ModalMeterNums($_POST['del_meter_num'], $_POST['r_type'], $_POST['r_id']);
        $modal_meters = new ModalMeters(null, $_POST['r_type'], $_POST['r_id']);
        $modal_meter_dates = new ModalMeterDates(null, $_POST['r_id']);
        $modal_rels = new ModalAgrRels(null, $_POST['r_type'], $_POST['r_id']);
        // check meter type
        switch ($_SESSION['meter_class']) {
            case 'WaterMeters':
                // update all 4 tables in modals
                echo json_encode( [$modal_meter_nums->drawAfterDel($water_del_query),
                                    $modal_meters->modalSample(), 
                                    $modal_meter_dates->modalSample(),
                                    $modal_rels->modalSample()] );
                break;
            
            case 'ElectricityMeters':
                // same
                echo json_encode( [$modal_meter_nums->drawAfterDel($electr_del_query),
                                    $modal_meters->modalSample(), 
                                    $modal_meter_dates->modalSample(),
                                    $modal_rels->modalSample()] );
                break;
            
            case 'AllMeters':
                // same, depending on resource type
                echo $_POST['del_meter_num']['res_name'] == '1' ? 
                    json_encode( [$modal_meter_nums->drawAfterDel($electr_del_query),
                                    $modal_meters->modalSample(), 
                                    $modal_meter_dates->modalSample(),
                                    $modal_rels->modalSample()] ) : 
                    json_encode( [$modal_meter_nums->drawAfterDel($water_del_query),
                                    $modal_meters->modalSample(), 
                                    $modal_meter_dates->modalSample(),
                                    $modal_rels->modalSample()] );
                break;
        }
        break;
    
    // 'X meter's full info' button
    case isset($_POST['del_meter_info']):
        // create instances
        $modal_meters = new ModalMeters($_POST['del_meter_info'], $_POST['r_type'], $_POST['r_id']);
        $modal_meter_dates = new ModalMeterDates(null, $_POST['r_id']);
        $modal_meter_nums = new ModalMeterNums(null, $_POST['r_type'], $_POST['r_id']);
        $modal_rels = new ModalAgrRels(null, $_POST['r_type'], $_POST['r_id']);
        // check meter type
        switch ($_SESSION['meter_class']) {
            case 'WaterMeters':
                // update all 3 tables in modals
                echo json_encode( [$modal_meters->drawAfterDel($water_del_query), 
                                    $modal_meter_dates->modalSample(), 
                                    $modal_meter_nums->modalSample(), 
                                    $modal_rels->modalSample()] );
                break;
            
            case 'ElectricityMeters':
                // same
                echo json_encode( [$modal_meters->drawAfterDel($electr_del_query), 
                                    $modal_meter_dates->modalSample(), 
                                    $modal_meter_nums->modalSample(), 
                                    $modal_rels->modalSample()] );
                break;
            
            case 'AllMeters':
                // same, depending on resource type
                echo $_POST['del_meter_info']['res_name'] == '1' ? 
                    json_encode( [$modal_meters->drawAfterDel($electr_del_query), 
                                    $modal_meter_dates->modalSample(), 
                                    $modal_meter_nums->modalSample(), 
                                    $modal_rels->modalSample()] ) : 
                    json_encode( [$modal_meters->drawAfterDel($water_del_query), 
                                    $modal_meter_dates->modalSample(), 
                                    $modal_meter_nums->modalSample(), 
                                    $modal_rels->modalSample()] );
                break;
        }
        break;
    
    // 'X agreement' button
    case isset($_POST['del_agr']):
        // create instances
        $modal_agrs = new ModalAgrs($_POST['del_agr'], $_POST['r_type'], $_POST['r_id']);
        $modal_rels = new ModalAgrRels(null, $_POST['r_type'], $_POST['r_id']);
        // update 2 tables
        echo json_encode( [$modal_agrs->drawAfterDel(), $modal_rels->modalSample()] );
        break;
    
    // 'X relation meter_num <-> agreement' button
    case isset($_POST['del_rel']):
        $modal_rels = new ModalAgrRels($_POST['del_rel'], $_POST['r_type'], $_POST['r_id']);
        echo $modal_rels->drawAfterDel();
        break;
}
