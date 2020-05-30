<?php


namespace app\api\model;

use think\Model;

class VipConfig extends Model
{
    protected $name = 'vip_config';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';


    // 获取全部vip配置
    public function listAll()
    {
        return $this->where(['show_switch' => ['=', 1]])->order('weigh', 'desc')->select();
    }

    // 获取一个数据
    public function infoById($vipId)
    {
        return $this->where(['show_switch' => ['=', 1], 'id' => ['=', $vipId]])->find();
    }
}