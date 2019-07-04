<?php
/**
 * Created by PhpStorm.
 * User: Fengl
 * Date: 7/2/2019
 * Time: 6:14 PM
 */

namespace app\admin\controller;


use app\common\model\Agency as AgencyModel;
use app\common\model\AgencyCategory;

class Agency extends AdminController
{
        public function _initialize()
    {
        parent::_initialize();
    }
    public function index(){
        $this->view->breadcrumb = array(
            array('name' => '中介列表'),
        );
        $this->view->title = '中介列表管理';
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
        $pageList = model('Agency')->getPageList($condition);

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
            array('name' => '中介详情页'),
            array('url' => '?s=admin/agency/index', 'name' => '中介详情页'),
        );


        $id = input('get.id', 0, 'intval');
        if ($id > 0) {
            $AgencyModel = new AgencyModel();
            $Agency = $AgencyModel->get_Agency_by_id($id);
            $this->assign([
                'Agency'  => $Agency,
                'title' => '中介更新',
            ]);
        } else {
            $this->assign([
                'Agency'  => [],
                'title' => '中介管理',
            ]);
        }
        return $this->fetch();
    }
 
      public function detail() {
        $id = input('param.id');
        $this->view->title = '中介详情';
        $this->view->Agency = model('Agency')->where('id', $id)->find();
        return $this->fetch();
    }

    public function save() {
        $res = model('Agency')->saveAgency($_POST);
        return $res;
    }
  
      public function xx() {
        $id = input('param.id');
        $row = model('Agency')->where('id', $id)->find();
        if ($row['status'] == 0) {
            $data['status'] = 1;
        } else {
            $data['status'] = 0;
        }
        $res = model('Agency')->where('id', $id)->update($data);
        if ($res === false) {
            return ['code' => -1, 'msg' => '设置失败'];
        }
        return ['code' => 0, 'msg' => '设置成功'];
    }

      public function delete() {
        $res = model('Agency')->deleteAgency(input('param.id'));
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
}