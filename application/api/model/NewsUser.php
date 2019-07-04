<?php

/**
 * @Author: Qian weidong
 * @Date:   2018-12-13 14:53:52
 * @Last Modified by:   Qian weidong
 * @Last Modified time: 2018-12-13 15:28:43
 */
namespace app\api\model;

class NewsUser extends \app\common\model\NewsUser {

	public function get_collection_news_user($user_id, $news_id) {
		$condition['user_id'] = $user_id;
		$condition['news_id'] = $news_id;
		$res = $this->where($condition)->find();
		if (empty($res)) {
			return 2;
		}
		return 1;
	}

	public function get_news_user_list($condition) {
		$pageSize = config('page');

		// 为翻页按钮准备query参数
		$pageParam['query']['s'] = '/api/news/get_news_user_list';
		foreach ($condition as $key => $value) {
			$pageParam['query']["condition[{$key}]"] = $value;
		}

		$count = $this->alias('nu')
            ->join('News n', 'nu.news_id=n.id', 'INNER')
            ->where($condition)->count();

		$list = $this->alias('nu')->join('News n', 'nu.news_id=n.id', 'INNER')->where($condition)
            ->field('n.*')
            ->order('nu.id desc')->paginate($pageSize, $count, $pageParam);
		return $list;
	}
}