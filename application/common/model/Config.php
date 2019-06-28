<?php
namespace app\common\model;

use think\Model;
class Config extends Model {
	//默认写入时间戳
	protected $autoWriteTimestamp = 'datetime';
	//重定义时间戳字段名
	protected $createTime = null;
	protected $updateTime = null;
	
	public function getPageList($condition = array()) {
		$pageSize = config('paginate.list_rows');
		$count = $this->where($condition)->count();
		$pageParam['query']['s'] = '/' . MODULE_NAME . '/' . CONTROLLER_NAME . '/' . ACTION_NAME;
		$list = self::where($condition)->order('id desc')->paginate($pageSize, $count, $pageParam);
		return array(
			'page_size' => $pageSize,
			'count' => $count,
			'page' => $list->render(),
			'list' => $list,
		);
	}
	
	public function get_all_config() {
		$rows = self::all();
		$dataMap = array();
		foreach ($rows as $row) {
			$dataMap[$row['name']] = $row['value'];
		}
		return $dataMap;
	}
	
	public function get_one_config_value_by_name($name) {
		return $this->where(['name' => $name])->value('value');
	}
	
	public function saveConfigData($data) {
		if (empty($data['id'])) {
			return array('code' => -1, 'msg' => 'id缺失');
		}
		$configInfo = $this->get($data['id']);
		if (!$configInfo) {
			return array('code' => -1, 'msg' => '不存在的配置');
		}
		$lines = $this->save($data, ['id' => $data['id']]);
		if ($lines === false) {
			return array('code' => -1, 'msg' => '数据保存失败');
		}
		cache($configInfo['name'], null);
		return array('code' => 0, 'msg' => 'ok', 'data' => $data);
	}
}