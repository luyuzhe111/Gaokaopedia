<?php


namespace app\api\model;

use think\Model;

class Third extends Model
{
    protected $name = 'third';



    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';

    // 获取用户的openid
    public function getOneByUserId($userId)
    {
        return $this->where(['user_id' => ['=', $userId]])->find();
    }

}