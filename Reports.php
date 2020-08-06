<?php
include_once "function.php";
include_once "reports/TEK.php";
include_once "reports/TGK.php";
include_once "reports/VodokanalPartial.php";
include_once "reports/VodokanalFull.php";
ini_set("default_charset","UTF-8");

// a common abstract class for all types of reports
abstract class Reports
{
    private $filename;
    private $rows;
    private $rows_edit;
    private $region;
    private $str_type;
    private $address;
    private $agreement;
    private $water_type;
    private $meter_num;
    private $last_value;
    private $value; 
    private $consum;
    
    public function __construct($table) {
        $this->getMeters($table);
        //$this->filename = $filename;
    }
    
    
    abstract public function getReport();
    
    
    abstract protected function getMeters($rows);
    
    
    abstract protected function editTable($rows, $to_edit);
    
            
    abstract protected function drawHTML($data);
    
    
    abstract protected function formReport($data);   
}

