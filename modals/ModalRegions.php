<?php
ini_set("default_charset","UTF-8");

// 
class ModalRegions extends Modals
{
    public function __construct($args) {
        $this->args = $args;
    }
    
    //
    public function modalSample() {
        $db = getConnect();
        $regs = selectQuery("SELECT reg_id, name FROM regions ORDER BY name");

        $html = "<thead> 
                        <tr>
                            <th class='align-middle'>Район</td>
                            <th class='align-middle' style='width: 10%'></td>
                        </tr>
                </thead>
                <tbody>";

        foreach ($regs as $r) {
            $html .= "  <tr>
                            <td class='align-middle' style='height: 40px'>" . $r['name'] . "</td>
                            <td class='align-middle' style='height: 40px'></td>
                        </tr>";
        }

        $html .= "      <tr>
                            <td class='align-middle'><input class='form-control' style='text-align:right;' id='new_reg' /></td>
                            <td class='align-middle'>
                                <button type='button' onclick=\"new_data('reg')\" class='btn btn-light' style='background-color: white;'>
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
        mysqli_query($db, "INSERT INTO regions (name) VALUES ('" . $this->args . "')");
        mysqli_close($db);
        return $this->modalSample();
    }
}
