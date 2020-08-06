<?php
ini_set("default_charset","UTF-8");

//
class AllMeters extends Meters
{
    public $meter_info;
    // electricity own constructor
    public function __construct() {
        // 
        $this->r_type = '1';
        $this->r_id = '1';
        // call the constructor of the base class
        parent::__construct();
        
        $this->meter_info = $this->specificResource();
    }
    
    public function specificResource() {
        // full info about water meter devices
        $wm_info = selectQuery("SELECT 
                                        am.meter_num AS meter_num, 
                                        wm.id AS id,
                                        mt.type AS meter_type, 
                                        reg.name AS reg, 
                                        tus.name AS tus, 
                                        st.type AS str_type, 
                                        struct_num AS str_num, 
                                        wm.address AS address, 
                                        res.name AS res_name, 
                                        wm.setup_date AS setup_date, 
                                        wm.comm_date AS comm_date, 
                                        wm.inspect_date AS inspect_date, 
                                        wm.next_insp_date AS next_insp_date, 
                                        wm.end_verif AS end_verif, 
                                        own.name AS owner 
                                                FROM all_meters AS am
                                                    INNER JOIN water_meters AS wm ON wm.wm_num = am.meter_num
                                                    INNER JOIN tuses AS tus ON tus.tus_id = wm.tus_id
                                                    INNER JOIN struct_types AS st ON st.st_id = wm.st_id
                                                    INNER JOIN meter_types AS mt ON mt.mt_id = wm.mt_id
                                                    INNER JOIN regions AS reg ON reg.reg_id = wm.reg_id
                                                    INNER JOIN resources AS res ON res.res_id = wm.res_id
                                                    LEFT JOIN owners AS own ON own.own_id = wm.own_id
                                                WHERE am.status = 1 and res.res_id in (2,3) 
                                                ORDER BY am.meter_num");
        
        // full info about water meter devices
        $em_info = selectQuery("SELECT 
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
                                                WHERE am.status = 1 and res.res_id = 1 
                                                ORDER BY am.meter_num");

        // collected full info about meter devices
        if (!empty($em_info)) {  
            if (!empty($wm_info)) {  
                return array_merge($em_info, $wm_info); // em == 1 && wm == 1
            } else {
                return $em_info; // em == 1 && wm == 0
            }
        } else {
            return $wm_info; // em == 0
        }
    }
}