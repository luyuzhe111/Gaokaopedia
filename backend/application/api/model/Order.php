<?php


namespace app\api\model;

use think\Model;

class Order extends Model
{
    protected $name = 'order';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';

    // 添加一个订单
    public function addOne($data)
    {
        return $this->insert($data);
    }

    // 根据条件查询订单
    public function listByWhere($where)
    {
        return $this->where($where)->useSoftDelete($this->deleteTime)->select();
    }

    // 获取并锁定一个订单
    public function infoAndLockById($id)
    {
        return $this->where(['order_id' => ['=', $id]])->useSoftDelete($this->deleteTime)->find();
    }

    // 修改一个订单
    public function updateOne($id, $data)
    {
        return $this->where(['id' => ['=', $id]])->update($data);
    }
}