<?php
namespace app\api\controller;

class Index extends \think\Controller {
  
     public function get_share_new() {
		// header("content-type","image/jpeg");
		$id = input('param.id');
		$access_token = $this->get_access_token();
		$url = "https://api.weixin.qq.com/wxa/getwxacodeunlimit?access_token=" . $access_token;
		$type = input('param.type');
		if ($type == "roommate") {
            $data['scene'] = 'r' . $id;
            $data['path'] = 'pages/roommateDetail/roommateDetail';
        }
		else{
            $data['scene'] = 'h' . $id;
            $data['path'] = 'pages/detail/detail';
        }
//        $data['scene'] = $id;

		$data['width'] = '430';
		$res = $this->http($url, json_encode($data),1);
		// var_dump($res);exit;
        if ($type == "roommate") {
            $path = 'uploads/qrcode/r' . $id . '.jpg';
        }else{
            $path = 'uploads/qrcode/h' . $id . '.jpg';
        }
		file_put_contents($path, $res);
		
	    $return['status_code'] = 2000;
	    $return['msg'] = 'ok';
	    $return['data'] = config('appurl').'/' . $path;
	    // dd($id);
	    // echo '<img src="'.$path.'" />';exit;
	    echo json_encode($return);exit;
	}
    
	public function get_share() {
		// header("content-type","image/jpeg");
		$id = input('param.id');
		$access_token = $this->get_access_token();
		$url = "https://api.weixin.qq.com/wxa/getwxacodeunlimit?access_token=" . $access_token;
//		$data['scene'] = 'house='.$id;
        $data['scene'] = $id;
		$data['path'] = 'pages/detail/detail';
		$data['width'] = '430';
		$res = $this->http($url, json_encode($data),1);
		// var_dump($res);exit;
		$path =  'uploads/qrcode/'.$id .'.jpg';
		file_put_contents($path, $res);
		
	    $return['status_code'] = 2000;
	    $return['msg'] = 'ok';
	    $return['data'] = config('appurl').'/' . $path;
	    // dd($id);
	    // echo '<img src="'.$path.'" />';exit;
	    echo json_encode($return);exit;
	}
    public function getForms() {
        $data = input('param.');
        $message = "
        <html>
        <head>
        <title>". $data['form']['name'] . "的一站式调查问卷</title>
        </head>
        <body>
        <table>
        <tr>
        <th>姓名</th>
        <th>". $data['form']['name'] . "</th>
        </tr>
        <tr>
        <th>性别</th>
        <th>". $data['form']['sex'] . "</th>
        </tr>
        <tr>
        <th>微信ID</th>
        <th>". $data['form']['wxid'] . "</th>
        </tr>
        <tr>
        <th>微信昵称</th>
        <th>". $data['form']['wxnickname'] . "</th>
        </tr>
        <tr>
        <th>电话</th>
        <th>". $data['form']['tel'] . "</th>
        </tr>
        <tr>
        <th>是否下签？</th>
        <th>". $data['form']['ifOffer'] . "</th>
        </tr>
        <tr>
        <th>签证日期是否超过12个月</th>
        <th>". $data['form']['is12'] . "</th>
        </tr>
        <tr>
        <th>申请人数</th>
        <th>". $data['form']['amount'] . "</th>
        </tr>
        <tr>
        <th>携带宠物？</th>
        <th>". $data['form']['ispet'] . "</th>
        </tr>
        <tr>
        <th>入住时间</th>
        <th>". $data['form']['date'] . "</th>
        </tr>
        <tr>
        <th>预算</th>
        <th>". $data['form']['budget'] . "</th>
        </tr>
        <tr>
        <th>房源类型</th>
        <th>". implode("|", $data['form']['type']) . "</th>
        </tr>
        <tr>
        <th>租期</th>
        <th>". $data['form']['length'] ."</th>
        </tr>
        <tr>
        <th>户型</th>
        <th>". implode("|", $data['form']['housetype']) ."</th>
        </tr>
        <tr>
        <th>对房屋其他要求</th>
        <th>". implode("|",$data['form']['requirements']) . "</th>
        </tr>
        <tr>
        <th>了解渠道</th>
        <th>". implode("|", $data['form']['fromwhere']) . "</th>
        </tr>
        </table>
        </body>
        </html>
        ";
        
        $res = \ExHelper\Email::sendEmail(
            'onepointmain123@gmail.com',
            $data['form']['name'], $message);
        if ($res === true) {
            $this->sucess('0', 'ok', $res);
        }
        var_dump($res);exit;
    }

	public function get_access_token() {
		$appid = config('wx.appid');
		$appsecret = config('wx.appsecret');
		$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$appid."&secret=".$appsecret;
		$res = file_get_contents($url);
		$res = json_decode($res, true);
		if (@$res['errcode']) {
			$return['status_code'] = '2001';
			$return['msg'] = '微信请求失败';
			$return['data'] = '';
			echo json_encode($return);exit;
		}
		// dd($res);
		return $res['access_token'];
	}



	// public function index() {
	// 	$this->view->title = '测试接口首页';
	// 	$this->view->userid = session('uid');
	// 	return $this->fetch();
	// }
	public function get_rock_picture() {
		$list = model('Ad')->where('status', 1)->order('id DESC')->select();
		$list = $this->init_html_list_content($list);
		$this->sucess('0', 'ok', $list);
	}

	public function get_rock_detail() {
		$id = input('param.id');
		$row = model('Ad')->where('id', $id)->select();
		$row = $this->init_html_list_content($row);
		$this->sucess('0', 'ok', @$row[0]);
	}

	public function get_poi() {
		$keyword = input('param.keyword');
		$city = input('param.city');
		$url = 'https://apis.map.qq.com/ws/place/v1/search?boundary=region('.$city.',0)&page_size=20&page_index=1&keyword='.$keyword.'&orderby=_distance&key='.config('qqkey');
		$res = file_get_contents($url);
		$res = json_decode($res, true);
		if ($res['status'] == 0) {
			$this->sucess('0', 'ok', $res['data']);
		}
		var_dump($res);exit;
	}

	public function get_city() {
		$x = input('param.x');
		$y = input('param.y');
		file_put_contents('get_city.txt', date('Y-m-d H:i:s').'经度：' . $y . '纬度：' . $x .PHP_EOL, FILE_APPEND);
		if ($x == '') {
			$this->sucess('-1', '维度不能为空');
		}
		if ($y == '') {
			$this->sucess('-1', '经度不能为空');
		}
		$url = "https://apis.map.qq.com/ws/geocoder/v1/?location={$x},{$y}&get_poi=1&key=".config('qqkey');
		$res = file_get_contents($url);
		// echo $res;exit;
		$res = json_decode($res, true);
		if ($res['status'] == 0) {
			if (@$res['result']['address_component']['nation'] =='澳大利亚') {
				$row = @$res['result']['address_component']['ad_level_3'];
			} else {
				$row = @$res['result']['address_component']['city'];

			}
			if (!@$row) {
				$city = model('cate')->where('pid', 0)->order('id DESC')->value('name');
			} else {
				$condition['name'] = ['like', "%{$row}%"];
				$condition['pid'] = 0;
				$city  = model('cate')->where($condition)->value('name');
				if (!@$city) {
					$city = model('cate')->where('pid', 0)->order('id ASC')->value('name');
				}
			}
			
			$this->sucess('0', 'ok', @$city);
		}
		$this->sucess('-1', '获取失败', $res);
	}
	public function get_cate() {
		$id = input('param.id');
		if (!@$id) {
			$id = 0;
			$list = model('Cate')->where(['pid' => $id])->field('id,pid,name,hot')->order('id ASC')->select();
		} else {
			//传入城市id
			//获取当前城市的热门区域
			$hot['pid'] = $id;
			$hot['hot'] = '是';
			$hot['type'] = 1;
			$hots = model('Cate')->where($hot)->field('id,pid,name,hot')->order('oseq ASC')->select();
//            $hots = model('Cate')->where($hot)->field('id,pid,name,hot')->select();
			//所有当前城市的的区域
			$con['pid'] = $id;
			$con['type'] = 1;
			$all =  model('Cate')->where($con)->field('id,pid,name,hot')->order('oseq ASC')->select();
//            $all =  model('Cate')->where($con)->field('id,pid,name,hot')->select();
			//获取当前校区
			$con['type'] = 2;
			$school = model('Cate')->where($con)->field('id,pid,name,hot')->order('oseq ASC')->select();
//            $school = model('Cate')->where($con)->field('id,pid,name,hot')->select();
			$list['hot'] = $hots;
			$list['all'] = $all;
			$list['school'] = $school;
		}
		$this->sucess('0', 'ok', $list);
	}

	public function get_news_list() {
		$condition = [];
		$list = model('News')->get_page_news_list($condition);
		$list = $this->init_html_list_content($list);
		$this->sucess('0', 'ok', $list);
	}

    public function get_around_list() {
        $condition = [];
        $list = model('Around')->get_page_around_list($condition);
        $list = $this->init_html_list_content($list);
        $this->sucess('0', 'ok', $list);
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
		model('Houses')->where('id', $id)->update(['view' => $row['view'] + 1]);
		$h = model('Houses')->get_house_detail_by_id($id);
		$this->sucess('0', 'ok', @$h);
	}


	//首页推荐房源
	public function get_index_houses() {
		$data = input('param.');
		$condition = [];
		if (@$data['city']) {
			$condition['h.city'] = ['like', "%{$data['city']}%"];
		}
		//过滤推荐字段
		// dd($condition);
		file_put_contents('get_index_houses.txt', json_encode($condition));
		$condition['h.status'] = 1;
		$condition['h.tj'] = '是';
		$list = model('Houses')->get_page_house_list($condition);
		$list = $this->init_html_list_houses($list);
		$this->sucess('0', 'ok', $list);
	}

	public function get_index_top_houses() {
		$condition['h.status'] = 1;
		$condition['h.tj'] = '是';
		$condition['h.top'] = '是';
		$list = model('Houses')->get_page_house_top_list($condition);
		$list = $this->init_html_list_houses($list);
		$this->sucess('0', 'ok', $list);
	}


	public function init_html_list_houses($list) {
		foreach ($list as &$row) {
			$row = $this->init_html_house($row);
			$row = $this->init_near_all($row);
			$row = $this->init_remark_detail($row);
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

	public function get_search_houses() {
		$data = input('param.');
		$condition = [];
		
		if (@$data['city']) {
			$condition['h.city'] = ['like', "%{$data['city']}%"];
		}
		if (@$data['area']) {
			$condition['h.area'] = $data['area'];//区域检索
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
		//过滤推荐字段
		// dd($condition);
		$condition['h.status'] = 1;
		$list = model('Houses')->get_page_search_list($condition, @$data['keyword']);
		$list = $this->init_html_list_houses($list);
		$this->sucess('0', 'ok', $list);
	}

	//post curl 请求参数
	function http($url, $data = NULL, $json = false)
{
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
	if (!empty($data)) {
	if($json && is_array($data)){
	$data = json_encode( $data );
	}
	curl_setopt($curl, CURLOPT_POST, 1);
	curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
	if($json){ //发送JSON数据
	curl_setopt($curl, CURLOPT_HEADER, 0);
	curl_setopt($curl, CURLOPT_HTTPHEADER,
	array(
	'Content-Type: application/json; charset=utf-8',
	'Content-Length:' . strlen($data))
	);
	}
	}
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	$res = curl_exec($curl);
	// var_dump($res);exit;
	$errorno = curl_errno($curl);

	if ($errorno) {
		return array('errorno' => false, 'errmsg' => $errorno);
	}
	curl_close($curl);
	return $res;
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


	public function login() {
		$data = input('param.');
		if (!@$data['nickname']) {
			$this->sucess(-1, '昵称不能为空');
		}
		if (!@$data['openid']) {
			$this->sucess('-1', 'openid不能为空');
		}
		if (!@$data['avaurl']) {
			$this->sucess('-1', '头像不能为空');
		}

		//登录
		$user = model('User')->where('openid', $data['openid'])->find();
		if (!empty($user)) {
			// $res = model
		} else {
			//保存张哈
			$res = model('User')->save_user_wx_data($data);
			if ($res['code']) {
				$this->sucess('-1', '账号信息保存失败,请联系管理员');
			}
			$user = $res['data'];
		}

		//bltoken 添加更新
		$bltoken = model('Bltoken')->save_bltoken_user_id($user['id']);
		if ($bltoken['code']) {
			$this->sucess('-1', '登录失败');
		}
		$return['user'] = $user;
		$return['bltoken'] = $bltoken['data'];
		$this->sucess('0', 'ok', $return);
		dd($bltoken);
	}

	public function get_code() {
		$data = input('param.');
		if (!isset($data['code']) || !$data['code']) {
            $this->sucess(-2,"code 为空");
        }
		$res = $this->code2Session($data['code']);
		if (@$res['errcode']) {
			$this->sucess('-1', $res['errmsg'] . $res['errcode']);
		}
		$this->sucess('0', 'ok', $res);
	}

	public function code2Session($code) {
		$appid = config('wx.appid');
		$appsecret = config('wx.appsecret');
		$url = "https://api.weixin.qq.com/sns/jscode2session?appid=". $appid ."&secret=". $appsecret ."&js_code=". $code ."&grant_type=authorization_code";
		// echo $url;
		$res = file_get_contents($url);
		$res = json_decode($res, true);
		// var_dump($res);exit;
		return $res;
	}

	public function ajaxReturn($arr) {
		echo json_encode($arr);
		exit;
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
    
    //调取中介数据
    public function get_agency_list() {
        $list = model('Agency')->where('status','1')->select();
        $list = $this->init_html_list_content($list);
        $this->sucess('0', 'ok', $list);
    }
  
      //调取学生公寓数据
      public function get_apartment_list() {
        $list = model('Apartment')->where('status','1')->select();
        $list = $this->init_html_list_content($list);
        $this->sucess('0', 'ok', $list);
    }
}