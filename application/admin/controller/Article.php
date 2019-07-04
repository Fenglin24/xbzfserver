<?php
namespace app\admin\controller;

class Article extends AdminController {
	public function _initialize() {
		parent::_initialize();
		$this->view->breadcrumb = array(
			array('name' => '常见问题'),
		);
	}
	public function index() {
		$this->view->title = '用户列表';
		$condition = $this->_get_search_condition();
		$this->view->pageList = model('Article')->getPageList($condition);
		$this->view->roleMap = model('Role')->get_role_map();
		return $this->fetch();
	}

	public function renting() {
		$this->view->title = '租房相关';
		$condition = $this->_get_search_condition();
		$condition['type'] = '租房';
		$this->view->pageList = model('Article')->getPageList($condition);
		$this->view->roleMap = model('Role')->get_role_map();
		return $this->fetch();
	}

	public function house() {
		$this->view->title = '房东相关';
		$condition = $this->_get_search_condition();
		$condition['type'] = '房东';
		$this->view->pageList = model('Article')->getPageList($condition);
		$this->view->roleMap = model('Role')->get_role_map();
		return $this->fetch();
	}

	public function add() {
		if (empty($_POST)) {
			$type = input('param.type');
			$this->view->type = $type;
			$this->view->title = '添加'. $type .'问题';
			return $this->fetch();
		}
		return $this->_save();
	}

	public function update() {
		if (empty($_POST)) {
			$type = input('param.type');
			$this->view->type = $type;
			$this->view->title = '编辑'. $type .'问题';
			$this->view->Article = model('Article')->where('id', input('param.id'))->find();
			return $this->fetch();
		}
		return $this->_save();
	}

	public function save() {
		return $this->_save();
	}
	
	private function _save() {
		$data = input('post.');
		$res = model('Article')->saveArticleData($data);
		return $res;
	}
	
	public function delete() {
		$id = input('param.id', 0, 'intval');
		$res = model('Article')->deleteArticleData($id);
		return $res;
	}
	
}