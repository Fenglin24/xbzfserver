<?php
namespace app\index\controller;

class Index extends IndexController {
	public function _initialize() {
		parent::_initialize();
	}
	
	public function index() {
		$file = new \app\common\helper\File;
		$tmp_path = '/tmp/';
		$target_path = '/upload/articles/';
		$old_html = '';
		$new_html = '<p>
    <img src="/tmp/20171016154217293546.jpg" title="SS:20171016154217293546.jpg；原始文件：电路.jpg" alt="电路.jpg"/>
</p>';
		$result = $file->moveAndDeleteFilesFromHtml($new_html, $old_html, $tmp_path, $target_path);
		dd($result);
	}
}
