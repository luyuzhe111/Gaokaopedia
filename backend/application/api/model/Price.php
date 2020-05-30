<?php


namespace app\api\model;

use think\Model;

class Price extends Model
{
    protected $name = 'price';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';


    // 获取所有的价目
    public function listAll()
    {
        return $this->where(['show_switch' => ['=', 1]])->useSoftDelete($this->deleteTime)->order('weigh', 'desc')->select();
    }
}