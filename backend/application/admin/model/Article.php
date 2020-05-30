<?php

namespace app\admin\model;

use think\Model;
use traits\model\SoftDelete;

class Article extends Model
{

    use SoftDelete;

    

    // 表名
    protected $name = 'article';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';

    // 追加属性
    protected $append = [
        'show_type_text'
    ];
    

    protected static function init()
    {
        self::afterInsert(function ($row) {
            $pk = $row->getPk();
            $row->getQuery()->where($pk, $row[$pk])->update(['weigh' => $row[$pk]]);
        });
    }

    
    public function getShowTypeList()
    {
        return ['1' => __('Show_type 1'), '2' => __('Show_type 2')];
    }


    public function getShowTypeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['show_type']) ? $data['show_type'] : '');
        $list = $this->getShowTypeList();
        return isset($list[$value]) ? $list[$value] : '';
    }




    public function articletype()
    {
        return $this->belongsTo('ArticleType', 'article_type_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }
}
