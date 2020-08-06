<?php
include_once "function.php";
include_once "meters/ElectricityMeters.php";
include_once "meters/WaterMeters.php";
include_once "meters/AllMeters.php";
ini_set("default_charset","UTF-8");

// a common abstract class for all types of meters
abstract class Meters
{
    // 
    public $regs;
    public $str_types;
    public $tuses;
    public $owners;
    public $meter_types;
    public $res_names;
    public $res_types;
    public $meter_nums;
    public $general;
    public $agrs;
    
    protected $r_type; // 1 - electr, 2 - water
    protected $r_id; // 1 - electr, 2 - cold water, 3 - hot water
    
    // constructor
    public function __construct() {   
        $this->regs = selectQuery("SELECT reg_id, name FROM regions ORDER BY name");
        $this->str_types = selectQuery("SELECT st_id, type FROM struct_types ORDER BY type");
        $this->tuses = selectQuery("SELECT tus_id, name FROM tuses ORDER BY name");
        $this->owners = selectQuery("SELECT own_id, name FROM owners ORDER BY name");
        
        //
        $this->res_names = selectQuery("SELECT res_id, name 
                                            FROM resources AS res
                                                INNER JOIN res_types AS rt ON rt.res_typ_id = res.res_typ_id
                                            WHERE " . $this->r_type);
        
        //
        $this->res_types = selectQuery("SELECT res_typ_id AS rt_id, type AS res_type 
                                            FROM res_types as rt 
                                            WHERE " . $this->r_type);

        // meter_nums <-> resource type relations
        $this->meter_nums = selectQuery("SELECT 
                                            am.id AS id, 
                                            am.meter_num AS meter_num, 
                                            res.name AS res_name 
                                                FROM all_meters AS am 
                                                    INNER JOIN resources AS res ON res.res_id = am.res_id 
                                                WHERE status = 1 and " . $this->r_id . " 
                                                ORDER BY am.meter_num"); 
        
        //
        $this->meter_types = selectQuery("SELECT mt_id, type FROM meter_types AS mt ORDER BY type"); 

        // agreements
        $this->agrs = selectQuery("SELECT 
                                        ag_id, 
                                        agr.name AS name, 
                                        conclusion_date, 
                                        res.name AS res_name 
                                                FROM agreements AS agr 
                                                    INNER JOIN resources AS res ON res.res_id = agr.res_id
                                                WHERE agr.status = 1 and " . $this->r_id . " 
                                                ORDER BY ag_id");
        
        // general table (relations between agreement nums and meters)
        $this->general = selectQuery("SELECT  
                                            gen.uid AS uid, 
                                            agr.name AS agr, 
                                            code,
                                            am.meter_num AS meter_num 
                                                    FROM general AS gen
                                                        INNER JOIN agreements AS agr ON gen.ag_id = agr.ag_id
                                                        INNER JOIN all_meters AS am ON am.id = gen.meter_num
                                                        INNER JOIN res_types AS rt ON rt.res_typ_id = am.res_typ_id
                                                    WHERE gen.status = 1 and " . $this->r_type . " 
                                                    ORDER BY agr.ag_id");    
    }
    
    // needed data
    public function getVars() {
        return ['r_type' => $this->r_type, 'r_id' => $this->r_id];
    }
    
    abstract public function specificResource();
}