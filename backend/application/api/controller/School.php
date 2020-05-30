<?php

namespace app\api\controller;

use app\common\controller\Api;
use app\api\model\School as SchoolModel;
/**
 * 高中相关
 */
class School extends Api
{

    // 无需登录的接口,*表示全部
    protected $noNeedLogin = ['*'];
    // 无需鉴权的接口,*表示全部
    protected $noNeedRight = ['*'];


    /**
     * 添加一个高中
     * @ApiTitle    (添加一个高中)
     * @ApiSummary  (添加一个高中)
     * @ApiMethod   (POST)
     * @ApiParams   (name="name", type="string", required=true, description="高中名")
     * @ApiParams   (name="province_id", type="integer", required=true, description="省ID")
     * @ApiParams   (name="city_id", type="integer", required=true, description="城市ID")
     * @ApiReturnParams   (name="code", type="integer", required=true, sample="0")
     * @ApiReturnParams   (name="msg", type="string", required=true, sample="返回成功")
     * @ApiReturnParams   (name="data", type="object", sample="{'user_id':'int','user_name':'string','profile':{'email':'string','age':'integer'}}", description="扩展数据返回")
     * @ApiReturn   ({
        "code": 1,
        "msg": "已经提交了哦，管理员正在审核",
        "time": "1586507878",
        "data": null
        })
     */
    public function addOneSchool()
    {
        $provinceId = $this->request->param('province_id', 0, 'int');
        $cityId = $this->request->param('city_id', 0, 'int');

        if(!$cityId || !$provinceId) {
            $this->error('您的操作有误');
        }

        $name = $this->request->param('name', '', 'string');
        if(!$name) {
            $this->error('学校名字不能是空的哦');
        }

        if(mb_strlen($name) > 50) {
            $this->error('学校名字不能超过50汉字长度哦');
        }

        $school = new SchoolModel();
        $insertData = [
            'name' => $name,
            'province_id' => $provinceId,
            'city_id' => $cityId,
            'createtime' => time(),
            'updatetime' => time(),
        ];

        $res = $school->addOne($insertData);
        if(!$res) {
            $this->error('对不起，提交失败了');
        }
        $this->success('已经提交了哦，管理员正在审核');
    }


    /**
     * 获取市的高中
     * @ApiTitle    (获取市的高中)
     * @ApiSummary  (获取市的高中)
     * @ApiMethod   (GET)
     * @ApiParams   (name="name", type="string", required=true, description="高中名")
     * @ApiParams   (name="city_id", type="integer", required=true, description="城市ID")
     * @ApiReturnParams   (name="code", type="integer", required=true, sample="0")
     * @ApiReturnParams   (name="msg", type="string", required=true, sample="返回成功")
     * @ApiReturnParams   (name="data", type="object", sample="{'user_id':'int','user_name':'string','profile':{'email':'string','age':'integer'}}", description="扩展数据返回")
     * @ApiReturn   ({
        "code": 1,
        "msg": "获取市的高中成功",
        "time": "1586508827",
        "data": [
        {
        "id": 1,
        "province_id": 1,
        "city_id": 1,
        "createtime": 0,
        "updatetime": 0,
        "name": "北京高中",
        "weigh": 0,
        "show_switch": 1,
        "deletetime": null
        },
        {
        "id": 2,
        "province_id": 1,
        "city_id": 1,
        "createtime": 0,
        "updatetime": 0,
        "name": "北京二高",
        "weigh": 0,
        "show_switch": 1,
        "deletetime": null
        }
        ]
        })
     */
    public function getSchoolList()
    {
        $cityId = $this->request->param('city_id', 0, 'int');
        if(!$cityId) {
            $this->error('您的操作有误');
        }

        $school = new SchoolModel();
        $schoolList = $school->listAllByCityId($cityId);
        $this->success('获取市的高中成功', $schoolList);
    }

}