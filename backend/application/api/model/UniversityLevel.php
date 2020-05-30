<?php


namespace app\api\model;

use think\Model;

class UniversityLevel extends Model
{
    protected $name = 'university_level';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';


    // 根据ID获取一个大学的数据
    public function listAll()
    {
        return $this->where(['show_switch' => ['=', 1]])->order('weigh', 'desc')->useSoftDelete($this->deleteTime)->select();
    }


}