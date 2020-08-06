<?php
ini_set("default_charset","UTF-8");

// 
class ModalMeterTypes extends Modals
{
    public function __construct($args) {
        $this->args = $args;
    }
    
    //
    public function modalSample() {
        $db = getConnect();
        // meter_id <-> resource type relations
        $meter_types = selectQuery("SELECT mt_id, type FROM meter_types AS mt ORDER BY type"); 

        $html = "<thead> 
                    <tr>
                        <th hidden class='align-middle'>id</th>
                        <th class='align-middle'>Тип счетчика</td>
                        <th class='align-middle' style='width: 10%'></td>
                    </tr>
                </thead>
                <tbody>";

        foreach ($meter_types as $mt) {
            $html .= "  <tr>
                            <td hidden class='align-middle' style='height: 40px'>" . $mt['mt_id'] . "</td>
                            <td class='align-middle' style='height: 40px'>" . $mt['type'] . "</td>
                            <td class='align-middle' style='height: 40px'></td>
                        </tr>";
        }

        $html .= "      <tr>
                            <td hidden></td>
                            <td class='align-middle'><input class='form-control' style='text-align:right;' id='meterTypes_meter_type' /></td>
                            <td class='align-middle'>
                                <button type='button' onclick=\"new_data('meter_type')\" class='btn btn-light' style='background-color: white;'>
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
        mysqli_query($db, "INSERT INTO meter_types ( 
                                        type
                                        ) VALUES (
                                            '" . $data_arr['meter_type'] . "'
                                        )");    
        mysqli_close($db);
        return $this->modalSample();
    }
}
