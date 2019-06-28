<?php
namespace app\admin\controller;

class Keyword extends AdminController {
	public function _initialize() {
		parent::_initialize();
		$this->view->breadcrumb = array(
			array('name' => '房源管理'),
		);
	}
	public function index() {
		$this->view->title = '热门检索';
		$condition = $this->_get_search_condition();
		// $condition['status'] = 0;
		$this->view->pageList = model('Keyword')->getPageList($condition);
		$this->view->roleMap = model('Role')->get_role_map();
		$this->view->citys = model('Cate')->where('pid', 0)->group('name')->select();
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
		// dd($data);
		$res = model('Keyword')->saveKeywordData($data);
		return $res;
	}
	
	public function delete() {
		$id = input('param.id', 0, 'intval');
		$res = model('Keyword')->deleteKeywordData($id);
		return $res;
	}

	public function xx() {
		$id = input('param.id');
		$row = model('Keyword')->where('id', $id)->find();
		if ($row['status'] == 0) {
			$data['status'] = 1;
		} else {
			$data['status'] = 0;
		}
		$res = model('Keyword')->where('id', $id)->update($data);
		if ($res === false) {
			return ['code' => -1, 'msg' => '设置失败'];
		}
		return ['code' => 0, 'msg' => '设置成功'];
	} 
	
}