<?php

namespace app\api\controller;

use addons\epay\library\Service;
use app\api\model\Order;
use app\api\model\Price;
use app\api\model\Third;
use app\api\model\VipConfig;
use app\common\controller\Api;
/**
 * 答谢
 */
class Thank extends Api
{

    // 无需登录的接口,*表示全部
    protected $noNeedLogin = ['getPriceList', 'pay'];
    // 无需鉴权的接口,*表示全部
    protected $noNeedRight = ['*'];



    /**
     * 支付下单
     * @ApiTitle    (支付下单)
     * @ApiSummary  (支付下单)
     * @ApiMethod   (POST)
     * @ApiHeaders  (name="token", type="string", required=true, description="请求的Token")
     * @ApiParams   (name="money", type="integer", required=true, description="价格")
     * @ApiParams   (name="userb_id", type="integer", required=true, description="学长ID")
     * @ApiReturnParams   (name="code", type="integer", required=true, sample="0")
     * @ApiReturnParams   (name="msg", type="string", required=true, sample="返回成功")
     * @ApiReturnParams   (name="data", type="object", sample="{'user_id':'int','user_name':'string','profile':{'email':'string','age':'integer'}}", description="扩展数据返回")
     * @ApiReturn   ()
     */
    public function pay()
    {
        $userId = $this->auth->id;

        $money = $this->request->param('money', 0, 'int');
        if(!$money || $money <= 0 || (!is_float($money) && !is_numeric($money))) {
            $this->error('您的操作有误');
        }

        $userbId = $this->request->param('userb_id', 0, 'int');
        if(!$userbId) {
            $this->error('您的操作有误');
        }

        //open_id
        $third = new Third();
        $thirdInfo = $third->getOneByUserId($userId);
        if(!$thirdInfo) {
            $this->error('您提交的内容有误');
        }
        $openid = $thirdInfo['openid'];
        $amount = $money;
        $amount = 0.01;

        $order = new Order();
        $out_trade_no = date("YmdHis", time()) . mt_rand(100000, 999999);
        $insertData = [
            'createtime' => time(),
            'updatetime' => time(),
            'order_id' => $out_trade_no,
            'money' => $amount,
            'type' => 1,
            'status' => 1,
            'other' => json_encode(['user_id' => $userbId, 'money' => $amount]),
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


    /**
     * 获取价目表
     * @ApiTitle    (获取价目表)
     * @ApiSummary  (获取价目表)
     * @ApiMethod   (POST)
     * @ApiReturnParams   (name="code", type="integer", required=true, sample="0")
     * @ApiReturnParams   (name="msg", type="string", required=true, sample="返回成功")
     * @ApiReturnParams   (name="data", type="object", sample="{'user_id':'int','user_name':'string','profile':{'email':'string','age':'integer'}}", description="扩展数据返回")
     * @ApiReturn   ({
        "code": 1,
        "msg": "获取价目表成功",
        "time": "1586398313",
        "data": [
        {
        "id": 1,
        "price": 1,
        "weigh": 0,
        "createtime": 0,
        "updatetime": 0,
        "deletetime": null,
        "show_switch": 1
        }
        ]
        })
     */
    public function getPriceList()
    {
        $price = new Price();
        $priceList = $price->listAll();
        $this->success('获取价目表成功', $priceList);
    }

}