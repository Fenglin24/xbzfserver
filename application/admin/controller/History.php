<?php
namespace app\admin\controller;

class History extends AdminController {
	public function _initialize() {
		parent::_initialize();
		$this->view->breadcrumb = array(
			array('name' => '搜索管理'),
		);
	}
	public function index() {
		$this->view->title = '搜索历史';
		$condition = $this->_get_search_condition();
		// $condition['status'] = 0;
		$this->view->pageList = model('History')->getPageList($condition);
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
		$res = model('History')->saveHistoryData($data);
		return $res;
	}
	
	public function delete() {
		$id = input('param.id', 0, 'intval');
		$res = model('History')->deleteHistoryData($id);
		return $res;
	}

	public function xx() {
		$id = input('param.id');
		$row = model('History')->where('id', $id)->find();
		if ($row['status'] == 0) {
			$data['status'] = 1;
		} else {
			$data['status'] = 0;
		}
		$res = model('History')->where('id', $id)->update($data);
		if ($res === false) {
			return ['code' => -1, 'msg' => '设置失败'];
		}
		return ['code' => 0, 'msg' => '设置成功'];
	} 

	public function dowload() {
		header("Content-type:application/octet-stream");
		header("Accept-Ranges:bytes");
		header("Content-Disposition:attachment;filename=".'id列表_'.date("YmdHis").".txt");
		header("Expires:&nbsp;0");
		header("Cache-Control:must-revalidate,post-check=0,pre-check=0");
		header("Pragma:public");
		$list = model('History')->order('id DESC')->select();
		echo 'id              历史搜索              搜索时间'."\r\n";
		foreach ($list as $key => $row) {
		echo $row['id'] . '              ' . $row['name'] . '              ' . $row['cdate'] . "\r\n";
		}

	}
	
}