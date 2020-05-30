<?php


namespace app\api\model;

use think\Model;

class Up extends Model
{
    protected $name = 'up';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';


    // 获取所有的升学方式
    public function listAll()
    {
        return $this->where(['show_switch' => ['=', 1]])->useSoftDelete($this->deleteTime)->order('weigh', 'desc')->select();
    }

    // 根据ID获取一个升学方式
    public function infoById($id)
    {
        return $this->where(['show_switch' => ['=', 1], 'id' => ['=', $id]])->useSoftDelete($this->deleteTime)->find();
    }

}