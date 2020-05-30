<?php

namespace app\api\controller;

use app\common\controller\Api;
use app\api\model\Mes as MesModel;
use app\api\model\Student;
use think\Log;

/**
 * 消息相关
 */
class Mes extends Api
{

    // 无需登录的接口,*表示全部
    protected $noNeedLogin = ['getMyMes'];
    // 无需鉴权的接口,*表示全部
    protected $noNeedRight = ['*'];



    /**
     * 发一条消息给学长
     * @ApiTitle    (发一条消息给学长)
     * @ApiSummary  (发一条消息给学长)
     * @ApiMethod   (POST)
     * @ApiHeaders  (name="token", type="string", required=true, description="请求的Token")
     * @ApiParams   (name="userb_id", type="integer", required=true, description="学长用户ID")
     * @ApiParams   (name="des_content", type="string", required=true, description="内容")
     * @ApiReturnParams   (name="code", type="integer", required=true, sample="0")
     * @ApiReturnParams   (name="msg", type="string", required=true, sample="返回成功")
     * @ApiReturnParams   (name="data", type="object", sample="{'user_id':'int','user_name':'string','profile':{'email':'string','age':'integer'}}", description="扩展数据返回")
     * @ApiReturn   ({
        "code": 0,
        "msg": "消息不能是空的哦",
        "time": "1586397638",
        "data": null
        })
     */
    public function sendMes()
    {
        $useraId = $this->auth->id;

        $userbId = $this->request->param('userb_id', 0, 'int');
        $desContent = $this->request->param('des_content', '', 'string');

        if(!$userbId) {
            $this->error('您的操作有误');
        }

        if($userbId == $useraId) {
            $this->error('不能发消息给自己哦');
        }

        if(!$desContent) {
            $this->error('消息不能是空的哦');
        }

        if(mb_strlen($desContent) > 200) {
            $this->error('消息最多200哦');
        }

        $insertData = [
            'usera_id' => $useraId,
            'userb_id' => $userbId,
            'createtime' => time(),
            'updatetime' => time(),
            'des_content' => $desContent
        ];
        $mes = new MesModel();
        $res = $mes->addOne($insertData);
        if(!$res) {
            $this->error('留言失败了，请您重试一下');
        }
        $this->success('留言成功了');
    }




    /**
     * 获取我的所有的留言列表
     * @ApiTitle    (获取我的所有的留言列表)
     * @ApiSummary  (获取我的所有的留言列表)
     * @ApiMethod   (GET)
     * @ApiHeaders  (name="token", type="string", required=true, description="请求的Token")
     * @ApiParams   (name="page", type="integer", required=true, description="")
     * @ApiParams   (name="size", type="integer", required=true, description="")
     * @ApiReturnParams   (name="code", type="integer", required=true, sample="0")
     * @ApiReturnParams   (name="msg", type="string", required=true, sample="返回成功")
     * @ApiReturnParams   (name="data", type="object", sample="{'user_id':'int','user_name':'string','profile':{'email':'string','age':'integer'}}", description="扩展数据返回")
     * @ApiReturn   ({
        "code": 1,
        "msg": "",
        "time": "1586503795",
        "data": [
        {
        "id": 3,
        "usera_id": 1,
        "userb_id": 3,
        "createtime": "2020-04-09",
        "updatetime": 1586413562,
        "des_content": "1",
        "readtime": null（用于判断是否已读）,
        "deletetime": null
        },
        {
        "id": 2,
        "usera_id": 1,
        "userb_id": 2,
        "createtime": "2020-04-09",
        "updatetime": 1586413562,
        "des_content": "1",
        "readtime": null,
        "deletetime": null,
        "head_image": "www.a.jpg"
        }
        ]
        })
     */
    public function getMyMes()
    {
        $userId = $this->auth->id;

        $page = $this->request->param('page', 1, 'int');
        $size = $this->request->param('size', 10, 'int');

        $mes = new MesModel();
        $mesList = $mes->listByUserId($userId, $page, $size);
        $student = new Student();

        $userData = [];
        $newMes = [];
        foreach ($mesList as $k => $v) {
            $users = [$v['usera_id'], $v['userb_id']];
            sort($users);
            if(!in_array($users, $userData)) {
                $userData[] = $users;
            } else {
                continue;
            }
            $mesInfo = $mes->infoOne($v['usera_id'], $v['userb_id']);
            $mesInfo['createtime'] = date('Y-m-d', $mesInfo['createtime']);

            if($v['usera_id'] != $userId) {
                $studentInfo = $student->infoByUserIdCanShow($v['usera_id']);
            }

            if($v['userb_id'] != $userId) {
                $studentInfo = $student->infoByUserIdCanShow($v['userb_id']);
            }

            if(isset($studentInfo)) {
                $mesInfo['head_image'] = $studentInfo['head_image'];
                $mesInfo['nickname'] = $studentInfo['nickname'];
            } else {
                $mesInfo['head_image'] = '';
                $mesInfo['nickname'] = '';
            }
            $newMes[] = $mesInfo;
        }
        $this->success('', $newMes);
    }



    /**
     * 已读消息
     * @ApiTitle    (已读消息)
     * @ApiSummary  (已读消息)
     * @ApiMethod   (POST)
     * @ApiHeaders  (name="token", type="string", required=true, description="请求的Token")
     * @ApiParams   (name="usera_id", type="integer", required=true, description="发消息的人的ID")
     * @ApiReturnParams   (name="code", type="integer", required=true, sample="0")
     * @ApiReturnParams   (name="msg", type="string", required=true, sample="返回成功")
     * @ApiReturnParams   (name="data", type="object", sample="{'user_id':'int','user_name':'string','profile':{'email':'string','age':'integer'}}", description="扩展数据返回")
     * @ApiReturn   ()
     */
    public function isReadMes()
    {
        $userId = $this->auth->id;

        $useraId = $this->request->param('usera_id', 0, 'int');

        if(!$useraId) {
            $this->error('您的操作有误');
        }

        if($userId == $useraId) {
            $this->error('您的操作有误');
        }

        $mes = new MesModel();
        $where['usera_id'] = ['=', $useraId];
        $where['userb_id'] = ['=', $userId];

        $updateData = [
            'readtime' => time()
        ];
        $res = $mes->updateByWhere($where, $updateData);
        if(!$res) {
            $this->error('已读失败');
        }
        $this->success('已读成功');
    }





    /**
     * 获取对话框详细内容
     * @ApiTitle    (获取对话框详细内容)
     * @ApiSummary  (获取对话框详细内容)
     * @ApiMethod   (GET)
     * @ApiHeaders  (name="token", type="string", required=true, description="请求的Token")
     * @ApiParams   (name="page", type="integer", required=true, description="")
     * @ApiParams   (name="size", type="integer", required=true, description="")
     * @ApiParams   (name="userb_id", type="integer", required=true, description="对话的用户的ID")
     * @ApiReturnParams   (name="code", type="integer", required=true, sample="0")
     * @ApiReturnParams   (name="msg", type="string", required=true, sample="返回成功")
     * @ApiReturnParams   (name="data", type="object", sample="{'user_id':'int','user_name':'string','profile':{'email':'string','age':'integer'}}", description="扩展数据返回")
     * @ApiReturn   ({
    "code": 1,
    "msg": "",
    "time": "1587697350",
    "data": [
    {
    "id": 2,
    "usera_id": 1,
    "userb_id": 2,
    "createtime": "2020-04-09",
    "updatetime": 1586413562,
    "des_content": "1",
    "readtime": null,
    "deletetime": null,
    "head_image": "/uploads/20200413/39270b1276af4c6af020cc85eb80fb29.jpg",
    "nickname": "我是高中生"
    }
    ]
    })
     */
    public function getMesDetail()
    {
        $userId = $this->auth->id;

        $page = $this->request->param('page', 1, 'int');
        $size = $this->request->param('size', 10, 'int');

        $userbId = $this->request->param('userb_id', 0, 'int');

        if(!$userbId) {
            $this->error('');
        }

        $limit = ($page-1)*$size;
        $mes = new MesModel();
        $sql = "select * from fa_mes where (usera_id = $userId and userb_id = $userbId) or (usera_id = $userbId and userb_id = $userId) order by createtime desc limit $limit, $size";
        $mesList = $mes->listBySql($sql);
        if($mesList) {
            $mesList = array_reverse($mesList);
        }

        $student = new Student();
        $studentInfo = $student->infoByUserIdCanShow($userbId);

        $userInfo = $student->infoByUserId($userId);

        foreach ($mesList as $k => $v) {
            $mesList[$k]['createtime'] = date('Y-m-d', $v['createtime']);

            if($studentInfo) {
                $mesList[$k]['userb_head_image'] = $this->qiNiu.$studentInfo['head_image'];
                $mesList[$k]['userb_nickname'] = $studentInfo['nickname'];
            } else {
                $mesList[$k]['userb_head_image'] = '';
                $mesList[$k]['userb_nickname'] = '';
            }

            $mesList[$k]['usera_head_image'] = $this->qiNiu.$userInfo['head_image'];
            $mesList[$k]['usera_nickname'] = $userInfo['nickname'];
        }
        $this->success('', $mesList);
    }

}