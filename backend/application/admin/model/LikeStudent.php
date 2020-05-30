<?php

namespace app\admin\model;

use think\Model;
use traits\model\SoftDelete;

class LikeStudent extends Model
{

    use SoftDelete;

    

    // 表名
    protected $name = 'like_student';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';

    // 追加属性
    protected $append = [

    ];
    

    







    public function student()
    {
        return $this->belongsTo('Student', 'usera_id', 'user_id', [], 'LEFT')->setEagerlyType(0);
    }
}
