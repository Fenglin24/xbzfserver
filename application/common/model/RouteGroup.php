<?php
namespace app\common\model;

use think\Model;
class RouteGroup extends Model {
	//默认写入时间戳
	protected $autoWriteTimestamp = 'datetime';
	//重定义时间戳字段名
	protected $createTime = null;
	protected $updateTime = null;
	
	
	public function get_menu_group_routes() {
		$menus = model('Menu')->get_all_menu();
		$menu_group_routes = [];
		foreach ($menus as $rootMenu) {
			if (!$rootMenu['children']) {
				$item = [
					'menu_id' => $rootMenu['id'],
					'menu_name' => $rootMenu['name'],
				];
				$item['groups'] = $this->get_groups_by_menu_id($rootMenu['id']);
				$menu_group_routes[] = $item;
				continue;
			} 
			foreach ($rootMenu['children'] as $childMenu) {
				$item = [
					'menu_id' => $childMenu['id'],
					'menu_name' => $childMenu['name'],
				];
				$item['groups'] = $this->get_groups_by_menu_id($childMenu['id']);
				$menu_group_routes[] = $item;
			}
		}
		return $menu_group_routes;
	}
	
	public function get_groups_by_menu_id($menu_id) {
		$rows = $this->where('menu_id', $menu_id)->select();
		$rows = collection($rows)->toArray();
		return $rows;
	}
	
	public function get_routes_map_by_menu_ids($ids) {
		$rows = collection($this->where(['id' => ['in', $ids]])->select())->toArray();
		$routes_map = [];
		foreach ($rows as $row) {
			$route_names = trim($row['route_names']);
			if (!$route_names) continue;
			$route_arr = explode(',', $route_names);
			foreach ($route_arr as $route) {
				$routes_map[trim($route)] = true;
			}
		}
		return $routes_map;
	}
	
	public function get_all_groups() {
		$rows = $this->order('menu_id')->select();
		$rows = collection($rows)->toArray();
		foreach ($rows as &$row) {
			$row['menu_name'] = model('Menu')->get_name_by_id($row['menu_id']);
			$row['routes'] = model('Route')->get_routes_array_by_names($row['route_names']);
		}
		return $rows;
	}
	
	public function saveRouteGroup($data) {
		if (empty($data['id'])) {
			if (empty($data['menu_id'])) {
				return ['code' => -1, 'msg' => '请选择菜单'];
			}
			if (empty($data['name'])) {
				return ['code' => -1, 'msg' => '请填写操作名称'];
			}
			$condition = ['name' => $data['name'], 'menu_id' => $data['menu_id']];
			$route_group = $this->where($condition)->find();
			if ($route_group) {
				return ['code' => -1, 'msg' => '重复的数据'];
			}
			$result = $this->data($data)->isUpdate(false)->save();
			if (false === $result) {
				return ['code' => -1, 'msg' => '添加失败'];
			}
			$data['id'] = $this->getLastInsID();
		} else {
			$lines = $this->save($data, ['id', $data['id']]);
			if (false === $lines) {
				return ['code' => -1, 'msg' => '更新失败'];
			}
		}
			
		$data['menu_name'] = model('Menu')->get_name_by_id($data['menu_id']);
		$data['routes'] = model('Route')->get_routes_array_by_names($data['route_names']);
		return ['code' => 0, 'msg' => 'ok', 'data' => $data];
	}
	
	public function deleteRouteGroup($id) {
		$myInfo = $this->get($id)->toArray();
		if (!$myInfo) {
			return array('code' => -1, 'msg' => '数据不存在');
		}
		
		$lines = $this->destroy($id);
		if ($lines == false) {
			return array('code' => -1, 'msg' => '删除失败');
		}
		return array('code' => 0, 'msg' => '删除成功');
	}
}