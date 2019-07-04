<?php
/**
 * Created by PhpStorm.
 * User: Fengl
 * Date: 7/2/2019
 * Time: 8:40 PM
 */

namespace app\common\model;
use think\Db;
use think\Model;

class Agency extends Model{
    //默认写入时间戳
    protected $autoWriteTimestamp = 'datetime';
    //重定义时间戳字段名
    protected $createTime = 'cdate';
    protected $updateTime = 'mdate';

    public function getPageList($condition = []){
        if (@$condition['title']) {
            $condition['title'] = ['like', "%{$condition['title']}%"];
        }
        $pageSize = config('paginate.list_rows');
        $count = $this->where($condition)->count();
        $pageParam['query']['s'] = MODULE_NAME . '/' . CONTROLLER_NAME . '/' . ACTION_NAME;

        $page = $this->where($condition)->order('id desc')->paginate($pageSize, $count, $pageParam);
        $dataList = $page->all();
        foreach ($dataList as $key => $row) {
            // $dataList[$key]['cname'] = model('AdCategory')->get_name_by_cid($row['cid']);
        }
        return array(
            'page_size' => $pageSize,
            'count' => $count,
            'page' => $page->render(),
            'list' => $dataList,
        );
    }

    public function mergeCate($oldcid, $newcid) {
        return $this->save(array('cid' => $newcid), array('cid' => $oldcid));
    }

    /**
     *
     * @param $id       Agency ID
     *
     * @return array    是否获取到json源数组信息
     */
    public function get_Agency_by_id($id){
        $Agency = $this->find($id);
        return $Agency ? $Agency : array();
    }

    /**
     * [保存信息]
     * @return [type] [description]
     */
    public function saveAgency($data) {
        $now = date('Y-m-d H:i:s');
        $id = intval(@$data['id']);
        // 获取旧数据
        if ($id > 0) {
            $oldInfo = $this->find($id);
            if (!$oldInfo) {
                return array('code' => -1, 'msg' => '数据不存在');
            }
            $old_thumbnail = $oldInfo['thumbnail'];
            $old_html = $oldInfo['content'];
        } else {
            $data['admin_id'] = session('id');
            $old_thumbnail = '';
            $old_html = '';
        }

        // 移动缩略图
        if ($data['thumbnail']) { // 确保从tmp中移动出thumbnail
            // 如果之前上传过缩略图，删除
            if ($old_thumbnail && $data['thumbnail'] != $old_thumbnail) {
                deleteFile($old_thumbnail);
            }
            if (false === ($data['thumbnail'] = $this->_move_thumbnail($data['thumbnail']))) {
                return array('code' => -1, 'msg' => '移动缩略图位置失败');
            }
        }

        // 移动百度编辑器内图片
        $new_html = $data['content'];
        $file = new \app\common\helper\File;
        $tmp_path = config("TMP_PATH") . '/';
        $target_path = config("Ad_IMG_PATH") . '/';
        $result = $file->moveAndDeleteFilesFromHtml($new_html, $old_html, $tmp_path, $target_path);
        if ($result['code']) {
            return array('code' => -1, 'msg' => '移动新闻图片失败:' . $result['msg']);
        }
        $data['content'] = $result['data']['html'];
        $data['cdate'] = $now;
        $data['mdate'] = $now;

        // 保存数据
        if ($data['id']) {
            $lines = $this->save($data, array('id' => $data['id']));
            if ($lines !== false) {
                $res = true;
            }
        } else {
            $data['show_time'] = $now;
            $insertId = $this->insertGetId($data);
            if ($insertId) {
                $data['id'] = $insertId;
                $res = true;
            }
        }

        if (!$res) {
            return array('code' => -1, 'msg' => $this->getDbError());
        }
        return array('code' => 0, 'msg' => 'ok', 'data' => $data);
    }

    private function _move_thumbnail($src_path, $dest_path = '') {
        if(!$dest_path) {
            $dest_path = config('THUMBNAIL_PATH') . '/' . basename($src_path);
        }
        if (!moveFile($src_path, $dest_path)) {
            return false;
        }
        return $dest_path;
    }

    public function deleteAgency($id) {
        $AgencyInfo = $this->find($id);
        if (!$AgencyInfo) {
            return array('code' => -1, 'msg' => '数据不存在！');
        }



        $lines = $this->where(['id' => $id])->delete();
        if ($lines === false) {
            return array('code' => -1, 'msg' => '删除失败！');
        }
        return array('code' => 0, 'msg' => 'ok', 'data' => $id);
    }
}