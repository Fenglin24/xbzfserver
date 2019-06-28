<?php

/**
 * @Author: Qian weidong
 * @Date:   2018-12-12 13:10:10
 * @Last Modified by:   Qian weidong
 * @Last Modified time: 2018-12-29 11:55:04
 */
namespace app\api\model;

class Houses extends \app\common\model\Houses {

	public function get_page_house_list($condition) {
		$pageSize = config('page');

		// 为翻页按钮准备query参数
		$pageParam['query']['s'] = '/api/questions/get_house_list';
		foreach ($condition as $key => $value) {
			$pageParam['query']["condition[{$key}]"] = $value;
		}

		$count = $this->alias('h')->join('user u', 'h.user_id = u.id', 'left')->where($condition)->count();
		$list = $this->alias('h')->join('user u', 'h.user_id = u.id', 'left')->field('h.*,u.real_name,u.wchat,u.tel')->where($condition)->order('h.cdate desc')->paginate($pageSize, $count, $pageParam);
		// foreach ($list as &$row) {
		// 	$row['images'] = $this->_get_image_string_to_array($row['images']);
		// }
        file_put_contents("/tmp/__debug.log",__FUNCTION__." || ".$this->alias('h')->getLastSql()."\n\n",FILE_APPEND);
		return $list;
	}

	public function get_page_house_top_list($condition) {
		$list = $this->alias('h')->join('user u', 'h.user_id = u.id', 'left')->field('h.*,u.real_name,u.wchat,u.tel')->where($condition)->order('h.top_datetime desc')->limit(5)->select();
		return $list;
	}

	public function _get_image_string_to_array($value) {
		$arr = explode(',', $value);
		return $arr;
	}

	public function get_page_search_list($condition, $order, $keyword) {
		$pageSize = config('page');

		// 为翻页按钮准备query参数
		$pageParam['query']['s'] = '/api/questions/get_house_list';
		foreach ($condition as $key => $value) {
			$pageParam['query']["condition[{$key}]"] = $value;
		}
		// dd($condition['keyword']);
		$c['h.title|h.address|h.price|h.source|h.type|h.sex|h.pet|h.smoke|h.bill|h.deposit|h.lease_term|h.house_type|h.furniture|h.car|h.toilet|h.home|h.sation|h.city|h.content|h.school|h.area'] = ['like', "%{$keyword}%"];
		$count = $this->alias('h')
				// ->whereOr('h.title', 'like', "%{$keyword}%")
				// ->whereOr('h.content', 'like', "%{$keyword}%")
				// ->whereOr('h.school', 'like', "%{$keyword}%")
				->where($c)
				->join('user u', 'h.user_id = u.id', 'left')
				->where($condition)
				->count();
		file_put_contents('index.search.txt', $this->getLastSql());
		// echo $this->getLastSql();exit;
		$list = $this->alias('h')
				->where($c)
				// ->whereOr('h.title', 'like', "%{$keyword}%")
				// ->whereOr('h.content', 'like', "%{$keyword}%")
				// ->whereOr('h.school', 'like', "%{$keyword}%")
				->join('user u', 'h.user_id = u.id', 'left')
				->field('h.*,u.real_name,u.wchat,u.tel')
				->where($condition)
				->order($order)
				->paginate($pageSize, $count, $pageParam);

        file_put_contents("/tmp/__debug.log",__FUNCTION__." || ".$this->alias('h')->getLastSql()."\n\n",FILE_APPEND);

        foreach ($list as &$row) {
			$row['images'] = $this->_get_image_string_to_array($row['images']);
		}
		return $list;
	}

	public function get_house_detail_by_id($id) {
		$row = $this->alias('h')->join('user u', 'u.id=h.user_id', 'left')->field('h.*, u.id as userid, u.avaurl, u.nickname')->where('h.id', $id)->find();
		//$row = $this->where('id', $id)->find();
		// dd($id);
		return $row;
	}
}
