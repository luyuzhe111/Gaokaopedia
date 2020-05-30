<?php


namespace app\api\model;

use think\Model;

class LikeUniversity extends Model
{
    protected $name = 'like_university';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';


    // 获取用户关注的大学
    public function listByUserId($userId, $page, $size)
    {
        return $this
            ->alias('a')
            ->field('a.university_id, a.user_id, b.*')
            ->join('university b', 'a.university_id = b.id')
            ->where(['a.user_id' => ['=', $userId], 'b.show_switch' => ['=', 1], 'b.deletetime' => null])
            ->useSoftDelete('a.'.$this->deleteTime)
            ->order('a.'.$this->createTime, 'desc')
            ->page($page, $size)
            ->select();
    }


    // 获取是否关注
    public function infoById($userId, $universityId)
    {
        return $this->where(['university_id' => ['=', $universityId], 'user_id' => ['=', $userId]])->useSoftDelete($this->deleteTime)->find();
    }

    // 修改一条数据
    public function updateOne($id, $data)
    {
        return $this->where(['id' => ['=', $id]])->update($data);
    }

    // 添加一条数据
    public function addOne($data)
    {
        return $this->insert($data);
    }
}