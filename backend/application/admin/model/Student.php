<?php

namespace app\admin\model;

use think\Model;
use traits\model\SoftDelete;

class Student extends Model
{

    use SoftDelete;

    

    // 表名
    protected $name = 'student';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'level_text',
        'starttime_text',
        'endtime_text',
        'vip_level_text',
        'vip_endtime_text'
    ];
    

    
    public function getLevelList()
    {
        return ['1' => __('Level 1'), '2' => __('Level 2')];
    }

    public function getVipLevelList()
    {
        return ['0' => __('Vip_level 0'), '1' => __('Vip_level 1'), '2' => __('Vip_level 2')];
    }


    public function getLevelTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['level']) ? $data['level'] : '');
        $list = $this->getLevelList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getStarttimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['starttime']) ? $data['starttime'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }


    public function getEndtimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['endtime']) ? $data['endtime'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }


    public function getVipLevelTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['vip_level']) ? $data['vip_level'] : '');
        $list = $this->getVipLevelList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getVipEndtimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['vip_endtime']) ? $data['vip_endtime'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }

    protected function setStarttimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }

    protected function setEndtimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }

    protected function setVipEndtimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }


    public function school()
    {
        return $this->belongsTo('School', 'school_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }


    public function subject()
    {
        return $this->belongsTo('Subject', 'subject_ids', 'id', [], 'LEFT')->setEagerlyType(0);
    }


    public function up()
    {
        return $this->belongsTo('Up', 'up_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }


    public function college()
    {
        return $this->belongsTo('College', 'college_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }


    public function graduated()
    {
        return $this->belongsTo('Graduated', 'graduated_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }
}
