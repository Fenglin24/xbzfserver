<?php

/**
 * @Author: Qian weidong
 * @Date:   2018-12-13 10:15:30
 * @Last Modified by:   Qian weidong
 * @Last Modified time: 2018-12-13 10:18:11
 */
namespace app\common\model;

use think\Model;

class Collection extends Model {

	// 默认写入时间戳
	protected $autoWriteTimestamp = 'datetime';
	// 重定义时间戳字段名
	protected $createTime = 'cdate';
	protected $updateTime = 'mdate';

}