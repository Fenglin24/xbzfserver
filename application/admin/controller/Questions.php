<?php
namespace app\admin\controller;

class Questions extends AdminController {
	public function _initialize() {
		parent::_initialize();
		$this->view->breadcrumb = array(
			array('name' => '常见问题'),
		);
	}
	public function index() {
		$this->view->title = '用户列表';
		$condition = $this->_get_search_condition();
		$condition['status'] = 0;
		$this->view->pageList = model('Questions')->getPageList($condition);
		$this->view->roleMap = model('Role')->get_role_map();
		return $this->fetch();
	}

	public function contact() {
		$this->view->title = '关于我们';
		$condition = $this->_get_search_condition();
		$condition['type'] = '关于我们';
		$this->view->pageList = model('Questions')->getPageList($condition);
		$this->view->roleMap = model('Role')->get_role_map();
		return $this->fetch();
	}

	public function live(){
		$this->view->title = '直播看房';
		$condition = $this->_get_search_condition();
		$condition['type'] = '直播看房';
		$this->view->pageList = model('Questions')->getPageList($condition);
		$this->view->roleMap = model('Role')->get_role_map();
		return $this->fetch();
	}

	public function sign() {
		$this->view->title = '房源申请';
		$condition = $this->_get_search_condition();
		$condition['type'] = '房源申请';
		$this->view->pageList = model('Questions')->getPageList($condition);
		$this->view->roleMap = model('Role')->get_role_map();
		return $this->fetch();
	}

	public function near() {
		$this->view->title = '周边服务';
		$condition = $this->_get_search_condition();
		$condition['type'] = '周边服务';
		$this->view->pageList = model('Questions')->getPageList($condition);
		$this->view->roleMap = model('Role')->get_role_map();
		return $this->fetch();
	}

	public function bolt() {
		$this->view->title = '闪电招租';
		$condition = $this->_get_search_condition();
		$condition['type'] = '闪电招租';
		$this->view->pageList = model('Questions')->getPageList($condition);
		$this->view->roleMap = model('Role')->get_role_map();
		return $this->fetch();
	}
    public function clean() {
        $this->view->title = '小宝清洁';
        $condition = $this->_get_search_condition();
        $condition['type'] = '小宝清洁';
        $this->view->pageList = model('Questions')->getPageList($condition);
        $this->view->roleMap = model('Role')->get_role_map();
        return $this->fetch();
    }
    public function sent() {
        $this->view->title = '接送机';
        $condition = $this->_get_search_condition();
        $condition['type'] = '接送机';
        $this->view->pageList = model('Questions')->getPageList($condition);
        $this->view->roleMap = model('Role')->get_role_map();
        return $this->fetch();
    }
    public function gas() {
        $this->view->title = '水电煤网';
        $condition = $this->_get_search_condition();
        $condition['type'] = '水电煤网';
        $this->view->pageList = model('Questions')->getPageList($condition);
        $this->view->roleMap = model('Role')->get_role_map();
        return $this->fetch();
    }
    public function decoration() {
        $this->view->title = '配置家具';
        $condition = $this->_get_search_condition();
        $condition['type'] = '配置家具';
        $this->view->pageList = model('Questions')->getPageList($condition);
        $this->view->roleMap = model('Role')->get_role_map();
        return $this->fetch();
    }

	public function platform() {
		$this->view->title = '平台声明';
		$condition = $this->_get_search_condition();
		$condition['type'] = '平台声明';
		$this->view->pageList = model('Questions')->getPageList($condition);
		$this->view->roleMap = model('Role')->get_role_map();
		return $this->fetch();
	}

	public function renting() {
		$this->view->title = '租房相关';
		$condition = $this->_get_search_condition();
		$condition['type'] = '租房';
		$this->view->pageList = model('Questions')->getPageList($condition);
		$this->view->roleMap = model('Role')->get_role_map();
		return $this->fetch();
	}

	public function agree() {
		$this->view->title = '房东协议';
		$condition = $this->_get_search_condition();
		$condition['type'] = '房东协议';
		$this->view->pageList = model('Questions')->getPageList($condition);
		$this->view->roleMap = model('Role')->get_role_map();
		return $this->fetch();
	}

	public function see() {
		$this->view->title = '看房服务';
		$condition = $this->_get_search_condition();
		$condition['type'] = '看房服务';
		$this->view->pageList = model('Questions')->getPageList($condition);
		$this->view->roleMap = model('Role')->get_role_map();
		return $this->fetch();
	}

	public function house() {
		$this->view->title = '房东相关';
		$condition = $this->_get_search_condition();
		$condition['type'] = '房东';
		// // dd($condition);
		// if (@$condition['title']) {
		// 	$condition['title'] = ['like', "%{$condition['title']}%"];
		// }
		// dd($condition);
		$this->view->pageList = model('Questions')->getPageList($condition);
		$this->view->roleMap = model('Role')->get_role_map();
		return $this->fetch();
	}

	public function add() {
		if (empty($_POST)) {
			$type = input('param.type');
			$this->view->type = $type;
			$this->view->title = '添加'. $type .'问题';
			if ($type == '房东' || $type == '租房') {
				$a = 1;
			} else {
				$a = 0;
			}
			$this->view->a = $a;
			return $this->fetch();
		}
		return $this->_save();
	}

	public function update() {
		if (empty($_POST)) {
			$type = input('param.type');
			$this->view->type = $type;
			$this->view->title = '编辑'. $type .'问题';
			$this->view->questions = model('Questions')->where('id', input('param.id'))->find();
			if ($type == '房东' || $type == '租房') {
				$a = 1;
			} else {
				$a = 0;
			}
			$this->view->a = $a;
			return $this->fetch();
		}
		return $this->_save();
	}

	public function save() {
		return $this->_save();
	}
	
	private function _save() {
		$data = input('post.');
		$res = model('Questions')->saveQuestionsData($data);
		return $res;
	}
	
	public function delete() {
		$id = input('param.id', 0, 'intval');
		$res = model('Questions')->deleteQuestionsData($id);
		return $res;
	}
	
}