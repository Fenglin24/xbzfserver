<?php
namespace app\common\helper;
/**
 * 作者：丁华能 2017年7月20日
 * 在线编辑器编辑HTML文本，包含上传图片，图片上传至tmp目录下，
 * 在保存文章时，需要将文件转移到正式目录中存储。
 * 另外，在编辑文章时，可能删除某些图片，这时要检测已经被用户删除的，
 * 还保存在正是目录下的图片进行删除。
 */
class File {
	/******************************** 迁移HTML中的文件逻辑 ************************/
	/**
	 * 根据旧文件数组old_files，从html中提取文件，删除失效的，移动临时的，并替换html内容。
	 * @param  string $new_html    本次提交的HTML代码，处理成功后html会被替换。
	 * @param  string $old_html    上次保存的HTML代码。新增时传入空字符串即可。
	 * @param  string $tmp_path    临时目录，必须以/结尾，比如/tmp/
	 * @param  string $target_path 目标目录，必须以/结尾，比如/upload/articles/
	 * @param  string $server_root 前导路径，移动图片只能在同一个前导路径中。
	 * @return array 失败返回FALSE，成功返回移动图片数量和删除图片数量（数组），新图片数组，
	 */
	public static function moveAndDeleteFilesFromHtml($new_html, $old_html, $tmp_path, $target_path, $server_root = '') {
		$data = ['moved' => 0, 'deleted' => 0];
		// 首先正则匹配以前HTML中已上传图片、本次新上传到临时目录图片、本次HTML中保留的上次上传的图片
		$old_files_on_server = self::get_file_from_html($old_html, $target_path); // 从上次保存的HTML中获取已上传图片列表
		$new_files_uploaded = self::get_file_from_html($new_html, $tmp_path); // 本次新上传到tmp_path中的HTML里的图片
		$uploaded_files_reserved = self::get_file_from_html($new_html, $target_path); // 上次已经上传了的，还保留在HTML里的图片
		// 目标目录增加年月日目录，用于保存图片
		$target_date_path = rtrim($target_path, '/') . '/' . date('Y-m-d') . '/';
		$count = self::delete_old_files($old_files_on_server, $uploaded_files_reserved, $server_root);
		if (false === $count) {
			return ['code' => -1, 'msg' => '删除无效图片失败'];
		}
		$data['deleted'] = $count;
		$count = self::move_new_files_to_path($new_files_uploaded, $tmp_path, $target_date_path, $server_root);
		if (false === $count) {
			return ['code' => -1, 'msg' => '迁移新图片失败'];
		}
		$data['moved'] = $count;
		$new_html = str_ireplace($tmp_path, $target_date_path, $new_html);
		$new_html = self::replace_host_info_in_html($new_html);
		$data['new_files'] = implode(',', array_merge($uploaded_files_reserved, $new_files_uploaded));
		$data['html'] = strval($new_html);
		return ['code' => 0, 'msg' => 'ok', 'data' => $data];
	}
	/**
	 * 根据路径前面的字符串，在html中提取本次上传的图片、文件
	 * @param  [type] $prefix_path  路径前几个字符，一般为/tmp/，或正式路径
	 * @param  [type] $html         从这个html中正则匹配提取
	 * @return [type]               返回匹配到的图片路径
	 */
	public static function get_file_from_html($html, $prefix_path) {
		$html = htmlspecialchars_decode(strval($html));
		$prefix_path = str_replace("/", "\/", $prefix_path);
		$reg = "/\s+(href|src)\s*=\s*['\"]*(" . $prefix_path . "[^'^\"^\s^>]+)['\"]*[^>]*>/i";
		preg_match_all($reg, $html, $matches, PREG_PATTERN_ORDER);
		// 0里保存的是整个匹配到的，1是第一个括弧，2是第二个括弧，就算$html为空，也会返回这个数组，只不过是空数组
		return $matches[2];
	}

	/**
	 * 根据路径前面的字符串，editor自动生成图片替换
	 */
	public static function replace_host_info_in_html($html) {
		$host = isset($_SERVER['HTTP_X_FORWARDED_HOST']) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '');
		$httpHostInfo = str_replace('/', '\/', $host);
		$pattern = "/(\s+(href|src)\s*=\s*['\"]*){$httpHostInfo}/i";
		return preg_replace($pattern, '$1', $html);
	}
	/**
	 * 从旧文件列表中删除在新文件列表中不存在的文件，注意返回值判断
	 * @param  [type] $old_files  旧文件数组
	 * @param  [type] $new_files  新文件数组
	 * @param  string $server_root 文件前导路径（加上文件路径就是绝对路径）
	 * @return [type] 成功返回删除文件数量，失败返回false
	 */
	public static function delete_old_files($old_files, $new_files, $server_root = '') {
		$count = 0;
		foreach ($old_files as $http_file_path) {
			if (!$http_file_path) {
				continue;
			}

			if (in_array($http_file_path, $new_files)) {
				continue;
			}
			if (!self::delete_file($http_file_path, $server_root)) {
				return false;
			}
			$count++;
		}
		return $count;
	}

	/**
	 * 将文件列表中存在于临时目录中的文件转移到正式目录下
	 * @param  [type] $file_list   文件列表，移动后文件列表会被替换
	 * @param  [type] $tmp_path    临时目录前导路径
	 * @param  [type] $target_path 正式目录前导路径
	 * @return [type] 成功返回移动文件数量，失败返回false
	 */
	public static function move_new_files_to_path(&$file_list, $tmp_path, $target_path, $server_root = '') {
		$count = 0;
		foreach ($file_list as &$http_file_path) {
			if (!$http_file_path) {
				continue;
			}

			if (strpos($http_file_path, $tmp_path) !== 0) {
				continue; // 不是临时文件
			}
			if (!self::file_is_exists($http_file_path, $server_root)) {
				continue;
			}
			$dest_http_path = rtrim($target_path, '/') . '/' . basename($http_file_path);
			if (false === self::move_file($http_file_path, $dest_http_path, $server_root)) {
				return false;
			}
			$http_file_path = $dest_http_path;
			$count++;
		}
		return $count;
	}

	public static function file_is_exists($filename, $server_root = '') {
		if (!$filename) {
			// 容错，空的时候返回正确
			return false;
		}
		$server_root = $server_root ? $server_root : $_SERVER['DOCUMENT_ROOT'];
		$abs_file_path = $server_root . '/' . $filename;
		return file_exists($abs_file_path);
	}

	public static function delete_file($filename, $server_root = '') {
		if (!$filename) {
			// 容错，空的时候返回正确
			return true;
		}
		$server_root = $server_root ? $server_root : $_SERVER['DOCUMENT_ROOT'];
		$abs_file_path = $server_root . '/' . $filename;
		if (file_exists($abs_file_path)) {
			return unlink($abs_file_path);
		}
		return true;
	}

	/**
	 * [move_file 移动文件]
	 * @param  [type] $from_path [文件源路径]
	 * @param  [type] $to_path   [文件目标路径]
	 * @return [type]            [成功返回true，失败返回false]
	 */
	public static function move_file($from_path, $to_path, $server_root = '') {
		if ($from_path == $to_path) {
			return true;
		}
		$server_root = $server_root ? $server_root : $_SERVER['DOCUMENT_ROOT'];
		$abs_from_path = $server_root . $from_path;
		$abs_to_path = $server_root . $to_path;
		if (!self::make_dir(dirname($abs_to_path))) {
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

	/**
	 * 创建目录（递归）
	 */
	public static function make_dir($dir) {
		if (is_dir($dir) || @mkdir($dir)) {
			return TRUE;
		}

		if (!self::make_dir(dirname($dir))) {
			return FALSE;
		}

		return @mkdir($dir);
	}
	/******************************** 迁移HTML中的文件逻辑 ************************/

	/**
	 * 迁移附件，从tmpPath到savePath
	 */
	public static function move_attr_file($attache_list, $tmp_path, $save_path) {
		$movedAttacheList = [];
		foreach ($attache_list as $from_path) {
			if (strpos($from_path, $tmp_path) !== 0) {
				$movedAttacheList[] = $from_path;
				continue;
			}
			$to_path = $save_path . '/' . basename($from_path);
			$result = moveFile($from_path, $to_path);
			if (!$result) {
				return false;
			}
			$movedAttacheList[] = $to_path;
		}
		return $movedAttacheList;
	}

	/**
	 * 迁移很多文件
	 * @param  array $postData 是POST过来的数据$_POST
	 * @param  string $imageRootPath 图片相对网站根目录的路径，比如/uploads/head
	 * @param  string $serverRoot 网站根目录，比如/data/web/www/web/public
	 * @return array 成功返回code为0，失败code为非0
	 */
	public static function moveUploadFileAndGetMoreData($postData, $imageRootPath, $serverRoot = '') {
		// PHP的全局变量$_FILES，需要在input中设置name="member[head_img]"或name="head_img"
		foreach ($_FILES as $key => $file) {
			if (!$file['tmp_name']) {
				continue;
			} else if (is_array($file['tmp_name'])) {
				$table_name = $key;
				// <input type="file" name="member[head_img]" >
				foreach ($file['tmp_name'] as $col_name => $tmp_name) {
					if (!$tmp_name) {
						continue;
					}

					$original_extension = getOrigExtName($file['name'][$col_name]);
					$size = $file['size'][$col_name];
					$res = uploadImageByTmpName($tmp_name, $imageRootPath, $serverRoot, 0, 0, $original_extension, $size);
					if ($res['code']) {
						return $res;
					}
					if (!isset($postData[$table_name])) {
						$postData[$table_name] = [];
					}
					if (!empty($postData[$table_name][$col_name])) {
						deleteFile($postData[$table_name][$col_name], $serverRoot);
					}
					$postData[$table_name][$col_name] = $res['data'];
				}
			} else {
				// <input type="file" name="head_img" >
				$tmp_name = $file['tmp_name'];
				if (!$tmp_name) {
					continue;
				}

				$original_extension = getOrigExtName($file['name']);
				$size = $file['size'];
				$res = uploadImageByTmpName($tmp_name, $imageRootPath, $serverRoot, 0, 0, $original_extension, $size);
				if ($res['code']) {
					return $res;
				}
				$col_name = $key;
				if (!empty($postData[$col_name])) {
					deleteFile($postData[$col_name]);
				}
				$postData[$col_name] = $res['data'];
			}
		}
		return ['code' => 0, 'msg' => 'ok', 'data' => $postData];
	}
}