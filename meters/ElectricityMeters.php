<?php
ini_set("default_charset","UTF-8");

// 
class ElectricityMeters extends Meters
{
    public $meter_info;
    // electricity own constructor
    public function __construct() {
        // 
        $this->r_type = 'rt.res_typ_id = 1';
        $this->r_id = 'res.res_id = 1';
        parent::__construct();
        
        $this->meter_info = $this->specificResource();
    }
    
    public function specificResource() {
        // full info about electricity meter devices
        $meter_info = selectQuery("SELECT 
                                        am.meter_num AS meter_num, 
                                        em.id AS id, 
                                        mt.type AS meter_type, 
                                        reg.name AS reg, 
                                        tus.name AS tus, 
                                        st.type AS str_type, 
                                        struct_num AS str_num, 
                                        em.address AS address, 
                                        res.name AS res_name, 
                                        em.setup_date AS setup_date, 
                                        em.comm_date AS comm_date, 
                                        em.inspect_date AS inspect_date, 
                                        em.next_insp_date AS next_insp_date, 
                                        em.end_verif AS end_verif, 
                                        own.name AS owner 
                                                FROM all_meters AS am
                                                    INNER JOIN electricity_meters AS em ON em.em_num = am.meter_num
                                                    INNER JOIN tuses AS tus ON tus.tus_id = em.tus_id
                                                    INNER JOIN struct_types AS st ON st.st_id = em.st_id
                                                    INNER JOIN meter_types AS mt ON mt.mt_id = em.mt_id
                                                    INNER JOIN regions AS reg ON reg.reg_id = em.reg_id
                                                    INNER JOIN resources AS res ON res.res_id = em.res_id
                                                    LEFT JOIN owners AS own ON own.own_id = em.own_id
                                                WHERE am.status = 1 and " . $this->r_id . " 
                                                ORDER BY am.meter_num");
        return $meter_info;
    }
}
