<?php

namespace app\admin\model;

use think\Model;
use traits\model\SoftDelete;

class WithdrawLog extends Model
{

    use SoftDelete;

    

    // 表名
    protected $name = 'withdraw_log';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';

    // 追加属性
    protected $append = [
        'status_text'
    ];
    

    
    public function getStatusList()
    {
        return ['0' => __('Status 0'), '1' => __('Status 1'), '2' => __('Status 2')];
    }


    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }




    public function student()
    {
        return $this->belongsTo('Student', 'user_id', 'user_id', [], 'LEFT')->setEagerlyType(0);
    }
}
