<?php

namespace app\api\controller;

use addons\epay\library\Service;
use app\api\model\Order;
use app\api\model\Third;
use app\api\model\VipConfig;
use app\common\controller\Api;
use app\api\model\Student as StudentModel;

/**
 * 高中相关
 */
class Vip extends Api
{

    // 无需登录的接口,*表示全部
    protected $noNeedLogin = [''];
    // 无需鉴权的接口,*表示全部
    protected $noNeedRight = ['*'];


    /**
     * 获取vip卡购买的配置
     * @ApiTitle    (获取vip卡购买的配置)
     * @ApiSummary  (获取vip卡购买的配置)
     * @ApiMethod   (POST)
     * @ApiParams   (name="name", type="string", required=true, description="高中名")
     * @ApiParams   (name="province_id", type="integer", required=true, description="省ID")
     * @ApiParams   (name="city_id", type="integer", required=true, description="城市ID")
     * @ApiReturnParams   (name="code", type="integer", required=true, sample="0")
     * @ApiReturnParams   (name="msg", type="string", required=true, sample="返回成功")
     * @ApiReturnParams   (name="data", type="object", sample="{'user_id':'int','user_name':'string','profile':{'email':'string','age':'integer'}}", description="扩展数据返回")
     * @ApiReturn   ({
        "code": 1,
        "msg": "获取vip卡购买的配置成功",
        "time": "1586511854",
        "data": [
        {
        "id": 1,
        "money": 100,
        "name": "省月卡",
        "des_content": "e额的撒大苏打",
        "show_switch": 1,
        "weigh": 0,
        "createtime": 0,
        "updatetime": 0,
        "deletetime": null,
        "vip_endtime": "2020-08-02"
        }
        ]
        })
     */
    public function getVipConf()
    {
        $userId = $this->auth->id;

        $student = new StudentModel();
        $studentInfo = $student->infoByUserIdCanShow($userId);
        if(!$studentInfo) {
            $this->error('您的操作有误');
        }

        $vipConfig = new VipConfig();
        $vipConfigList = $vipConfig->listAll();
        foreach ($vipConfigList as $k => $v) {
            if($studentInfo['vip_endtime'] > time() && $v['vip_level'] == $studentInfo['vip_level']) {
                $vipConfigList[$k]['vip_endtime'] = date('Y-m-d', $studentInfo['vip_endtime']);
            } else {
                $vipConfigList[$k]['vip_endtime'] = '';
            }
        }
        $this->success('获取vip卡购买的配置成功', $vipConfigList);
    }



    /**
     * 购买vip卡
     * @ApiTitle    (购买vip卡)
     * @ApiSummary  (购买vip卡)
     * @ApiMethod   (POST)
     * @ApiHeaders  (name="token", type="string", required=true, description="请求的Token")
     * @ApiParams   (name="vip_id", type="string", required=true, description="vip卡ID")
     * @ApiReturnParams   (name="code", type="integer", required=true, sample="0")
     * @ApiReturnParams   (name="msg", type="string", required=true, sample="返回成功")
     * @ApiReturnParams   (name="data", type="object", sample="{'user_id':'int','user_name':'string','profile':{'email':'string','age':'integer'}}", description="扩展数据返回")
     * @ApiReturn   ()
     */
    public function buyVip()
    {
        $userId = $this->auth->id;

        $vipId = $this->request->param('vip_id', 0, 'int');
        if(!$vipId) {
            $this->error('您的操作有误');
        }

        $vipConfig = new VipConfig();
        $vipConfigInfo = $vipConfig->infoById($vipId);
        if(!$vipConfigInfo) {
            $this->error('您的操作有误');
        }

        //open_id
        $third = new Third();
        $thirdInfo = $third->getOneByUserId($userId);
        if(!$thirdInfo) {
            $this->error('您提交的内容有误');
        }
        $openid = $thirdInfo['openid'];
        $amount = $vipConfigInfo['money'];
        $amount = 0.01;

        $order = new Order();
        $out_trade_no = date("YmdHis", time()) . mt_rand(100000, 999999);
        $insertData = [
            'createtime' => time(),
            'updatetime' => time(),
            'order_id' => $out_trade_no,
            'money' => $amount,
            'type' => 2,
            'status' => 1,
            'other' => json_encode(['user_id' => $userId, 'keep' => 30, 'vip_level' => $vipConfigInfo['vip_level']]),
            'user_id' => $userId
        ];
        $res = $order->addOne($insertData);
        if(!$res) {
            $this->error('订单提交错误');
        }

        //订单标题
        $title = 'FastAdmin测试订单';

        //其他
        $type = 'wechat';
        $method = 'miniapp';

        //回调链接
        $notifyurl = 'http://school.t.brotop.cn/api/Common/payNotify';
        $returnurl = $this->request->root(true) . '/addons/epay/index/returnx/paytype/' . $type . '/out_trade_no/' . $out_trade_no;

        //订单号
        $params = [
            'amount' => $amount,
            'orderid' => $out_trade_no,
            'type' => $type,
            'title' => $title,
            'notifyurl' => $notifyurl,
            'returnurl' => $returnurl,
            'method' => $method,
            'openid' => $openid,
            'auth_code' => uniqid()
        ];
        $this->success('申请支付', json_decode(Service::submitOrder($params), true));
    }
}