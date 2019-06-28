<?php

/**
 * @Author: Qian weidong
 * @Date:   2018-12-13 10:11:56
 * @Last Modified by:   Qian weidong
 * @Last Modified time: 2018-12-18 10:03:34
 */
namespace app\api\model;

class Collection extends \app\common\model\Collection {

	public function get_house_collection_by_user_id($user_id, $house_id) {
		$res = $this->where(['user_id' => $user_id, 'house_id' => $house_id])->find();
		if ($res == false) {
			return 2;
		}
		return 1;
	}

	public function get_page_collection_house_list($condition) {
		$pageSize = config('page');

		// 为翻页按钮准备query参数
		$pageParam['query']['s'] = '/api/collection/get_collection_house_list';
		foreach ($condition as $key => $value) {
			$pageParam['query']["condition[{$key}]"] = $value;
		}

		$count = $this->alias('c')->join('houses h', 'h.id=c.house_id', 'left')->where($condition)->count();
		$list = $this->alias('c')->join('houses h', 'h.id=c.house_id', 'left')->where($condition)->field('c.id,c.house_id,h.title, h.images, h.home, h.sation, h.free_view, h.free_in, h.tj, h.check, h.address, h.price, h.thumnail')->order('c.id desc')->paginate($pageSize, $count, $pageParam);
		return $list;
	}
  
    public function get_page_collection_house_list_by_id($condition) {
        $pageSize = config('page');

        // 为翻页按钮准备query参数
        $pageParam['query']['s'] = '/api/collection/get_collection_house_list_by_id';
        foreach ($condition as $key => $value) {
            $pageParam['query']["condition[{$key}]"] = $value;
        }

        $count = $this->alias('c')->join('houses h', 'h.id=c.house_id', 'left')->where($condition)->count();
        $list = $this->alias('c')->join('houses h', 'h.id=c.house_id', 'left')->where($condition)->field('c.id,c.house_id,h.title, h.images, h.home, h.sation, h.free_view, h.free_in, h.tj, h.check, h.address, h.price, h.thumnail')->paginate($pageSize, $count, $pageParam);
        return $list;
    }
}