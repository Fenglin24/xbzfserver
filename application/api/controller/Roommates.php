<?php

/**
 * @Author: Qian weidong
 * @Date:   2018-12-12 08:59:43
 * @Last Modified by:   Qian weidong
 * @Last Modified time: 2019-01-14 12:38:58
 */
namespace app\api\controller;

class Roommates extends BaseController {

	public function get_roommates_detail() {
		$id = input('param.id');
		if (!@$id) {
			$this->sucess('-1', '找室友帖子id不能为空');
		}
		$row = model('Roommates')->where('id', $id)->find();
		if (empty($row)) {
			$this->sucess('-1', '该找室友帖子已经不存在了');
		}
		model('Roommates')->where('id', $id)->update(['view' => $row['view'] + 1]);
		$r = model('Roommates')->get_roommates_detail_by_id($id);

		//0：未收藏；1：已收藏
		//$r['collection_status'] = model('Collection')->get_roommates_collection_by_user_id($this->user->id, $id);
        //小程序显示空格
		$r['content'] = str_replace(" ","&nbsp;",$r['content']);

		$r = $this->init_near_all($r);
		$r = $this->init_html_content($r);
		// This part is ignored from find roommates
//		$h = $this->init_remark_detail($h);
		
		$this->sucess('0', 'ok', @$r);
	}

	public function init_near_all($val) {
		
		$service = [];
		$arr = [
			'游泳池' => config('appurl').'/uploads/icon/youyongchi.png',
			'健身房' => config('appurl').'/uploads/icon/jianshen.png',
			'车位' => config('appurl').'/uploads/icon/chewei.png',
			'火车站' => config('appurl').'/uploads/icon/huochezhan.png',
			'电车站' => config('appurl').'/uploads/icon/gongjiaozhan.png',
			'免费电车' => config('appurl').'/uploads/icon/ic_tram_px.png',
		];
//		if (@$val['home']) {
//			$a = explode(',', $val['home']);
//			foreach ($a as $v) {
//		// dd($arr[$v]);
//
//				if (@$arr[$v]) {
//					$service[$v] = $arr[$v];
//				}
//			}
//		}
//		if (@$val['sation']) {
//			$a = explode(',', $val['sation']);
//			foreach ($a as $v) {
//				if (@$arr[$v]) {
//					$service[$v] = $arr[$v];
//				}
//			}
//		}
//		$val['service'] = $service;
		return $val;
	}

    public function get_my_roommates_list() {
        $condition = [];
        $condition['user_id'] = $this->user->id;
        //dd($condition);
        $list = model('Roommates')->get_page_roommates_list($condition);
        $list = $this->init_html_list_content($list);
        //dd($sql);
        $this->sucess('0', 'ok', $list);
    }
    public function get_my_roommates_list_by_id() {
        $data = input('param.');
        $condition = [];
        $condition['user_id'] = @$data['userid'];
        //dd($condition);
        $list = model('Roommates')->get_page_roommates_list($condition);
        $list = $this->init_html_list_content($list);
        //dd($sql);
        $this->sucess('0', 'ok', $list);
    }

	public function update_status() {
		$id = input('param.id');
		$status = input('param.status');
		// dd($status != 1 && $status != 2);
		if ($status != 1 && $status != 2) {
			$this->sucess('-1', '参数错误');
		}
		$row = model('Roommates')->where('id', $id)->find();
		if (empty($row)) {
			$this->sucess('-1', '该室友帖已经不存在了');
		}
		$data['status'] = $status;
		if ($status == 1) {
			$data['publish_date'] = date('Y-n-d H:i:s');
		}
		$res = model('Roommates')->where('id', $id)->update($data);
		if ($res === false) {
			if ($status == 1) {
				$msg = '发布失败';
			} else {
				$msg = '下线失败';
			}
			$this->sucess('-1', $msg);
		}
		if ($status == 1) {
		    $msg = '发布成功';
		} else {
			$msg = '下线成功';
		}
		$this->sucess('0', $msg);
	}

	public function delete() {
		$id = input('param.id');
		$res = model('Roommates')->deleteRoommatesData($id);
		if ($res['code']) {
			$this->sucess('-1', $res['msg']);
		}
		$this->sucess('0', $res['msg']);
	}
	
	public function add() {
		$this->_save();
	}

	public function update() {
		$this->_save();
	}
	
	private function _save() {
		$data = input('post.');
		file_put_contents('_save_roommates.txt', json_encode($data));
		$data['user_id'] = $this->user->id;
		if (@$data['id']) {
          	$a = model('Roommates')->where('id', $data['id'])->value('status');
          	$data['status'] = $a;
        }

		//$ressult = $this->check_data($data);
		//if ($ressult['code']) {
		//	$this->sucess('-1', $ressult['msg']);
		//}
        if(sizeof($data['collectedhouses'])>0){
            $data['collectedhouses'] = join(',', $data['collectedhouses']);
        }
        //dd(sizeof($data['collection']));
		$res = model('Roommates')->saveRoommatesData($data);
		if ($res['code']) {
			$this->sucess('-1', $res['msg']);
		}
		//修改用户信息
//		$condition['real_name'] = $data['real_name'];
//		$condition['tel'] = $data['tel'];
//		$condition['wchat'] = $data['wchat'];
        //只能status关联，其他不能动
		$condition['status'] = 1;
		model('User')->where('id', $this->user->id)->update($condition);
		$this->sucess('0', $res['msg'], $res['data']);
	}

	private function check_data($data) {
		if (!@$data['title']) {
			$this->sucess('-1', '房源名称不能为空');
		}
		if (!@$data['price']) {
			$this->sucess('-1', '租金不能为空');
		}
		if (!@is_numeric($data['price'])) {
			$this->sucess('-1', '期望租金请填写数字');
		}

		if (!@$data['sex']) {
			$this->sucess('-1', '性别限制不能为空');
		}
		if (!@$data['pet']) {
			$this->sucess('-1', '宠物不能为空');
		}
		if (!@$data['smoke']) {
			$this->sucess('-1', '吸烟不能为空');
		}

		if (!@$data['live_date']) {
			$this->sucess('-1', '可入住时间不能为空');
		}
		if (!@$data['lease_term']) {
			$this->sucess('-1', '租期不能为空');
		}

		if (!@$data['city']) {
			$this->sucess('-1', '市不能为空');
		}
		if (!@$data['area']) {
			$this->sucess('-1', '区域不能为空');
		}
		if (!@$data['thumnail']) {
			$this->sucess('-1', '封面图片不能为空');
		}
		if (!@$data['images']) {
			$this->sucess('-1', '详情图片不能为空');
		}
		if (!@$data['content']) {
			$this->sucess('-1', '描述不能为空');
		}
		if (!@$data['real_name']) {
			$this->sucess('-1', '姓名不能为空');
		}
	}

	public function init_html_content($row) {
		if (@$row['thumnail']) {
			$row['thumnail'] = config('appurl').$row['thumnail'];
		}

		if (@$row['images']) {
			$arr = explode(',', $row['images']);
			foreach ($arr as &$val) {
				$val = config('appurl') . $val;
			}
			$row['images'] = $arr;
		}
		return $row;
	}

	public function init_html_list_content($list) {
		foreach ($list as &$row) {
			$row = $this->init_html_content($row);
			//$row = $this->init_near_all($row);
			//$row = $this->init_remark_detail($row);
		}
		return $list;
	}


	public function get_search_roommates() {
		$data = input('param.');
		$condition = [];
		
		if (@$data['city']) {
			$condition['r.city'] = ['like', "%{$data['city']}%"];
		}
		if (@$data['area']) {
			$condition['r.area'] = ['like', "%{$data['area']}%"];//区域检索
		}
        if (@$data['school']) {
            $condition['r.school'] = $data['school'];//校区检索
        }
//		if (@$data['price']) {
//			$condition['r.price'] = $this->get_price_condition_by_price($data['price']);//租金
//		}
		if (@$data['sex']) {
			$condition['r.sex'] = $data['sex'];//性别限制
		}
		if (@$data['pet']) {
			$condition['r.pet'] = $data['pet'];//宠物
		}
		if (@$data['smoke']) {
			$condition['r.smoke'] = $data['smoke'];//吸烟
		}
//		if (@$data['bill']) {
//			$condition['r.bill'] = $data['bill'];//宠物
//		}

		if (@$data['lease_term']) {
			$condition['r.lease_term'] = $data['lease_term'];//租期
		}

		$order = 'r.top desc, ';
		if (@$data['order']) {
			$order .= $this->_get_order($data['order']);
		} else {
			$order .= 'r.id DESC';
		}
		//过滤推荐字段
        //dd($condition);
		$condition['r.status'] = 1;
//		if (@$data['keyword']) {
//			model('History')->save(['name' => $data['keyword'], 'user_id' => @$this->user->id]);
//		}
		$list = model('Roommates')->get_page_search_list($condition, $order, @$data['keyword']);
		$list = $this->init_html_list_roommates($list);
		foreach ($list as &$row) {
			//0：未收藏；1：已收藏
			$row['collection_status'] = model('Collectionr')->get_roommates_collection_by_user_id($this->user->id, $row['id']);
		}
		$this->sucess('0', 'ok', $list);
	}

	private function _get_order($order) {
		if ($order == 1) {
			return 'r.cdate DESC, r.id DESC';  //发布时间倒序
		} elseif ($order == 2) {
			return 'r.price DESC';
		} elseif ($order == 3) {
			return 'r.price ASC';
		}
	}

    private function get_condition_by_column($value) {
        $arr = explode(',', $value);
        if (count($arr)) {
            return ['like', "%{$arr[0]}%"];
        }
        $tmp = [];
        foreach ($arr as $v) {
            $tmp[] = ['like', "%{$v}%"];
        }
        $tmp[] = 'and';
        return $tmp;
    }
	

}