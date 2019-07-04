<?php
/**
 * Created by PhpStorm.
 * User: Fengl
 * Date: 7/2/2019
 * Time: 7:14 PM
 */

namespace app\admin\controller;
use app\common\model\Apartment as ApartmentModel;

class Apartment extends AdminController
{

    public function _initialize() {
        parent::_initialize();
    }

    public function index(){
        $this->view->breadcrumb = array(
            array('name' => '学生公寓列表'),
        );
        $this->view->title = '学生公寓管理';
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
        $pageList = model('Apartment')->getPageList($condition);

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
            array('name' => '学生公寓详情页'),
            array('url' => '?s=admin/apartment/index', 'name' => '学生公寓详情页'),
        );


        $id = input('get.id', 0, 'intval');
        if ($id > 0) {
            $ApartmentModel = new ApartmentModel();
            $Apartment = $ApartmentModel->get_Apartment_by_id($id);
            $this->assign([
                'Apartment'  => $Apartment,
                'title' => '学生公寓更新',
            ]);
        } else {
            $this->assign([
                'Apartment'  => [],
                'title' => '学生公寓管理',
            ]);
        }
        return $this->fetch();
    }

    public function detail() {
        $id = input('param.id');
        $this->view->title = '学生公寓详情';
        $this->view->Apartment = model('Apartment')->where('id', $id)->find();
        return $this->fetch();
    }

    public function save() {
        $res = model('Apartment')->saveApartment($_POST);
        return $res;
    }

    public function xx() {
        $id = input('param.id');
        $row = model('Apartment')->where('id', $id)->find();
        if ($row['status'] == 0) {
            $data['status'] = 1;
        } else {
            $data['status'] = 0;
        }
        $res = model('Apartment')->where('id', $id)->update($data);
        if ($res === false) {
            return ['code' => -1, 'msg' => '设置失败'];
        }
        return ['code' => 0, 'msg' => '设置成功'];
    }

    public function delete() {
        $res = model('Apartment')->deleteApartment(input('param.id'));
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