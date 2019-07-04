<?php

/**
 * @Author: Qian weidong
 * @Date:   2018-12-13 10:08:49
 * @Last Modified by:   Qian weidong
 * @Last Modified time: 2018-12-18 10:00:42
 */
namespace app\api\controller;

class Collection extends BaseController {

    public function save() {
        $house_id = input('param.id');
        $data['house_id'] = $house_id;
        $data['user_id'] = $this->user->id;
        if (!@$house_id) {
            $this->sucess('-1', '房源id不能为空');
        }
        $row = model('Collection')->where($data)->find();
        if (empty($row)) {
            //收藏
            $res = model('Collection')->save($data);
            if ($res == false) {
                $this->sucess('-1', '收藏失败');
            }
            //统计收藏数
            $collection = model('Houses')->where('id', $house_id)->value('collection');
            model('Houses')->where('id', $house_id)->update(['collection' => $collection+1]);
            $this->sucess('0', '收藏成功', 1);
        } else {
            $res = model('Collection')->where($data)->delete();
            if ($res == false) {
                $this->sucess('-1', '取消失败');
            }
            $this->sucess('0', '取消成功', 2);
        }
    }

    public function saveroommate() {
        $roommate_id = input('param.id');
        $data['roommate_id'] = $roommate_id;
        $data['user_id'] = $this->user->id;
        if (!@$roommate_id) {
            $this->sucess('-1', '找室友帖子id不能为空');
        }
        $row = model('Collectionr')->where($data)->find();
        if (empty($row)) {
            //收藏
            $res = model('Collectionr')->save($data);
            if ($res == false) {
                $this->sucess('-1', '收藏失败');
            }
            //统计收藏数
            $collection = model('Roommates')->where('id', $roommate_id)->value('collection');
            model('Roommates')->where('id', $roommate_id)->update(['collection' => $collection+1]);
            $this->sucess('0', '收藏成功', 1);
        } else {
            $res = model('Collectionr')->where($data)->delete();
            if ($res == false) {
                $this->sucess('-1', '取消失败');
            }
            $this->sucess('0', '取消成功', 2);
        }
    }


    public function get_collection_house_list() {
        $condition = [];
        $condition['c.user_id'] = $this->user->id;
        $list = model('Collection')->get_page_collection_house_list($condition);
        $list = $this->init_html_list_houses($list);
        $this->sucess('0', 'ok', $list);
    }
  
    public function get_collection_house_list_by_id() {
        $data = input('param.');
        if(@$data['houses']!=""){
          $condition['house_id'] = ['in',@$data['houses']];
        }
        $condition['c.user_id'] = @$data['userid'];
        $list = model('Collection')->get_page_collection_house_list_by_id($condition);
        $list = $this->init_html_list_houses($list);
        $this->sucess('0', 'ok', $list);
    }

    public function get_collection_roommate_list() {
        $condition = [];
        $condition['c.user_id'] = $this->user->id;
        $list = model('Collectionr')->get_page_collection_roommate_list($condition);
        $list = $this->init_html_roommate($list);
        $this->sucess('0', 'ok', $list);
    }




}