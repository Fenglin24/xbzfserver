<?php
namespace app\admin\controller;

use app\common\model\Houses as HouseModel;

class Houses extends AdminController {
	public function _initialize() {
		parent::_initialize();
		error_reporting(E_ALL ^ E_NOTICE);
		$this->view->breadcrumb = array(
			array('name' => '房源管理'),
		);
	}
	public function index() {
		$this->view->title = '房源列表';
		$condition = $this->_get_search_condition();
		if (@$_GET['status'] == '') {
			
		} else {
			$condition['status'] = $_GET['status'];
		}
//		 dd($condition);
		// $condition['status'] = 0;
		$this->view->pageList = model('Houses')->getPageList($condition);
		$this->view->roleMap = model('Role')->get_role_map();
		if (@$_GET['status'] == '') {
			$condition['status'] = '';
		}
		if (!@$condition['city']) {
			$condition['city'] = '';
		}
		$this->view->condition = $condition;
		$this->view->citys = model('cate')->where('pid', 0)->order('id ASC')->select();
		return $this->fetch();
	}

	public function detail() {
		$this->view->title = '房源详情';
		$id = input('param.id');
		$house = model('Houses')->where('id', $id)->find();
		if (@$house['images']) {
			$house['images'] = explode(',', $house['images']);
		}
		// dd($house);
		$this->view->house = $house;

		return $this->fetch();
	}
	
	public function edit() {
//        $this->view->title = '添加房源';
//		return $this->_save();
        $this->view->breadcrumb = array(
            array('name' => '房源管理'),
            array('url' => '?s=admin/houses/index', 'name' => '房源管理'),
        );
        $id = input('get.id', 0, 'intval');
        if ($id > 0) {
            $houseModel = new HouseModel();
            $house = $houseModel->get_house_by_id($id);
            for ($i=0;$i<explode(',',$house.images).length;$i++){
                $this->assign([
                    'image'+$i  => explode(',',$house.images)[$i],
                ]);
                dump('image'+$i);
            }
            $this->assign([
                'house'  => $house,
                'title' => '房源更新',
            ]);
        } else {
            $this->assign([
                'news'  => [],
                'title' => '新增房源',
            ]);
        }
        return $this->fetch();
	}

    public function upload_thumbnail() {
        return $this->upload_image(getimagesize($_FILES['file']['tmp_name'])[0], getimagesize($_FILES['file']['tmp_name'])[1], '/uploads/houses');
    }

    private function upload_image($width, $height, $path) {
        $selector = $_POST['selector'];
        $res = uploadImageByTmpName($_FILES['file']['tmp_name'], $path, '', $width, $height);
        if ($res['code']) {
            return $res;
        }
        return ['code' => 0, 'msg' => '正常', 'data' => ['url' => $res['data'], 'selector' => $selector]];
    }

	public function update() {
		return $this->_save();
	}

	// This part is for backend add
	public function save(){
        $res = model('Houses')->saveHousesDataInternal($_POST);
        return $res;
    }


    // This part is for Api add
	private function _save() {
		$data = input('post.');
		$res = model('Houses')->saveHousesData($data);
		return $res;
	}
	
	public function delete() {
		$id = input('param.id', 0, 'intval');
		$res = model('Houses')->deleteHousesData($id);
		return $res;
	}

	public function deleteAll() {
		$id = input('param.id');
		$id = explode(',', $id);
		$condition['id'] = ['in', $id];
		$res = model('Houses')->where($condition)->delete();
		if ($res === false) {
			return ['code' => -1, 'msg' => '删除失败']; 
		}
		return ['code' => 0, 'msg' => '删除成功'];
	}

	public function xxAll() {
		$id = input('param.id');
		$id = explode(',', $id);
		$condition['id'] = ['in', $id];
		$res = model('Houses')->where($condition)->update(['status' => 2]);
		if ($res === false) {
			return ['code' => -1, 'msg' => '设置失败']; 
		}
		return ['code' => 0, 'msg' => '已下线'];
	}

	public function xx() {
		$id = input('param.id', 0, 'intval');
		$status = input('param.status');
		if ($status == 2) {
			$msg = '已下线';
		} else {
			$msg = '已上线';
		}
		$res = model('Houses')->where('id', $id)->update(['status' => $status]);
		if ($res === false) {
			return ['code' => -1, 'msg' => '设置失败'];
		}
		return ['code' => 0, 'msg' => $msg];
	}

	public function update_tag() {
		$data = input('param.');
		if ($data['value'] == '是') {
			$data['value'] = '否';
		} else {
			$data['value'] = '是';
		}
		
		$condition = [$data['key'] => $data['value']];
		if (@$data['key'] == 'top') {
			$condition['top_datetime'] = date('Y-m-d H:i:s');
		}
		// dd($condition);
		$res = model('Houses')->where('id', $data['id'])->update($condition);
		if ($res === false) {
			return ['code' => -1, 'msg' => '设置失败'];
		}
		return ['code' => 0, 'msg' => '设置成功'];
	}

	public function update_top($data) {
		if (@$data['key' == 'top']) {
			$condition = [$data['key'] => $data['value']];
			$condition['top_datetime'] = date('Y-m-d H:i:s');
				$res = model('Houses')->where('id', $data['id'])->update($condition);
			if ($res === false) {
				return ['code' => -1, 'msg' => '设置失败'];
			}
			return ['code' => 0, 'msg' => '设置成功'];
		}
	}
	
}