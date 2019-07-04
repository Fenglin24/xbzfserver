<?php
namespace app\common\model;

use think\Model;

class Route extends Model {
	//默认写入时间戳
	protected $autoWriteTimestamp = 'datetime';
	//重定义时间戳字段名
	protected $createTime = null;
	protected $updateTime = null;
	
	public function getPageList($condition = array()) {
		$pageSize = config('paginate.list_rows');
		// 为翻页按钮准备query参数
		$pageParam['query']['s'] = '/' . MODULE_NAME . '/' . CONTROLLER_NAME . '/' . ACTION_NAME;
		foreach ($condition as $key => $value) {
			$pageParam['query']["condition[{$key}]"] = $value;
		}

		if (@$condition['name']) {
			$condition['name'] = ['like', "%{$condition['name']}%"];
		}
		if (@$condition['op_name']) {
			$condition['op_name'] = ['like', "%{$condition['op_name']}%"];
		}

		$count = $this->where($condition)->count();
		$list = self::where($condition)->order('menu_id desc, name asc')->paginate($pageSize, $count, $pageParam);
		foreach ($list as &$row) {
			$row['menu_name'] = model('Menu')->get_name_by_id($row['menu_id']);
		}

		return array(
			'page_size' => $pageSize,
			'count' => $count,
			'page' => $list->render(),
			'list' => $list,
		);
	}

	public static function is_ignore_route($route) {
		return self::where('name', $route)->value('ignore') == 1;
	}
	
	public function get_routes_array_by_names($names) {
		if (trim($names) == '') return [];
		$names = explode(',', $names);
		foreach ($names as &$name) {
			$name = trim($name);
		}
		$condition['name'] = ['in', $names];
		$routes = collection($this->where($condition)->order('name')->select())->toArray();
		return $routes;
	}
	
	public function get_menu_route_group_by_menu() {
		$menus = model('Menu')->get_all_menu();
		$menu_routes = [];
		foreach ($menus as $rootMenu) {
			if (!$rootMenu['children']) {
				$item = [
					'menu_id' => $rootMenu['id'],
					'menu_name' => $rootMenu['name'],
				];
				$item['routes'] = $this->get_routes_by_menu_id($rootMenu['id']);
				$menu_routes[] = $item;
				continue;
			} 
			foreach ($rootMenu['children'] as $childMenu) {
				$item = [
					'menu_id' => $childMenu['id'],
					'menu_name' => $childMenu['name'],
				];
				$item['routes'] = $this->get_routes_by_menu_id($childMenu['id']);
				$menu_routes[] = $item;
			}
		}
		// dump($menu_routes);
		return $menu_routes;
	}
	private function get_routes_by_menu_id($menu_id) {
		$rows = $this->where('menu_id', $menu_id)->order('name asc')->select();
		$rows = collection($rows)->toArray();
		return $rows;
	}
	public function get_all_routes() {
		$routes = collection($this->order('name')->select())->toArray();
		foreach ($routes as $key => $route) {
			$routes[$key]['menu_name'] = model('Menu')->get_name_by_id($route['menu_id']);
		}
		return $routes;
	}
	public function get_routes_inside_table() {
		$routes = $this->order('name')->column('name');
		return $routes;
	}
	
	public function saveRoute($data) {
		if (empty($data['name'])) {
			return ['code' => -1, 'msg' => '请填写路由'];
		}
		$route = $this->get($data['name']);
		if ($route) {
			$lines = $this->save($data, ['name' => $data['name']]);
			if (false === $lines) {
				return ['code' => -1, 'msg' => '更新失败'];
			}
		} else {
			$result = $this->data($data)->isUpdate(false)->save();
			if (false === $result) {
				return ['code' => -1, 'msg' => '添加失败'];
			}
		}
		$data['menu_name'] = model('Menu')->get_name_by_id($data['menu_id']);
		return ['code' => 0, 'msg' => 'ok', 'data' => $data];
	}
	public function addRoutes($routes) {
		$data = [];
		foreach ($routes as $route) {
			$data[] = [
				'name' => $route
			];
		}
		if (!$data) {
			return ['code' => -1, 'msg' => '请传递数据'];
		}
		$rows = $this->saveAll($data, false);
		if (!$rows) {
			return ['code' => -1, 'msg' => '保存失败'];
		}
		$routes = $this->get_routes_inside_table();
		return ['code' => 0, 'msg' => 'ok', 'data' => $routes];
	}
	
	public function delRoutes($routes) {
		$lines = $this->destroy($routes);
		if (false === $lines) {
			return ['code' => -1, 'msg' => '删除失败'];
		}
		$exist_routes = $this->get_routes_inside_table();
		return ['code' => 0, 'msg' => 'ok', 'data' => $exist_routes];
	}

	public static function get_request_route() {
		$request = \think\Request::instance();
		$module_name = strtolower($request->module());
		$controller_name = strtolower($request->controller());
		$action_name = strtolower($request->action());
		$route = "/{$module_name}/{$controller_name}/{$action_name}";
		return $route;
	}
}