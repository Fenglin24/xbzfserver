<?php
namespace app\admin\behavior;

class Auth {
	public function run(&$params) {
		$ignoreControllerMap = [
			'Index' => true,
		];
		$request = \think\Request::instance();
		define('MODULE_NAME', $request->module());
		define('CONTROLLER_NAME', $request->controller());
		define('ACTION_NAME', $request->action());
		if (key_exists(CONTROLLER_NAME, $ignoreControllerMap)) {
			if (!(CONTROLLER_NAME == 'Index' && ACTION_NAME == 'index')) {
				return true;
			}
		}
		$aid = session('id');
		if (!$aid) {
			$this->show_invalid_result('请登录后操作');
		} 
	}
	
	static private function show_invalid_result($msg) {
		if (\think\Request::instance()->isAjax() || input('post.auth_md5', '')) {
			exit(json_encode(['code' => 111111, 'msg' => $msg]));
		} else {
			redirect('/login');
		}
		exit;
	}
}