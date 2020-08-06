<?php
ini_set("default_charset","UTF-8");

// 
class ModalStructures extends Modals
{
    public function __construct($args) {
        $this->args = $args;
    }
    
    //
    public function modalSample() {
        $db = getConnect();
        $str_types = selectQuery("SELECT st_id, type FROM struct_types ORDER BY type");

        $html = "<thead> 
                        <tr>
                            <th class='align-middle'>Наименование объекта</td>
                            <th class='align-middle' style='width: 10%'></td>
                        </tr>
                </thead>
                <tbody>";

        foreach ($str_types as $st) {
            $html .= "  <tr>
                            <td class='align-middle' style='height: 40px'>" . $st['type'] . "</td>
                            <td class='align-middle' style='height: 40px'></td>
                        </tr>";
        }

        $html .= "      <tr>
                            <td class='align-middle'><input class='form-control' style='text-align:right;' id='new_str' /></td>
                            <td class='align-middle'>
                                <button type='button' onclick=\"new_data('str')\" class='btn btn-light' style='background-color: white;'>
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
        mysqli_query($db, "INSERT INTO struct_types (type) VALUES ('" . $this->args . "')");
        mysqli_close($db);
        return $this->modalSample();
    }
}

