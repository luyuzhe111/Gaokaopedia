<?php


namespace app\api\model;

use think\Model;

class Student extends Model
{
    protected $name = 'student';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';

    // 添加一条学生的数据
    public function addOne($data)
    {
        return $this->insert($data);
    }

    // 获取高中同校的本校学长总数
    public function countByUniversityIdAndSchoolId($universityId, $schoolId)
    {
        return $this->where(['university_id' => ['=', $universityId], 'show_switch' => ['=', 1], 'school_id' => ['=', $schoolId]])->count();
    }

    // 获取高中同城/省的本校学长总数
    public  function countByUniversityIdAndSchoolIds($universityId, $schoolIds)
    {
        return $this->where(['university_id' => ['=', $universityId], 'show_switch' => ['=', 1], 'school_id' => ['in', $schoolIds]])->count();
    }

    // 根据用户ID获取一个可以展示的学生的数据
    public function infoByUserIdCanShow($userId)
    {
        return $this
            ->where(['user_id' => ['eq', $userId]])
//            ->useSoftDelete($this->deleteTime)
            ->find();
    }

    // 获取高中同城/省的本校学长
    public  function listByUniversityIdAndSchoolIds($where, $page, $size)
    {
        return $this
            ->where($where)
            ->order($this->createTime, 'desc')
            ->page($page, $size)
            ->select();
    }

    // 修改一个学生的数据
    public function updateOne($id, $data)
    {
        return $this->where(['user_id' => ['=', $id]])->update($data);
    }


    // 修改一群学生的数据
    public function updateByIds($userIds, $data)
    {
        return $this->where(['user_id' => ['in', $userIds]])->update($data);
    }

    // 根据条件查询
    public function listByWhere($where)
    {
        return $this->where($where)->select();
    }


    public function infoByUserId($userId)
    {
        return $this->where(['user_id' => ['=', $userId]])->find();
    }
}