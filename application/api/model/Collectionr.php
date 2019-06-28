<?php


namespace app\api\model;


class Collectionr extends \app\common\model\Collectionr
{
    public function get_roommates_collection_by_user_id($user_id, $roommates_id) {
        $res = $this->where(['user_id' => $user_id, 'roommate_id' => $roommates_id])->find();
        if ($res == false) {
            return 2;
        }
        return 1;
    }
    public function get_page_collection_roommate_list($condition) {
        $pageSize = config('page');

        // 为翻页按钮准备query参数
        $pageParam['query']['s'] = '/api/collection/get_collection_roommate_list';
        foreach ($condition as $key => $value) {
            $pageParam['query']["condition[{$key}]"] = $value;
        }

        $count = $this->alias('c')->join('roommates r', 'r.id=c.roommate_id', 'left')->where($condition)->count();
        $list = $this->alias('c')->join('roommates r', 'r.id=c.roommate_id', 'left')->where($condition)->field('c.id,c.roommate_id,r.title, r.price')->order('c.id desc')->paginate($pageSize, $count, $pageParam);
        return $list;
    }
}