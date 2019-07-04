<?php
namespace app\admin\controller;

class Route extends AdminController {
	public function _initialize() {
		parent::_initialize();
	}
	
	public function index() {
		$this->assign('title', '路由列表');
		$this->view->to_routes = model('Route')->get_routes_inside_table();
		$this->view->from_routes = $this->get_routes_outside_table($this->view->to_routes);
		return $this->fetch();
	}
	
	public function name() {
		$this->assign('title', '路由名称');
		$this->view->menus = model('Menu')->get_all_menu();
		$condition = $this->_get_search_condition();
		$this->view->pageList = model('Route')->getPageList($condition);
		return $this->fetch();
	}
	public function save_route() {
		$data = input('param.');
		$res = model('Route')->saveRoute($data);
		return json($res);
	}
	public function add_routes() {
		$routes = input('param.routes');
		$res = model('Route')->addRoutes($routes);
		return json($res);
	}
	
	public function del_routes() {
		$routes = input('param.routes');
		$res = model('Route')->delRoutes($routes);
		if ($res['code']) return json($res);
		$res['data'] = $this->get_routes_outside_table($res['data']);
		return json($res);
	}
	
	public function group() {
		$this->assign('title', '路由分组');
		$this->view->menus = model('Menu')->get_all_menu();
		$this->view->menu_routes = model('Route')->get_menu_route_group_by_menu();
		$this->view->groups = model('RouteGroup')->get_all_groups();
		return $this->fetch();
	}
	
	public function save_group() {
		$data = input('param.');
		$res = model('RouteGroup')->saveRouteGroup($data);
		return json($res);
	}
	
	public function del_group() {
		$id = input('param.id/d', 0);
		$res = model('RouteGroup')->deleteRouteGroup($id);
		return json($res);
	}
	
	private function get_routes_outside_table($exist_routes) {
		$modules = ['admin'];
		$routes = [];
		foreach ($modules as $module) {
			$module_routes = $this->getRoutesFromModule($module, ['Route', 'AdminController']);
			$routes = array_merge($routes, $module_routes);
		}
		$customer_routes = [];
		foreach ($routes as $route) {
			if (!in_array($route, $exist_routes)) {
				$customer_routes[] = $route;
			}
		}
		return $customer_routes;
	}	

	private function getRoutesFromModule($module, $filterController = []) {
		$routes = [];
		$files = $this->get_files_from_module($module, $filterController);
		foreach ($files as $fileObj) {
			$file_routes = $this->get_routes_from_file($module, $fileObj['controller'], $fileObj['file']);
			$routes = array_merge($routes, $file_routes);
		}
		return $routes;
	}
	
	private function get_files_from_module($module, $filterController = []) {
		if (empty($module)) return [];
		$module_path = APP_PATH . '/' . $module . '/controller/';
		if (!is_dir($module_path)) return [];
		$module_path .= '*.php';
		$arr_files = glob($module_path);
		$files = [];
		foreach ($arr_files as $file) {
			if (is_dir($file)) continue;
			$controllerName = basename($file, '.php');
			if (in_array($controllerName, $filterController)) {
				continue;
			}
			$files[] = ['file' => $file, 'controller' => $controllerName];
		}
		return $files;
	}
	
	private function get_routes_from_file($moduleName, $controllerName, $file) {
		if (!file_exists($file)) return [];
		$content = file_get_contents($file);
		$rule  = "/public[\s]+function[\s]+(\w+)\(/i";
		$match_num = preg_match_all($rule, $content, $matches);
		if ($match_num == 0) {
			return [];
		}
		$ignore_actions = ['_initialize', '__construct'];
		$routes = [];
		foreach ($matches[1] as $key => $actionName) {
			if (!in_array($actionName, $ignore_actions)) {
				$routes[] = '/' . strtolower($moduleName) . '/' . strtolower($controllerName) . '/' . $actionName;
			}
		}
		return $routes;
	}
}