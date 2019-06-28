<?php
namespace app\common\model;

use think\Model;

class Menu extends Model {
	//默认写入时间戳
	protected $autoWriteTimestamp = 'datetime';
	//重定义时间戳字段名
	protected $createTime = null;
	protected $updateTime = null;
	public function get_name_by_id($id) {
		return strval($this->where('id', $id)->value('name'));
	}
	public function get_parent_code_list() {
		$rows = $this->where('pid', 0)->order('sequence asc, id asc')->select();
		return collection($rows)->toArray();
	}
	public function getAllMenuList() {
		$menus = $this->select();
		return array('code' => 0, 'msg' => "ok", 'data' => $menus);
	}
	public function get_all_menu() {
		$menus = [];
		$rootMenus = $this->where('pid', 0)->order('sequence asc')->select();
		$rootMenus = collection($rootMenus)->toArray();
		foreach ($rootMenus as $key => $row) {
			$childMenus = $this->where('pid', $row['id'])->order('sequence asc, id asc')->select();
			$childMenus = collection($childMenus)->toArray();
			$row['children'] = $childMenus;
			$menus[] = $row;
		}
		return $menus;
	}
	public function get_all_show_menu() {
		$menus = $this->get_all_menu();
		foreach ($menus as $key => $rootMenu) {
			if ($rootMenu['hidden']) {
				unset($menus[$key]);
			}
			foreach ($rootMenu['children'] as $childKey => $childMenu) {
				if ($childMenu['hidden']) {
					unset($menus[$key]['children'][$childKey]);
				}
			}
		}
		return $menus;
	}
	
	public function get_user_authority_map($authority) {
		if (!$authority) return [];
		$authority_map = model('RouteGroup')->get_routes_map_by_menu_ids(explode(',', $authority));
		return $authority_map;
	}
	
	public function get_user_menu($role_id, $authority_map) {
		$rootMenus = $this->get_all_show_menu();
		if (model('Role')->is_admin_role($role_id)) { // 超级管理员
			return $rootMenus;
		}
		
		$menus = [];
		foreach ($rootMenus as $key => $rootMenu) {
			// 菜单只有一级，且设置了route，那么必然不可以有子菜单
			if ($rootMenu['route'] && empty($rootMenu['children'])) {
				if (array_key_exists($rootMenu['route'], $authority_map)) {
					$menus[] = $rootMenu;
				}
				continue;
			}
			
			$childMenus = &$rootMenu['children'];
			foreach ($childMenus as $key => $childMenu) {
				if (!array_key_exists($childMenu['route'], $authority_map)) {
					unset($childMenus[$key]); // 没有这个菜单的权限
				}
			}
			
			// 如果有子菜单，但是子菜单都没有权限，忽略这个父级菜单
			if (!$rootMenu['route'] && empty($rootMenu['children'])) {
				continue;
			}
			
			$menus[] = $rootMenu;
		}
		
		return $menus;
	}
	
	public function saveMenu($data) {
		if (!$data) {
			return array('code' => -1, 'msg' => "error");
		}
		if ($data["id"]) {
			$oldInfo = $this->where(['id' => $data['id']])->find();
			if (!$oldInfo) {
				return array('code' => -1, 'msg' => "不存在的菜单");
			}
			$lines = $this->save($data, ['id' => $data['id']]);
			if ($lines === false) {
				return array('code' => -1, 'msg' => $this->getDbError());
			}
		} else {
			$result = $this->data($data)->save();
			if ($result === false) {
				return array('code' => -1, 'msg' => $this->getDbError());
			}
			$data['id'] = $this->id;
		}
		return array('code' => 0, 'msg' => 'ok', 'data' => $data);
	}
	
	public function deleteMenu($id) {
		$myInfo = $this->get($id)->toArray();
		if (!$myInfo) {
			return array('code' => -1, 'msg' => '不存在的菜单');
		}
		$parentinfo = $this->where(array('pid' => $myInfo['id']))->find();
		if ($parentinfo) {
			return array('code' => -1, 'msg' => "该分类下面有子类，不能删除");
		}
		$lines = $this->where(array("id" => $id))->delete();
		if ($lines == false) {
			return array('code' => -1, 'msg' => '删除失败');
		}
		return array('code' => 0, 'msg' => '删除成功');
	}
}