<?php
namespace app\admin\controller;

class User extends AdminController {
	public function _initialize() {
		parent::_initialize();
		$this->view->breadcrumb = array(
			array('name' => '用户管理'),
		);
	}
	public function index() {
		$this->view->title = '用户列表';
		$condition = $this->_get_search_condition();
		// $condition['status'] = 0;
		$this->view->pageList = model('User')->getPageList($condition);
		$this->view->pageList = model('User')->getPageList($condition);
		$this->view->roleMap = model('Role')->get_role_map();
		return $this->fetch();
	}

	public function house() {
		$this->view->title = '用户列表';
		$condition = $this->_get_search_condition();
		$condition['status'] = 1;
		$this->view->pageList = model('User')->getPageList($condition);
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
		$res = model('User')->saveUserData($data);
		return $res;
	}
	
	public function delete() {
		$id = input('param.id', 0, 'intval');
		$res = model('User')->deleteUserData($id);
		return $res;
	}
	
}