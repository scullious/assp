<?php
ini_set("default_charset","UTF-8");

// 
class ModalAgrs extends Modals
{
    
    public function __construct($args, $r_type, $r_id) {
        $this->args = $args;
        $this->r_type = $r_type;
        $this->r_id = $r_id;
    }
    
    public function modalSample() {
        $db = getConnect();
        $res_names = selectQuery("SELECT res_id, name 
                                            FROM resources AS res
                                                INNER JOIN res_types AS rt ON rt.res_typ_id = res.res_typ_id
                                            WHERE " . $this->r_type);

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

        $html = "<thead>
                    <th hidden>id</th>
                    <th class='align-middle' style='width: 31.7%'>Договор</th>
                    <th class='align-middle'>Дата заключения</th>
                    <th class='align-middle'>Тип ресурса</th>
                    <th class='align-middle' style='width: 10%'></th>
                </thead>
                <tbody>";

        foreach ($agrs as $a) {
            $html .= "  <tr>
                            <td hidden class='align-middle'>" . $a['ag_id'] . "</td>
                            <td class='align-middle'>" . $a['name'] . "</td>
                            <td class='align-middle'>" . $a['conclusion_date'] . "</td>
                            <td class='align-middle'>" . $a['res_name'] . "</td>
                            <td class='align-middle'>
                                <button type='button' onclick=\"del_data('agr', $(this))\" class='btn btn-light' style='background-color: white;'>
                                    <img src='img/del.png' width='30px' />
                                </button>
                            </td>
                        </tr>";
        }

        $html .= "      <tr>
                            <td hidden></td>
                            <td class='align-middle'><input class='form-control' style='text-align:right;' id='new_agr' /></td>
                            <td class='align-middle'><input type='date' class='form-control' style='text-align:right;' id='concl_date' /></td>
                            <td class='align-middle'>
                                <select class='form-control' style='text-align:right;' id='agrs_res_name'>";

        foreach ($res_names as $rn) {
            $html .= "              <option value='" . $rn['res_id'] . "'>" . $rn['name'] . "</option>";
        }

        $html .= "              </select>
                            </td>
                            <td class='align-middle'>
                                <button type='button' onclick=\"new_data('agr')\" class='btn btn-light' style='background-color: white;'>
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

        mysqli_query($db, "INSERT INTO agreements (
                                            name, 
                                            conclusion_date, 
                                            res_id, 
                                            status
                                            ) VALUES (
                                                '" . $data_arr['agr'] . "', 
                                                '" . $data_arr['concl_date'] . "', 
                                                '" . $data_arr['res_name'] . "', 
                                                1
                                            )");

        mysqli_close($db);
        return $this->modalSample();
    }
    
    //
    public function drawAfterDel() {
        $db = getConnect();
        mysqli_query($db, "UPDATE agreements SET status = 0 WHERE ag_id = " . $this->args);
        mysqli_close($db);
        return $this->modalSample();
    }
}
