<?php

/**
 * @Author: qian 
 * @Date:   2018-12-11 23:28:58
 * @Last Modified by:   qian
 * @Last Modified time: 2018-12-18 23:39:25
 */
namespace app\api\controller;

class News extends BaseController {

	public function get_news_user_list() {
		$condition = [];
		$condition['user_id'] = $this->user->id;
		$list = model('NewsUser')->get_news_user_list($condition);
		$list = $this->init_html_list_content($list);
		$this->sucess('0', 'ok', $list);
	}
	

	public function get_news_detail() {
		$id = input('param.id');
		//增加查看次数
		$r = model('News')->where('id', $id)->find();
		if (empty($r)) {
			$this->sucess('-1', '该文章不存在');
		}
		model('News')->save(['view' => $r['view'] + 1], ['id' => $id]);
		$row = model('News')->where('id', $id)->field('id,title,thumbnail,summary,collection,view,content,cdate')->find();
		$row['collection_status'] = model('NewsUser')->get_collection_news_user($this->user->id, $id);
		$row = $this->init_html_content($row);
		$this->sucess('0', 'ok', $row);
	}

    public function get_around_detail() {
        $id = input('param.id');
        //增加查看次数
        $r = model('Around')->where('id', $id)->find();
        if (empty($r)) {
            $this->sucess('-1', '该文章不存在');
        }
        model('Around')->save(['view' => $r['view'] + 1], ['id' => $id]);
        $row = model('Around')->where('id', $id)->field('id,title,thumbnail,summary,collection,view,content,cdate')->find();
        $row = $this->init_html_content($row);
        $this->sucess('0', 'ok', $row);
    }

	public function sucess($code, $msg = '', $data = '') {
		$arr['code'] = $code;
		$arr['msg'] = $msg;
		$arr['data'] = $data;
		echo json_encode($arr);exit;
	}

	public function save() {
		$id = input('param.id');
		if (!@$id) {
			$this->sucess('-1', '该锦囊已经不存在了');
		}
		$condition['user_id'] = $this->user->id;
		$condition['news_id'] = $id;
		$row = model('NewsUser')->where($condition)->find();
		if (empty($row)) {
			//添加收藏
			$res = model('NewsUser')->save($condition);
			if ($res == false) {
				$this->sucess('-1', '收藏失败');
			}
			$a = model('News')->where('id', $id)->find();
			model('News')->where('id', $id)->update(['collection' => $a['collection'] + 1]);
			$this->sucess('0', '收藏成功', '1');
		} else {
			$res = model('NewsUser')->where($condition)->delete();
			if ($res === false) {
				$this->sucess('-1', '取消失败');
			}
			$this->sucess('0', '取消成功', '2');
		}
	}

}