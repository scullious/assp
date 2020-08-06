<?php
ini_set("default_charset","UTF-8");

//
class ModalMeterNums extends Modals
{
    public function __construct($args, $r_type, $r_id) {
        $this->args = $args;
        $this->r_type = $r_type;
        $this->r_id = $r_id;
    }
    
    //
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

        //
        $res_names = selectQuery("SELECT res_id, name 
                                        FROM resources AS res
                                            INNER JOIN res_types AS rt ON rt.res_typ_id = res.res_typ_id
                                        WHERE " . $this->r_type);

        $html = "<thead> 
                    <tr>
                        <th hidden class='align-middle'>id</th>
                        <th class='align-middle'>Заводской номер счетчика</td>
                        <th class='align-middle' style='width: 40%'>Тип ресурса</th>
                        <th class='align-middle' style='width: 10%'></td>
                    </tr>
                </thead>
                <tbody>";

        foreach ($meter_nums as $mn) {
            $html .= "  <tr>
                            <td hidden class='align-middle'>" . $mn['id'] . "</td>
                            <td class='align-middle'>" . $mn['meter_num'] . "</td>
                            <td class='align-middle'>" . $mn['res_name'] . "</td>
                            <td class='align-middle'>
                                <button type='button' onclick=\"del_data('meter_num', $(this))\" class='btn btn-light' style='background-color: white;'>
                                    <img src='img/del.png' width='30px' />
                                </button>
                            </td>
                        </tr>";
        }

        $html .= "      <tr>
                            <td hidden></td>
                            <td class='align-middle'><input class='form-control' style='text-align:right;' id='meterNums_meter_num' /></td>
                            <td class='align-middle'>
                                <select class='form-control' style='text-align:right;' id='meterNums_res_name'>";

        foreach ($res_names as $rn) {
            $html .= "              <option value='" . $rn['res_id'] . "'>" . $rn['name'] . "</option>";
        }

        $html .= "              </select>
                            </td>
                            <td class='align-middle'>
                                <button type='button' onclick=\"new_data('meter_num')\" class='btn btn-light' style='background-color: white;'>
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
        mysqli_query($db, "INSERT INTO all_meters (
                                        meter_num, 
                                        res_id, 
                                        status
                                        ) VALUES (
                                            '" . $data_arr['meter_num'] . "', 
                                            '" . $data_arr['res_name'] . "',
                                            1
                                        )");    
        mysqli_close($db);
        return $this->modalSample();
    }
    
    // 
    public function drawAfterDel($q) {
        $db = getConnect();
        mysqli_query($db, "UPDATE all_meters SET status = 0 WHERE meter_num = '" . $this->args['meter_num'] . "'"); // change in all_meters table
        mysqli_query($db, $q . "'" . $this->args['meter_num'] . "'"); // change in water_meters/electr_meters table
        mysqli_close($db);
        return $this->modalSample();
    }
}
