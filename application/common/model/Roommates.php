<?php
namespace app\common\model;

use think\Model;

class Roommates extends Model {
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
		if (@$condition['price']) {
			$condition['price'] = ['like', "%{$condition['price']}%"];
		}
		if (@$condition['title']) {
			$condition['title'] = ['like', "%{$condition['title']}%"];
		}
		if (@$condition['city']) {
			$condition['city'] = ['like', "%{$condition['city']}%"];
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
    public function deleteRoommatesData($id) {
        $lines = self::where(['id' => $id])->delete();
        if (false === $lines) {
            return ['code' => -1, 'msg' => '数据删除失败'];
        }
        return ['code' => 0, 'msg' => 'ok', 'data' => $id];
    }
	public function saveRoommatesData($data) {
		$now = date('Y-m-d H:i:s');
		$data['mdate'] = $now;
		file_put_contents('_save_roommates_model.txt', json_encode($data));
		if (isset($data['id']) && intval($data['id'] <= 0)) {
			unset($data['id']);
		}
		if (!isset($data['id'])) {
			// if (empty($data['Housesname'])) {
			// 	return ['code' => -1, 'msg' => '请提供用户名'];
			// }
			// if (empty($data['password'])) {
			// 	return ['code' => -1, 'msg' => '请提供密码'];
			// }
			// if (self::get(['Housesname' => $data['Housesname']])) {
			// 	return ['code' => -1, 'msg' => '用户名已存在'];
			// }
			$data['cdate'] = $now;
            //添加找室友帖子编号new
            $data['dsn'] = $this->gen_roomates_dsn();
            $this->data = $data;
            //dd($data);

			//访问频繁
			if (cache('add_roommate_'.$data['user_id'])) {
			    $left_second = time() - cache('add_roommate_'.$data['user_id']);
                return ['code' => -1, 'msg' => '访问过于频繁，请'.$left_second.'s后再试'];
            }

			//防并发操作 解决ID重复的问题 #A#
            $fp = fopen(RUNTIME_PATH."lock.txt", "w+");
            if(flock($fp,LOCK_EX | LOCK_NB))
            {
                //..处理订单
                flock($fp,LOCK_UN);

                $result = $this->save();

                if ($result) {
                    cache('add_roommate_'.$data['user_id'], time(),10);
                }
            }else{
                fclose($fp);
                return ['code' => -1, 'msg' => '系统繁忙，请稍后再试'];
            }
            fclose($fp);
            //防并发操作 #A#

			if (false === $result) {
				return ['code' => -1, 'msg' => '添加数据失败'];
			}
			$data['id'] = $this->id;
			
		} else {
            $data['id'] = intval($data['id']);
            if ($data['id'] <= 0) {
                return ['code' => -1, 'msg' => 'id 必须大于0'];
            }
            // if (self::get(['Housesname' => $data['Housesname'], 'id' => ['neq', $data['id']]])) {
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

	//重新生成找室友编码
	private function gen_roomates_dsn()
    {
        // 找室友编号开始为B
        $dsn = 'B';
        $count = $this->count();
        $s = '';
        for ($i = 1; $i < 10 - strlen($count); $i++) {
            $s .= '0';
        }
        $count++;
        $dsn .= $s.$count;
        return $dsn;
    }
	
}