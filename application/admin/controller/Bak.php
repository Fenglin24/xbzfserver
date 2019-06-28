<?php
namespace app\admin\controller;

class Bak extends AdminController {
	public function _initialize() {
		parent::_initialize();
		$this->view->breadcrumb = array(
			array('name' => '系统设置'),
		);
	}
	public function index() {
		$this->view->title = '备份数据库';
		$condition = $this->_get_search_condition();
		// $condition['status'] = 0;
		$this->view->pageList = model('Bak')->getPageList($condition);
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
		$res = model('Bak')->saveBakData($data);
		return $res;
	}
	
	public function delete() {
		$id = input('param.id', 0, 'intval');
		$res = model('Bak')->deleteBakData($id);
		return $res;
	}

	
	
}