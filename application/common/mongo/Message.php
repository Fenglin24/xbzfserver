<?php
namespace app\common\mongo;

use think\Model;

class Message extends Model {
	protected $connection = 'mongodb';
	// 默认写入时间戳
	protected $autoWriteTimestamp = 'datetime';
	// 重定义时间戳字段名
	protected $createTime = 'cdate';
	protected $updateTime = 'mdate';
	
	public function saveMessage($data) {
		$this->data = $data;
		$result = $this->save();
		var_dump($result);
	}
	
	public function getMessage($name) {
		$rows = collection($this->select())->toArray();
		echo json_encode($rows);
	}
}