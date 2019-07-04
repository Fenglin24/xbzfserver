<?php
require_once('PHPExcel.php');

class Excel {
	private $_invalidFlag = false;
	private $_excel = null;
	private $_activeExcel = null;
	private $_title = null;
	private $_name = null;
	private $_prop = null;
	private $_fontFamily = '宋体';
	
	private $sheetIndex = 0; // 当前工作表索引
	private $maxIndex = 0; // 最大工作表索引
	
	public function __construct($name = '', $title = '', $filename = '') {
		date_default_timezone_set('PRC');
		$this->_title = $title;
		$this->_name = $name;
		
		if ($filename) {
			$phpReader = new PHPExcel_Reader_Excel2007();
			if (!$phpReader->canRead($filename)) {
				$phpReader = new PHPExcel_Reader_Excel5();
				if (!$phpReader->canRead($filename)) {
					$this->_invalidFlag = true;
				}
			}
			if (!$this->isInvalid()) {
				$this->_excel = $phpReader->load($filename);
			}
		} else {
			$this->_excel = new PHPExcel();
		}
		
		$this->_prop = $this->_excel->getProperties();
		if ($this->_name) {
			$this->_prop->setCreator($this->_name);
		}

		// 一般只有在使用多个sheet的时候才需要显示调用。
		$this->setActiveSheetIndex(0);
		if ($this->_title) {
			$this->_activeExcel->setTitle($this->_title);
		}
		
		$this->_excel->getDefaultStyle()->getFont()->setName($this->_fontFamily)->setSize(12);
	}
	
	public function isInvalid() {
		return $this->_invalidFlag;
	}
	
	public function getXColName($xCol) {
		$characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$len = strlen($characters);
		$times = intval($xCol / $len);
		$ch = substr($characters, $xCol % $len, 1);
		return str_repeat($ch, $times) . $ch;
	}
	
	public function setCellValue($xCol, $yCol, $value) {
		$posName = $this->getXColName($xCol) . ($yCol + 1);
		$styleArray = array(  
			'borders' => array(  
				'allborders' => array(  
					//'style' => PHPExcel_Style_Border::BORDER_THICK,//边框是粗的  
					'style' => PHPExcel_Style_Border::BORDER_THIN,//细边框  
					//'color' => array('argb' => 'FFFF0000'),  
				), 
			),
		);
		$this->_activeExcel->getStyle("{$posName}:{$posName}")->applyFromArray($styleArray);
		$this->_activeExcel->setCellValue($posName, $value);
	}
	
	public function addData($row, $yCol = 0) {
		$xCol = 0;
		foreach ($row as $key => $value) {
			$this->setCellValue($xCol++, $yCol, $value);
		}
		return $yCol + 1;
	}
	
	public function addTitleData($row) {
		return $this->addData($row, 0);
	}
	
	public function addListsData($rows, $yCol = 0) {
		foreach ($rows as $row) {
			$yCol = $this->addData($row, $yCol);
		}
		return $yCol;
	}
	
	public function setActiveSheetIndex($index = 0) {
		if ($index < 0) $index = 0;
		$this->sheetIndex = $index > $this->maxIndex ? $this->maxIndex : $index;
		$this->_excel->setActiveSheetIndex($this->sheetIndex);
		$this->_activeExcel = $this->_excel->getActiveSheet($this->sheetIndex);
	}
	
	public function createSheet($title = '') {
		$this->maxIndex++;
		if (!$title) $title = '工作表' . ($this->maxIndex+1);
		$this->_excel->getSheet($this->maxIndex)->setTitle($title);
	}
	
	public function setWidth($xCol, $width) {
		$xColName = $this->getXColName($xCol);
		$this->_excel->getSheet($this->maxIndex)->getColumnDimension($xColName)->setWidth($width);
	}
	public function export($title = '') {
		$objWriter = new PHPExcel_Writer_Excel5($this->_excel);
		if (!$title) $title = $this->_title;
		$outputFileName = $title . ".xls";
		header("Content-Type: application/force-download");
		header("Content-Type: application/octet-stream");
		header("Content-Type: application/download");
		header('Content-Disposition:attachment;filename="' . $outputFileName . '"');  //到文件
		////header(‘Content-Disposition:inline;filename="‘.$outputFileName.‘"‘);  //到浏览器
		header("Content-Transfer-Encoding: binary");
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Pragma: no-cache");
		$objWriter->save('php://output');
	}
	
	/**
	 * [getExcelSheetData description]
	 * @param  integer $index    [取第几个工作表，默认0，为第一个，1为第二个]
	 * @param  integer $fromLine [从第几行开始，1为第一行，2为第二行]
	 * @param  integer $getLines [一共要获取多少行，0为获取所有行]
	 * @return [type]            [返回数组]
	 */
	public function getExcelSheetData($index = 0, $fromLine = 1, $getLines = 0) {
		$excel = $this->_excel;
		$sheet = $excel->getSheet(0);
		$highestRow = $sheet->getHighestRow(); // 取得总行数
		$colsNum = $sheet->getHighestColumn(); // 取得总列数
		$highestColumm= PHPExcel_Cell::columnIndexFromString($colsNum); //字母列转换为数字列 如:AA变为27
		$sheetData = array();
		$limitLines = $getLines > 0 ? true : false;
		/** 循环读取每个单元格的数据 */
		for ($row = $fromLine; $row <= $highestRow; $row++){//行数是以第1行开始
			if ($limitLines) { // 需要限制行数
				if ($getLines <= 0) {
					break;
				}
				$getLines--;
			}
		    $lineData = array();
		    for ($column = 0; $column < $highestColumm; $column++) {//列数是以第0列开始
		        // $columnName = PHPExcel_Cell::stringFromColumnIndex($column);
		        $cell = $sheet->getCellByColumnAndRow($column, $row);
		        $value = $cell->getValue();
		        // 判断数据是否为日期类型
				$cellstyleformat = $cell->getStyle( $cell->getCoordinate() )->getNumberFormat();  
				$formatcode = $cellstyleformat->getFormatCode();
				if (preg_match('/^(\[\$[A-Z]*-[0-9A-F]*\])*[hmsdy]/i', $formatcode)) {  
					$value = gmdate("Y-m-d", PHPExcel_Shared_Date::ExcelToPHP($value));  
				}
		        $lineData[] = $value;
		    }
		    $sheetData[] = $lineData;
		}
		return $sheetData;
	}
	
	public function __destruct() {
		
	}
}

// $excel = new Excel('', '', 't.xls');
// $sheetData = $excel->getExcelSheetData(0, 2, 1);
// var_dump($sheetData);exit;