<?php

return array (
  0 => 
  array (
    'name' => 'wechat',
    'title' => '微信',
    'type' => 'array',
    'content' => 
    array (
    ),
    'value' => 
    array (
      'appid' => 'wxf06ef1898e5e6d55',
      'app_id' => 'wxf06ef1898e5e6d55',
      'app_secret' => 'afdf55fd5d385f0f0900c6b350576310',
      'miniapp_id' => 'wxf06ef1898e5e6d55',
      'mch_id' => '1591095591',
      'key' => 'iqEyMLJMfBzOyJYwEAZGrGXqOIxWp176',
      'notify_url' => '/addons/epay/api/notifyx/type/wechat',
      'cert_client' => '/epay/certs/apiclient_cert.pem',
      'cert_key' => '/epay/certs/apiclient_key.pem',
      'log' => '1',
    ),
    'rule' => '',
    'msg' => '',
    'tip' => '微信参数配置',
    'ok' => '',
    'extend' => '',
  ),
  1 => 
  array (
    'name' => 'alipay',
    'title' => '支付宝',
    'type' => 'array',
    'content' => 
    array (
    ),
    'value' => 
    array (
      'app_id' => '',
      'notify_url' => '/addons/epay/api/notifyx/type/alipay',
      'return_url' => '/addons/epay/api/returnx/type/alipay',
      'ali_public_key' => '',
      'private_key' => '',
      'log' => 1,
    ),
    'rule' => 'required',
    'msg' => '',
    'tip' => '支付宝参数配置',
    'ok' => '',
    'extend' => '',
  ),
  2 => 
  array (
    'name' => '__tips__',
    'title' => '温馨提示',
    'type' => 'array',
    'content' => 
    array (
    ),
    'value' => '请注意微信支付证书路径位于/addons/epay/certs目录下，请替换成你自己的证书<br>appid：APP的appid<br>app_id：公众号的appid<br>app_secret：公众号的secret<br>miniapp_id：小程序ID<br>mch_id：微信商户ID<br>key：微信商户支付的密钥',
    'rule' => '',
    'msg' => '',
    'tip' => '微信参数配置',
    'ok' => '',
    'extend' => '',
  ),
);
