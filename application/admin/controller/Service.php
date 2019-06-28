<?php
namespace app\admin\controller;

class Service extends AdminController {
	public function _initialize() {
		parent::_initialize();
		$this->view->breadcrumb = array(
			array('name' => '客服管理'),
		);
	}
	public function index() {
		$this->view->title = '客服列表';
		$condition = $this->_get_search_condition();
		// $condition['status'] = 0;
		$this->view->pageList = model('Service')->getPageList($condition);
		$this->view->roleMap = model('Role')->get_role_map();
		return $this->fetch();
	}

	

	public function add() {
		return $this->_save();
	}

	public function update() {
		return $this->_save();
	}
	
	private function _save() {
		$data = input('post.');
		$res = model('Service')->saveServiceData($data);
		return $res;
	}
	
	public function delete() {
		$id = input('param.id', 0, 'intval');
		$res = model('Service')->deleteServiceData($id);
		return $res;
	}
	
}