<?php


namespace app\api\model;

use think\Model;

class LikeArticle extends Model
{
    protected $name = 'like_article';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';

    // 是否关注文章
    public function infoById($userId, $articleId)
    {
        return $this->where(['user_id' => ['=', $userId], 'article_id' => ['=', $articleId]])->useSoftDelete($this->deleteTime)->find();
    }

    // 修改一个数据
    public function updateOne($id, $data)
    {
        return $this->where(['id' => ['=', $id]])->update($data);
    }

    // 添加一个数据
    public function addOne($data)
    {
        return $this->insert($data);
    }

    // 根据用户ID获取文章
    public function listByUserId($userId, $page, $size)
    {
        return $this
            ->alias('a')
            ->field('a.article_id, c.*')
            ->join('article c', 'a.article_id = c.id')
            ->where(['a.user_id' => ['=', $userId]])
            ->useSoftDelete('a.'.$this->deleteTime)
            ->page($page, $size)
            ->order('a.'.$this->createTime, 'desc')
            ->select();
    }
}