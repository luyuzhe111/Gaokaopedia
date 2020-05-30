<?php


namespace app\api\model;

use think\Model;

class Graduated extends Model
{
    protected $name = 'graduated';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';

    public function listAll()
    {
        return $this->where(['show_switch' => ['=', 1]])->useSoftDelete($this->deleteTime)->order('weigh', 'desc')->select();
    }

    public function infoById($id)
    {
        return $this->where(['show_switch' => ['=', 1], 'id' => ['=', $id]])->useSoftDelete($this->deleteTime)->find();
    }
}