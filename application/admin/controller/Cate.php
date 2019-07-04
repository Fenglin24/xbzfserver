<?php

/**
 * @Author: Qian weidong
 * @Date:   2018-12-13 16:01:12
 * @Last Modified by:   qian
 * @Last Modified time: 2018-12-23 15:39:47
 */
namespace app\admin\controller;

class Cate extends AdminController {
	public function _initialize() {
		parent::_initialize();
		$this->view->breadcrumb = array(
			array('name' => '下拉管理'),
		);
	}
	public function index() {
		$this->view->title = '城市区域校区';
		$condition = $this->_get_search_condition();
		// $condition['status'] = 0;
		$list = model('Cate')->order('id ASC')->select();
		$list = getTree(json_decode(json_encode($list), true));
		foreach ($list as $key => &$value) {
			if ($value['pid'] > 0) {
				$str = '|';
				for ($i = 0; $i <= $value['level']; $i++) {
					$str .= '——';
				}
				// echo $str.'<br/>';
				
				$value['p'] = $str;
			}
			
		}
		// exit;
		$this->view->pageList = $list;
		$this->view->roleMap = model('Role')->get_role_map();
		$this->view->cates = options($list);

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
		$res = model('Cate')->saveCateData($data);
		return $res;
	}
	
	public function delete() {
		$id = input('param.id', 0, 'intval');
		$res = model('Cate')->deleteCateData($id);
		return $res;
	}

	public function hot() {
		$data = input('param.');
		if ($data['hot'] == '否') {
			$hot = '是';
		} else {
			$hot = '否';
		}
		$res = model('Cate')->where('id', $data['id'])->update(['hot' => $hot]);
		if ($res == false) {
			return ['code' => -1, 'msg' => '设置失败'];
		}
		return ['code' => 0, 'msg' => '设置成功'];
	}

	public function get_cate_area() {
		$name = input('param.id');
		if (!@$name) {
			return ['code' => -1, 'msg' => '城市不能为空'];
		}
		$option = input('param.option');
		$id = model('Cate')->where('name', $name)->value('id');
		$rows = model('Cate')->where('pid', $id)->select();
		$option = $this->optionString($rows, @$option);
		return ['code' => 0, 'msg' => 'ok', 'data' => $option];
	}

	public function optionString($list, $s) {
		$option = "<option value=''>请选择</option>";
		foreach ($list as $row) {
			// dd($s);
			if ($row['name'] == $s) {
				$selected = 'selected';
			} else {
				$selected = '';
			}
			$option .= "<option ".$selected." value='".$row['name']."'>".$row['name']."</option>";

		}
		return $option;

	}

}