<?php


namespace app\api\model;

use think\Model;

class LikeStudent extends Model
{
    protected $name = 'like_student';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';


    // 获取用户关注的学长
    public function listAllByUserId($userId)
    {
        return $this
            ->where(['usera_id' => ['=', $userId]])
            ->useSoftDelete($this->deleteTime)
            ->select();
    }


    // 获取用户关注的学长
    public function listByUserId($userId, $page, $size)
    {
        return $this
            ->alias('a')
            ->join('student b', 'a.userb_id = b.user_id')
            ->where(['a.usera_id' => ['=', $userId]])
            ->useSoftDelete('a.'.$this->deleteTime)
            ->page($page, $size)
            ->order('a.'.$this->createTime, 'desc')
            ->select();
    }


    // 获取一个
    public function infoById($useraId, $userbId)
    {
        return $this
            ->where(['usera_id' => ['=', $useraId], 'userb_id' => ['=', $userbId]])
            ->useSoftDelete($this->deleteTime)
            ->find();
    }


    // 修改一个
    public function updateOne($id, $data)
    {
        return $this->where(['id' => ['=', $id]])->update($data);
    }

    //
    public function addOne($data)
    {
        return $this->insert($data);
    }
}