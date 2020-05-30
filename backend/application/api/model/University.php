<?php


namespace app\api\model;

use think\Model;

class University extends Model
{
    protected $name = 'university';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';


    // 根据ID获取一个大学的数据
    public function infoById($id)
    {
        return $this->where(['id' => ['=', $id], 'show_switch' => ['=', 1]])->useSoftDelete($this->deleteTime)->find();
    }


    // 分页获取大学
    public function listByWhere($page, $size, $where)
    {
        return $this->where($where)->useSoftDelete($this->deleteTime)->page($page, $size)->order('weigh', 'desc')->select();
    }

    // 分页获取大学
    public function listNoBy($page, $size)
    {
        return $this->where(['show_switch' => ['=', 1]])->useSoftDelete($this->deleteTime)->page($page, $size)->order('weigh', 'desc')->select();
    }

    // 获取所有的大学
    public function listAll()
    {
        return $this->where(['show_switch' => ['=', 1]])->useSoftDelete($this->deleteTime)->select();
    }

    // 根据城市ID获取大学
    public function listByCityId($cityId)
    {
        return $this->where(['city_id' => ['=', $cityId], 'show_switch' => ['=', 1]])->useSoftDelete($this->deleteTime)->select();
    }
}