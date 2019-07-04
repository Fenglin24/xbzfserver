<?php

/**
 * @Author: Qian weidong
 * @Date:   2018-12-13 14:53:04
 * @Last Modified by:   Qian weidong
 * @Last Modified time: 2018-12-13 14:53:37
 */
namespace app\common\model;

use think\Model;

class NewsUser extends Model {
	// 默认写入时间戳
	protected $autoWriteTimestamp = 'datetime';
	// 重定义时间戳字段名
	protected $createTime = 'cdate';
	protected $updateTime = 'mdate';


}