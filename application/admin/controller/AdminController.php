<?php
namespace app\admin\controller;

class AdminController extends \think\Controller {
	public function _initialize() {
		$admin_session = model('Admin')->get_login_session();
		$userAuthMap   = model('Menu')->get_user_authority_map($admin_session['role_info']['authority']);
		$this->view->baseURL      = BASE_URL;
		$this->view->admin        = $admin_session;
		$this->view->breadcrumb   = [];
		$this->view->role_id      = $this->view->admin['role_id'];
		$this->view->admin_id     = $this->view->admin['id'];
		$this->view->config       = model('Config')->get_all_config();
		$this->view->userAuthMap  = $userAuthMap;
		$this->view->requestRoute = model('Route')->get_request_route();
		$this->view->menu         = model('Menu')->get_user_menu($admin_session['role_id'], $userAuthMap);
		$this->check_authority($admin_session['role_id'], $userAuthMap);
	}
	
	private function check_authority($role_id, $userAuthMap) {
		$result = model('Role')->check_authority($role_id, $userAuthMap);
		if ($result == true) {
			return;
		}
		// 用户没有权限，根据是否为ajax请求，或者APP请求（必然带auth_md5）以不同方式返回
		if (\think\Request::instance()->isAjax() || input('post.auth_md5', '')) {
			exit(json_encode(['code' => 222222, 'msg' => '您没有权限']));
		} else {
			$this->view->title = "您没有权限";
			$this->view->msg = "对不起，您没有权限！您可以联系管理员索要权限。";
			echo $this->fetch('./msg');
			exit;
		}
	}

	protected function _get_search_condition() {
		$condition = input('get.condition/a', array());
		$this->view->condition = $condition;
		$condition = array_filter($condition);
		return $condition;
	}
}