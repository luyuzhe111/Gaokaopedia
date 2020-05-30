<?php
/**
 * Created by PhpStorm.
 * User: DELL
 * Date: 2019/5/31
 * Time: 9:25
 */

/**
 * Created by PhpStorm.
 * User: DELL
 * Date: 2019/4/9
 * Time: 10:58
 */
namespace app\command;

use app\api\model\Order;
use think\console\Input;
use think\console\Output;
use think\console\Command;
use addons\epay\library\Service;
use think\Db;
use think\Log;
use Yansongda\Pay\Pay;

class OrderQuery extends Command{

    protected function configure()
    {
        $this->setName('OrderQuery')->setDescription('查询订单的支付结果');
    }

    protected function execute(Input $input, Output $output)
    {
        $config = Service::getConfig('wechat');
        $pay = new Pay($config);
        $order = new Order();

        $where = ['status' => ['=', 1], 'createtime' => ['<', bcsub(time(), 3600, 0)]]; // 下单已经一小时并等待支付的订单
        $orderList = $order->listByWhere($where);
        foreach ($orderList as $v) {
            $res = $pay->driver('wechat')->find($v['order_id']);
            if(isset($res['return_code']) && ($res['return_code'] == 'SUCCESS') && isset($res['result_code']) && ($res['result_code'] == 'SUCCESS')) {
                Db::startTrans();
                try {
                    $res = $order->infoAndLockById($v['id']);
                    if(!$res) {
                        Db::rollback();
                    } else {
                        if($res['status'] == 3) {
                            Db::rollback();
                        } else {
                            $res = $order->updateOne($v['id'], ['status' => 3, 'updatetime' => time()]);
                            if(!$res) {
                                Db::rollback();
                            } else {
                                // 根据订单内容修改相应的数据
                                $res = handleOrder($v);
                                if(!$res) {
                                    Db::rollback();
                                } else {
                                    Db::commit();
                                }
                            }
                        }
                    }
                } catch (\Exception $e) {
                    Db::rollback();
                    Log::write('订单查询成功，脚本修改异常:'.$v['order_id'], 'debug');
                }
            } else {
                Log::write('订单查询异常:'.$v['order_id'], 'debug');
            }
        }
    }
}