<?php
    ini_set("default_charset","UTF-8");

    //*******************************************************************
    //ячейка с границами
    $border_cell = array(
        //рамка
        'borders' => array(
            'top' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
                'color' => array(
                        'rgb' => '808080',
                    )
                ),
            'bottom' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
                'color' => array(
                        'rgb' => '808080',
                    )
                ),
            'left' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
                'color' => array(
                        'rgb' => '808080',
                    )
                ),
            'right' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
                'color' => array(
                        'rgb' => '808080',
                    )
                ),
            ),
        //выравнивание
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            'wrap' => true,
            )
    );

    //*******************************************************************
    //ячейка с границами (жирный текст)
    $border_cell_bold = array(
        //рамка
        'borders' => array(
            'top' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
                'color' => array(
                        'rgb' => '808080',
                    )
                ),
            'bottom' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
                'color' => array(
                        'rgb' => '808080',
                    )
                ),
            'left' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
                'color' => array(
                        'rgb' => '808080',
                    )
                ),
            'right' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
                'color' => array(
                        'rgb' => '808080',
                    )
                ),
            ),
        //выравнивание
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            'wrap' => true,
            ),
        //шрифт
        'font' => array(
            'bold' => true,
        )
    );

    //*******************************************************************
    //ячейка без границ
    $unborder_cell = array(
        //выравнивание
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            )
    );
	
	 //*******************************************************************
    //ячейка без границ с жирным текстом
    $unboarded_cell_bold = array(
		//выравнивание
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            'wrap' => true,
            ),
        //шрифт
        'font' => array(
            'bold' => true,
        )
	);
?>