<?php

namespace app\api\controller;

use addons\epay\library\Service;
use addons\third\Third;
use addons\wechat\library\Config as ConfigService;
use app\api\model\Order;
use app\common\controller\Api;
use app\common\library\Auth;
use app\common\model\Area;
use app\common\model\Attachment;
use app\common\model\Version;
use EasyWeChat\Encryption\EncryptionException;
use EasyWeChat\Foundation\Application;
use fast\Http;
use fast\Random;
use think\Config;
use think\Db;
use think\Log;

/**
 * 公共接口
 */
class Common extends Api
{
    protected $noNeedLogin = ['init', 'getSessionKey', 'login', 'codeToToken', 'upload', 'payNotify'];
    protected $noNeedRight = '*';

    /**
     * 加载初始化
     *
     * @param string $version 版本号
     * @param string $lng     经度
     * @param string $lat     纬度
     */
    public function init()
    {
        if ($version = $this->request->request('version')) {
            $lng = $this->request->request('lng');
            $lat = $this->request->request('lat');
            $content = [
                'citydata'    => Area::getCityFromLngLat($lng, $lat),
                'versiondata' => Version::check($version),
                'uploaddata'  => Config::get('upload'),
                'coverdata'   => Config::get("cover"),
            ];
            $this->success('', $content);
        } else {
            $this->error(__('Invalid parameters'));
        }
    }



    /**
     * 上传文件
     * @ApiMethod (POST)
     * @param File $file 文件流
     */
    public function upload()
    {
        $config = get_addon_config('qiniu');

        $file = $this->request->file('file');
        if (!$file || !$file->isValid()) {
            $this->error("请上传有效的文件");
        }
        $fileInfo = $file->getInfo();
        $filePath = $file->getRealPath() ?: $file->getPathname();
        preg_match('/(\d+)(\w+)/', $config['maxsize'], $matches);
        $type = strtolower($matches[2]);
        $typeDict = ['b' => 0, 'k' => 1, 'kb' => 1, 'm' => 2, 'mb' => 2, 'gb' => 3, 'g' => 3];
        $size = (int)$config['maxsize'] * pow(1024, isset($typeDict[$type]) ? $typeDict[$type] : 0);

        $suffix = strtolower(pathinfo($fileInfo['name'], PATHINFO_EXTENSION));
        $suffix = $suffix ? $suffix : 'file';

        $md5 = md5_file($filePath);
        $search = ['$(year)', '$(mon)', '$(day)', '$(etag)', '$(ext)'];
        $replace = [date("Y"), date("m"), date("d"), $md5, '.' . $suffix];
        $object = ltrim(str_replace($search, $replace, $config['savekey']), '/');

        $mimetypeArr = explode(',', strtolower($config['mimetype']));
        $typeArr = explode('/', $fileInfo['type']);

        //检查文件大小
        if (!$file->checkSize($size)) {
            $this->error("起过最大可上传文件限制");
        }

        //验证文件后缀
        if ($config['mimetype'] !== '*' &&
            (
                !in_array($suffix, $mimetypeArr)
                || (stripos($typeArr[0] . '/', $config['mimetype']) !== false && (!in_array($fileInfo['type'], $mimetypeArr) && !in_array($typeArr[0] . '/*', $mimetypeArr)))
            )
        ) {
            $this->error(__('上传格式限制'));
        }

        $savekey = '/' . $object;

        $uploadDir = substr($savekey, 0, strripos($savekey, '/') + 1);
        $fileName = substr($savekey, strripos($savekey, '/') + 1);
        //先上传到本地
        $splInfo = $file->move(ROOT_PATH . '/public' . $uploadDir, $fileName);
        if ($splInfo) {
            $extparam = $this->request->post();
            $filePath = $splInfo->getRealPath() ?: $splInfo->getPathname();
            $sha1 = sha1_file($filePath);
            $imagewidth = $imageheight = 0;
            if (in_array($suffix, ['gif', 'jpg', 'jpeg', 'bmp', 'png', 'swf'])) {
                $imgInfo = getimagesize($splInfo->getPathname());
                $imagewidth = isset($imgInfo[0]) ? $imgInfo[0] : $imagewidth;
                $imageheight = isset($imgInfo[1]) ? $imgInfo[1] : $imageheight;
            }
            $params = array(
                'admin_id' => session('admin.id'),
                'user_id' => $this->auth->id,
                'filesize' => $fileInfo['size'],
                'imagewidth' => $imagewidth,
                'imageheight' => $imageheight,
                'imagetype' => $suffix,
                'imageframes' => 0,
                'mimetype' => $fileInfo['type'],
                'url' => $uploadDir . $splInfo->getSaveName(),
                'uploadtime' => time(),
                'storage' => 'local',
                'sha1' => $sha1,
                'extparam' => json_encode($extparam),
            );
            $attachment = Attachment::create(array_filter($params), true);
            $policy = array(
                'saveKey' => ltrim($savekey, '/'),
            );
            $auth = new \addons\qiniu\library\Auth($config['app_key'], $config['secret_key']);
            $token = $auth->uploadToken($config['bucket'], null, $config['expire'], $policy);
            $multipart = [
                ['name' => 'token', 'contents' => $token],
                [
                    'name' => 'file',
                    'contents' => fopen($filePath, 'r'),
                    'filename' => $fileName,
                ]
            ];
            try {
                $client = new \GuzzleHttp\Client();
                $res = $client->request('POST', $config['uploadurl'], [
                    'multipart' => $multipart
                ]);
                $code = $res->getStatusCode();
                //成功不做任何操作
            } catch (\GuzzleHttp\Exception\ClientException $e) {
                $attachment->delete();
                unlink($filePath);
                $this->error("上传失败");
            }
            $url = '/' . $object;
            //上传成功后将存储变更为qiniu
            $attachment->storage = 'qiniu';
            $attachment->save();
            $this->success("上传成功", ['url' => cdnurl($url), 'save_path' => $url]);
        } else {
            $this->error('上传失败');
        }
        return;
    }



    /**
     * 支付回调
     * @ApiTitle    (支付回调)
     * @ApiSummary  ()
     * @ApiMethod   (POST)
     */
    public function payNotify()
    {
        $payType = 'wechat';
        $pay = Service::checkNotify($payType);
        if (!$pay) {
            echo '签名错误';
            return;
        }
        $data = $pay->verify();
        $order = new Order();

        Db::startTrans();
        try {
            $out_trade_no = $data['out_trade_no'];
            $orderInfo = $order->infoAndLockById($out_trade_no);
            if(!$orderInfo) {
                Db::rollback();
            } else {
                if($orderInfo['status'] != 1) {
                    Db::rollback();
                } else {
                    $res = $order->updateOne($orderInfo['id'], ['status' => 3, 'updatetime' => time()]);
                    if(!$res) {
                        Db::rollback();
                    } else {
                        // 根据订单内容修改相应的数据
                        $res = handleOrder($orderInfo);
                        if(!$res) {
                            Db::rollback();
                        } else {
                            Db::commit();
                        }
                    }
                }
            }
        } catch (\Exception $e) {

        }
        $pay->success();
    }


    /**
     * 获取SessionKey
     * @ApiTitle    (获取SessionKey)
     * @ApiSummary  (使用code换取SessionKey)
     * @ApiMethod   (POST)
     * @ApiParams   (name="code", type="string", required=true, description="code")
     */
    public function getSessionKey(){
        $config = get_addon_config('third');
        $code = $this->request->param('code');
        if (!$code) {
            $this->error('参数不正确');
        }
        $params = [
            'appid'      => $config['wechat']['app_id'],
            'secret'     => $config['wechat']['app_secret'],
            'js_code'    => $code,
            'grant_type' => 'authorization_code'
        ];
        $result = Http::sendRequest("https://api.weixin.qq.com/sns/jscode2session", $params, 'GET');
        if ($result['ret']) {
            $json = (array)json_decode($result['msg'], true);
            $this->success("SessionKey",$json);
        }else{
            $this->error('获取失败',$result);
        };
    }


    /**
     * 小程序登录
     * @ApiTitle    (小程序登录)
     * @ApiSummary  (微信小程序登录)
     * @ApiMethod   (POST)
     * @ApiParams   (name="sessionKey", type="string", required=true, description="sessionKey")
     * @ApiParams   (name="iv", type="string", required=true, description="iv")
     * @ApiParams   (name="encryptedData", type="object", required=true, description="encryptedData")
     * @ApiParams   (name="store_id", type="integer", required=false, description="门店ID，二维码进入小程序可能会带入此值")
     */
    public function login(){
        $sessionKey = $this->request->param('sessionKey');
        $iv = $this->request->param('iv');
        $encryptData = $this->request->param('encryptedData');
        $storeId = $this->request->param('store_id',0,'int');
        if (!$sessionKey || !$iv || !$encryptData) {
            $this->error('参数不正确');
        }

        try {
            $app = new Application(ConfigService::load());
            $miniProgram = $app->mini_program;
            $result = $miniProgram->encryptor->decryptData($sessionKey, $iv, $encryptData);
            if (isset($result['openId'])) {
                $platform = 'wxapp';
                $data = [
                    'openid' => $result['openId'],
                    'userinfo' => [
                        'nickname' => $result['nickName'],
                        'unionid' => isset($result['unionId']) ? $result['unionId'] : '',
                    ],
                    'access_token' => $sessionKey,
                    'refresh_token' => '',
                    'expires_in' => 0,
                ];
                $extend = [
                    'group_id'=>1,
                    'gender' => $result['gender'],
                    'nickname' => $result['nickName'],
                    'name' => $result['nickName'],
                    'avatar' => $result['avatarUrl'],
                    'store_id'=>$storeId,
                ];
                $ret = \addons\third\library\Service::connect($platform, $data, $extend);
                if ($ret) {
                    $auth = Auth::instance();
                    $this->success('登录成功', ['userInfo' => $auth->getUserinfo()]);
                } else {
                    $this->error('登录失败');
                }
            } else {
                $this->error('登录失败',$result);
            }
        } catch (EncryptionException $e) {
            $this->error($e->getMessage());
        }
    }


    /**
     * CODE换取Token
     * @ApiTitle    (CODE换取Token)
     * @ApiSummary  (使用code换取Token)
     * @ApiMethod   (POST)
     * @ApiParams   (name="code", type="string", required=true, description="code")
     */
    public function codeToToken()
    {
        $config = get_addon_config('third');
        $code = $this->request->param('code');
        if (!$code) {
            $this->error('参数不正确');
        }
        $params = [
            'appid' => $config['wechat']['app_id'],
            'secret' => $config['wechat']['app_secret'],
            'js_code' => $code,
            'grant_type' => 'authorization_code',
        ];
        $result = Http::sendRequest("https://api.weixin.qq.com/sns/jscode2session", $params, 'GET');
        if ($result['ret']) {
            $json = (array)json_decode($result['msg'], true);
            if (isset($json['openid'])) {
                $userId = Db::name('third')->where(['platform'=>'wxapp','openid'=>$json['openid']])->value('user_id');
                $auth = Auth::instance();
                $ret=$auth->direct($userId);
                if ($ret) {
                    Db::name('third')->where(['user_id'=>$userId])->setField('access_token', $json['session_key']);
                    $userInfo = $auth->getUserinfo();
                    $student = new \app\api\model\Student();
                    $studentInfo = $student->infoByUserId($userInfo['id']);
                    if($studentInfo) {
                        $userInfo['is_register'] = 1;
                    } else {
                        $userInfo['is_register'] = 0;
                    }
                    $this->success("登录成功", ['userInfo' => $userInfo]);
                } else {
                    $this->error("登录失败",$userId,'401');
                }
            } else {
                $this->error("登录失败",$json);
            }
        } else {
            $this->error("登录失败");
        }
    }
}
