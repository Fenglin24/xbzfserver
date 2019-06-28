<?php

/**
 * @Author: Qian weidong
 * @Date:   2018-10-09 08:46:19
 * @Last Modified by:   Qian weidong
 * @Last Modified time: 2019-01-10 16:33:43
 */
namespace app\api\controller;

class BaseController extends \think\Controller {
	protected $_uid = 0;
	protected $user;
	
	public function _initialize() {
		// 允许跨域
		$origin = isset($_SERVER['HTTP_ORIGIN'])? $_SERVER['HTTP_ORIGIN'] : ''; 
	    header("Access-Control-Allow-Origin: {$origin}");
	    header("Access-Control-Allow-Credentials: true");
	    header("Access-Control-Allow-Methods: 'GET, POST'");
	    $res = $this->check_bltoken();
	    if (!$res['sucess']) {
	    	$this->ajaxReturn($res);
	    }
	    $this->user = $this->get_user_info_by_bltoken();
	}

	public function check_bltoken() {
		$blToken = input('param.blToken');
		//验证bltoken是否过期，有效，是否存在
		$res = model('Bltoken')->check_bltoken_is_right($blToken);
		return $res;
	}

	public function get_user_info_by_bltoken() {
		$blToken = input('param.blToken');
		$user = model('User')->get_user_info_by_bltoken($blToken);
		// if ($user['status'] == 1) {
		// 	// return ['code' => -1, 'msg' => '您的账号已禁用,请联系管理员'];
		// 	$return['status_code'] = 2001;
		// 	$return['msg'] = '你已被平台拉黑,暂不能访问';
		// 	$return['sucess'] = false;
		// 	$this->ajaxReturn($return);
		// }
		// var_dump($user);
		file_put_contents('userinfo-BaseController.text', json_encode($user));
		return $user;
	}

	public function ajaxReturn($arr) {
		echo json_encode($arr);
		exit;
	}

	//post curl 请求参数
	public function post_data($url, $post_data) {
		//初始化
	    $curl = curl_init();
	    //设置抓取的url
	    curl_setopt($curl, CURLOPT_URL, $url);
	    //设置头文件的信息作为数据流输出
	    curl_setopt($curl, CURLOPT_HEADER, 1);
	    //设置获取的信息以文件流的形式返回，而不是直接输出。
	    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	    //设置post方式提交
	    curl_setopt($curl, CURLOPT_POST, 1);
	    //设置post数据
	    curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
	    //执行命令
	    $data = curl_exec($curl);
	    //关闭URL请求
	    curl_close($curl);
	    //显示获得的数据
	   	return $data;
	}

	function postUrlForCalling($url, $reqParams){
		$ch=curl_init();
		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt($ch,CURLOPT_HEADER,0);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch,CURLOPT_POST,1);
		curl_setopt($ch,CURLOPT_POSTFIELDS,$reqParams);
		$data = curl_exec($ch);
		curl_close($ch);
		$data = json_decode($data, true);
		return $data;
	}


	public function sucess($code, $msg = '', $data = '') {
		$arr['code'] = $code;
		$arr['msg'] = $msg;
		$arr['data'] = $data;
		echo json_encode($arr);exit;
	}

	public function init_html_content($row) {
		if (@$row['content']) {
			$row['content'] = str_ireplace('src="', 'src="'.config('appurl'), $row['content']);
		}
		if (@$row['thumbnail']) {
			$row['thumbnail'] = config('appurl').$row['thumbnail'];
		}
		
		return $row;
	}

	public function init_html_list_content($list) {
		foreach ($list as &$row) {
			$row = $this->init_html_content($row);
		}
		return $list;
	}

	public function init_html_list_houses($list) {
		foreach ($list as &$row) {
			$row = $this->init_html_house($row);
			$row = $this->init_near_all($row);
			$row = $this->init_remark_detail($row);
		}
		return $list;
	}
    
    public function init_html_list_roommates($list) {
		foreach ($list as &$row) {
			$row = $this->init_html_roommate($row);
			//$row = $this->init_near_all($row);
			//$row = $this->init_remark_detail($row);
		}
		return $list;
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

	public function init_html_house($row) {
		if (@$row['thumnail']) {
			$row['thumnail'] = config('appurl').$row['thumnail'];
		}
		if (@$row['images'] && !is_array($row['images'])) {
		// var_dump($row['images']);

			$arr = explode(',', $row['images']);
			foreach ($arr as &$val) {
				$val = config('appurl') . $val;
			}
			$row['images'] = $arr;
		}
		
		return $row;
	}
  
    public function init_html_roommate($row) {
        if (@$row['thumnail']) {
            $row['thumnail'] = config('appurl').$row['thumnail'];
        }
        return $row;
    }

	public function get_price_condition_by_price($price) {
		if ($price == 1) {
			return ['between', [0, 200]];
		}
		if ($price == 2) {
			return ['between', [200, 350]];
		}
		if ($price == 3) {
			return ['between', [350, 500]];
		}
		if ($price == 4) {
			return ['between', [500, 650]];
		}
		if ($price == 5) {
			return ['between', [650, 800]];
		}
		if ($price == 6) {
			return ['between', [800, 950]];
		}
		if ($price == 7) {
			return ['between', [950, 1100]];
		}
		if ($price == 8 ) {
			return ['>', 1100];
		}
	}

}