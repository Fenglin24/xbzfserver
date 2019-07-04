<?php
namespace app\admin\controller;

class Config extends AdminController {
	public function _initialize() {
		parent::_initialize();
	}
	
	public function index() {
		$this->view->title = '网站配置';
		$this->view->pageList = model('Config')->getPageList();
		return $this->fetch();
	}
	
	public function save() {
		$res = model('config')->saveConfigData($_POST);
		return $res;
	}
}