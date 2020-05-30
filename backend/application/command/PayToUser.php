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

use app\api\model\Third;
use app\api\model\User;
use app\api\model\UserMoneyLog;
use app\api\model\WithdrawLog;
use think\console\Input;
use think\console\Output;
use think\console\Command;
use addons\epay\library\Service;
use think\Db;
use think\Log;
use Yansongda\Pay\Pay;

class PayToUser extends Command{

    protected function configure()
    {
        $this->setName('PayToUser')->setDescription('企业微信付款到用户，每1分钟执行，执行必须带锁防止同时出现两个脚本');
    }

    protected function execute(Input $input, Output $output)
    {
        $config = Service::getConfig('wechat');
        $pay = new Pay($config);
        $withdrawLog = new WithdrawLog();
        $withdrawLogList = $withdrawLog->listNeedPay(30);
        $third = new Third();
        $userMoneyLog = new UserMoneyLog();

        try {
            foreach ($withdrawLogList as $v) {
                $thirdInfo = $third->getOneByUserId($v['user_id']);
                $order = [
                    'partner_trade_no' => $v['order_id'],
                    'openid' => $thirdInfo['openid'],
                    'check_name' => 'NO_CHECK',
                    'amount' => bcmul($v['money'], 10000, 0),
                    'desc' => '提现到零钱',
                    'mchid' => '1591095591',
                    'mch_appid' => 'wxf06ef1898e5e6d55',
                    'nonce_str' => uniqid(),
                ];
                $res = $pay->driver('wechat')->gateway('transfer')->pay($order);
                if(isset($res['return_code']) && ($res['return_code'] == 'SUCCESS') && isset($res['result_code']) && ($res['result_code'] == 'SUCCESS')) {
                    Db::startTrans();
                    try {
                        $insertData = [
                            'user_id' => $v['user_id'],
                            'money' => $v['money'],
                            'memo' => '提现到账',
                            'createtime' => time(),
                        ];
                        $res = $userMoneyLog->addOne($insertData);
                        if(!$res) {
                            Db::rollback();
                        } else {
                            $res = $withdrawLog->updateOne($v['id'], ['status' => 1, 'updatetime' => time(), 'deletetime' => time()]);
                            if(!$res) {
                                Db::rollback();
                            } else {
                                Db::commit();
                            }
                        }
                    } catch (\Exception $e) {
                        Db::rollback();
                    }
                } else {
                    $user = new User();
                    Db::startTrans();
                    try {
                        $userInfo = $user->infoAndLockById($v['user_id']);
                        if(!$userInfo) {
                            Db::rollback();
                        } else {
                            $insertData = [
                                'user_id' => $v['user_id'],
                                'money' => $v['money'],
                                'memo' => '提现失败，全额返还',
                                'createtime' => time(),
                            ];
                            $res = $userMoneyLog->addOne($insertData);
                            if(!$res) {
                                Db::rollback();
                            } else {
                                $res = $withdrawLog->updateOne($v['id'], ['status' => 2, 'updatetime' => time()]);
                                if(!$res) {
                                    Db::rollback();
                                } else { // 返还
                                    $res = $user->updateOne($v['user_id'], ['money' => bcadd($userInfo['money'], $v['money'], 0)]);
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
                    }
                }
            }
        } catch (\Exception $e) {
            Log::write($e, '错误');
        }
    }
}