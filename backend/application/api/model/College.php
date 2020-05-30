<?php


namespace app\api\model;

use think\Model;

class College extends Model
{
    protected $name = 'college';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';

    // 获取所有得学院
    public function listAll()
    {
        return $this->where(['show_switch' => ['=', 1]])->useSoftDelete($this->deleteTime)->order('weigh', 'desc')->select();
    }

    // 获取单个学院
    public function infoById($id)
    {
        return $this->where(['show_switch' => ['=', 1], 'id' => ['=', $id]])->useSoftDelete($this->deleteTime)->find();
    }

    // 根据大学ID获取学院
    public function listByUniversityId($universityId)
    {
        return $this->where(['show_switch' => ['=', 1], 'university_id' => ['=', $universityId]])->useSoftDelete($this->deleteTime)->select();
    }
}