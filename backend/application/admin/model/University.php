<?php

namespace app\admin\model;

use think\Model;
use traits\model\SoftDelete;

class University extends Model
{

    use SoftDelete;

    

    // 表名
    protected $name = 'university';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';

    // 追加属性
    protected $append = [

    ];
    

    protected static function init()
    {
        self::afterInsert(function ($row) {
            $pk = $row->getPk();
            $row->getQuery()->where($pk, $row[$pk])->update(['weigh' => $row[$pk]]);
        });
    }

    







    public function province()
    {
        return $this->belongsTo('Province', 'province_id', 'code', [], 'LEFT')->setEagerlyType(0);
    }


    public function city()
    {
        return $this->belongsTo('City', 'city_id', 'code', [], 'LEFT')->setEagerlyType(0);
    }


    public function universitylevel()
    {
        return $this->belongsTo('UniversityLevel', 'level_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }
}
