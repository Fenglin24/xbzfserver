<?php

/**
 * @Author: Qian weidong
 * @Date:   2018-10-09 09:26:41
 * @Last Modified by:   Qian weidong
 * @Last Modified time: 2018-12-11 16:43:41
 */
namespace app\api\model;

class Bltoken extends \think\Model {
	// 默认写入时间戳
	protected $autoWriteTimestamp = 'datetime';
	// 重定义时间戳字段名
	protected $createTime = 'cdate';
	protected $updateTime = 'mdate';

	// 记录日志的文件名前缀，writeLog用到，子类可以覆盖
	protected static $log_file_prefix_name = 'run';

	// 数据表的主键字段名
	protected $pk = 'id';

	public function check_bltoken_is_right($blToken) {
		//验证是否存在
		$res = $this->where(['blToken' => $blToken])->find();
		if (!$res) {
			return ['status_code' => 2002, 'msg' => 'blToken不存在', 'sucess' => false];
		}
		//是否有效
		if ($res['overtime'] < time()) {
			return ['status_code' => 2002, 'msg' => 'blToken已过期', 'sucess' => false];
		}
		return ['status_code' => 2000, 'msg' => 'ok', 'sucess' => true];
	}

	public function saveBltokenData($data) {
		
		if (!@$data['id']) {
			//添加
			unset($data['id']);
			$res = $this->save($data);
			if ($res == false) {
				return ['code' => -1, 'msg' => '保存失败'];
			}
			$data['id'] = $this->id;
			return ['code' => 0, 'msg' => '保存成功', 'data' => $data];
		} else {
			$res = $this->save($data, ['id' => $data['id']]);
			if ($res == false) {
				return ['code' => -1, 'msg' => '保存失败'];
			}
			return ['code' => 0, 'msg' => '保存成功', 'data' => $data];
		}
	}

	public function save_bltoken_user_id($user_id) {
		$bltoken = $this->where(['user_id' => $user_id])->find();
		$data['user_id'] = $user_id;
		$data['blToken'] = md5(date('Y-m-d H:i:s'). $user_id);
		$data['overtime'] = time() + 60 * 60 * 24*30*12;
		if ($bltoken) {
			$data['id'] = $bltoken['id'];
		}
		$res = $this->saveBltokenData($data);
		return $res;
	}

	public function deleteBltokenData($id) {
        $lines = self::where(['id' => $id])->delete();
        if (false === $lines) {
            return ['code' => -1, 'msg' => '数据删除失败'];
        }
        return ['code' => 0, 'msg' => 'ok', 'data' => $id];
    }
}