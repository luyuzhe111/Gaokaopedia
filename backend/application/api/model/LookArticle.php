<?php


namespace app\api\model;

use think\Model;

class LookArticle extends Model
{
    protected $name = 'look_article';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';

    // 是否查看文章
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

    // 修改一些数据
    public function updateSomeByUserId($userId, $data)
    {
        return $this->where(['user_id' => ['=', $userId]])->update($data);
    }

    // 获取用户的浏览历史
    public function listById($userId, $page, $size)
    {
        return $this
            ->alias('a')
            ->field('a.article_id, a.user_id, b.user_id, b.level, b.nickname, b.head_image, b.school_id, b.subject_ids, b.up_id, b.college_id, b.university_id, b.graduated_id, b.starttime, b.endtime, b.show_switch, b.email, b.vip_level, b.vip_endtime, c.*')
            ->join('student b', 'a.user_id = b.user_id')
            ->join('article c', 'a.article_id = c.id')
            ->where(['a.user_id' => ['=', $userId]])
            ->useSoftDelete('a.'.$this->deleteTime)
            ->page($page, $size)
            ->order('a.'.$this->createTime, 'desc')
            ->select();
    }
}