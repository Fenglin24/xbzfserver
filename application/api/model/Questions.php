<?php

/**
 * @Author: qian 
 * @Date:   2018-12-11 22:29:57
 * @Last Modified by:   qian
 * @Last Modified time: 2018-12-11 22:42:40
 */
namespace app\api\model;

class Questions extends \app\common\model\Questions {

	public function get_page_list($condition) {
		$pageSize = config('page');

		// 为翻页按钮准备query参数
		$pageParam['query']['s'] = '/api/questions/get_house_list';
		foreach ($condition as $key => $value) {
			$pageParam['query']["condition[{$key}]"] = $value;
		}

		$count = $this->where($condition)->count();
		$list = self::where($condition)->field('id,title,summary,content,cdate')->order('id desc')->paginate($pageSize, $count, $pageParam);
		return $list;
	}

    public function get_page_list_roommate($condition) {
        $pageSize = config('page');

        // 为翻页按钮准备query参数
        $pageParam['query']['s'] = '/api/questions/get_roommates_list';
        foreach ($condition as $key => $value) {
            $pageParam['query']["condition[{$key}]"] = $value;
        }

        $count = $this->where($condition)->count();
        $list = self::where($condition)->field('id,title,summary,content,cdate')->order('id desc')->paginate($pageSize, $count, $pageParam);
        return $list;
    }

}