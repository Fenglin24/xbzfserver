<?php
namespace app\index\controller;

class IndexController extends \think\Controller {
	protected $_uid = 0;
	public function _initialize() {
		$this->_uid = 1;
	}
}
