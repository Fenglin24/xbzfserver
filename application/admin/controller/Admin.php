<?php
namespace app\admin\controller;

class Admin extends AdminController {
	public function _initialize() {
		parent::_initialize();
		$this->view->breadcrumb = array(
			array('name' => '管理员/权限'),
		);
	}
	public function index() {
		$this->view->title = '管理员列表';
		$condition = $this->_get_search_condition();
		$this->view->pageList = model('Admin')->getPageList($condition);
		$this->view->roleMap = model('Role')->get_role_map();
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
		$res = model('admin')->saveAdminData($data);
		return $res;
	}
	
	public function delete() {
		$id = input('param.id', 0, 'intval');
		$res = model('admin')->deleteAdminData($id);
		return $res;
	}
	
	public function modify_password() {
		$res = model('admin')->modifyPassword($_POST);
		return $res;
	}
	
	public function modify_nick() {
		$res = model('admin')->modifyNick($_POST);
		return $res;
	}
	
	public function menu() {
		$this->view->title = '菜单列表';
		$this->view->menus = model('Menu')->get_all_menu();
		$this->view->parentCodeList = model('Menu')->get_parent_code_list();
		return $this->fetch();
	}
	
	public function menu_save() {
		$res = model('Menu')->saveMenu($_POST);
		return $res;
	}

	public function menu_delete() {
		$id = input('param.id', 0, 'intval');
		$res = model('Menu')->deleteMenu($id);
		return $res;
	}
	
	public function role() {
		$this->assign('title', '角色列表');
		$this->view->menu_group_routes = model('RouteGroup')->get_menu_group_routes();
		$this->view->pageList = model('Role')->getPageList();
		return $this->fetch();
	}
	
	public function role_save() {
		$res = model('role')->saveRoleData($_POST);
		return $res;
	}
	
	public function role_delete() {
		$res = model('role')->deleteRoleData(input('param.id'));
		return $res;
	}
	
	public function get_all_menu() {
		$res = model("Menu")->getAllMenuList();
		return $res;
	}
}