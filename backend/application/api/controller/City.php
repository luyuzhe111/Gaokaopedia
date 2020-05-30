<?php

namespace app\api\controller;

use app\api\model\Area;
use app\api\model\Province;
use app\common\controller\Api;
use app\api\model\City as CityModel;
use app\api\model\University as UniversityModel;
/**
 * 城市相关
 */
class City extends Api
{

    // 无需登录的接口,*表示全部
    protected $noNeedLogin = ['*'];
    // 无需鉴权的接口,*表示全部
    protected $noNeedRight = ['*'];



    /**
     * 获取省
     * @ApiTitle    (获取省)
     * @ApiSummary  (获取省)
     * @ApiMethod   (GET)
     * @ApiReturnParams   (name="code", type="integer", required=true, description="状态码")
     * @ApiReturnParams   (name="msg", type="string", required=true, description="提示语")
     * @ApiReturnParams   (name="data", type="object", description="对象")
     * @ApiReturn   ({
        "code": 1,
        "msg": "获取省份成功",
        "time": "1586756898",
        "data": [
        {
        "code": 11,
        "name": "北京市"
        }
        ]
        })
     */
    public function getAllProvince()
    {
        $province = new Province();
        $provinceList = $province->listAll();
        $this->success('获取省份成功', $provinceList);
    }



    /**
     * 获取市
     * @ApiTitle    (获取市)
     * @ApiSummary  (获取市)
     * @ApiMethod   (GET)
     * @ApiParams   (name="province_id", type="integer", required=true, description="城市ID")
     * @ApiReturnParams   (name="code", type="integer", required=true, description="状态码")
     * @ApiReturnParams   (name="msg", type="string", required=true, description="提示语")
     * @ApiReturnParams   (name="data", type="object", description="对象")、
     * @ApiReturn   ({
        "code": 1,
        "msg": "获取市成功",
        "time": "1586757436",
        "data": [
        {
        "code": 1301,
        "name": "石家庄市",
        "provinceCode": 13
        }
        ]
        })
     */
    public function getCity()
    {
        $provinceId = $this->request->param('province_id', '', 'int');
        if(!$provinceId || !is_numeric($provinceId)) {
            $this->error('您的提交存在问题');
        }

        $city = new CityModel();
        $cityList = $city->listByCode($provinceId);
        $this->success('获取市成功', $cityList);
    }



    /**
     * 获取市的大学
     * @ApiTitle    (获取市的大学)
     * @ApiSummary  (获取市的大学)
     * @ApiMethod   (GET)
     * @ApiParams   (name="city_id", type="integer", required=true, description="城市ID")
     * @ApiReturnParams   (name="code", type="integer", required=true, description="状态码")
     * @ApiReturnParams   (name="msg", type="string", required=true, description="提示语")
     * @ApiReturnParams   (name="data", type="object", description="对象")、
     * @ApiReturn   ({
        "code": 1,
        "msg": "获取市的大学成功",
        "time": "1586845398",
        "data": [
        {
        "id": 1,
        "name": "北京大学",
        "des_content": "1",
        "des_image": "/uploads/20200413/39270b1276af4c6af020cc85eb80fb29.jpg",
        "updatetime": 1586767790,
        "createtime": 1586768302,
        "url": "www.beijing.com",
        "wechat": "北京大学官方公众号",
        "show_switch": 1,
        "icon_image": "/assets/img/qrcode.png",
        "weigh": 0,
        "province_id": 11,
        "city_id": 1101,
        "deletetime": null,
        "level_id": 2
        }
        ]
        })
     */
    public function getUniversityByCity()
    {
        $cityId = $this->request->param('city_id', 0, 'int');
        if(!$cityId) {
            $this->error('您的提交存在问题');
        }

        $university = new UniversityModel();
        $universityList = $university->listByCityId($cityId);

        foreach ($universityList as $k => $v) {
            $universityList[$k]['icon_image'] = $this->qiNiu.$v['icon_image'];
            $universityList[$k]['des_image'] = $this->qiNiu.$v['des_image'];
        }

        $this->success('获取市的大学成功', $universityList);
    }
}