<?php


namespace app\api\model;

use think\Model;

class Subject extends Model
{
    protected $name = 'subject';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';


    // 根据ID集获取多个
    public function listByIds($ids)
    {
        return $this->where(['id' => ['in', $ids], 'show_switch' => ['=', 1]])->useSoftDelete($this->deleteTime)->order('weigh', 'desc')->select();
    }


    // 获取所有
    public function listAll()
    {
        return $this->where(['show_switch' => ['=', 1]])->useSoftDelete($this->deleteTime)->order('weigh', 'desc')->select();
    }
}