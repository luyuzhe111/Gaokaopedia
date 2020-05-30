<?php

namespace app\admin\model;

use think\Model;
use traits\model\SoftDelete;

class VipConfig extends Model
{

    use SoftDelete;

    

    // 表名
    protected $name = 'vip_config';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';

    // 追加属性
    protected $append = [
        'vip_level_text'
    ];
    

    protected static function init()
    {
        self::afterInsert(function ($row) {
            $pk = $row->getPk();
            $row->getQuery()->where($pk, $row[$pk])->update(['weigh' => $row[$pk]]);
        });
    }

    
    public function getVipLevelList()
    {
        return ['1' => __('Vip_level 1'), '2' => __('Vip_level 2')];
    }


    public function getVipLevelTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['vip_level']) ? $data['vip_level'] : '');
        $list = $this->getVipLevelList();
        return isset($list[$value]) ? $list[$value] : '';
    }




}
