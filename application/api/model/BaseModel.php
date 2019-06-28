<?php

/**
 * @Author: Qian weidong
 * @Date:   2018-10-09 09:24:34
 * @Last Modified by:   Qian weidong
 * @Last Modified time: 2018-12-11 16:17:18
 */
namespace app\api\model;

class BaseModel extends \think\Model {
	// 默认写入时间戳
	protected $autoWriteTimestamp = 'datetime';
	// 重定义时间戳字段名
	protected $createTime = 'cdate';
	protected $updateTime = 'mdate';

	// 记录日志的文件名前缀，writeLog用到，子类可以覆盖
	protected static $log_file_prefix_name = 'run';

	// 数据表的主键字段名
	protected $pk = 'id';



	public function must_delete_old_file($old_file, $new_file) {
		return ($old_file && $new_file !== null && $old_file != $new_file);
	}

	public function writeLog($str, $error = false) {
		$data = date('Y-m-d H:i:s ') . ($error ? '[ERROR] ' : '') . $str . PHP_EOL;
		echo $data;
		$filename = $error ? self::$log_file_prefix_name . '.error.log' : self::$log_file_prefix_name . '.run.log';
		$filename = BASE_PATH . 'log/' . $filename;
		makeDIR(dirname($filename));
		file_put_contents($filename, $data, FILE_APPEND);
	}

	public static function change_log_file_prefix_name($prefix_name) {
		if (!$prefix_name) {
			return;
		}
		self::$log_file_prefix_name = $prefix_name;
	}



	

}