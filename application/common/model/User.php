<?php
namespace app\common\model;

use think\Model;

class User extends Model {
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

		if (@$condition['real_name']) {
			$condition['real_name'] = ['like', "%{$condition['real_name']}%"];
		}
		if (@$condition['nickname']) {
			$condition['nickname'] = ['like', "%{$condition['nickname']}%"];
		}
		if (@$condition['tel']) {
			$condition['tel'] = ['like', "%{$condition['tel']}%"];
		}
		// dd($condition);
		$count = $this->where($condition)->count();
		$list = self::where($condition)->order('id desc')->paginate($pageSize, $count, $pageParam);
		foreach ($list as &$row) {
			$row['count'] = model('Houses')->where('user_id', $row['id'])->count();
			$row['collection'] = model('Collection')->where('user_id', $row['id'])->count();
		}
		return array(
			'page_size' => $pageSize,
			'count' => $count,
			'page' => $list->render(),
			'list' => $list,
		);
	}
    public function deleteUserData($id) {
        $lines = self::where(['id' => $id])->delete();
        if (false === $lines) {
            return ['code' => -1, 'msg' => '数据删除失败'];
        }
        return ['code' => 0, 'msg' => 'ok', 'data' => $id];
    }
	public function saveUserData($data) {
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