<?php


namespace app\api\model;

use think\Model;

class UserMoneyLog extends Model
{
    protected $name = 'user_money_log';

    // 定义时间戳字段名
    protected $createTime = 'createtime';


    public function listByUserId($userId, $page, $size)
    {
        return $this->where(['user_id' => ['=', $userId]])->page($page, $size)->order($this->createTime, 'desc')->select();
    }

    // 添加一个记录
    public function addOne($data)
    {
        return $this->insert($data);
    }
}