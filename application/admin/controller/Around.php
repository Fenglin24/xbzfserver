<?php
/**
 * Created by thinkphp5.
 * Author   :   Hyder
 */

namespace app\admin\controller;

use app\common\model\Around as AroundModel;
use app\common\model\AroundCategory;

/**
 * Class Around                       新闻管理
 * @package app\admin\controller
 */
class Around extends AdminController {
    public function _initialize() {
        parent::_initialize();
    }

    public function index(){
        $this->view->breadcrumb = array(
            array('name' => '周边服务列表'),
        );
        $this->view->title = '周边服务列表管理';
        $condition = input('param.condition/a', array());
        foreach ($condition as $key => $value) {
            if ($value === '') {
                unset($condition[$key]);
            }
        }
        if (@$_GET['status'] == '') {
            // $condition['status'] = '';
        } else {
            $condition['status'] = $_GET['status'];
        }

        // dd($condition);
        $pageList = model('Around')->getPageList($condition);

        $this->assign([
            'condition' => $condition,
            'pageList'  => $pageList,
        ]);
        if (@$_GET['status'] == '') {
            $condition['status'] = '';
        }
        $this->view->condition = $condition;
        return $this->fetch();
    }

    public function edit(){
        $this->view->breadcrumb = array(
            array('name' => '周边服务详情页'),
            array('url' => '?s=admin/around/index', 'name' => '周边服务详情页'),
        );


        $id = input('get.id', 0, 'intval');
        if ($id > 0) {
            $AroundModel = new AroundModel();
            $Around = $AroundModel->get_Around_by_id($id);
            $this->assign([
                'Around'  => $Around,
                'title' => '周边服务更新',
            ]);
        } else {
            $this->assign([
                'Around'  => [],
                'title' => '周边服务管理',
            ]);
        }
        return $this->fetch();
    }

    public function detail() {
        $id = input('param.id');
        $this->view->title = '周边服务详情';
        $this->view->Around = model('Around')->where('id', $id)->find();
        return $this->fetch();
    }

    public function save() {
        $res = model('Around')->saveAround($_POST);
        return $res;
    }

    public function xx() {
        $id = input('param.id');
        $row = model('Around')->where('id', $id)->find();
        if ($row['status'] == 0) {
            $data['status'] = 1;
        } else {
            $data['status'] = 0;
        }
        $res = model('Around')->where('id', $id)->update($data);
        if ($res === false) {
            return ['code' => -1, 'msg' => '设置失败'];
        }
        return ['code' => 0, 'msg' => '设置成功'];
    }

    public function delete() {
        $res = model('Around')->deleteAround(input('param.id'));
        return $res;
    }

    public function upload_thumbnail() {
        return $this->upload_image(400, 300, config("THUMBNAIL_PATH"));
    }

    private function upload_image($width, $height, $path) {
        $selector = $_POST['selector'];
        $res = uploadImageByTmpName($_FILES['file']['tmp_name'], $path, '', $width, $height);
        if ($res['code']) {
            return $res;
        }
        return ['code' => 0, 'msg' => '正常', 'data' => ['url' => $res['data'], 'selector' => $selector]];
    }

    public function cates() {
        $this->view->breadcrumb = array(
            array('name' => '站内新闻'),
        );
        $this->view->title = '新闻分类';
        $this->view->cates = model('AroundCategory')->get_cates_tree();
        return $this->fetch();
    }

    public function get_cates() {
        $data = model('AroundCategory')->get_cates_tree();
        foreach ($data as $rootcid => &$cate) {
            $cate['articlenum'] = \think\Db::name('Ad')->where('cid', $rootcid)->count();
            if ($cate['child']) {
                foreach ($cate['child'] as $childcid => &$child_cate) {
                    $child_cate['articlenum'] = \think\Db::name('Ad')->where('cid', $childcid)->count();
                }
                unset($child_cate);
            }
        }
        unset($cate);
        return array('code' => 0, 'msg' => 'ok', 'data' => $data);
    }

    public function save_cate() {
        $res = model('AroundCategory')->saveCate($_POST);
        return $res;
    }

    public function merge_cate() {
        $cid = input('param.cid', 0, 'intval');
        $pid = input('param.pid', 0, 'intval'); // 其实应该叫new_cid，而非pid
        $res = model('AroundCategory')->mergeCate($cid, $pid);
        return $res;
    }

    public function delete_cate() {
        $cid = input('param.cid', 0, 'intval');
        $res = model('AroundCategory')->deleteCate($cid);
        return json($res);
    }
}
