<?php
require_once APP_PATH . '/../extend/getallheaders.php'; // 支持getallheaders函数
require_once APP_PATH . '/common/phpexcel/Excel.class.php';
function dd($data) {
	echo json_encode($data, JSON_UNESCAPED_UNICODE);
	exit;
}
function check_auth($role_id, $authMap, $route) {
	return !$role_id || @$authMap[$route];
}
if (!function_exists('redirect')) {
	function redirect($url) {
		Header('Location: ' . $url);
		exit;
	}
}
/**
 * 取JS的时间戳，追加到JS后面
 * @param  string $file_path JS的HTTP绝对路径，请传递以'/'开头的路径，比如/js/admin/user.index.js
 * @return string 将该文件的时间戳作为文件后缀的version的值，返回。
 */
function stamp($file_path) {
	$file_path = ltrim(trim($file_path), '/');
	$file_abs_path = $_SERVER['DOCUMENT_ROOT'] . '/' . $file_path;
	return '/' . $file_path . '?version=' . strval(@filemtime($file_abs_path));
}

function show_money($floatNum) {
	return sprintf('%.2f', $floatNum);
}

function getEndOfArray($array) {
	return end($array);
}

/**
 * 生成订单号：
 * 如果不传递ID，ID取毫秒数的小数点后的6位，注意不能用microtime(true);
 * 如果传递ID号为订单ID，那么每秒钟可以生成1000万个订单不可能重复。
 * 有1-9999999的随机数，所以即使时间重复了，也有1000万分之1的概率不重复。
 */
function get_unique_no($prev_name = 'OD-', $id = 0) {
	date_default_timezone_set('Asia/Shanghai');
	$date_str = date('YmdHis'); // 4+4+6 = 14位
	if (!$id) {
		$id = str_pad(substr(microtime(), 2, 6), 6, '0', STR_PAD_LEFT); // 6位
	} else {
		$id = str_pad(intval($id) % 1000000, 6, '0', STR_PAD_LEFT); // 最多6位，1秒内可以生成1000000个订单
	}
	$rand_num = str_pad(rand(1, 9999999), 7, '0', STR_PAD_LEFT); // 7位随机数

	// 如果$prev_name是3位的，那么长度为3+14+6+7+2(-) = 32位
	return $prev_name . $date_str . '-' . $id . '-' . $rand_num;
}

function get_php_file($fileName) {
	return trim(substr(file_get_contents($fileName), 15));
}

function set_php_file($fileName, $content) {
	$fp = fopen($fileName, "w");
	fwrite($fp, "<?php exit();?>" . $content);
	fclose($fp);
}
function http_download_file($path, $fileName = '', $path_is_abs = false) {
	$base_fileName = $fileName ? basename($fileName) : basename($path);
	$abs_path = $path_is_abs ? $path : $_SERVER['DOCUMENT_ROOT'] . '/' . $path;
	header("Cache-Control: public");
	header("Content-Description: File Transfer");
	header('Content-disposition: attachment; fileName=' . $base_fileName); //文件名
	header("Content-Type: application/zip"); //zip格式的
	header("Content-Transfer-Encoding: binary"); //告诉浏览器，这是二进制文件
	header('Content-Length: ' . @filesize($abs_path)); //告诉浏览器，文件大小
	@readfile($abs_path);
}

function authAndSendMail($config, $rcpt_to, $subject, $body, $attachements = array()) {
	require_once APP_PATH . '/common/mail/mail.class.php';
	return sendMimeMail($config, $rcpt_to, $subject, $body, $attachements);
}
function microtime_float() {
	return str_replace('.', '', microtime(true));
}
function arrayMultSort($arr, $field, $direction = 'SORT_ASC') {
	$sort = array(
		'direction' => $direction == 'SORT_ASC' ? 'SORT_ASC' : 'SORT_DESC',
		'field' => $field,
	);
	$arrSort = array();
	foreach ($arr AS $uniqid => $row) {
		foreach ($row AS $key => $value) {
			$arrSort[$key][$uniqid] = $value;
		}
	}
	if ($sort['direction']) {
		array_multisort($arrSort[$sort['field']], constant($sort['direction']), $arr);
	}
	return $arr;
}
function getDateArray($from_date, $to_date) {
	$from_date = date('Y-m-d', strtotime($from_date));
	$to_date = date('Y-m-d', strtotime($to_date));
	$fromSec = strtotime($from_date);
	$toSec = strtotime($to_date);
	$dateArray = array();
	for ($seconds = $fromSec; $seconds <= $toSec; $seconds += 24 * 3600) {
		$dateArray[] = date('Y-m-d', $seconds);
	}
	return $dateArray;
}
function getMonthBeginDate($timeStamp = 0, $Ymd = 'Y-m-d') {
	if (!$timeStamp) {
		$timeStamp = time();
	}
	$beginDate = date('Y-m-01', strtotime(date("Y-m-d", $timeStamp)));
	return date($Ymd, strtotime($beginDate));
}
function getMonthEndDate($timeStamp = 0, $Ymd = 'Y-m-d') {
	if (!$timeStamp) {
		$timeStamp = time();
	}
	$beginDate = date('Y-m-01', strtotime(date("Y-m-d", $timeStamp)));
	$endDate = date('Y-m-d', strtotime("$beginDate +1 month -1 day"));
	return date($Ymd, strtotime($endDate));
}

function uploadImageByTmpName($file_tmp_name, $dir = '/tmp', $serverRoot = '', $width = 0, $height = 0, $original_extension = '', $size = -1) {
	$fileNameDate = date('YmdHis') . microtime(true) * 10000;
	if ($size !== -1 && $size <= 1) {
		$extName = $original_extension;
	} else {
		$extName = getExtName($file_tmp_name, '.jpg', true, $original_extension);
	}
	$fileName = $fileNameDate . $extName;

	$fileHttpPath = $dir . '/' . $fileName;

	if ($serverRoot) {
		// 考虑后台传前端图片的情况
		$fileAbsPath = $serverRoot . $fileHttpPath;
	} else {
		$fileAbsPath = $_SERVER['DOCUMENT_ROOT'] . $fileHttpPath;
	}

	makeDIR(dirname($fileAbsPath));
	if (!@move_uploaded_file($file_tmp_name, $fileAbsPath)) {
		// 上传错误提示错误信息
		$res = array('code' => -1, 'msg' => '文件移动失败', 'data' => array());
	} else {
		if ($width > 0 && $height > 0) {
			$image = \think\Image::open($fileAbsPath);
			$image->thumb($width, $height, \Think\Image::THUMB_SCALING)->save($fileAbsPath, ltrim($extName, '.'));
		}
		$res = array('code' => 0, 'msg' => 'ok', 'data' => $fileHttpPath);
	}
	return $res;
}

function makeThumbImg($file_path, $width, $height, $is_abs_path = true) {
	$file_abs_path = $is_abs_path ? $file_path : rtrim($_SERVER['DOCUMENT_ROOT'], '/') . '/' . ltrim($file_path, '/');
	$image = \think\Image::open($file_abs_path);
	$image->thumb($width, $height, \Think\Image::THUMB_SCALING)->save($file_abs_path);
}

function curlGet($url) {
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
	$output = curl_exec($ch);
	curl_close($ch);
	return $output;
}

function curlPost($url, $data = array()) {
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	if (!empty($data)) {
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	}
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
	$output = curl_exec($ch);
	curl_close($ch);
	return $output;
}

/**
 * 创建目录（递归）
 */
function makeDIR($dir) {
	if (is_dir($dir) || @mkdir($dir)) {
		return TRUE;
	}

	if (!makeDIR(dirname($dir))) {
		return FALSE;
	}

	return @mkdir($dir);
}

/**
 * 递归创建目录并将数据写入文件
 */
function writeFile($fileName, $data) {
	makeDIR(dirname($fileName));
	if (FALSE === @file_put_contents($fileName, $data)) {
		return FALSE;
	} else {
		return TRUE;
	}

}

function getOrigExtName($origFilename) {
	$arr = explode('.', $origFilename);
	if (!$arr) {
		return '';
	}
	return '.' . end($arr);
}

function getExtName($fileName, $default_name = '.jpg', $abs_flag = true, $original_extension = '') {
	if (!$abs_flag) {
		$fileName = $_SERVER['DOCUMENT_ROOT'] . '/' . $fileName;
	}
	$mimeMap = [
		'application/pdf' => '.pdf',
		'text/plain' => '.txt',
		'image/gif' => '.gif',
		'image/jpg' => '.jpg',
		'image/jpeg' => '.jpg',
		'image/png' => '.png',
		'image/bmp' => '.bmp',
		'application/msword' => '.doc',
		'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => '.docx',
		'application/octet-stream"' => '.docx',
		'application/vnd.ms-powerpoint' => '.ppt',
		'application/vnd.ms-excel' => '.xls',
		'application/zip' => '.zip',
		'application/x-rar-compressed' => '.rar',
	];

	$mime_type = getFileType($fileName);
	// 如果是office 2007文档，它没有默认的mime，而是跟zip的mime type一样，只能返回原始的扩展名
	$arrayZips = array("application/zip", "application/x-zip", "application/x-zip-compressed");
	$arrayExtensions = array(".pptx", ".docx", ".dotx", ".xlsx");
	if (in_array($mime_type, $arrayZips) && in_array($original_extension, $arrayExtensions)) {
		return $original_extension;
	}
	if (!isset($mimeMap[$mime_type])) {
		return $default_name;
	}

	return $mimeMap[$mime_type];
}

function get_office_mime_type($mime_type, $original_extension) {
	// office2007文档没有自己特有的mime type，所以只能从原始文件名判断！
	$arrayZips = array("application/zip", "application/x-zip", "application/x-zip-compressed");
	$arrayExtensions = array(".pptx", ".docx", ".dotx", ".xlsx");
	if (!in_array($original_extension, $arrayExtensions)) {
		// 不是office文档，直接返回原来的mime
		return $mime_type;
	}
	if (in_array($mime_type, $arrayZips)) {
		return $original_extension;
	}
}

function deleteFile($fileName, $server_root = '') {
	if (!$fileName) {
		// 容错，空的时候返回正确
		return true;
	}
	$server_root = $server_root ? $server_root : $_SERVER['DOCUMENT_ROOT'];
	$abs_path = $server_root . '/' . $fileName;
	if (file_exists($abs_path)) {
		return unlink($abs_path);
	}
	return true;
}

/**
 * [moveFile 移动上传的文件]
 * @param  [type] $from_path [文件源路径，由于是上传的，所以是绝对路径]
 * @param  [type] $to_path   [文件目标路径]
 * @return [type]            [成功返回true，失败返回false]
 */
function moveUploadFile($from_path, $to_path) {
	if ($from_path == $to_path) {
		return true;
	}

	if (substr($from_path, 0, 1) == '/') {
		$from_path = substr($from_path, 1);
	}
	if (substr($to_path, 0, 1) == '/') {
		$to_path = substr($to_path, 1);
	}
	$abs_from_path = $from_path;
	$abs_to_path = $_SERVER['DOCUMENT_ROOT'] . '/' . $to_path;
	if (!makeDIR(dirname($abs_to_path))) {
		return false;
	}
	if (!move_uploaded_file($abs_from_path, $abs_to_path)) {
		if (!copy($abs_from_path, $abs_to_path)) {
			return false;
		} else {
			unlink($abs_from_path);
		}
	}
	return true;
}

/**
 * [moveFile 移动文件]
 * @param  [type] $from_path [文件源路径]
 * @param  [type] $to_path   [文件目标路径]
 * @return [type]            [成功返回true，失败返回false]
 */
function moveFile($from_path, $to_path) {
	if ($from_path == $to_path) {
		return true;
	}

	if (substr($from_path, 0, 1) == '/') {
		$from_path = substr($from_path, 1);
	}
	if (substr($to_path, 0, 1) == '/') {
		$to_path = substr($to_path, 1);
	}
	$abs_from_path = $_SERVER['DOCUMENT_ROOT'] . '/' . $from_path;
	$abs_to_path = $_SERVER['DOCUMENT_ROOT'] . '/' . $to_path;
	if (!makeDIR(dirname($abs_to_path))) {
		return false;
	}
	if (!rename($abs_from_path, $abs_to_path)) {
		if (!copy($abs_from_path, $abs_to_path)) {
			return false;
		} else {
			unlink($abs_from_path);
		}
	}
	return true;
}

function fileExists($fileName, $abs_flag = false) {
	if (!$abs_flag) {
		$abs_path = $_SERVER['DOCUMENT_ROOT'] . '/' . $fileName;
	}
	return file_exists($abs_path);
}

/**
 * 删除目录下的所有文件
 */
function rmTree($dir) {
	if (!file_exists($dir)) {
		return true;
	}
	if (is_dir($dir)) {
		$files = @scandir($dir);
		foreach ($files as $k => $file) {
			if (is_file($dir . '/' . $file)) {
				@unlink($dir . '/' . $file);
			} else if ($file !== '.' && $file !== '..' && is_dir($dir . '/' . $file)) {
				rmTree($dir . '/' . $file);
				@rmdir($dir . '/' . $file);
			}
		}
		@rmdir($dir);
	} else {
		@unlink($dir);
	}
}

// 获取html代码里的任意图片路径
function GetImgFromHTML($html) {
	$reg = "/<img\s+[^>]*src\s*=\s*['\"]*([^\"^\s^'^>]+)[\"']*[^>]*>/i";
	preg_match_all($reg, $html, $result);
	return $result[1];
}

function headerNoCache() {
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	header("Cache-Control: no-cache, must-revalidate");
	header("Pragma: no-cache");
}

function checkEmail($str) {
	if (preg_match("/\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/", $str) == 1) {
		return true;
	}
	return false;
}

function getIP() {
	if (getenv('HTTP_CLIENT_IP')) {
		$ip = getenv('HTTP_CLIENT_IP');
	} elseif ($tmp = getenv('HTTP_X_FORWARDED_FOR')) {
		$arr = explode(',', $tmp);
		$ip = array_shift($arr);
	} elseif (getenv('REMOTE_ADDR')) {
		$ip = getenv('REMOTE_ADDR');
	} else {
		$ip = @$_SERVER['REMOTE_ADDR'];
	}

	$pattern = '/^192\.168/i';
	$ok = preg_match($pattern, $ip);
	if ($ok || trim($ip) == "") {
		$ip = @$_SERVER['HTTP_X_REAL_IP'];
	}

	return $ip;
}

/**
 * 判断字符串是否为utf8编码
 */
function isUTF8Encode($string) {
	$str1 = @iconv("UTF-8", "GBK", $string);
	$str2 = @iconv("GBK", "UTF-8", $str1);
	return $string == $str2 ? true : false;
}

/**
 * 将字符串转成utf8编码，如果不是的话
 * @author zhongying
 */
function toUTF8Encode($string) {
	if ($string == "") {
		return "";
	}

	if (!isUTF8Encode($string)) {
		return @iconv("GBK", "UTF-8", $string);
	} else {
		return $string;
	}
}

/**
 * 将字符串转成GBK编码，如果不是的话
 * @author zhongying
 */
function toGBKEncode($string) {
	if ($string == "") {
		return "";
	}

	if (isUTF8Encode($string)) {
		return @iconv("UTF-8", "GBK", $string);
	} else {
		return $string;
	}
}

/**
 * 判断字符串是否全部是中文字符，兼容gb2312,utf-8；该方法不能排除ａ等非中文汉字字符
 */
function isChineseString($str) {
	$str1 = @iconv("UTF-8", "GBK", $str);
	$str2 = @iconv("GBK", "UTF-8", $str1);

	if ($str2 != $str) //GBK编码
	{
		$str = @iconv("GBK", "UTF-8", $str);
	}

	if (preg_match("/^[\x7f-\xff]+$/", $str)) //兼容gb2312,utf-8
	{
		return true;
	} else {
		return false;
	}

}

/**
 * 判断字符串是否全部是UTF-8编码的中文汉字字符，排除ａ等非中文汉字字符
 * 参见：完善匹配中文的Php正则表达式
 * http://student.csdn.net/space.php?uid=41678&do=blog&id=818
 */
function isUtf8ChineseString($str) {
	$pattern = "/^[\x{4e00}-\x{9fa5}]+$/u";
	if (preg_match($pattern, $str)) {
		return true;
	} else {
		return false;
	}

}

/**
 *获取文件MIME类型
 */
function getFileType($fileName) {
	return finfo_file(finfo_open(FILEINFO_MIME_TYPE), $fileName);
}
function isImage($fileName) {
	$mime = getFileType($fileName);
	if ($mime == 'image/jpg' || $mime == 'image/gif' || $mime == 'image/png' || $mime == 'image/bmp' || $mime == 'image/jpeg') {
		return true;
	} else {
		return false;
	}
}

function guid() {
	if (function_exists('com_create_guid')) {
		return com_create_guid();
	} else {
		mt_srand((double) microtime() * 10000); //optional for php 4.2.0 and up.
		$charid = strtoupper(md5(uniqid(rand(), true)));
		$hyphen = chr(45); // "-"
		$uuid = chr(123) // "{"
		 . substr($charid, 0, 8) . $hyphen
		. substr($charid, 8, 4) . $hyphen
		. substr($charid, 12, 4) . $hyphen
		. substr($charid, 16, 4) . $hyphen
		. substr($charid, 20, 12)
		. chr(125); // "}"
		return $uuid;
	}
}

function getRandString($length) {
	$str = '';
	$string_dict = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
	$max = strlen($string_dict) - 1;
	for ($i = 0; $i < $length; $i++) {
		$str .= $string_dict[rand(0, $max)]; //rand($min,$max)生成介于min和max两个数之间的一个随机整数
	}
	return $str;
}

/**
 * 分布式自增ID生成 twitter/snowflake PHP实现
 * http://www.oschina.net/p/snowflake
 * ID 生成策略
 * 毫秒级时间41位+机器ID 10位+毫秒内序列12位。
 * 0           41     51     64
+-----------+------+------+
|time       |pc    |inc   |
+-----------+------+------+
 *  前41bits是以微秒为单位的timestamp。
 *  接着10bits是事先配置好的机器ID。
 *  最后12bits是累加计数器。
 *  macheine id(10bits)标明最多只能有1024台机器同时产生ID，sequence number(12bits)也标明1台机器1ms中最多产生4096个ID，
 *
 * auth: zhouyuan
 * $workerId: 配置服务器序列号，0~15支持最大共16台设备
 */
class idwork {
	const debug = 1;
	static $workerId;
	static $twepoch = 1361775855078;
	static $sequence = 0;
	const workerIdBits = 4;
	static $maxWorkerId = 15;
	const sequenceBits = 10;
	static $workerIdShift = 10;
	static $timestampLeftShift = 14;
	static $sequenceMask = 1023;
	private static $lastTimestamp = -1;

	function __construct($workerId) {
		$workId = intval($workerId);
		if ($workId > $maxWorkerId || $workId < 0) {
			throw new Exception("worker Id can't be greater than 15 or less than 0");
		}
		$workerId = $workId;

	}

	function timeGen() {
		//获得当前时间戳
		$time = explode(' ', microtime());
		$time2 = substr($time[0], 2, 3);
		return $time[1] . $time2;
	}
	function tilNextMillis($lastTimestamp) {
		$timestamp = $this->timeGen();
		while ($timestamp <= $lastTimestamp) {
			$timestamp = $this->timeGen();
		}

		return $timestamp;
	}

	function nextId() {
		$timestamp = $this->timeGen();
		if ($lastTimestamp == $timestamp) {
			$sequence = ($sequence + 1) & $sequenceMask;
			if ($sequence == 0) {
				$timestamp = $this->tilNextMillis($lastTimestamp);
			}
		} else {
			$sequence = 0;
		}
		if ($timestamp < $lastTimestamp) {
			throw new Excwption("Clock moved backwards.  Refusing to generate id for " . ($lastTimestamp - $timestamp) . " milliseconds");
		}
		$lastTimestamp = $timestamp;
		$nextId = ((sprintf('%.0f', $timestamp) - sprintf('%.0f', $twepoch)) << $timestampLeftShift) | ($workerId << $workerIdShift) | $sequence;
		return $nextId;
	}

}
function snowflakeId() {
	$idworkObj = new idwork(TWITTER_SNOWFLAKE_WORKER_ID);
	return $idworkObj->nextId();
}

/**
 * 导出文件
 * @param array   $data 数据
 * @param String  $fileName 文件名
 * @param boolean $download 是否为下载文件
 * @param boolean $headers 是否包含头
 */
function export_csv($data, $fileName, $download = false) {
	ob_end_clean();
	if ($download) {
		header('Content-Type: text/csv; charset=utf-8');
		header('Content-Disposition: attachment;fileName=' . $fileName);
		$fp = fopen('php://output', 'w');
	} else {
		$fp = fopen($fileName, 'w');
	}
	// Windows下使用BOM来标记文本文件的编码方式
	// fwrite($fp,chr(0xEF).chr(0xBB).chr(0xBF));
	foreach ($data as $value) {
		fputcsv($fp, $value);
	}

	fclose($fp);
}

function GetMyExploreVersion() {
	$ExploreVersion = "Other";
	$Agent = $_SERVER["HTTP_USER_AGENT"];
	if (stristr($Agent, 'Edge')) {
		$res = preg_match('/Edge\/([0-9]+\.[0-9]+)/i', $Agent, $result);
		$ExploreVersion = "Edge";
	} else if (stristr($Agent, 'MSIE ')) {
		$res = preg_match('/MSIE ([0-9]+\.[0-9]+)/i', $Agent, $result);
		if ($res) {
			$ExploreVersion = "IE " . $result[1];
		} else {
			$ExploreVersion = "IE";
		}
	} else if (stristr($Agent, 'Windows') && stristr($Agent, 'rv:11')) {
		$ExploreVersion = "IE 11";
	} else if (stristr($Agent, 'rv:')) {
		$ExploreVersion = "Firefox";
	} else if (stristr($Agent, 'UCWEB')) {
		$ExploreVersion = "UCWEB";
	} else if (stristr($Agent, 'Maxthon')) {
		$ExploreVersion = "遨游";
	} else if (stristr($Agent, 'UBrowser')) {
		$ExploreVersion = "UC浏览器";
	} else if (stristr($Agent, 'Chrome')) {
		$ExploreVersion = "Chrome";
	} else if (stristr($Agent, 'Safari')) {
		$ExploreVersion = "Safari";
	} else if (stristr($Agent, 'Opera')) {
		$ExploreVersion = "Opera";
	} else if (stristr($Agent, 'bot') || stristr($Agent, 'http://') || $Agent == "Mozilla/4.0") {
		$ExploreVersion = "收录";
	}
	return $ExploreVersion;
}

function GetMyOSVersion() {
	$OSInfo = "Other OS";
	$Agent = $_SERVER["HTTP_USER_AGENT"];
	if (stristr($Agent, 'Windows NT 5.0')) {
		$OSInfo = "Windows 2000";
	} else if (stristr($Agent, 'Windows NT 5.1')) {
		$OSInfo = "Windows XP";
	} else if (stristr($Agent, 'Windows NT 5.2')) {
		$OSInfo = "Windows 2003";
	} else if (stristr($Agent, 'Windows NT 6.0')) {
		$OSInfo = "Windows Vista";
	} else if (stristr($Agent, 'Windows NT 6.1')) {
		$OSInfo = "Windows 7";
	} else if (stristr($Agent, 'Windows NT 6.2')) {
		$OSInfo = "Windows 8";
	} else if (stristr($Agent, 'Windows NT 6.3') || stristr($Agent, 'Windows NT 10')) {
		$OSInfo = "Windows 10";
	} else if (stristr($Agent, 'Linux')) {
		if (stristr($Agent, 'Ubuntu')) {
			$OSInfo = "Ubuntu";
		} else if (stristr($Agent, 'Android')) {
			$OSInfo = "Android";
		} else {
			$OSInfo = "Linux";
		}
	} else if (stristr($Agent, 'Mac OS')) {
		if (stristr($Agent, 'iPhone')) {
			$OSInfo = "iPhone";
		} else if (stristr($Agent, 'iPad')) {
			$OSInfo = "iPad";
		} else if (stristr($Agent, 'Mac OS X ')) {
			preg_match('/Mac OS X (?P<version>[\d_]+)\)/', $Agent, $matches);
			$version = $matches ? ' ' . str_replace('_', '.', $matches['version']) : '';
			$OSInfo = "Mac OS";
		} else {
			$OSInfo = "Mac OS";
		}
	} else if (stristr($Agent, 'NOKIA')) {
		$OSInfo = "NOKIA";
	} else if (stristr($Agent, 'Baiduspider')) {
		$OSInfo = "百度收录";
	} else if (stristr($Agent, 'Sosospider')) {
		$OSInfo = "搜搜收录";
	} else if (stristr($Agent, 'bingbot')) {
		$OSInfo = "必应收录";
	} else if (stristr($Agent, '360Spider')) {
		$OSInfo = "360收录";
	} else if (stristr($Agent, 'JikeSpider')) {
		$OSInfo = "即刻收录";
	} else if (stristr($Agent, 'Googlebot')) {
		$OSInfo = "谷歌收录";
	} else if (stristr($Agent, 'Mediapartners-google')) {
		$OSInfo = "Google_Adsense收录";
	} else if (stristr($Agent, 'Sogou')) {
		$OSInfo = "搜狗收录";
	} else if (stristr($Agent, 'YoudaoBot')) {
		$OSInfo = "有道收录";
	} else if (stristr($Agent, 'MJ12bot')) {
		$OSInfo = "MJ12(国外)收录";
	} else if (stristr($Agent, 'Yahoo')) {
		$OSInfo = "雅虎收录";
	} else if (stristr($Agent, 'Ia_archiver') || stristr($Agent, 'iaarchiver')) {
		$OSInfo = "Alexa收录";
	} else if (stristr($Agent, 'Yodaobot')) {
		$OSInfo = "Yodao收录";
	} else if (stristr($Agent, 'iaskspider')) {
		$OSInfo = "Task收录";
	} else if (stristr($Agent, 'archive.org_bot')) {
		$OSInfo = "archive(国外)收录";
	} else if (stristr($Agent, 'Ezooms')) {
		$OSInfo = "Gmail收录";
	} else if (stristr($Agent, 'EasouSpider')) {
		$OSInfo = "宜搜收录";
	} else if (stristr($Agent, 'eYou')) {
		$OSInfo = "亿邮收录";
	} else if (stristr($Agent, 'Ezooms')) {
		$OSInfo = "Gmail收录";
	} else if (stristr($Agent, 'Python')) {
		$OSInfo = "python收录";
	} else if (stristr($Agent, 'Wget')) {
		$OSInfo = "wget收录";
	} else if (stristr($Agent, 'Ezooms') == "Mozilla/4.0") {
		$OSInfo = "其它收录";
	}
	return $OSInfo;
}

function getIPInfo($ip) {
	if ($ip == '127.0.0.1') {
		return array('code' => 0, 'msg' => 'ok', 'data' => array(
			'city' => '本机',
			'isp' => '本机',
		));
	}
	$dat_path = $_SERVER['DOCUMENT_ROOT'] . '/../extend/ip/qqwry.dat';
	//检查IP地址
	if (!preg_match("/^([0-9]{1,3}.){3}[0-9]{1,3}$/", $ip)) {
		return array('code' => -1, 'msg' => 'IP格式错误', "");
	}
	//打开IP数据文件
	if (!$fd = @fopen($dat_path, 'rb')) {
		return array('code' => -1, 'msg' => 'can not open' . $dat_path, "");
	}
	//分解IP进行运算，得出整形数
	$ip = explode('.', $ip);
	$ipNum = $ip[0] * 16777216 + $ip[1] * 65536 + $ip[2] * 256 + $ip[3];
	//获取IP数据索引开始和结束位置
	$DataBegin = fread($fd, 4);
	$DataEnd = fread($fd, 4);
	$ipbegin = implode('', unpack('L', $DataBegin));
	if ($ipbegin < 0) {
		$ipbegin += pow(2, 32);
	}

	$ipend = implode('', unpack('L', $DataEnd));
	if ($ipend < 0) {
		$ipend += pow(2, 32);
	}

	$ipAllNum = ($ipend - $ipbegin) / 7 + 1;
	$BeginNum = 0;
	$EndNum = $ipAllNum;
	//使用二分查找法从索引记录中搜索匹配的IP记录
	$ip1num = 0;
	$ip2num = 0;
	$ipAddr1 = "";
	$ipAddr2 = "";
	while ($ip1num > $ipNum || $ip2num < $ipNum) {
		$Middle = intval(($EndNum + $BeginNum) / 2);
		//偏移指针到索引位置读取4个字节
		fseek($fd, $ipbegin + 7 * $Middle);
		$ipData1 = fread($fd, 4);
		if (strlen($ipData1) < 4) {
			fclose($fd);
			return array('code' => -1, 'msg' => '系统错误', "");
		}
		//提取出来的数据转换成长整形，如果数据是负数则加上2的32次幂
		$ip1num = implode('', unpack('L', $ipData1));
		if ($ip1num < 0) {
			$ip1num += pow(2, 32);
		}

		//提取的长整型数大于我们IP地址则修改结束位置进行下一次循环
		if ($ip1num > $ipNum) {
			$EndNum = $Middle;
			continue;
		}
		//取完上一个索引后取下一个索引
		$DataSeek = fread($fd, 3);
		if (strlen($DataSeek) < 3) {
			fclose($fd);
			return array('code' => -1, 'msg' => '系统错误', "");
		}
		$DataSeek = implode('', unpack('L', $DataSeek . chr(0)));
		fseek($fd, $DataSeek);
		$ipData2 = fread($fd, 4);
		if (strlen($ipData2) < 4) {
			fclose($fd);
			return array('code' => -1, 'msg' => '系统错误', "");
		}
		$ip2num = implode('', unpack('L', $ipData2));
		if ($ip2num < 0) {
			$ip2num += pow(2, 32);
		}

		//没找到提示未知
		if ($ip2num < $ipNum) {
			if ($Middle == $BeginNum) {
				fclose($fd);
				return array('code' => -1, 'msg' => '未知错误', "");
			}
			$BeginNum = $Middle;
		}
	}
	//下面的代码读晕了，没读明白，有兴趣的慢慢读
	$ipFlag = fread($fd, 1);
	if ($ipFlag == chr(1)) {
		$ipSeek = fread($fd, 3);
		if (strlen($ipSeek) < 3) {
			fclose($fd);
			return array('code' => -1, 'msg' => '系统错误', "");
		}
		$ipSeek = implode('', unpack('L', $ipSeek . chr(0)));
		fseek($fd, $ipSeek);
		$ipFlag = fread($fd, 1);
	}
	if ($ipFlag == chr(2)) {
		$AddrSeek = fread($fd, 3);
		if (strlen($AddrSeek) < 3) {
			fclose($fd);
			return array('code' => -1, 'msg' => '系统错误', "");
		}
		$ipFlag = fread($fd, 1);
		if ($ipFlag == chr(2)) {
			$AddrSeek2 = fread($fd, 3);
			if (strlen($AddrSeek2) < 3) {
				fclose($fd);
				return array('code' => -1, 'msg' => '系统错误', "");
			}
			$AddrSeek2 = implode('', unpack('L', $AddrSeek2 . chr(0)));
			fseek($fd, $AddrSeek2);
		} else {
			fseek($fd, -1, SEEK_CUR);
		}
		$ipAddr2 = "";
		while (($char = fread($fd, 1)) != chr(0)) {
			$ipAddr2 .= $char;
		}

		$AddrSeek = implode('', unpack('L', $AddrSeek . chr(0)));
		fseek($fd, $AddrSeek);
		$ipAddr1 = "";
		while (($char = fread($fd, 1)) != chr(0)) {
			$ipAddr1 .= $char;
		}

	} else {
		fseek($fd, -1, SEEK_CUR);
		while (($char = fread($fd, 1)) != chr(0)) {
			$ipAddr1 .= $char;
		}

		$ipFlag = fread($fd, 1);
		if ($ipFlag == chr(2)) {
			$AddrSeek2 = fread($fd, 3);
			if (strlen($AddrSeek2) < 3) {
				fclose($fd);
				return array('code' => -1, 'msg' => '系统错误', "");
			}
			$AddrSeek2 = implode('', unpack('L', $AddrSeek2 . chr(0)));
			fseek($fd, $AddrSeek2);
		} else {
			fseek($fd, -1, SEEK_CUR);
		}
		while (($char = fread($fd, 1)) != chr(0)) {
			$ipAddr2 .= $char;
		}
	}
	fclose($fd);
	//最后做相应的替换操作后返回结果
	if (preg_match('/http/i', $ipAddr2)) {
		$ipAddr2 = '';
	}
	$ipaddr = "${ipAddr1}||${ipAddr2}";
	$ipaddr = preg_replace('/CZ88.Net/is', '', $ipaddr);
	$ipaddr = preg_replace('/^s*/is', '', $ipaddr);
	$ipaddr = preg_replace('/s*$/is', '', $ipaddr);
	if (preg_match('/http/i', $ipaddr) || $ipaddr == '') {
		$ipaddr = '未知';
	}

	$ipaddr = iconv("GBK", "UTF-8", $ipaddr);
	$retArray = explode("||", $ipaddr);

	$defaultWords = "未知";
	if (trim($retArray[0]) == "") {
		$retArray[0] = $defaultWords;
	}
	if (trim($retArray[1]) == "") {
		$retArray[1] = $defaultWords;
	}
	return array('code' => 0, 'msg' => 'ok', 'data' => array(
		'city' => $retArray[0],
		'isp' => $retArray[1],
	));
}

function quanPin($_String, $_Code = 'UTF8') {
	//GBK页面可改为gb2312，其他随意填写为UTF8
	$_DataKey = "a|ai|an|ang|ao|ba|bai|ban|bang|bao|bei|ben|beng|bi|bian|biao|bie|bin|bing|bo|bu|ca|cai|can|cang|cao|ce|ceng|cha" .
		"|chai|chan|chang|chao|che|chen|cheng|chi|chong|chou|chu|chuai|chuan|chuang|chui|chun|chuo|ci|cong|cou|cu|" .
		"cuan|cui|cun|cuo|da|dai|dan|dang|dao|de|deng|di|dian|diao|die|ding|diu|dong|dou|du|duan|dui|dun|duo|e|en|er" .
		"|fa|fan|fang|fei|fen|feng|fo|fou|fu|ga|gai|gan|gang|gao|ge|gei|gen|geng|gong|gou|gu|gua|guai|guan|guang|gui" .
		"|gun|guo|ha|hai|han|hang|hao|he|hei|hen|heng|hong|hou|hu|hua|huai|huan|huang|hui|hun|huo|ji|jia|jian|jiang" .
		"|jiao|jie|jin|jing|jiong|jiu|ju|juan|jue|jun|ka|kai|kan|kang|kao|ke|ken|keng|kong|kou|ku|kua|kuai|kuan|kuang" .
		"|kui|kun|kuo|la|lai|lan|lang|lao|le|lei|leng|li|lia|lian|liang|liao|lie|lin|ling|liu|long|lou|lu|lv|luan|lue" .
		"|lun|luo|ma|mai|man|mang|mao|me|mei|men|meng|mi|mian|miao|mie|min|ming|miu|mo|mou|mu|na|nai|nan|nang|nao|ne" .
		"|nei|nen|neng|ni|nian|niang|niao|nie|nin|ning|niu|nong|nu|nv|nuan|nue|nuo|o|ou|pa|pai|pan|pang|pao|pei|pen" .
		"|peng|pi|pian|piao|pie|pin|ping|po|pu|qi|qia|qian|qiang|qiao|qie|qin|qing|qiong|qiu|qu|quan|que|qun|ran|rang" .
		"|rao|re|ren|reng|ri|rong|rou|ru|ruan|rui|run|ruo|sa|sai|san|sang|sao|se|sen|seng|sha|shai|shan|shang|shao|" .
		"she|shen|sheng|shi|shou|shu|shua|shuai|shuan|shuang|shui|shun|shuo|si|song|sou|su|suan|sui|sun|suo|ta|tai|" .
		"tan|tang|tao|te|teng|ti|tian|tiao|tie|ting|tong|tou|tu|tuan|tui|tun|tuo|wa|wai|wan|wang|wei|wen|weng|wo|wu" .
		"|xi|xia|xian|xiang|xiao|xie|xin|xing|xiong|xiu|xu|xuan|xue|xun|ya|yan|yang|yao|ye|yi|yin|ying|yo|yong|you" .
		"|yu|yuan|yue|yun|za|zai|zan|zang|zao|ze|zei|zen|zeng|zha|zhai|zhan|zhang|zhao|zhe|zhen|zheng|zhi|zhong|" .
		"zhou|zhu|zhua|zhuai|zhuan|zhuang|zhui|zhun|zhuo|zi|zong|zou|zu|zuan|zui|zun|zuo";
	$_DataValue = "-20319|-20317|-20304|-20295|-20292|-20283|-20265|-20257|-20242|-20230|-20051|-20036|-20032|-20026|-20002|-19990" .
		"|-19986|-19982|-19976|-19805|-19784|-19775|-19774|-19763|-19756|-19751|-19746|-19741|-19739|-19728|-19725" .
		"|-19715|-19540|-19531|-19525|-19515|-19500|-19484|-19479|-19467|-19289|-19288|-19281|-19275|-19270|-19263" .
		"|-19261|-19249|-19243|-19242|-19238|-19235|-19227|-19224|-19218|-19212|-19038|-19023|-19018|-19006|-19003" .
		"|-18996|-18977|-18961|-18952|-18783|-18774|-18773|-18763|-18756|-18741|-18735|-18731|-18722|-18710|-18697" .
		"|-18696|-18526|-18518|-18501|-18490|-18478|-18463|-18448|-18447|-18446|-18239|-18237|-18231|-18220|-18211" .
		"|-18201|-18184|-18183|-18181|-18012|-17997|-17988|-17970|-17964|-17961|-17950|-17947|-17931|-17928|-17922" .
		"|-17759|-17752|-17733|-17730|-17721|-17703|-17701|-17697|-17692|-17683|-17676|-17496|-17487|-17482|-17468" .
		"|-17454|-17433|-17427|-17417|-17202|-17185|-16983|-16970|-16942|-16915|-16733|-16708|-16706|-16689|-16664" .
		"|-16657|-16647|-16474|-16470|-16465|-16459|-16452|-16448|-16433|-16429|-16427|-16423|-16419|-16412|-16407" .
		"|-16403|-16401|-16393|-16220|-16216|-16212|-16205|-16202|-16187|-16180|-16171|-16169|-16158|-16155|-15959" .
		"|-15958|-15944|-15933|-15920|-15915|-15903|-15889|-15878|-15707|-15701|-15681|-15667|-15661|-15659|-15652" .
		"|-15640|-15631|-15625|-15454|-15448|-15436|-15435|-15419|-15416|-15408|-15394|-15385|-15377|-15375|-15369" .
		"|-15363|-15362|-15183|-15180|-15165|-15158|-15153|-15150|-15149|-15144|-15143|-15141|-15140|-15139|-15128" .
		"|-15121|-15119|-15117|-15110|-15109|-14941|-14937|-14933|-14930|-14929|-14928|-14926|-14922|-14921|-14914" .
		"|-14908|-14902|-14894|-14889|-14882|-14873|-14871|-14857|-14678|-14674|-14670|-14668|-14663|-14654|-14645" .
		"|-14630|-14594|-14429|-14407|-14399|-14384|-14379|-14368|-14355|-14353|-14345|-14170|-14159|-14151|-14149" .
		"|-14145|-14140|-14137|-14135|-14125|-14123|-14122|-14112|-14109|-14099|-14097|-14094|-14092|-14090|-14087" .
		"|-14083|-13917|-13914|-13910|-13907|-13906|-13905|-13896|-13894|-13878|-13870|-13859|-13847|-13831|-13658" .
		"|-13611|-13601|-13406|-13404|-13400|-13398|-13395|-13391|-13387|-13383|-13367|-13359|-13356|-13343|-13340" .
		"|-13329|-13326|-13318|-13147|-13138|-13120|-13107|-13096|-13095|-13091|-13076|-13068|-13063|-13060|-12888" .
		"|-12875|-12871|-12860|-12858|-12852|-12849|-12838|-12831|-12829|-12812|-12802|-12607|-12597|-12594|-12585" .
		"|-12556|-12359|-12346|-12320|-12300|-12120|-12099|-12089|-12074|-12067|-12058|-12039|-11867|-11861|-11847" .
		"|-11831|-11798|-11781|-11604|-11589|-11536|-11358|-11340|-11339|-11324|-11303|-11097|-11077|-11067|-11055" .
		"|-11052|-11045|-11041|-11038|-11024|-11020|-11019|-11018|-11014|-10838|-10832|-10815|-10800|-10790|-10780" .
		"|-10764|-10587|-10544|-10533|-10519|-10331|-10329|-10328|-10322|-10315|-10309|-10307|-10296|-10281|-10274" .
		"|-10270|-10262|-10260|-10256|-10254";
	$_TDataKey = explode('|', $_DataKey);
	$_TDataValue = explode('|', $_DataValue);
	$_Data = array_combine($_TDataKey, $_TDataValue);
	arsort($_Data);
	reset($_Data);
	if ($_Code != 'gb2312') {
		$_String = _U2_Utf8_Gb($_String);
	}

	$_Res = '';
	for ($i = 0; $i < strlen($_String); $i++) {
		$_P = ord(substr($_String, $i, 1));
		if ($_P > 160) {
			$_Q = ord(substr($_String, ++$i, 1));
			$_P = $_P * 256 + $_Q - 65536;
			$_Res .= _Pinyin($_P, $_Data);
		}
	}
	$rqp = preg_replace("/[^a-z0-9]*/", '', $_Res);
	return strtolower($rqp);
}
function _Pinyin($_Num, $_Data) {
	if ($_Num > 0 && $_Num < 160) {
		return chr($_Num);
	} elseif ($_Num < -20319 || $_Num > -10247) {
		return '';
	} else {
		foreach ($_Data as $k => $v) {
			if ($v <= $_Num) {
				break;
			}
		}
		return $k;
	}
}
function _U2_Utf8_Gb($_C) {
	$_String = '';
	if ($_C < 0x80) {
		$_String .= $_C;
	} elseif ($_C < 0x800) {
		$_String .= chr(0xC0 | $_C >> 6);
		$_String .= chr(0x80 | $_C & 0x3F);
	} elseif ($_C < 0x10000) {
		$_String .= chr(0xE0 | $_C >> 12);
		$_String .= chr(0x80 | $_C >> 6 & 0x3F);
		$_String .= chr(0x80 | $_C & 0x3F);
	} elseif ($_C < 0x200000) {
		$_String .= chr(0xF0 | $_C >> 18);
		$_String .= chr(0x80 | $_C >> 12 & 0x3F);
		$_String .= chr(0x80 | $_C >> 6 & 0x3F);
		$_String .= chr(0x80 | $_C & 0x3F);
	}
	return iconv('UTF-8', 'GB2312', $_String);
}

function getfirstchar($s0) {
	$fchar = ord($s0{0});
	if ($fchar >= ord("A") and $fchar <= ord("z")) {
		return strtoupper($s0{0});
	}

	$s1 = @iconv("UTF-8", "GB2312", $s0);
	$s2 = iconv("GB2312", "UTF-8", $s1);
	if ($s2 == $s0) {$s = $s1;} else { $s = $s0;}
	$asc = ord($s{0}) * 256 + ord($s{1}) - 65536;
	if ($asc >= -20319 and $asc <= -20284) {
		return "a";
	}

	if ($asc >= -20283 and $asc <= -19776) {
		return "b";
	}

	if ($asc >= -19775 and $asc <= -19219) {
		return "c";
	}

	if ($asc >= -19218 and $asc <= -18711) {
		return "d";
	}

	if ($asc >= -18710 and $asc <= -18527) {
		return "e";
	}

	if ($asc >= -18526 and $asc <= -18240) {
		return "f";
	}

	if ($asc >= -18239 and $asc <= -17923) {
		return "g";
	}

	if ($asc >= -17922 and $asc <= -17418) {
		return "h";
	}

	if ($asc >= -17417 and $asc <= -16475) {
		return "j";
	}

	if ($asc >= -16474 and $asc <= -16213) {
		return "k";
	}

	if ($asc >= -16212 and $asc <= -15641) {
		return "l";
	}

	if ($asc >= -15640 and $asc <= -15166) {
		return "m";
	}

	if ($asc >= -15165 and $asc <= -14923) {
		return "n";
	}

	if ($asc >= -14922 and $asc <= -14915) {
		return "o";
	}

	if ($asc >= -14914 and $asc <= -14631) {
		return "p";
	}

	if ($asc >= -14630 and $asc <= -14150) {
		return "q";
	}

	if ($asc >= -14149 and $asc <= -14091) {
		return "r";
	}

	if ($asc >= -14090 and $asc <= -13319) {
		return "s";
	}

	if ($asc >= -13318 and $asc <= -12839) {
		return "t";
	}

	if ($asc >= -12838 and $asc <= -12557) {
		return "w";
	}

	if ($asc >= -12556 and $asc <= -11848) {
		return "x";
	}

	if ($asc >= -11847 and $asc <= -11056) {
		return "y";
	}

	if ($asc >= -11055 and $asc <= -10247) {
		return "z";
	}

	return null;
}
function jianPin($zh) {
	$ret = "";
	$s1 = iconv("UTF-8", "gb2312", $zh);
	$s2 = iconv("gb2312", "UTF-8", $s1);
	if ($s2 == $zh) {$zh = $s1;}
	for ($i = 0; $i < strlen($zh); $i++) {
		$s1 = substr($zh, $i, 1);
		$p = ord($s1);
		if ($p > 160) {
			$s2 = substr($zh, $i++, 2);
			$ret .= getfirstchar($s2);
		}
	}
	return strtolower($ret);
}

function getHttpType() {
	return ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';
}
function getWebDomain() {
	$host = $_SERVER['HTTP_HOST'];
	if (strpos($host, '.') === false) {
		return $host;
	}
	$arr = explode('.', $host);
	array_splice($arr, 0, 1);
	return implode('.', $arr);
}

function getWebHttpHost() {
	$host = $_SERVER['HTTP_HOST'];
	$http = getHttpType();
	return $http . $host;
}

function convertUpperToUnderLine($name) {
	$temp_array = array();
	for ($i = 0; $i < strlen($name); $i++) {
		$ascii_code = ord($name[$i]);
		if ($ascii_code >= 65 && $ascii_code <= 90) {
			if ($i == 0) {
				$temp_array[] = chr($ascii_code + 32);
			} else {
				$temp_array[] = '_' . chr($ascii_code + 32);
			}
		} else {
			$temp_array[] = $name[$i];
		}
	}
	return implode('', $temp_array);
}

function get_domain_from_url($url) {
	$url = strval($url);
	if (!$url) {
		return '';
	}
	$result = preg_match('/(https|http)?(:\/\/)?([^\/]+)/', $url, $matches);
	if (!$result) {
		return '';
	}
	if (empty($matches[3])) {
		return '';
	}
	return $matches[3];
}

function get_readable_memory_size($size) {
	if ($size < 1024) {
		return $size . 'B';
	}
	if ($size < 1024 * 1024) {
		return round($size / 1024, 3) . 'KB';
	}
	if ($size < 1024 * 1024 * 1024) {
		return round($size / 1024 / 1024, 3) . 'MB';
	}
	if ($size < 1024 * 1024 * 1024 * 1024) {
		return round($size / 1024 / 1024 / 1024, 3) . 'GB';
	}
	return round($size / 1024 / 1024 / 1024 / 1024, 3) . 'TB';
}

function get_readable_time($timeStamp) {
	$timeString = date('Y年n月j日 H:i:s', $timeStamp);
	if (date('Y-m-d', $timeStamp) == date('Y-m-d')) {
		$timeString = date('H:i:s');
	} else if (date('Y-m-d', $timeStamp) == date('Y-m-d', strtotime(date('Y-m-d')) - 1)) {
		$timeString = '昨天' . date(' H:i:s');
	} else if (date('Y-m-d', $timeStamp) == date('Y-m-d', strtotime(date('Y-m-d')) - 24 * 3600 - 1)) {
		$timeString = '前天' . date(' H:i:s');
	} else if (date('Y', $timeStamp) == date('Y')) {
		$timeString = date('n月j日 H:i:s');
	} else if (date('Y', $timeStamp) == date('Y') - 1) {
		$timeString = date('去年 n月j日 H:i:s');
	}
	return $timeString;
}

function get_http_domain($domain = '') {
	$domain = trim($domain);
	if ($domain == '') {
		return '';
	}
	if (stripos($domain, 'http://') === 0) {
		return $domain;
	}

	if (stripos($domain, 'https://') === 0) {
		return $domain;
	}

	return 'http://' . $domain;
}

function isMobile() {
	if (isset($_SERVER['HTTP_VIA']) && stristr($_SERVER['HTTP_VIA'], "wap")) {
        return true;
    } elseif (isset($_SERVER['HTTP_ACCEPT']) && strpos(strtoupper($_SERVER['HTTP_ACCEPT']), "VND.WAP.WML")) {
        return true;
    } elseif (isset($_SERVER['HTTP_X_WAP_PROFILE']) || isset($_SERVER['HTTP_PROFILE'])) {
        return true;
    } elseif (isset($_SERVER['HTTP_USER_AGENT']) && preg_match('/(blackberry|configuration\/cldc|hp |hp-|htc |htc_|htc-|iemobile|kindle|midp|mmp|motorola|mobile|nokia|opera mini|opera |Googlebot-Mobile|YahooSeeker\/M1A1-R2D2|android|iphone|ipod|mobi|palm|palmos|pocket|portalmmm|ppc;|smartphone|sonyericsson|sqh|spv|symbian|treo|up.browser|up.link|vodafone|windows ce|xda |xda_)/i', $_SERVER['HTTP_USER_AGENT'])) {
        return true;
    } else {
        return false;
    }
}

function isWeixin() {
	if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
		return true;
	}
	return false;
}

/**
 * 根据数组的某个键的值进行排序
 * @param  array $data       待排序数组
 * @param  string $sortKey    需要排序的值的键名
 * @param  string $upSortFlag 升序传递: 'asc'或不传递这个参数，否则就是降序
 * @return array 排好顺序的数组
 */
function sortArrayByValueOfKey($data, $sortKey, $upSortFlag = 'asc') {
	$tmpArray = array();
	foreach ($data as $key => $val) {
		$tmpArray[] = $val[$sortKey]; //这里要注意$val['nums']不能为空，不然后面会出问题
	}
	// 先排序
	if (strtolower($upSortFlag) == 'asc') {
		sort($tmpArray);
	} else {
		rsort($tmpArray);
	}
	// 交换数组中的键和值，键成为了需要排序的数据
	$tmpArray = array_flip($tmpArray);
	$resultArray = array();
	foreach ($data as $k => $v) {
		$value = $v[$sortKey];
		$index = $tmpArray[$value];
		$resultArray[$index] = $v;
	}
	//这里还要把$result进行排序，健的位置不对
	ksort($resultArray);
	return $resultArray;
}

//数组转XML
function arrayToXml($arr) {
	$xml = "<xml>";
	foreach ($arr as $key => $val) {
		if (is_numeric($val)) {
			$xml .= "<" . $key . ">" . $val . "</" . $key . ">";
		} else {
			$xml .= "<" . $key . "><![CDATA[" . $val . "]]></" . $key . ">";
		}
	}
	$xml .= "</xml>";
	return $xml;
}

//将XML转为array
function xmlToArray($xml) {
	//禁止引用外部xml实体
	libxml_disable_entity_loader(true);
	$values = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
	return $values;
}


function getIPInfoFromTaoBao($ip) {
	static $ipInfoCache = [];
	if (!empty($ipInfoCache[$ip])) {
		return $ipInfoCache[$ip];
	}
	$url = "http://ip.taobao.com/service/getIpInfo.php?ip={$ip}";
	$result = curlGet($url);
	if (!$result) {
		return ['code' => -1, 'msg' => '获取IP地址信息失败'];
	}
	$res = json_decode($result, true);
	if ($res['code']) {
		return ['code' => -1, 'msg' => $res['data']];
	}
	$res = ['code' => 0, 'msg' => 'ok', 'data' => $res['data']];
	$ipInfoCache[$ip] = $res;
	return $res;
}

/**
 * 递归实现无限极分类
 * @param $array 分类数据
 * @param $pid 父ID
 * @param $level 分类级别
 * @return $list 分好类的数组 直接遍历即可 $level可以用来遍历缩进
 */

function getTree($array, $fid =0, $level = 0){

    //声明静态数组,避免递归调用时,多次声明导致数组覆盖
    static $list = [];
    foreach ($array as $key => $value){
        //第一次遍历,找到父节点为根节点的节点 也就是pid=0的节点
        if ($value['pid'] == $fid){
            //父节点为根节点的节点,级别为0，也就是第一级
            $value['level'] = $level;
            //把数组放到list中
            $list[] = $value;
            //把这个节点从数组中移除,减少后续递归消耗
            unset($array[$key]);
            //开始递归,查找父ID为该节点ID的节点,级别则为原级别+1
            getTree($array, $value['id'], $level+1);
        }
    }
    return $list;
}


function options($list) {
	$option = '';
	foreach ($list as $key => $value) {
		// var_dump($value);
		$option .= "<option data-pid='".$value['pid']."' value='". $value['id'] ."'>";
		if ($value['pid'] > 0) {
			$option .='|';
			for ($i = 0; $i < $value['level']; $i++) {
				$option .= '—';
			}
		}
		$option .= "{$value['name']}</option>";
	}
	return $option;
}
/**
 * 对象 转 数组
 *
 * @param object $obj 对象
 * @return array
 */
function object_to_array($obj) {
    $obj = (array)$obj;
    foreach ($obj as $k => $v) {
        if (gettype($v) == 'resource') {
            return;
        }
        if (gettype($v) == 'object' || gettype($v) == 'array') {
            $obj[$k] = (array)object_to_array($v);
        }
    }
 
    return $obj;
}
