<?php
ini_set("default_charset","UTF-8");

class VodokanalPartial extends Reports
{
    public function __construct($table, $filename) {
        $this->rows = $table;
        $this->filename = $filename;
        parent::__construct($this->rows);
    }
    
    // output ready xls-file 
    public function getReport() {
        $this->formReport( ['last_value' => $this->last_value, 'value' => $this->value, 'consum' => $this->consum] );
    }
    
    // obtain all data on meters related to meter_nums given from xls template
    protected function getMeters($rows) {
        require_once 'function.php';
        
        $this->last_value = array();
        $this->value      = array();
        $this->consum     = array();
        
        $prev_readings = selectQuery('SELECT wm_num, value, consumption FROM wm_' . reduce_date((date('m')), date('Y')));
        $readings = selectQuery('SELECT wm_num, value, consumption FROM wm_' . date('m_Y'));
        for ($i = 1; $i < count($rows); ++$i) {
            $index = array_search($rows[$i][4], array_map(function($a) { preg_match('/\s?([a-zа-я0-9]+)$/iu', $a['wm_num'], $b); return @$b[1]; }, $prev_readings));
            $index2 = array_search($rows[$i][4], array_map(function($a) { preg_match('/\s?([a-zа-я0-9]+)$/iu', $a['wm_num'], $b); return @$b[1]; }, $readings));  
            if ($index !== false) {
                array_push($this->consum, $prev_readings[$index]['consumption']); // consumtion for the previous month
            } else {
                array_push($this->consum, null);
                
            }
            
            if ($index2 !== false) {
                array_push($this->last_value, $readings[$index2]['value'] - $readings[$index2]['consumption']);
                array_push($this->value, $readings[$index2]['value']);
            } else {
                array_push($this->last_value, null);
                array_push($this->value, null);
            }
        }
        
        $this->editTable($rows, ['last_value' => $this->last_value, 'value' => $this->value, 'consum' => $this->consum]);
    }

    // edit initial table
    protected function editTable($rows, $to_edit) {
        $last_value = $to_edit['last_value'];
        $value = $to_edit['value'];
        $consum = $to_edit['consum'];
        $this->rows_edit = $rows; // create new array for edited table
        
        for ($i = 0; $i < count($value); ++$i) {
            $this->rows_edit[$i + 1][7] = $consum[$i];
            $this->rows_edit[$i + 1][8] = $last_value[$i];
            $this->rows_edit[$i + 1][9] = $value[$i];
        }
        
        $this->drawHTML($this->rows_edit);
    }
    
    // HTML drawer
    protected function drawHTML($data) {
        include_once('view/header.html');
        include_once('view/menu.html');
        $html = "</br><table class='table rounded table-bordered text-center tableFixHead' style='width: 85%' align='center' bordercolor='black' border=5>
                    <thead>";
        for ($i = 0; $i < count($data[0]); ++$i) {
            $html .=    "<th class='align-middle'>" . $data[0][$i] . '</th>';
        }
        
        $html .= '  </thead>
                    <tbody>';
        
        for ($i = 1; $i < count($data); ++$i) {
            $html .= '  <tr>';
            for ($j = 0; $j < count($data[$i]); ++$j) {
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
        
        $xls = PHPExcel_IOFactory::load($this->filename);
        $xls->setActiveSheetIndex(0);
        for ($i = 0; $i < count($data['value']); ++$i) {
            $xls->getActiveSheet()->setCellValue('H' . ($i + 2), $data['consum'][$i]);
            $xls->getActiveSheet()->setCellValue('I' . ($i + 2), $data['last_value'][$i]);
            $xls->getActiveSheet()->setCellValue('J' . ($i + 2), $data['value'][$i]);
        }
        
        $objWriter = PHPExcel_IOFactory::createWriter($xls, 'Excel5');
        $objWriter->save('output/Отчет для Водоканала.xls');
    }
}