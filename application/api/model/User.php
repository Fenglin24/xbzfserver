<?php

/**
 * @Author: Qian weidong
 * @Date:   2018-12-11 16:28:51
 * @Last Modified by:   Qian weidong
 * @Last Modified time: 2018-12-11 16:31:29
 */
namespace app\api\model;

class User extends \app\common\model\User {

	public function get_user_info_by_bltoken($blToken) {
		$user_id = model('Bltoken')->where(['blToken' => $blToken])->value('user_id');
		if (!@$user_id) {
			return ['status_code' => 2001, 'msg' => 'Bltoken不存在'];
		}
		$user = self::get($user_id);
		if (!$user) {
			return ['status_code' => -1, 'msg' => '没有该账户'];
		}
		return $user;
	}

	public function save_user_wx_data($data) {
		$res = $this->saveUserData($data);
		return $res;
	}

	public function get_avaurl($user_id) {
		return $this->where('id', $user_id)->find();
	}
}