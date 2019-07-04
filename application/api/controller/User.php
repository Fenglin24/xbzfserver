<?php

/**
 * @Author: Qian weidong
 * @Date:   2018-12-11 17:18:14
 * @Last Modified by:   Qian weidong
 * @Last Modified time: 2018-12-29 10:22:32
 */
namespace app\api\controller;

class User extends BaseController {
	public function user_info() {
		return $this->user;
	}
  
    public function user_info_by_id() {
        $data = input('param.');
        $res = model('User')->where('id', $data['user_id']);
        return $res;
    }

	//修改用户联系方式
	public function update() {
		$data = input('param.');
		if (!@$data['real_name']) {
			$this->sucess('-1', '姓名不能为空');
		}
//		if (!@$data['tel']) {
//			$this->sucess('-1', '电话不能为空');
//		}
//		if (!@$data['wchat']) {
//			$this->sucess('-1', '微信号不能为空');
//		}
        if (@$data['email'] != "" && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $this->sucess('-1', '电子邮箱不合法');
        }
		$condition['real_name'] = $data['real_name'];
		$condition['tel'] = $data['tel'];
		$condition['wchat'] = $data['wchat'];
        $condition['email'] = $data['email'];
		$res = model('User')->where('id', $this->user->id)->update($condition);
		if ($res === false) {
			$this->sucess('-1', '修改失败');
		}
		$this->sucess('0', '修改成功', $condition);
	}

	//添加搜索关键字
	public function add_history() {
		$name = input('param.name');
		if (!@$name) {
			$this->sucess('0', '关键字空');
		}
		$data['name'] = $name;
		$data['user_id'] = $this->user->id;
		$res = model('History')->save($data);
		if ($res == false) {
			$this->sucess('-1', '添加失败');
		}

		$this->sucess('0', '添加成功');
	}


}