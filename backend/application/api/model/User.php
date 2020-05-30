<?php


namespace app\api\model;

use think\Model;

class User extends Model
{
    protected $name = 'user';

    // 定义时间戳字段名


    // 获取一个用户的数据
    public function infoById($id)
    {
        return $this->where(['id' => ['=', $id]])->find();
    }


    // 获取一个用户的数据并加锁
    public function infoAndLockById($id)
    {
        return $this->where(['id' => ['=', $id]])->lock(true)->find();
    }


    // 修改一条数据
    public function updateOne($id, $data)
    {
        return $this->where(['id' => ['=', $id]])->update($data);
    }
}