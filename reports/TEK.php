<?php
ini_set("default_charset","UTF-8");

class TEK extends Reports
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
        
        $readings = selectQuery('SELECT wm_num, value, consumption FROM wm_' . date('m_Y'));
        for ($i = 2; $i < count($rows); ++$i) {
            preg_match('/\s?([a-zа-я0-9]+)$/iu', $rows[$i][5], $meter_num);
            $index = array_search($meter_num[1], array_column($readings, 'wm_num'));
            if ($index !== false) {
                array_push($this->value, $readings[$index]['value']);
                array_push($this->consum, $readings[$index]['consumption']);
                array_push($this->last_value, $readings[$index]['value'] - $readings[$index]['consumption']);
            } else {
                array_push($this->value, null);
                array_push($this->consum, null);
                array_push($this->last_value, null);
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
            preg_match('/[0-9]{2,4}[\.\-][0-9]{2}[\.\-][0-9]{2,4}/', $this->rows_edit[$i + 2][4], $date);
            $this->rows_edit[$i + 2][4] = join('.', array_reverse(preg_split('/\-/', substr(date('c', strtotime($date[0])), 0, 10))));
            $this->rows_edit[$i + 2][6] = $last_value[$i];
            $this->rows_edit[$i + 2][7] = $value[$i];
            $this->rows_edit[$i + 2][8] = $consum[$i];
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
            $xls->getActiveSheet()->setCellValue('G' . ($i + 3), $data['last_value'][$i]);
            $xls->getActiveSheet()->setCellValue('H' . ($i + 3), $data['value'][$i]);
            $xls->getActiveSheet()->setCellValue('I' . ($i + 3), $data['consum'][$i]);
        }
        
        $objWriter = PHPExcel_IOFactory::createWriter($xls, 'Excel2007');
        $objWriter->save('output/Отчет для ТЭК.xlsx');
    }
}