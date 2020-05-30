<?php


namespace app\api\model;

use think\Model;

class Mes extends Model
{
    protected $name = 'mes';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';


    // 天加一个留言
    public function addOne($data)
    {
        return $this->insert($data);
    }

    // 获取一个用户的留言列表
    public function listByUserId($userId, $page, $size)
    {
         return $this
            ->whereor(['usera_id' => ['=', $userId], 'userb_id' => ['=', $userId]])
            ->useSoftDelete($this->deleteTime)
            ->order('createtime desc')
            ->group('usera_id, userb_id')
            ->page($page, $size)
            ->select();
    }

    // 获取最新的一条消息
    public function infoOne($useraId, $userbId)
    {
        return $this->where(("usera_id = $useraId AND userb_id = $userbId) OR (usera_id = $userbId and userb_id = $useraId"))->useSoftDelete($this->deleteTime)->order('createtime desc')->find();
    }


    // 修改为已读
    public function updateByWhere($where, $data)
    {
        return $this->where($where)->update($data);
    }


    public function listBySql($sql)
    {
        return $this->query($sql);
    }
}