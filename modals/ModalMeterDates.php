<?php
ini_set("default_charset","UTF-8");

// 
class ModalMeterDates extends Modals
{
    public function __construct($args, $r_id) {
        $this->args = $args;
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
        
        $meter_info = $_SESSION['meter_obj']->specificResource(); // pass object and call its method

        $html = "<thead>
                    <th class='align-middle' style='width: 25%;'>Заводской номер</th>
                    <th class='align-middle' style='width: 156px;'>Дата</br>установки</th>
                    <th class='align-middle' style='width: 156px;'>Дата ввода в</br>эксплуатацию</th>
                    <th hidden class='align-middle' style='width: 156px;'>Дата осмотра</th>
                    <th hidden class='align-middle' style='width: 156px;'>Дата</br>след. осмотра</th>
                    <th class='align-middle' style='width: 156px;'>Поверка</br>до</th>
                    <th hidden>res_name</th>
                    <th class='align-middle' style='width: 1%;'></th>
                </thead>
                <tbody>";

        foreach ($meter_info as $m) {
            $html .= "      <tr>
                                <td class='align-middle'>" . $m['meter_num'] . "</td>
                                <td class='align-middle'>" . $m['setup_date'] . "</td>
                                <td class='align-middle'>" . $m['comm_date'] . "</td>
                                <td hidden class='align-middle'>" . $m['inspect_date'] . "</td>
                                <td hidden class='align-middle'>" . $m['next_insp_date'] . "</td>
                                <td class='align-middle'>" . $m['end_verif'] . "</td>
                                <td hidden class='align-middle'>" . $m['res_name'] . "</td>
                                <td class='align-middle'>
                                    <button type='button' onclick=\"edit_data($(this))\" class='btn btn-light' style='background-color: white;'>
                                        <img src='img/edit.png' width='30px' />
                                    </button>
                                </td>
                            </tr>";
        }
    
        mysqli_close($db);
        return $html;
    }
    
    //
    public function drawAfterAdd($q) {
        $db = getConnect();
        $data_arr = $this->args;
        mysqli_query($db, "UPDATE " . $q[0] . " SET 
                                                setup_date = '"     . $data_arr['setup_date'] . "', 
                                                comm_date = '"      . $data_arr['comm_date'] . "', 
                                                inspect_date = '"   . $data_arr['inspect_date'] . "', 
                                                next_insp_date = '" . $data_arr['next_insp_date'] . "', 
                                                end_verif = '"      . $data_arr['end_verif'] . "'
                                        WHERE " . $q[1] . " = '"     . $this->args['meter_num'] . "'" );
        
        mysqli_close($db);
        return $this->modalSample();
    }
}

