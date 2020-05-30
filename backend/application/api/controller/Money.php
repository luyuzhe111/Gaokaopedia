<?php

namespace app\api\controller;

use app\api\model\User as UserModel;
use app\api\model\UserMoneyLog;
use app\api\model\WithdrawLog;
use app\common\controller\Api;
use think\Db;

/**
 * 钱相关
 */
class Money extends Api
{

    // 无需登录的接口,*表示全部
    protected $noNeedLogin = [''];
    // 无需鉴权的接口,*表示全部
    protected $noNeedRight = ['*'];


    /**
     * 获取学生余额
     * @ApiTitle    (获取学生余额)
     * @ApiSummary  (获取学生余额)
     * @ApiMethod   (GET)
     * @ApiHeaders  (name="token", type="string", required=true, description="请求的Token")
     * @ApiReturnParams   (name="code", type="integer", required=true, sample="0")
     * @ApiReturnParams   (name="msg", type="string", required=true, sample="返回成功")
     * @ApiReturnParams   (name="data", type="object", sample="{'user_id':'int','user_name':'string','profile':{'email':'string','age':'integer'}}", description="扩展数据返回")
     * @ApiReturn   ({
        "code": 1,
        "msg": "获取学生余额成功",
        "time": "1586487848",
        "data": "0.00"
        })
     */
    public function getStudentMoney()
    {
        $userId = $this->auth->id;

        $user = new UserModel();
        $userInfo = $user->infoById($userId);
        if(!$userInfo) {
            $this->error('您的操作有误');
        }

        $this->success('获取学生余额成功', $userInfo['money']);
    }



    /**
     * 获取账户资金明细
     * @ApiTitle    (获取账户资金明细)
     * @ApiSummary  (获取账户资金明细)
     * @ApiMethod   (GET)
     * @ApiHeaders  (name="token", type="string", required=true, description="请求的Token")
     * @ApiParams   (name="page", type="integer", required=true, description="分页码")
     * @ApiParams   (name="size", type="integer", required=true, description="分页数")
     * @ApiReturnParams   (name="code", type="integer", required=true, sample="0")
     * @ApiReturnParams   (name="msg", type="string", required=true, sample="返回成功")
     * @ApiReturnParams   (name="data", type="object", sample="{'user_id':'int','user_name':'string','profile':{'email':'string','age':'integer'}}", description="扩展数据返回")
     * @ApiReturn   ({
        "code": 1,
        "msg": "获取账户资金明细成功",
        "time": "1586488760",
        "data": [
        {
        "id": 1,
        "user_id": 1,
        "money": "0.00",
        "before": "0.00",
        "after": "0.00",
        "memo": "测试",
        "createtime": "2020-04-10"
        }
        ]
        })
     */
    public function listMoneyLog()
    {
        $userId = $this->auth->id;

        $page = $this->request->param('page', 1, 'int');
        $size = $this->request->param('size', 10, 'int');

        $userMoneyLog = new UserMoneyLog();
        $userMoneyLogList = $userMoneyLog->listByUserId($userId, $page, $size);
        foreach ($userMoneyLogList as $k => $v) {
            $userMoneyLogList[$k]['createtime'] = date('Y-m-d', $v['createtime']);
        }
        $this->success('获取账户资金明细成功', $userMoneyLogList);
    }



    /**
     * 申请提现
     * @ApiTitle    (申请提现)
     * @ApiSummary  (申请提现)
     * @ApiMethod   (POST)
     * @ApiHeaders  (name="token", type="string", required=true, description="请求的Token")
     * @ApiParams   (name="money", type="integer", required=true, description="提现金额")
     * @ApiReturnParams   (name="code", type="integer", required=true, sample="0")
     * @ApiReturnParams   (name="msg", type="string", required=true, sample="返回成功")
     * @ApiReturnParams   (name="data", type="object", sample="{'user_id':'int','user_name':'string','profile':{'email':'string','age':'integer'}}", description="扩展数据返回")
     * @ApiReturn   ({
        "code": 0,
        "msg": "您的账户余额不足",
        "time": "1586491759",
        "data": null
        })
     */
    public function gotMoney()
    {
        $userId = $this->auth->id;

        $money = $this->request->param('money', 0, 'float');
        $money = bcmul($money, 1, 2);
        if(!$money || bcmul($money, 100, 0) < 1) {
            $this->error('您输入的提现金额有误');
        }

        $money = 0.01;

        Db::startTrans();
        try {
            $user = new UserModel();
            $userInfo = $user->infoAndLockById($userId);
            if(!$userInfo) {
                Db::rollback();
                $this->error('您的操作有误');
            }

            if(bcmul($userInfo['money'], 100, 0) < bcmul($money, 100, 0)) {
                Db::rollback();
                $this->error('您的账户余额不足');
            }

            $withdrawLog = new WithdrawLog();
            $insertData = [
                'user_id' => $userId,
                'money' => $money,
                'createtime' => time(),
                'updatetime' => time(),
                'order_id' => date("YmdHis", time()) . mt_rand(100000, 999999)
            ];
            $res = $withdrawLog->addOne($insertData);
            if(!$res) {
                Db::rollback();
                $this->error('抱歉，系统异常，请您重试或联系客服');
            }

            $insertData = [
                'user_id' => $userId,
                'money' => $money,
                'memo' => '提现冻结',
                'createtime' => time()
            ];
            $userMoneyLog = new UserMoneyLog();
            $res = $userMoneyLog->addOne($insertData);
            if(!$res) {
                Db::rollback();
                $this->error('抱歉，系统异常，请您重试或联系客服');
            }

            $updateData = [
                'money' => bcsub($userInfo['money'], $money, 2)
            ];
            $res = $user->updateOne($userId, $updateData);
            if(!$res) {
                Db::rollback();
                $this->error('抱歉，系统异常，请您重试或联系客服');
            }
            Db::commit();
            $this->success('您的账户已经冻结提现金额，请等待平台审核后自动打款到微信零钱');

        } catch (\Exception $e) {
            Db::rollback();
            $this->error($e->getTrace()[0]['args'][0]);
        }
        $this->success('您的账户已经冻结提现金额，请等待平台审核后自动打款到微信零钱');
    }
}