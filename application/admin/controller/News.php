<?php
/**
 * Created by thinkphp5.
 * Author   :   Hyder
 */

namespace app\admin\controller;

use app\common\model\News as NewsModel;
use app\common\model\NewsCategory;

/**
 * Class News                       新闻管理
 * @package app\admin\controller
 */
class News extends AdminController {
	public function _initialize() {
		parent::_initialize();
	}

	public function index(){
		$this->view->breadcrumb = array(
			array('name' => '站内新闻'),
		);
		$this->view->title = '新闻管理';
		$condition = input('param.condition/a', array());
		foreach ($condition as $key => $value) {
			if ($value === '') {
				unset($condition[$key]);
			}
		}
		$pageList = model('News')->getPageList($condition);
		$catesMap = model('NewsCategory')->get_cates_map();
		$cates    = model('NewsCategory')->get_cates_tree();
		
		$this->assign([
			'condition' => $condition,
			'pageList'  => $pageList,
			'catesMap'     => $catesMap,
			'cates' => $cates,
		]);
		return $this->fetch();
	}

	public function edit(){
		$this->view->breadcrumb = array(
			array('name' => '站内新闻'),
			array('url' => '?s=admin/news/index', 'name' => '新闻管理'),
		);
		$cates = model('NewsCategory')->get_cates_tree();
		$this->assign([
			'cates' =>  $cates,
		]);

		$id = input('get.id', 0, 'intval');
		if ($id > 0) {
			$newsModel = new NewsModel();
			$news = $newsModel->get_news_by_id($id);
			$this->assign([
				'news'  => $news,
				'title' => '新闻更新',
			]);
		} else {
			$this->assign([
				'news'  => [],
				'title' => '新增新闻',
			]);
		}
		return $this->fetch();
	}

	public function save() {
		$res = model('News')->saveNews($_POST);
		return $res;
	}

	public function delete() {
		$res = model('News')->deleteNews(input('param.id'));
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
		$this->view->cates = model('NewsCategory')->get_cates_tree();
		return $this->fetch();
	}

	public function get_cates() {
        $data = model('NewsCategory')->get_cates_tree();
        foreach ($data as $rootcid => &$cate) {
        	$cate['articlenum'] = \think\Db::name('News')->where('cid', $rootcid)->count();
        	if ($cate['child']) {
        		foreach ($cate['child'] as $childcid => &$child_cate) {
        			$child_cate['articlenum'] = \think\Db::name('News')->where('cid', $childcid)->count();
        		}
        		unset($child_cate);
        	}
        }
        unset($cate);
		return array('code' => 0, 'msg' => 'ok', 'data' => $data);
    }

    public function save_cate() {
        $res = model('NewsCategory')->saveCate($_POST);
		return $res;
    }

     public function merge_cate() {
     	$cid = input('param.cid', 0, 'intval');
		$pid = input('param.pid', 0, 'intval'); // 其实应该叫new_cid，而非pid
        $res = model('NewsCategory')->mergeCate($cid, $pid);
		return $res;
    }

    public function delete_cate() {
     	$cid = input('param.cid', 0, 'intval');
    	$res = model('NewsCategory')->deleteCate($cid);
		return json($res);
    }

    public function xx() {
    	$id = input('param.id');
    	$row = model('News')->where('id', $id)->find();
    	if ($row['status'] == 0) {
    		$data['status'] = 1;
    	} else {
    		$data['status'] = 0;
    	}
    	$res = model('News')->where('id', $id)->update($data);
    	if ($res === false) {
    		return ['code' => -1, 'msg' => '设置失败'];
    	}
    	return ['code' => 0, 'msg' => '设置成功'];
    }
}
