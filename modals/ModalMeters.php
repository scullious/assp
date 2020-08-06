<?php
ini_set("default_charset","UTF-8");

// 
class ModalMeters extends Modals
{
    public function __construct($args, $r_type, $r_id) {
        $this->args = $args;
        $this->r_type = $r_type;
        $this->r_id = $r_id;
    }
    
    public function modalSample() {
        $db = getConnect();
        $regs = selectQuery("SELECT reg_id, name FROM regions ORDER BY name");
        $str_types = selectQuery("SELECT st_id, type FROM struct_types ORDER BY type");
        $tuses = selectQuery("SELECT tus_id, name FROM tuses ORDER BY name");
        $owners = selectQuery("SELECT own_id, name FROM owners ORDER BY name");
        $meter_types = selectQuery("SELECT mt_id, type FROM meter_types AS mt ORDER BY type"); 
        $res_names = selectQuery("SELECT res_id, name 
                                            FROM resources AS res
                                                INNER JOIN res_types AS rt ON rt.res_typ_id = res.res_typ_id
                                            WHERE " . $this->r_type);

        // meter_id <-> resource type relations
        $meter_nums = selectQuery("SELECT 
                                        am.id AS id, 
                                        am.meter_num AS meter_num, 
                                        res.name AS res_name 
                                            FROM all_meters AS am 
                                                INNER JOIN resources AS res ON res.res_id = am.res_id 
                                            WHERE status = 1 and " . $this->r_id . " 
                                            ORDER BY am.meter_num"); 
        
        $meter_info = $_SESSION['meter_obj']->specificResource(); // pass an instance and call its method

        $html = "<thead>
                    <th class='align-middle' style='width: 11%'>Заводской номер</th>
                    <th hidden class='align-middle' style='width: 8%'>ID</th>
                    <th class='align-middle' style='width: 7%'>Тип счетчика</th>
                    <th class='align-middle' style='width: 11%'>Район</th>
                    <th class='align-middle' style='width: 6%'>ТУС</th>
                    <th class='align-middle' style='width: 15%'>Объект</th>
                    <th class='align-middle' style='width: 5%'>Номер</br>объекта</th>
                    <th class='align-middle' style='width: 15%'>Адрес</th>
                    <th class='align-middle' style='width: 5%'>Тип</br>ресурса</th>
                    <!--th class='align-middle' style='width: 10%'>Балансовая</br>принадлежность</th-->
                    <th class='align-middle' style='width: 1%'></th>
                </thead>
                <tbody>";

        foreach ($meter_info as $m) {
            $html .= "  <tr>
                            <td class='align-middle'>" . $m['meter_num'] . "</td>
                            <td hidden class='align-middle'>" . $m['id'] . "</td>
                            <td class='align-middle'>" . $m['meter_type'] . "</td>
                            <td class='align-middle'>" . $m['reg'] . "</td>
                            <td class='align-middle'>" . $m['tus'] . "</td>
                            <td class='align-middle'>" . $m['str_type'] . "</td>
                            <td class='align-middle'>" . $m['str_num'] . "</td>
                            <td class='align-middle'>" . $m['address'] . "</td>
                            <td class='align-middle'>" . $m['res_name'] . "</td>
                            <!--td class='align-middle'>" . $m['owner'] . "</td!-->
                            <td class='align-middle'>
                                <button type='button' onclick=\"del_data('meter_info', $(this))\" class='btn btn-light' style='background-color: white;'>
                                    <img src='img/del.png' width='30px' />
                                </button>
                            </td>
                        </tr>";
        }

        $html .= "      <tr>
                                <td class='align-middle'>
                                    <select class='form-control' style='text-align:right;' id='meters_meter_num'>";

        foreach ($meter_nums as $mn) { 
            $html .= "                  <option value='" . $mn['meter_num'] . "'>" . $mn['meter_num'] . "</option>";
        }

        $html .= "                  </select>
                                </td>
                                <td hidden class='align-middle'><input class='form-control' style='text-align:right;' id='meters_id' /></td>
                                <td class='align-middle'>
                                    <select class='form-control' style='text-align:right;' id='meters_meter_type'>";

        foreach ($meter_types as $mt) { 
            $html .= "                  <option value='" . $mt['mt_id'] . "'>" . $mt['type'] . "</option>";
        }

        $html .= "                   </select>
                                </td>
                                <td class='align-middle'>
                                    <select class='form-control' style='text-align:right;' id='reg'>";

        foreach ($regs as $r) {
            $html .= "                  <option value='" . $r['reg_id'] . "'>" . $r['name'] . "</option>";
        }

        $html .= "                  </select>
                                </td>
                                <td class='align-middle'>
                                    <select class='form-control' style='text-align:right;' id='tus'>";

        foreach ($tuses as $t) {
            $html .= "                  <option value='" . $t['tus_id'] . "'>" . $t['name'] . "</option>";
        }

        $html .= "                  </select>
                                </td>
                                <td class='align-middle'>
                                    <select class='form-control' style='text-align:right;' id='str_type'>";

        foreach ($str_types as $s) {
            $html .= "                  <option value='" . $s['st_id'] . "'>" . $s['type'] . "</option>";
        }

        $html .= "                  </select>
                                </td>
                                <td class='align-middle'><input class='form-control' style='text-align:right;' id='str_num' /></td>
                                <td class='align-middle'><input class='form-control' style='text-align:right;' id='address' /></td>
                                <td class='align-middle'>
                                    <select class='form-control' style='text-align:right;' id='meters_res_name'>";

        foreach ($res_names as $rn) {
            $html .= "                  <option value='" . $rn['res_id'] . "'>" . $rn['name'] . "</option>";
        }

        $html .= "                  </select>
                                </td>
                                <!--td class='align-middle'>
                                    <select class='form-control' style='text-align:right;' id='owner'>";

//        foreach ($owners as $o) {
//            $html .= "                  <option value='" . $o['own_id'] . "'>" . $o['name'] . "</option>";
//        }

        $html .= "                  </select>
                                </td-->
                                <td class='align-middle'>
                                    <button type='button' onclick=\"new_data('meter_info')\" class='btn btn-light' style='background-color: white;'>
                                        <img src='img/add.png' width='30px' />
                                    </button>
                                </td>
                            </tr>
                    </tbody>";
    
        mysqli_close($db);
        return $html;
    }
    
    //
    public function drawAfterAdd($q) {
        $db = getConnect();
        $data_arr = $this->args;
        mysqli_query($db, $q[0] . ") VALUES (
                                        '" . $data_arr['meter_num'] . "', 
                                        '" . $data_arr['id'] . "', 
                                        '" . $data_arr['meter_type'] . "', 
                                        '" . $data_arr['reg'] . "', 
                                        '" . $data_arr['tus'] . "', 
                                        '" . $data_arr['str_type'] . "', 
                                        '" . $data_arr['str_num'] . "', 
                                        '" . $data_arr['address'] . "', 
                                        '" . $data_arr['res_name'] ."', 
                                        1
                                    )"); // '" . $data_arr['owner'] . "',
        
        mysqli_query($db, $q[1] . ") VALUES (
                                        '" . $data_arr['meter_num'] . "', 
                                        0, 
                                        0
                                    )");
        
        mysqli_close($db);
        
        return $this->modalSample();
    }
    
    // 
    public function drawAfterDel($q) {
        $db = getConnect();
        mysqli_query($db, $q . "'" . $this->args['meter_num'] . "'" ); // change in water_meters/electr_meters table
        mysqli_query($db, "UPDATE all_meters SET status = 0 WHERE meter_num = '" . $this->args['meter_num'] . "'" ); // change in all_meters table
        mysqli_close($db);
        return $this->modalSample();
    }
}
