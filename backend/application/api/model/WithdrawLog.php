<?php


namespace app\api\model;

use think\Model;

class WithdrawLog extends Model
{
    protected $name = 'withdraw_log';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';


    // 添加一个提现
    public function addOne($data)
    {
        return $this->insert($data);
    }

    // 获取需要支付的提现
    public function listNeedPay($size)
    {
        return $this->where(['pay_switch' => ['=', 1], 'status' => ['=', 0]])->useSoftDelete($this->deleteTime)->limit(30)->order('id', 'asc')->select();
    }

    public function updateOne($id, $data)
    {
        return $this->where(['id' => ['=', $id]])->update($data);
    }
}