<?php


namespace app\api\model;

use think\Log;
use think\Model;

class Article extends Model
{
    protected $name = 'article';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';


    // 根据条件获取文章
    public function listByWhere($where, $page, $size, $schoolIds)
    {
        $schoolIds = implode(',', $schoolIds);
        $data = $this
            ->alias('a')
            ->field('a.*, b.user_id, b.level, b.nickname, b.head_image, b.school_id, b.subject_ids, b.up_id, b.college_id, b.university_id, b.graduated_id, b.starttime, b.endtime, b.show_switch, b.email, b.vip_level, b.vip_endtime')
            ->join('student b', 'a.user_id = b.user_id')
            ->where($where)
            ->where(' (b.school_id in ('.$schoolIds.')  and a.show_type  = 1) or a.show_type = 2 ')
//            ->where(['b.school_id' => ['in', $schoolIds], 'a.show_type' => ['=', 1]])
//            ->whereor('1=1')
            ->order('a.createtime', 'desc')
            ->page($page, $size)
            ->select();

        Log::write($this->getLastSql(), 'debug');
        return $data;
    }

    // 获取文章详细
    public function infoById($id)
    {
        return $this
            ->alias('a')
            ->field('a.*, b.user_id, b.level, b.nickname, b.head_image, b.school_id, b.subject_ids, b.up_id, b.college_id, b.university_id, b.graduated_id, b.starttime, b.endtime, b.show_switch, b.email, b.vip_level, b.vip_endtime')
            ->join('student b', 'a.user_id = b.user_id')
            ->where(['a.id' => ['=', $id]])
            ->useSoftDelete('a.'.$this->deleteTime)->find();
    }

    // 添加一个文章
    public function addOne($data)
    {
        return $this->insert($data);
    }

    // 获取我发布的文章
    public function listByUserId($userId, $page, $size)
    {
        return $this->where(['user_id' => ['=', $userId]])->page($page, $size)->order($this->createTime, 'desc')->useSoftDelete($this->deleteTime)->select();
    }

    // 获取
    public function infoByIdNoWhere($articleId)
    {
        return $this->where(['id' => ['=', $articleId]])->find();
    }

    // 删除某个文章
    public function delOne($articleId, $data)
    {
        return $this->where(['id' => ['=', $articleId]])->update($data);
    }
}