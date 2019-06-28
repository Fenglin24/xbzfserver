<?php
/**
 * Created by thinkphp5.
 * Author   :   Hyder
 */
namespace app\common\model;
use think\Model;
/**
 * Class NewsCategory           新闻分类模型类
 * @package app\common\model
 */
class NewsCategory extends Model{
	//默认写入时间戳
	protected $autoWriteTimestamp = 'datetime';
	//重定义时间戳字段名
	protected $createTime = null;
	protected $updateTime = null;
	
	public function get_name_by_cid($cid) {
		$name = strval($this->where('cid', $cid)->value('name'));
		return $name;
	}
	/**
	 * 获取并重新排列新闻分类顺序
	 * @return array    新闻分类json源格式数组
	 */
	public function get_cates_tree() {
		$rows = $this->order('pid asc, cid asc')->select();
		$catesTree = [];
		foreach ($rows as $row) {
			$cid = $row['cid'];
			$toCid = $row['pid'];
			if ($toCid == 0) {
				$catesTree[$cid] = ['info' => $row, 'child' => []];
			} else {
				if (isset($catesTree[$toCid])) {
					$catesTree[$toCid]['child'][$cid] = ['info' => $row];
				}
			}
		}
		// dd($catesTree);
		return $catesTree;
	}
	
	public function get_cates_map() {
		$rows = $this->order('pid asc, cid asc')->select();
		$catesMap = [];
		foreach ($rows as $row) {
			$catesMap[$row['cid']] = $row['name'];
		}
		return $catesMap;
	}
	
	public function saveCate($data) {
		$res = false;
		if ($data['cid']) {
			$lines = $this->save($data, array('cid' => $data['cid']));
			if ($lines === false) {
				return array('code' => -1, 'msg' => '保存分类失败');
			}
		} else {
			$insertId = $this->insertGetId($data);
			if (false === $insertId) {
				return array('code' => -1, 'msg' => '添加分类失败');
			}
			$data['cid'] = $insertId;
		}
		return array('code' => 0, 'msg' => 'ok', 'data' => $data);
	}
	
	/**
	 * 分类合并情况复杂，分为如下几种：
	 * 1、将二级分类合并到另一个二级分类
	 * 2、将一级分类合并到另一个一级分类（其实是将所有子分类的pid设置为另一个一级分类的cid）
	 * 3、不允许将一个一级分类合并到另一个二级分类
	 */
	public function mergeCate($cid, $toCid) {
		$fromCategory = $this->where(array('cid' => $cid))->find();
		if (!$fromCategory) {
			return array('code' => -1, 'msg' => '不存在的分类');
		}
		// 目标分类和原分类相同或者目标分类和原分类的父级分类相同，则不必修改
        if ($toCid == $fromCategory['cid'] || $toCid == $fromCategory['pid']) { 
            return array('code' => 0, 'msg' => 'ok', 'data' => $fromCategory);
        }

        $toCategory = $this->find($toCid);
        if (!$toCategory) {
            return array('code' => -1, 'msg' => '不存在的目标分类');
        }
        // 将子分类和别的子分类合并才需要修改文章cid
        // 将一级分类和别的一级分类合并才需要修改文章cid
        if ($fromCategory['pid'] > 0 && $toCategory['pid'] > 0 || 
            $fromCategory['pid'] == 0 && $toCategory['pid'] == 0
            ) { 
            model('News')->mergeCate($cid, $toCid); // 修改文章cid
			self::destroy($fromCategory['cid']);
        }
		
		if ($fromCategory['pid'] == 0) { // 将一级栏目下所有栏目都移动到另一个一级栏目下
			$this->save(['pid' => $toCid], ['pid' => $cid]);
		} else { // 将二级栏目移动到另一个栏目下
			if ($toCategory['pid'] == 0) { // 移动到另一个一级栏目下
				$this->save(['pid' => $toCid], ['cid' => $cid]);
			} 
		}
		return array('code' => 0, 'msg' => 'ok', 'data' => $fromCategory);
	}
	
	public function deleteCate($cid) {
		$cid = intval($cid);
		$articleNum = \think\Db::name('News')->where('cid', $cid)->count();
		if ($articleNum > 0) {
			return array('code' => -1, 'msg' => '分类下有文章，请使用合并功能');
		}
		$fromCategory = $this->find($cid);
		if (!$fromCategory) {
			return array('code' => -1, 'msg' => '分类已经被删除');
		}
		if ($fromCategory['pid'] == 0) {
			$pCates = $this->where('pid', $cid)->select();
			if ($pCates) {
				foreach ($pCates as $key => $row) {
					$articleNum = \think\Db::name('News')->where('cid', $row['cid'])->count();
					if ($articleNum > 0) {
						return array('code' => -1, 'msg' => '分类的分类下有文章，请使用合并功能');
					}
				}
			}
		}
		$lines = $this->where('cid', $cid)->delete();
		if ($lines === false) {
			return array('code' => -1, 'msg' => '删除失败');
		}
		return array('code' => 0, 'msg' => 'ok', 'data' => $cid);
	}
}