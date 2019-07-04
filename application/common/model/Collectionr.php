<?php


namespace app\common\model;


use think\Model;

class Collectionr extends Model
{

    // 默认写入时间戳
    protected $autoWriteTimestamp = 'datetime';
    // 重定义时间戳字段名
    protected $createTime = 'cdate';
    protected $updateTime = 'mdate';
}