<?php
namespace app\common\model;

use think\Model;

class Admin extends Model {
	// 默认写入时间戳
	protected $autoWriteTimestamp = 'datetime';
	// 重定义时间戳字段名
	protected $createTime = 'cdate';
	protected $updateTime = 'mdate';
	
	public function getPageList($condition = array()) {
		$pageSize = config('paginate.list_rows');

		// 为翻页按钮准备query参数
		$pageParam['query']['s'] = '/' . MODULE_NAME . '/' . CONTROLLER_NAME . '/' . ACTION_NAME;
		foreach ($condition as $key => $value) {
			$pageParam['query']["condition[{$key}]"] = $value;
		}

		if (@$condition['nick']) {
			$condition['nick'] = ['like', "%{$condition['nick']}%"];
		}

		$count = $this->where($condition)->count();
		$list = self::where($condition)->order('id desc')->paginate($pageSize, $count, $pageParam);

		return array(
			'page_size' => $pageSize,
			'count' => $count,
			'page' => $list->render(),
			'list' => $list,
		);
	}
    public function deleteAdminData($id) {
        $lines = self::where(['id' => $id])->delete();
        if (false === $lines) {
            return ['code' => -1, 'msg' => '数据删除失败'];
        }
        return ['code' => 0, 'msg' => 'ok', 'data' => $id];
    }
	public function saveAdminData($data) {
		$now = date('Y-m-d H:i:s');
		$data['mdate'] = $now;
		if (isset($data['id']) && intval($data['id'] <= 0)) {
			unset($data['id']);
		}
		if (!isset($data['id'])) {
			if (empty($data['username'])) {
				return ['code' => -1, 'msg' => '请提供用户名'];
			}
			if (empty($data['password'])) {
				return ['code' => -1, 'msg' => '请提供密码'];
			}
			if (self::get(['username' => $data['username']])) {
				return ['code' => -1, 'msg' => '用户名已存在'];
			}
			$data['cdate'] = $now;
			$this->data = $data;
			$result = $this->save();
			if (false === $result) {
				return ['code' => -1, 'msg' => '添加数据失败'];
			}
			$data['id'] = $this->id;
		} else {
			$data['id'] = intval($data['id']);
			if ($data['id'] <= 0) {
				return ['code' => -1, 'msg' => 'id 必须大于0'];
			}
			if (self::get(['username' => $data['username'], 'id' => ['neq', $data['id']]])) {
				return ['code' => -1, 'msg' => '用户名已存在'];
			}
			if (isset($data['password']) && $data['password'] == '') {
				unset($data['password']);
			}
			$result = $this->save($data, ['id' => $data['id']]);
			if ($result === false) {
				return ['code' => -1, 'msg' => '修改数据失败'];
			}
		}
		return ['code' => 0, 'msg' => 'ok', 'data' => $data];
	}
	public function login($data) {
		if ($data['code'] != session('code')) {
			return ['code' => -1, 'msg' => 'code不正确，请刷新页面'];
		}

		if (empty($data['username'])) {
			return ['code' => -1, 'msg' => '请输入用户名'];
		}
		$adminInfo = self::where(['username' => $data['username']])->find();
		if (!$adminInfo) {
			return ['code' => -1, 'msg' => '用户名不存在'];
		}

		$password = md5(md5($data['code']) . $adminInfo['password']);
		if ($password != $data['password']) {
			return ['code' => -1, 'msg' => '密码不正确'];
		}
		$this->set_login_session($adminInfo);
		return ['code' => 0, 'msg' => 'ok', 'data' => $adminInfo['id']];
	}

	private function set_login_session($adminInfo) {
		session('id', $adminInfo['id']);
		session('username', $adminInfo['username']);
		session('nick', $adminInfo['nick']);
		session('role_id', $adminInfo['role_id']);
		session('code', null);
	}

	public function get_login_session() {
		$userSession = array(
			'id' => intval(session('id')),
			'username' => session('username'),
			'nick' => session('nick'),
			'role_id' => session('role_id'),
		);
		$userSession['role_info'] = model('Role')->get_role_info($userSession['role_id'], true);
		return $userSession;
	}

	public function logout() {
		session(null);
	}
	
	public function get_admin_info($id) {
		return $this->find($id);
	}
	
	public function modifyPassword($data) {
        $oldpassword = $data['oldpassword'];
        $new_password = $data['new_password'];
        if (!$oldpassword || !$new_password) {
            return array('code' => -1, 'msg' => '密码不能为空');
        }
        $adminInfo = $this->get_login_session();
        $aInfo = $this->get_admin_info($adminInfo['id']);
        if ($aInfo['password'] != $oldpassword) {
            return array('code' => -1, 'msg' => '旧密码错误');
        }
        $lines = $this->save(array('password' => $new_password), array('id' => $aInfo['id']));
        if ($lines === false) {
            return array('code' => -1, 'msg' => '密码保存失败');
        }
        return array('code' => 0, 'msg' => 'ok');
    }
    
    public function modifyNick($data) {
    	$adminInfo = $this->get_login_session();
    	$lines = $this->save(array('nick' => $data['nick']), array('id' => $adminInfo['id']));
        if ($lines === false) {
            return array('code' => -1, 'msg' => '昵称修改失败');
        }
        session('nick', $data['nick']);
        return array('code' => 0, 'msg' => 'ok');
    }

	private function getMCAList($url) {
		$url = substr($url, 3);
		$arr = explode('/', $url);
		if (count($arr) == 0) {
			return array(
				'm' => 'index',
				'c' => 'index',
				'a' => 'index',
			);
		}
		if (count($arr) == 1) {
			return array(
				'm' => $arr[0],
				'c' => 'index',
				'a' => 'index',
			);
		}
		if (count($arr) == 2) {
			return array(
				'm' => $arr[0],
				'c' => $arr[1],
				'a' => 'index',
			);
		}
		return array(
			'm' => $arr[0],
			'c' => $arr[1],
			'a' => $arr[2],
		);
	}
	
	
}