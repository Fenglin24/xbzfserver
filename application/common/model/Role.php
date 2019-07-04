<?php
/**
 * 角色表
 * id = 0 (role表第一条记录)代表超级管理员，这个已经固化在代码里了，后台不让删除此角色
 */
namespace app\common\model;

use think\Model;
class Role extends Model {
	//默认写入时间戳
	protected $autoWriteTimestamp = 'datetime';
	//重定义时间戳字段名
	protected $createTime = null;
	protected $updateTime = null;
	
	public function getPageList($condition = array()) {
		$pageSize = config('paginate.list_rows');
		$count = $this->where($condition)->count();
		$pageParam['query']['s'] = '/' . MODULE_NAME . '/' . CONTROLLER_NAME . '/' . ACTION_NAME;
		$list = self::where($condition)->order('id desc')->paginate($pageSize, $count, $pageParam);
		return array(
			'page_size' => $pageSize,
			'count' => $count,
			'page' => $list->render(),
			'list' => $list,
		);
	}

	public function check_authority($role_id, $authority_map, $route = '') {
		// 超级管理员，不必验证权限
		if (self::is_admin_role($role_id)) {
			return true;
		}
		
		// 获取本次请求的路由名称
		$route = $route ? $route : Route::get_request_route();
		$arr = explode('/', $route);
		$controller_name = $arr[2];
		
		// 如果是忽略权限验证的路由，直接返回
		if (Route::is_ignore_route($route)) {
			return true;
		}
		
		// 如果没有权限，返回true
		if (array_key_exists($route, $authority_map)) {
			return true;
		}
		return false;
	}
	
	// 角色id翻译成name
	public function get_role_map() {
		$rows = self::all();
		$roleMap = array();
		foreach ($rows as $key => $row) {
			$roleMap[$row['id']] = $row['name'];
		}
		return $roleMap;
	}
	
	// 获取一个角色的信息
	public function get_role_info($id) {
		$id = intval($id);
		if ($id < 0) {
			return array();
		}
		$info = $this->where(array('id' => $id))->find()->toArray();
		return $info;
	}
	
	public static function is_admin_role($role_id) {
		return $role_id == 0;
	}
	
	public function get_role_name($id) {
		return strval($this->where('id', $id)->value('name'));
	}
	
	public function saveRoleData($data) {
		if ($data['id']) {
			$lines = $this->save($data, ['id' => $data['id']]);
			if ($lines === false) {
				return array('code' => -1, 'msg' => $this->getDbError());
			}
		} else {
			$insert_id = $this->insertGetId($data);
			if ($insert_id === false) {
				return array('code' => -1, 'msg' => $this->getDbError());
			}
			$data['id'] = $insert_id;
		}
		$role_mem_key = 'role_' . $data['id'];
		cache($role_mem_key, null);
		return array('code' => 0, 'msg' => 'ok', 'data' => $data);
	}
	
	public function deleteRoleData($id) {
		$id = intval($id);
		$adminCount = \think\Db::name('admin')->where(['role_id' => $id])->count();
		if ($adminCount) {
			return array('code' => -1, 'msg' => "该角色下面有管理员，不能删除！");
		}
		$lines = $this->where(['id' => $id])->delete();
		if ($lines == false) {
			return array('code' => -1, 'msg' => $this->getDbError());
		}
		return array('code' => 0, 'msg' => 'ok');
	}
}