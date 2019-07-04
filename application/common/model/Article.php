<?php
namespace app\common\model;

use think\Model;

class Article extends Model {
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

		if (@$condition['title']) {
			$condition['title'] = ['like', "%{$condition['title']}%"];
		}

		$count = $this->where($condition)->count();
		$list = self::where($condition)->order('id desc')->paginate($pageSize, $count, $pageParam);

		return array(
			'page_size' => $pageSize,
			'count' => $count,
			'page' => $list->render(),
			'list' => $list,
		);
	}
    public function deleteArticleData($id) {
        $lines = self::where(['id' => $id])->delete();
        if (false === $lines) {
            return ['code' => -1, 'msg' => '数据删除失败'];
        }
        return ['code' => 0, 'msg' => 'ok', 'data' => $id];
    }
	public function saveArticleData($data) {
		$now = date('Y-m-d H:i:s');
		$data['mdate'] = $now;
		if (isset($data['id']) && intval($data['id'] <= 0)) {
			unset($data['id']);
		}
		if (!isset($data['id'])) {
			// if (empty($data['Articlename'])) {
			// 	return ['code' => -1, 'msg' => '请提供用户名'];
			// }
			// // if (empty($data['password'])) {
			// 	return ['code' => -1, 'msg' => '请提供密码'];
			// }
			// if (self::get(['Articlename' => $data['Articlename']])) {
			// 	return ['code' => -1, 'msg' => '用户名已存在'];
			// }
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
			// if (self::get(['Articlename' => $data['Articlename'], 'id' => ['neq', $data['id']]])) {
			// 	return ['code' => -1, 'msg' => '用户名已存在'];
			// }
			// if (isset($data['password']) && $data['password'] == '') {
			// 	unset($data['password']);
			// }
			$result = $this->save($data, ['id' => $data['id']]);
			if ($result === false) {
				return ['code' => -1, 'msg' => '修改数据失败'];
			}
		}
		return ['code' => 0, 'msg' => 'ok', 'data' => $data];
	}
	
}