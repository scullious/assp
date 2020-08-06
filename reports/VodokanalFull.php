<?php
ini_set("default_charset","UTF-8");

class VodokanalFull extends Reports
{
    
    public function __construct($table, $filename) {
        $this->rows = $table;
        parent::__construct($this->rows);
    }
    
    // output ready xls-file
    public function getReport() {
        $this->formReport($this->rows_edit);
    }
    
    // obtain all data on meters related to meter_nums given from xls template
    protected function getMeters($rows) {
        require_once 'function.php';
        $last_month = reduce_date(date('m'), date('Y'));
        
        $meter_info = selectQuery('SELECT 
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
                                        own.name AS owner, 
                                        agr.name AS agr, 
                                        wm_val.value AS value, 
                                        wm_val.consumption AS consumption
                                                FROM all_meters AS am
                                                    INNER JOIN water_meters AS wm ON wm.wm_num = am.meter_num
                                                    INNER JOIN tuses AS tus ON tus.tus_id = wm.tus_id
                                                    INNER JOIN struct_types AS st ON st.st_id = wm.st_id
                                                    INNER JOIN meter_types AS mt ON mt.mt_id = wm.mt_id
                                                    INNER JOIN regions AS reg ON reg.reg_id = wm.reg_id
                                                    INNER JOIN resources AS res ON res.res_id = wm.res_id
                                                    INNER JOIN owners AS own ON own.own_id = wm.own_id
                                                    INNER JOIN general AS gen ON gen.meter_num = am.id
                                                    INNER JOIN agreements AS agr ON gen.ag_id = agr.ag_id
                                                    INNER JOIN wm_' . date('m_Y') . ' AS wm_val ON wm_val.wm_num = am.meter_num
                                                WHERE res.res_id in (2,3) 
                                                ORDER BY reg');
        
        $this->editTable($rows, $meter_info);
    }

    // edit initial table
    protected function editTable($rows, $to_edit) {
        // table header
        foreach ($rows[0] as $i => $r) {
            !empty($r) ? $this->rows_edit[0][$i] = $r : null;
        }
        
        // table body
        foreach ($to_edit as $i => $mi) {
            $this->rows_edit[$i + 1][0] = $mi['reg'];
            $this->rows_edit[$i + 1][1] = $mi['str_type'] . (!empty($mi['str_num']) ? ' №' .  $mi['str_num'] : null);
            $this->rows_edit[$i + 1][2] = $mi['address'];
            $this->rows_edit[$i + 1][3] = $mi['agr'];
            $this->rows_edit[$i + 1][4] = $mi['res_name'];
            $this->rows_edit[$i + 1][5] = $mi['meter_num'];
            $this->rows_edit[$i + 1][6] = $mi['value'];
            $this->rows_edit[$i + 1][7] = $mi['consumption'];
            $this->rows_edit[$i + 1][8] = $mi['value'] - $mi['consumption'];
        }
        
        $this->drawHTML($this->rows_edit); // call table drawer
    }
    
    // HTML drawer
    protected function drawHTML($data) {
        include_once('view/header.html');
        include_once('view/menu.html');
        $html = "</br><table class='table rounded table-bordered text-center tableFixHead' style='width: 85%' align='center' bordercolor='black' border=5>
                    <thead>";
        for ($i = 0; $i < count($data[0]); ++$i) { // drop 2 excess columns from the right edge
            $html .=    "<th class='align-middle'>" . $data[0][$i] . '</th>';
        }
        
        $html .= '  </thead>
                    <tbody>';
        
        for ($i = 1; $i < count($data); ++$i) { 
            $html .= '  <tr>';
            for ($j = 0; $j < count($data[$i]); ++$j) { // drop 2 excess columns from the right edge
                $html .= "  <td class='align-middle'>" . $data[$i][$j] . "</td>";
            }
            $html .= '  </tr>';
        }
        
        $html .= '  </tbody>
                </table>';
        
        echo $html;
    }
    
    // form xls-file
    protected function formReport($data) {
        include_once('PHPExcel/Classes/PHPExcel.php');
        include_once('css/excelStyles.php');
        
        $xls = PHPExcel_IOFactory::load('output/vodokanal_full_template.xlsx');
        $xls->setActiveSheetIndex(0);
        
        // table body in xls
        foreach ($data as $i => $d) { 
            $i === 0 ? $i = 1 : false; // ignore table header (it exists already)
            $xls->getActiveSheet()->setCellValue('A' . ($i + 1), $d[0]);
            $xls->getActiveSheet()->setCellValue('B' . ($i + 1), $d[1]);
            $xls->getActiveSheet()->setCellValue('C' . ($i + 1), $d[2]);
            $xls->getActiveSheet()->setCellValue('D' . ($i + 1), $d[3]);
            $xls->getActiveSheet()->setCellValue('E' . ($i + 1), $d[4]);
            $xls->getActiveSheet()->setCellValue('F' . ($i + 1), $d[5]);
            $xls->getActiveSheet()->setCellValue('G' . ($i + 1), $d[6]);
            $xls->getActiveSheet()->setCellValue('H' . ($i + 1), $d[7]);
            $xls->getActiveSheet()->setCellValue('I' . ($i + 1), $d[8]);
            
            // cell style
            $xls->getActiveSheet()->getStyle('A' . ($i + 1))->applyFromArray($border_cell);
            $xls->getActiveSheet()->getStyle('B' . ($i + 1))->applyFromArray($border_cell);
            $xls->getActiveSheet()->getStyle('C' . ($i + 1))->applyFromArray($border_cell);
            $xls->getActiveSheet()->getStyle('D' . ($i + 1))->applyFromArray($border_cell);
            $xls->getActiveSheet()->getStyle('E' . ($i + 1))->applyFromArray($border_cell);
            $xls->getActiveSheet()->getStyle('F' . ($i + 1))->applyFromArray($border_cell);
            $xls->getActiveSheet()->getStyle('G' . ($i + 1))->applyFromArray($border_cell);
            $xls->getActiveSheet()->getStyle('H' . ($i + 1))->applyFromArray($border_cell);
            $xls->getActiveSheet()->getStyle('I' . ($i + 1))->applyFromArray($border_cell);
        }
        
        $objWriter = PHPExcel_IOFactory::createWriter($xls, 'Excel2007');
        $objWriter->save('output/Отчет для Водоканала полный.xlsx');
    }
}