<?php

/**
 * @Author: qian 
 * @Date:   2018-12-11 22:32:40
 * @Last Modified by:   qian
 * @Last Modified time: 2018-12-19 23:45:32
 */
namespace app\api\controller;

class Questions extends \think\Controller {

	public function get_house_list() {
		// $condition = [];
		$data = input('param.');
		if (!$data['type']) {
			$condition['type'] = '房东';
		} else {
			$condition['type'] = '租房';
		}
		$list = model('Questions')->get_page_list($condition);
		$this->sucess('0', 'ok', $list);
	}

    public function get_questions_list() {
        // $condition = [];
        $data = input('param.');
        if (!$data['type']) {
            $condition['type'] = '房东';
        } else {
            $condition['type'] = '租房';
        }
        $list = model('Questions')->get_page_list($condition);
        $this->sucess('0', 'ok', $list);
    }

    public function get_roommates_list() {
        // $condition = [];
        $data = input('param.');
        if (!$data['type']) {
            $condition['type'] = '房东';
        } else {
            $condition['type'] = '租房';
        }
        $list = model('Questions')->get_page_list_roommate($condition);
        $this->sucess('0', 'ok', $list);
    }

	public function contract() {
		$data = input('param.type');
		if (@$data == 0) {
			$id = 12; //关于我们
		} elseif (@$data == 1) {
			$id = 5; //平台声明
		} elseif (@$data == 2) {
			$id = 14;//直播看房
		} elseif (@$data == 3) {
			$id = 16;//房源申请
		} elseif (@$data == 4) {
			$id = 19;//闪电招租
		} elseif (@$data == 5) {
			$id = 18;//周边服务
		} elseif (@$data == 6) {
			$id = 22;//房东协议
		} elseif (@$data == 7) {
			$id = 23;//看房服务
		} elseif (@$data == 8) {
            $id = 44;//小宝清洁
        } elseif (@$data == 9) {
            $id = 45;//接送机
        } elseif (@$data == 10) {
            $id = 46;//水电煤网
        } elseif (@$data == 11) {
            $id = 47;//配置家具
        } else {
			$this->sucess('-1', '该文章不存在');
		}
		$row = model('Questions')->where('id', $id)->find();
		$row = $this->init_html_content($row);
		$this->sucess('0', 'ok', $row);
	}

	public function sucess($code, $msg = '', $data = '') {
		$arr['code'] = $code;
		$arr['msg'] = $msg;
		$arr['data'] = $data;
		echo json_encode($arr);exit;
	}

	public function init_html_content($row) {
		if (@$row['content']) {
			$row['content'] = str_ireplace('src="', 'src="'.config('appurl'), $row['content']);
		}
		// if (@$row['thumbnail']) {
		// 	$row['thumbnail'] = config('appurl').$row['thumbnail'];
		// }
		
		return $row;
	}

	public function init_html_list_content($list) {
		foreach ($list as &$row) {
			$row = $this->init_html_content($row);
		}
		return $list;
	}

}