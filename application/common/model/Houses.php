<?php
namespace app\common\model;

use think\Model;

class Houses extends Model {
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

    /**
     * 获取房源详细信息
     *
     * @param $id       新闻ID
     *
     * @return array    是否获取到房源信息json源数组信息
     */
    public function get_house_by_id($id){
        $house = $this->find($id);
        return $house ? $house : array();
    }

    public function deleteHousesData($id) {
        $lines = self::where(['id' => $id])->delete();
        if (false === $lines) {
            return ['code' => -1, 'msg' => '数据删除失败'];
        }
        return ['code' => 0, 'msg' => 'ok', 'data' => $id];
    }

    /**
     * This part is save houses data from backend
     * @param $data
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function saveHousesDataInternal($data) {
        $now = date('Y-m-d H:i:s');
        $id = intval(@$data['id']);
        // 获取旧数据
        if ($id > 0) {
            $oldInfo = $this->find($id);
            if (!$oldInfo) {
                return array('code' => -1, 'msg' => '文章不存在');
            }
            $old_thumbnail = $oldInfo['thumnail'];
        } else {
            $data['admin_id'] = session('id');
            $old_thumbnail = '';
        }

        // 移动标题图
        if ($data['thumnail']) { // 确保从tmp中移动出thumnail
            // 如果之前上传过缩略图，删除
            if ($old_thumbnail && $data['thumnail'] != $old_thumbnail) {
                deleteFile($old_thumbnail);
            }
            if (false === ($data['thumnail'] = $this->_move_thumbnail($data['thumnail']))) {
                return array('code' => -1, 'msg' => '移动缩略图位置失败');
            }
        }

        $data['cdate'] = $now;
        $data['mdate'] = $now;
        // Generate DSN for current situtation
        //$data['dsn'] = $this->gen_house_dsn($data['source'], $data['city']);
        $data['dsn'] = $this->gen_house_dsn_new();
        // 保存数据
        if ($data['id']) {
            $lines = $this->save($data, array('id' => $data['id']));
            if ($lines !== false) {
                $res = true;
            }
        } else {
            $insertId = $this->insertGetId($data);
            if ($insertId) {
                $data['id'] = $insertId;
                $res = true;
            }
        }

        if (!$res) {
            return array('code' => -1, 'msg' => $this->getDbError());
        }
        return array('code' => 0, 'msg' => 'ok', 'data' => $data);
    }


    /**
     * This part is save houses data from API
     * @param $data
     * @return array
     */
	public function saveHousesData($data) {
		$now = date('Y-m-d H:i:s');
		$data['mdate'] = $now;
		file_put_contents('_save_house_model.txt', json_encode($data));
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
            //添加房源编号new
//            $data['dsn'] = $this->gen_house_dsn($data['source'], $data['city']);
            $data['dsn'] = $this->gen_house_dsn_new();
            $this->data = $data;

			//访问频繁
			if (cache('add_house_'.$data['user_id'])) {
			    $left_second = time() - cache('add_house_'.$data['user_id']);
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
                    cache('add_house_'.$data['user_id'], time(),10);
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
		//添加房源编号
        //$this->set_house_dsn($data['id']);
		return ['code' => 0, 'msg' => 'ok', 'data' => $data];
	}

	public function set_house_dsn($id) {
		$data['dsn'] = $this->get_house_dsn($id);
		$this->where('id', $id)->update($data);
	}

	public function get_house_dsn($id) {
		$row = self::get($id);
		$dsn = '00';
		if ($row['source'] == '个人') {
			$dsn .= '02';
		} else if ($row['source'] == '中介') {
			$dsn .= '01';
		} else {
			$dsn .= '03';
		}
		$condition['name'] = ['like', "%{$row['city']}%"];
		$condition['pid'] = 0;
		$city_dsn = model('Cate')->where(@$condition)->value('dsn');
		$dsn .= $city_dsn;
		$count = $this->count();
		$s = '';
		for ($i = 1; $i < 8-strlen($count); $i++) {
			$s .= '0';
		}
		$dsn .= $s.$count;
		return $dsn;
	}

	//重新生成房屋编码
	private function gen_house_dsn($source, $city)
    {
        $dsn = '00';
        if ($source == '个人') {
            $dsn .= '02';
        } else if ($source == '中介') {
            $dsn .= '01';
        } else {
            $dsn .= '03';
        }
        $condition['name'] = ['like', "%{$city}%"];
        $condition['pid'] = 0;
        $city_dsn = model('Cate')->where(@$condition)->value('dsn');
        $dsn .= $city_dsn;
        $count = $this->count();
        $s = '';
        for ($i = 1; $i < 8-strlen($count); $i++) {
            $s .= '0';
        }
        $count++;
        $dsn .= $s.$count;
        return $dsn;
    }
    private function _move_thumbnail($src_path, $dest_path = '') {
        if(!$dest_path) {
            $dest_path = '/uploads/houses' . '/' . basename($src_path);
        }
        if (!moveFile($src_path, $dest_path)) {
            return false;
        }
        return $dest_path;
    }
    //重新生成找室友编码
    private function gen_house_dsn_new()
    {
        // 找室友编号开始为B
        $dsn = 'A';
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