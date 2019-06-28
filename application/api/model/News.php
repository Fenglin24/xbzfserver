<?php

/**
 * @Author: qian 
 * @Date:   2018-12-11 23:29:28
 * @Last Modified by:   qian
 * @Last Modified time: 2018-12-18 02:23:33
 */
namespace app\api\model;

class News extends \app\common\model\News {

	public function get_page_news_list($condition) {
		$pageSize = config('page');

		// 为翻页按钮准备query参数
		$pageParam['query']['s'] = '/api/news/get_news_list';
		foreach ($condition as $key => $value) {
			$pageParam['query']["condition[{$key}]"] = $value;
		}
		$condition['status'] = 1;
		$count = $this->where($condition)->count();
		$list = self::where($condition)->field('id,title,thumbnail,summary,content,cdate')->order('cdate desc')->paginate($pageSize, $count, $pageParam);
		return $list;
	}

}