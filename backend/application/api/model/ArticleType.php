<?php


namespace app\api\model;

use think\Model;

class ArticleType extends Model
{
    protected $name = 'article_type';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';


    // 获取所有的文章类型
    public function listAll()
    {
        return $this->where(['show_switch' => ['=', 1]])->useSoftDelete($this->deleteTime)->select();
    }
}