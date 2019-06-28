<?php
namespace app\api\controller;

class Api extends \think\Controller {
	public function getApiList() {
		$api['index'] = $this->index();
		return array('baseURL' => BASE_URL, 'api' => $api);
	}
	public function index() {
		$api['get_region'] = [
			'name' => '获取行政区域',
			'info' =>'幼儿园初期就北京市，后期可能增加上海、广州等城市',
			'params' => [
			],
			'returns' => [
				'region_code' => '行政区域代码',
				'region_name' => '行政区域名称',
			],
		];
		return [
			'name' => '首页功能',
			'module' => 'index',
			'api' => $api,
		];
	}


	public function upload() {
		// 移动注册信息里除了公司LOGO以外的其它图片
		$filesDIR = '/uploads/houses';
		$res = (new \app\common\helper\File)->moveUploadFileAndGetMoreData([], $filesDIR);
		if ($res['code']) {
			$this->sucess('-1', $res['msg']);
		}
		if (@$res['data']['img']) {
			$file = substr($res['data']['img'], 1);
            // dd($file);
            $image = \think\Image::open($file);
          
            // 给原图左上角添加透明度为50的水印并保存alpha_image.png
            // $image->water('./1.png',\think\Image::WATER_NORTHWEST,100)->save($file);
            $image->water('./1.png',\think\Image::WATER_SOUTHEAST,100)->save($file);
            // $image->water('./1.png',\think\Image::WATER_EAST,100)->save($file);
            // $image->water('./1.png',\think\Image::WATER_SOUTHWEST,100)->save($file);

		}
		if (@$res['data']['thumbnail']) {
			$file = substr($res['data']['thumbnail'], 1);
            // dd($file);
            $image = \think\Image::open($file);
          
            // 给原图左上角添加透明度为50的水印并保存alpha_image.png
            // $image->water('./1.png',\think\Image::WATER_NORTHWEST,100)->save($file);
            //$image->water('./1.png',\think\Image::WATER_SOUTHEAST,100)->save($file);
            $image->save($file);
            // $image->water('./1.png',\think\Image::WATER_EAST,100)->save($file);
            // $image->water('./1.png',\think\Image::WATER_SOUTHWEST,100)->save($file);

		}
		$this->sucess('0', 'ok', $res['data']);
	}
  
    public function uploadnowatermark() {
        // 移动注册信息里除了公司LOGO以外的其它图片
        $filesDIR = '/uploads/houses';
        $res = (new \app\common\helper\File)->moveUploadFileAndGetMoreData([], $filesDIR);
        if ($res['code']) {
            $this->sucess('-1', $res['msg']);
        }
        if (@$res['data']['img']) {
            $file = substr($res['data']['img'], 1);
            // dd($file);
            $image = \think\Image::open($file);
            $image->save($file);
        }
		if (@$res['data']['thumbnail']) {
			$file = substr($res['data']['thumbnail'], 1);
            // dd($file);
            $image = \think\Image::open($file);
            $image->save($file);
		}
        $this->sucess('0', 'ok', $res['data']);
    }
  
	public function water($url) {

	}

	public function get_services() {
		$list = model('Service')->order('id DESC')->select();
		$this->sucess('0', 'ok', $list);
	}

	public function get_hot() {
		$data = input('param.');
		if (!@$data['city']) {
			$this->sucess('-1', '城市不能为空');
		}
		// dd($_POST);
		$condition['city'] = $data['city'];
		
		$condition['status'] = 1;
		file_put_contents('api_get_hot.txt', json_encode($condition));
		// dd($condition);
		$list = model('Keyword')->where($condition)->field('name')->order('id DESC')->select();
		$this->sucess('0', 'ok', $list);
	}

	public function sucess($code, $msg = '', $data = '') {
		$arr['code'] = $code;
		$arr['msg'] = $msg;
		$arr['data'] = $data;
		echo json_encode($arr);exit;
	}

	public function get_dsn() {
		$id = input('param.id');
		model('Houses')->saveHousesData(['id' => $id]);
	}
}