<?php
namespace ExHelper;

class Excel {

	/**
	 * 将数据导出并生成excel文件。
	 * @param  [string] $fileName   文件名称
	 * @param  [string] $sheetTitle sheet标题名称
	 * @param  [array] $columnData 栏目名称集合
	 * @param  [array] $data       导出的数组集合，且每一个子数组键值与栏目相对应
	 * @return [type]             [description]
	 */
	public static function downloadFromData($fileName, $titles, $datas, $sheetTitle = 'sheet1') {
		$excel = new \PHPExcel();
		// $excel->getProperties()->setCreator("zhang san")  
		// 				            ->setLastModifiedBy("李四")  
		// 				            ->setTitle("媒体数据")  
		// 				            ->setSubject("媒体数据")  
		// 				            ->setDescription("曝光量和访客数统计数据")  
		// 				            ->setKeywords("曝光量 访客数")  
		// 				            ->setCategory("test");
		//设置当前sheet
		$excel->setActiveSheetIndex(0);
		//获取当前sheet
		$phpSheet = $excel->getActiveSheet();
		//设置sheet的标题
		$phpSheet->setTitle($sheetTitle);
		
		foreach ($titles as $key => $value) {
			$key += 1;
			$column = self::getXColName($key);
			$phpSheet -> setCellValue( $column . '1', $value);
			//设置加粗
			$phpSheet->getStyle($column . '1')->getFont()->setBold(true);
		}
		unset($column);
		$i = 2;
		foreach ($datas as $row) {
			$j = 1;
			foreach ($row as $k => $v) {
				$column = self::getXColName($j);
				// echo $column[$j]. $i . '=====' . $v .'<br/>';
				$phpSheet -> setCellValue( $column . $i, $v);
				$j ++;
			}
			$i++;
		}
		unset($column);
		$savename = $fileName;
		$ua = $_SERVER["HTTP_USER_AGENT"];
		$datetime = date('Y-m-d', time());        
		if (preg_match("/MSIE/", $ua)) {
		    $savename = urlencode($savename); //处理IE导出名称乱码
		} 

		// excel头参数  
		header('Content-Type: application/vnd.ms-excel');  
		header('Content-Disposition: attachment;filename="'.$savename.'.xls"');  //日期为文件名后缀  
		header('Cache-Control: max-age=0'); 
		$objWriter = \PHPExcel_IOFactory::createWriter($excel, 'Excel5');  //excel5为xls格式，excel2007为xlsx格式  
		$objWriter->save('php://output');
	}

	public static function getXColName($xCol) {
		$xCol--;
		$characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$len = 26; // 26个字母（Excel里列名是26进制的字符串）
		$xColName = '';
		$times = intval($xCol / $len);
		$exts = $xCol % $len;
		if ($times > 0) {
			$newXCol = ($xCol - $exts) / 26;
			$xColName = $this->getXColName($newXCol);
		}
		$xColName .= substr($characters, $xCol % $len, 1);
		return $xColName;
	}

	/**
	 * 获取excel文件的数据
	 * @return [array] 返回获取到的数组
	 */
	public static function readExcelAsArray() {

		//判断截取文件
        $extension = strtolower( pathinfo($_FILES['excel']['name'], PATHINFO_EXTENSION) );
		 //区分上传文件格式
        if($extension == 'xlsx') {
            $objReader =\PHPExcel_IOFactory::createReader('Excel2007');
            $objPHPExcel = $objReader->load($_FILES['excel']['tmp_name'], $encode = 'utf-8');
        }else if($extension == 'xls'){
            $objReader =\PHPExcel_IOFactory::createReader('Excel5');
            $objPHPExcel = $objReader->load($_FILES['excel']['tmp_name'], $encode = 'utf-8');
        }

        $excel_array = $objPHPExcel->getsheet(0)->toArray();   //转换为数组格式
        array_shift($excel_array);  //删除第一个数组(标题);
		return $excel_array;
	}
}

?>