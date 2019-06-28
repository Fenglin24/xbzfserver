<?php
namespace app\common\model;

use think\Model;

class Bak extends Model {
	// 默认写入时间戳
	protected $autoWriteTimestamp = 'datetime';
	// 重定义时间戳字段名
	protected $createTime = 'cdate';
	protected $updateTime = 'mdate';
	
	public function getPageList($condition = array()) {
		$pageSize = config('paginate.list_rows');

		// 为翻页按钮准备query参数
		$pageParam['query']['s'] = '/' . MODULE_NAME . '/' . CONTROLLER_NAME . '/' . ACTION_NAME;
		foreach ($condition as $key => $value) {
			$pageParam['query']["condition[{$key}]"] = $value;
		}

		if (@$condition['name']) {
			$condition['name'] = ['like', "%{$condition['name']}%"];
		}
		
		// dd($condition);
		$count = $this->where($condition)->count();
		$list = self::where($condition)->order('id desc')->paginate($pageSize, $count, $pageParam);
		
		return array(
			'page_size' => $pageSize,
			'count' => $count,
			'page' => $list->render(),
			'list' => $list,
		);
	}

	public function deldir($path){
	   //如果是目录则继续
	   if(is_dir($path)){
		    //扫描一个文件夹内的所有文件夹和文件并返回数组
		   $p = scandir($path);
		// dd($p);exit;

			   foreach($p as $val){
				    //排除目录中的.和..
				    if($val !="." && $val !=".."){
					    // echo $path.$val.'<br/>';

					     //如果是目录则递归子目录，继续操作
					     if(is_dir($path.$val)){
						      //子目录中操作删除文件夹和文件
						      $this->deldir($path.$val.'/');
						      //目录清空后删除空文件夹
						      @rmdir($path.$val.'/');
					     }else{
						      //如果是文件直接删除
						      unlink($path.$val);
					     }
					}
			    }
		   }
	}

    public function deleteBakData($id) {
    	$src = self::where('id', $id)->value('src');
    	if (file_exists($src)) {
    	 	unlink($src);
    	}
        $lines = self::where(['id' => $id])->delete();
        if (false === $lines) {
            return ['code' => -1, 'msg' => '数据删除失败'];
        }
        return ['code' => 0, 'msg' => 'ok', 'data' => $id];
    }
	public function saveBakData($data) {
		$now = date('Y-m-d H:i:s');
		$data['mdate'] = $now;
		if (isset($data['id']) && intval($data['id'] <= 0)) {
			unset($data['id']);
		}
		if (!isset($data['id'])) {
			
			$data['cdate'] = $now;
			$this->data = $data;
			$result = $this->save();
			if (false === $result) {
				return ['code' => -1, 'msg' => '添加数据失败'];
			}
			$data['id'] = $this->id;
		} else {
			$data['id'] = intval($data['id']);
			if ($data['id'] <= 0) {
				return ['code' => -1, 'msg' => 'id 必须大于0'];
			}
			$result = $this->save($data, ['id' => $data['id']]);
			if ($result === false) {
				return ['code' => -1, 'msg' => '修改数据失败'];
			}
		}
		return ['code' => 0, 'msg' => 'ok', 'data' => $data];
	}
	
}