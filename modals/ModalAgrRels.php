<?php
ini_set("default_charset","UTF-8");

// 
class ModalAgrRels extends Modals
{
    public function __construct($args, $r_type, $r_id) {
        $this->args = $args;
        $this->r_type = $r_type;
        $this->r_id = $r_id;
    }
    
    public function modalSample() {
        $db = getConnect();
        // meter_id <-> resource type relations
        $meter_nums = selectQuery("SELECT 
                                        am.id AS id, 
                                        am.meter_num AS meter_num, 
                                        res.name AS res_name 
                                            FROM all_meters AS am 
                                                INNER JOIN resources AS res ON res.res_id = am.res_id 
                                            WHERE status = 1 and " . $this->r_id . " 
                                            ORDER BY am.meter_num");  

        // agreements
        $agrs = selectQuery("SELECT 
                                    ag_id, 
                                    agr.name AS name, 
                                    conclusion_date, 
                                    res.name AS res_name 
                                            FROM agreements AS agr 
                                                INNER JOIN resources AS res ON res.res_id = agr.res_id
                                            WHERE agr.status = 1 and " . $this->r_id . " 
                                            ORDER BY ag_id");

        //
        $general = selectQuery("SELECT  
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

        $html = "<thead>
                    <th hidden>uid</th>
                    <th class='align-middle' style='width: 30%'>Договор</th>
                    <th class='align-middle' style='width: 18%'>Код точки учета</th>
                    <th class='align-middle' style='width: 31%'>Заводской номер счетчика</th>
                    <th class='align-middle' style='width: 1%'></th>
                </thead>
                <tbody>";

        foreach ($general as $g) {
            $html .= "  <tr>
                            <td hidden class='align-middle'>" . $g['uid'] . "</td>
                            <td class='align-middle'>" . $g['agr'] . "</td>
                            <td class='align-middle'>" . $g['code'] . "</td>
                            <td class='align-middle'>" . $g['meter_num'] . "</td>
                            <td class='align-middle'>
                                <button type='button' onclick=\"del_data('rel', $(this))\" class='btn btn-light' style='background-color: white;'>
                                    <img src='img/del.png' width='30px' />
                                </button>
                            </td>
                        </tr>";
        }

        $html .= "      <tr>
                            <td hidden></td>
                            <td class='align-middle'>
                                <select class='form-control' style='text-align:right;' id='agr'>";

        foreach ($agrs as $a) {
            $html .= "              <option value='" . $a['ag_id'] . "'>" . $a['name'] . "</option>";
        }

        $html .= "              </select>
                            </td>
                            <td class='align-middle'><input class='form-control' style='text-align:right;' id='code' /></td>
                            <td class='align-middle'>
                                <select class='form-control' style='text-align:right;' id='agrRel_meter_num'>";

        foreach ($meter_nums as $mn) {
            $html .= "              <option value='" . $mn['id'] . "'>" . $mn['meter_num'] . "</option>";
        }

        $html .= "              </select>
                            </td>
                            <td class='align-middle'>
                                <button type='button' onclick=\"new_data('rel')\" class='btn btn-light' style='background-color: white;'>
                                    <img src='img/add.png' width='30px' />
                                </button>
                            </td>
                        </tr>
                    </tbody>";
    
        mysqli_close($db);
        return $html;
    }
    
    //
    public function drawAfterAdd($q = null) {
        $db = getConnect();
        $data_arr = $this->args;
        
        mysqli_query($db, "INSERT INTO general (
                                            ag_id, 
                                            code, 
                                            meter_num, 
                                            status
                                            ) VALUES (
                                                '" . $data_arr['agr'] . "', 
                                                '" . $data_arr['code'] . "', 
                                                '" . $data_arr['meter_num'] . "', 
                                                1
                                            )");
        
        mysqli_close($db);
        return $this->modalSample();
    }
    
    // 
    public function drawAfterDel() {
        $db = getConnect();
        mysqli_query($db, "UPDATE general SET status = 0 WHERE uid = " . $this->args);
        mysqli_close($db);
        return $this->modalSample();
    }
}
