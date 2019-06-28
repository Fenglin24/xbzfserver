<?php

/**
 * @Author: Qian weidong
 * @Date:   2018-12-12 08:59:43
 * @Last Modified by:   Qian weidong
 * @Last Modified time: 2019-01-14 12:38:58
 */
namespace app\api\controller;

class Houses extends BaseController {

	public function get_area($id, $x = 39.8802147, $y =116.415794) {
		$url = "https://apis.map.qq.com/ws/staticmap/v2/?center={$x},{$y}&zoom=12&size=750*375&maptype=roadmap&key=". config('qqkey');
		$res = file_get_contents($url);
		// echo $res;exit;
		file_put_contents('uploads/area/'.$id.'.png', $res);
		model('Houses')->where('id', $id)->update(['area_img' => config('appurl').'/uploads/area/'.$id.'.png']);
	}	

	public function get_area_id($id, $x, $y) {
		$url = "https://maps.google.cn/maps/api/staticmap?zoom=13&size=750x375&maptype=roadmap&markers=color:red%7Clabel:A%7C{$x},{$y}&key=AIzaSyCo3_jFbxNdOHSVQTBmCin0W4rmxiUFOQY";
		$res = file_get_contents($url);
		file_put_contents('uploads/area/'.$id.'.png', $res);
		model('Houses')->where('id', $id)->update(['area_img' => config('appurl').'/uploads/area/'.$id.'.png']);
	}

	public function get_area_house() {
		$x = input('params.x');
		$x = input('params.x');
	}

	public function get_house_detail() {
		$id = input('param.id');
		if (!@$id) {
			$this->sucess('-1', '房源id不能为空');
		}
		$row = model('Houses')->where('id', $id)->find();
		if (empty($row)) {
			$this->sucess('-1', '该房源已经不存在了');
		}
		if (!@$row['area_img']) {
			$this->get_area_id($row['id'], $row['x'], $row['y']);

		}
		model('Houses')->where('id', $id)->update(['view' => $row['view'] + 1]);
		$h = model('Houses')->get_house_detail_by_id($id);

		//0：未收藏；1：已收藏
		$h['collection_status'] = model('Collection')->get_house_collection_by_user_id($this->user->id, $id);
        //小程序显示空格
		$h['content'] = str_replace(" ","&nbsp;",$h['content']);

		$h = $this->init_near_all($h);
		$h = $this->init_html_content($h);
		$h = $this->init_remark_detail($h);
		
		$this->sucess('0', 'ok', @$h);
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
		if (@$val['home']) {
			$a = explode(',', $val['home']);
			foreach ($a as $v) {
		// dd($arr[$v]);

				if (@$arr[$v]) {
					$service[$v] = $arr[$v];
				}
			}
		}
		if (@$val['sation']) {
			$a = explode(',', $val['sation']);
			foreach ($a as $v) {
				if (@$arr[$v]) {
					$service[$v] = $arr[$v];
				}
			}
		}
		$val['service'] = $service;
		return $val;
	}


	public function get_my_house_list() {
		$type = input('param.type');
		$condition = [];
		$condition['h.user_id'] = $this->user->id;
		if ($type === '0') {
			$condition['h.status'] = $type;
		} else if ($type == 1) {
			$condition['h.status'] = $type;
		}
		// dd($condition);
 		$list = model('Houses')->get_page_house_list($condition);
 		$list = $this->init_html_list_content($list);
		$this->sucess('0', 'ok', $list);
	}
  
    public function get_my_house_list_by_id() {
        $type = input('param.type');
        $data = input('param.');
        $condition = [];
        $condition['h.user_id'] = @$data['userid'];
        if ($type === '0') {
            $condition['h.status'] = $type;
        } else if ($type == 1) {
            $condition['h.status'] = $type;
        }
        //dd($condition);
        $list = model('Houses')->get_page_house_list($condition);
        $list = $this->init_html_list_content($list);
        $this->sucess('0', 'ok', $list);
    }

	public function update_status() {
		$id = input('param.id');
		$status = input('param.status');
		// dd($status != 1 && $status != 2);
		if ($status != 1 && $status != 2) {
			$this->sucess('-1', '参数错误');
		}
		$row = model('Houses')->where('id', $id)->find();
		if (empty($row)) {
			$this->sucess('-1', '该房源已经不存在了');
		}
		$data['status'] = $status;
		if ($status == 1) {
			$data['publish_date'] = date('Y-n-d H:i:s');
		}
		$res = model('Houses')->where('id', $id)->update($data);
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
		$res = model('Houses')->deleteHousesData($id);
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
		file_put_contents('_save_house.txt', json_encode($data));
		$data['user_id'] = $this->user->id;
		if (@$data['id']) {
          	$a = model('Houses')->where('id', $data['id'])->value('status');
          	$data['status'] = $a;
        }
		
		$ressult = $this->check_data($data);
		if ($ressult['code']) {
			$this->sucess('-1', $ressult['msg']);
		}
		
		$res = model('Houses')->saveHousesData($data);
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
			$this->sucess('-1', '租金请填写数字');
		}
		if (!@$data['address']) {
			$this->sucess('-1', '地址不能为空');
		}
		if (!@$data['source']) {
			$this->sucess('-1', '房屋来源不能为空');
		}
		if (!@$data['type']) {
			$this->sucess('-1', '出租方式不能为空');
		}
		if (!@$data['sex']) {
			$this->sucess('-1', '性别限制不能为空');
		}
		if (!@$data['pet']) {
			$this->sucess('-1', '宠物不能为空');
		}
//		if (!@$data['smoke']) {
//			$this->sucess('-1', '吸烟不能为空');
//		}
		if (!@$data['bill']) {
			$this->sucess('-1', 'Bill相关不能为空');
		}
//		if (!@$data['deposit']) {
//			$this->sucess('-1', '押金不能为空');
//		}
//		if (!@is_numeric($data['deposit'])) {
//			$this->sucess('-1', '押金请填写数字');
//		}
		if (!@$data['live_date']) {
			$this->sucess('-1', '可入住时间不能为空');
		}
		if (!@$data['lease_term']) {
			$this->sucess('-1', '租期不能为空');
		}
		if (!@$data['house_type']) {
			$this->sucess('-1', '房屋类型不能为空');
		}
		if (!@$data['house_room']) {
			$this->sucess('-1', '户型不能为空');
		}
		if (!@$data['furniture']) {
			$this->sucess('-1', '家具不能为空');
		}
		if (!@$data['car']) {
			$this->sucess('-1', '车位不能为空');
		}
		if (!@$data['toilet']) {
			$this->sucess('-1', '卫生间不能为空');
		}
		if (!@$data['home']) {
			// $this->sucess('-1', '设施不能为空');
		}
		if (!@$data['sation']) {
			// $this->sucess('-1', '交通不能为空');
		}
		// if (!@$data['province']) {
		// 	$this->sucess('-1', '省不能为空');
		// }
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
		if (!@$data['tel']) {
			// $this->sucess('-1', '电话不能为空');
		}
		if (!@$data['wchat']) {
			// $this->sucess('-1', '微信号不能为空');
		}
        if (@$data['email'] != "" && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $this->sucess('-1', '电子邮箱不合法');
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

	public function init_remark_detail($row) {
		$arr = [];
		if (@$row['free_in'] == '是') {
			$arr['free_in'] = '免费申请';
		}
		if (@$row['free_view'] == '是') {
			$arr['free_view'] = '支持订金宝';
		}
		if (@$row['tj'] == '是') {
			$arr['tj'] = '推荐房源';
		}
		if (@$row['check'] == '是') {
			$arr['check'] = '已验证房源';
		}
		$row['remark'] = $arr;
		return $row;
	}

	public function init_html_list_content($list) {
		foreach ($list as &$row) {
			$row = $this->init_html_content($row);
			$row = $this->init_near_all($row);
			$row = $this->init_remark_detail($row);
		}
		return $list;
	}


	public function get_search_houses() {
		$data = input('param.');
		$condition = [];
		
		if (@$data['city']) {
			$condition['h.city'] = ['like', "%{$data['city']}%"];
		}
		if (@$data['area']) {
            //$condition['h.area'] = $data['area'];//区域检索
			$condition['h.area'] = ['like', "%{$data['area']}%"];//区域检索
		}
        if (@$data['school']) {
            $condition['h.school'] = $data['school'];//校区检索
        }
		if (@$data['type']) {
			$condition['h.type'] = $data['type'];//出租方式
		}
		if (@$data['price']) {
			$condition['h.price'] = $this->get_price_condition_by_price($data['price']);//租金
		}
		if (@$data['source']) {
			$condition['h.source'] = $data['source'];//房屋来源
		}
		if (@$data['sex']) {
			$condition['h.sex'] = $data['sex'];//性别限制
		}
		if (@$data['pet']) {
			$condition['h.pet'] = $data['pet'];//宠物
		}
		if (@$data['smoke']) {
			$condition['h.smoke'] = $data['smoke'];//吸烟
		}
		if (@$data['bill']) {
			$condition['h.bill'] = $data['bill'];//宠物
		}
		if (@$data['house_type']) {
			$condition['h.house_type'] = $data['house_type'];//房屋类型
		}
		if (@$data['house_room']) {
			$condition['h.house_room'] = $data['house_room'];//户型
		}
		if (@$data['home']) {
			$condition['h.home'] = $this->get_condition_by_column($data['home']);//设施
		}
		if (@$data['furniture']) {
			$condition['h.furniture'] = $data['furniture'];//家具
		}
		if (@$data['car']) {
			$condition['h.car'] = $data['car'];//车位
		}
		if (@$data['toilet']) {
			$condition['h.toilet'] = $data['toilet'];//卫生间
		}
		if (@$data['sation']) {
			$condition['h.sation'] = $this->get_condition_by_column($data['sation']);//交通

		}
		if (@$data['lease_term']) {
			$condition['h.lease_term'] = $data['lease_term'];//租期
		}
		$order = 'h.top desc,h.top_datetime DESC, ';
		if (@$data['order']) {
			$order .= $this->_get_order($data['order']);
		} else {
			$order .= 'h.id DESC';
		}
		//过滤推荐字段
		// dd($condition);
		$condition['h.status'] = 1;
		if (@$data['keyword']) {
			model('History')->save(['name' => $data['keyword'], 'user_id' => @$this->user->id]);
		}
		$list = model('Houses')->get_page_search_list($condition, $order, @$data['keyword']);
		$list = $this->init_html_list_houses($list);
		foreach ($list as &$row) {
			//0：未收藏；1：已收藏
			$row['collection_status'] = model('Collection')->get_house_collection_by_user_id($this->user->id, $row['id']);
		}
		$this->sucess('0', 'ok', $list);
	}

	private function _get_order($order) {
		if ($order == 1) {
			return 'h.cdate DESC, h.id DESC';  //发布时间倒序
		} elseif ($order == 2) {
			return 'h.price DESC';
		} elseif ($order == 3) {
			return 'h.price ASC';
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