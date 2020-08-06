<?php
include_once "function.php";
include_once "modals/ModalRegions.php";
include_once "modals/ModalStructures.php";
include_once "modals/ModalTuses.php";
include_once "modals/ModalOwners.php";
include_once "modals/ModalMeterTypes.php";
include_once "modals/ModalMeterNums.php";
include_once "modals/ModalMeters.php";
include_once "modals/ModalMeterDates.php";
include_once "modals/ModalAgrs.php";
include_once "modals/ModalAgrRels.php";
ini_set("default_charset","UTF-8");

// a common abstract class for all types of modals
abstract class Modals
{
    protected $args;
    protected $r_type;
    protected $r_id;
    
    // common modal view after any actions
    abstract public function modalSample();
    
    // 'add' action
    abstract public function drawAfterAdd($q);
    
    // there is also a method drawAfterDel() in some instances;   
}
