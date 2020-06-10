<?php

namespace app\api\controller;

use app\api\model\College;
use app\api\model\Graduated;
use app\api\model\LikeUniversity;
use app\api\model\School;
use app\api\model\Student;
use app\api\model\Subject;
use app\api\model\University as UniversityModel;
use app\api\model\UniversityLevel;
use app\api\model\Up;
use app\common\controller\Api;
use think\Db;
use think\Log;

/**
 * 大学相关
 */
class University extends Api
{


    // 无需登录的接口,*表示全部
    protected $noNeedLogin = ['getAllUp', 'getUniversityLevel', 'getUniversityList', 'getGraduated', 'getCollege','getCollegeDetail'];
    // 无需鉴权的接口,*表示全部
    protected $noNeedRight = ['*'];


    /**
     * 获取大学的学院
     * @ApiTitle    (获取大学的学院)
     * @ApiSummary  (获取大学的学院)
     * @ApiMethod   (GET)
     * @ApiParams   (name="university_id", type="integer", required=true, description="大学ID")
     * @ApiReturnParams   (name="code", type="integer", required=true, sample="0")
     * @ApiReturnParams   (name="msg", type="string", required=true, sample="返回成功")
     * @ApiReturnParams   (name="data", type="object", sample="{'user_id':'int','user_name':'string','profile':{'email':'string','age':'integer'}}", description="扩展数据返回")
     * @ApiReturn   ({
        "code": 1,
        "msg": "获取大学的学院成功",
        "time": "1586846504",
        "data": [
        {
        "id": 1,
        "university_id": 1,
        "name": "计算机",
        "des_image": "www.qiniu.com/uploads/20200413/39270b1276af4c6af020cc85eb80fb29.jpg",
        "weigh": 0,
        "createtime": 1586768521,
        "updatetime": 1586768484,
        "show_switch": 1,
        "deletetime": null
        }
        ]
        })
     */
    public function getCollege()
    {
        $universityId = $this->request->param('university_id', '', 'int');
        if(!$universityId) {
            $this->error('您的提交有误');
        }

        $college = new College();
        $collegeList = $college->listByUniversityId($universityId);
        foreach ($collegeList as $k => $v) {
            $collegeList[$k]['des_image'] = $this->qiNiu.$v['des_image'];
        }
        $this->success('获取大学的学院成功', $collegeList);
    }

    public function getCollegeDetail(){
        $user_id = $this->auth->id;
        $college_id = $this->request->param('college_id',0,'intval');
        // 获取学院
        $collegeModel = new College();
        $data = $collegeModel->infoById($college_id);
        foreach ($data as $k => $v) {
            $data[$k]['des_image'] = $this->qiNiu.$v['des_image'];
        }
        $studentModel = new Student();
        //获取当前用户信息
        $user = $studentModel->infoByUserId($user_id);
        //获取当前用户所属高中信息
        $schoolModel = new School();
        $school = $schoolModel->infoById($user['school_id']);
        //获取当前用户同大学、院系、高中人数
        $total1 = $studentModel->where([
//            'level'=>'2',
//            'show_switch'=>1,
            'university_id'=>$data['university_id'],
            'school_id'=>$user['school_id'],
            'college_id'=>$college_id,
//            'deletetime'=>['exp',Db::raw('is null')]
        ])->count();
        //获取当前用户同省、大学、学院人数
        //获取当前用户同省的高中
        $school_province = $schoolModel->where(['province_id'=>$school['province_id']])->select();
        $school_ids = [];
        foreach($school_province as $key => $s_p){
            $school_ids[] = $s_p['id'];
        }
        $total2 = $studentModel->where([
            'level'=>'2',
            'show_switch'=>1,
            'university_id'=>$data['university_id'],
            'school_id'=>['in',$school_ids],
            'college_id'=>$college_id,
            'deletetime'=>['exp',Db::raw('is null')]
        ])->count();
        //获取当前用户同城市、大学、学院人数
        //获取当前用户同城市的高中
        $school_city = $schoolModel->where(['city_id'=>$school['city_id']])->select();
        $school_ids = [];
        foreach($school_city as $key => $s_c){
            $school_ids[] = $s_c['id'];
        }
        $total3 = $studentModel->where([
            'level'=>'2',
            'show_switch'=>1,
            'university_id'=>$data['university_id'],
            'school_id'=>['in',$school_ids],
            'college_id'=>$college_id,
            'deletetime'=>['exp',Db::raw('is null')]
        ])->count();
        $result['data'] = $data;
        $result['total1'] = $total1;
        $result['total2'] = $total2;
        $result['total3'] = $total3;
        $this->success('SUCCESS',$result);
    }



    /**
     * 获取所有的专业
     * @ApiTitle    (获取所有的专业)
     * @ApiSummary  (获取所有的专业)
     * @ApiMethod   (GET)
     * @ApiReturnParams   (name="code", type="integer", required=true, sample="0")
     * @ApiReturnParams   (name="msg", type="string", required=true, sample="返回成功")
     * @ApiReturnParams   (name="data", type="object", sample="{'user_id':'int','user_name':'string','profile':{'email':'string','age':'integer'}}", description="扩展数据返回")
     * @ApiReturn   ({
        "code": 1,
        "msg": "获取专业成功",
        "time": "1586313439",
        "data": [
        {
        "id": 1,
        "show_switch": 1,
        "name": "软件工程",
        "createtime": 0,
        "updatetime": 0,
        "weigh": 0,
        "deletetime": null
        }
        ]
        })
     */
    public function getGraduated()
    {
        $graduated = new Graduated();
        $graduatedList = $graduated->listAll();
        $this->success('获取所有的专业成功', $graduatedList);
    }



    /**
     * 获取关注的学校
     * @ApiTitle    (获取关注的学校)
     * @ApiSummary  (获取关注的学校)
     * @ApiMethod   (GET)
     * @ApiHeaders  (name="token", type="string", required=true, description="请求的Token")
     * @ApiParams   (name="page", type="integer", required=true, description="页码")
     * @ApiParams   (name="size", type="integer", required=true, description="分页数：默认10")
     * @ApiReturnParams   (name="code", type="integer", required=true, sample="0")
     * @ApiReturnParams   (name="msg", type="string", required=true, sample="返回成功")
     * @ApiReturnParams   (name="data", type="object", sample="{'user_id':'int','user_name':'string','profile':{'email':'string','age':'integer'}}", description="扩展数据返回")
     * @ApiReturn   ({
        "code": 1,
        "msg": "获取关注的学校成功",
        "time": "1586846652",
        "data": [
        {
        "id": 1,
        "user_id": 1,
        "university_id": 1,
        "createtime": 1586768302,
        "updatetime": 1586767790,
        "deletetime": null,
        "name": "北京大学",
        "des_content": "1",
        "des_image": "www.qiniu.com/uploads/20200413/39270b1276af4c6af020cc85eb80fb29.jpg",
        "url": "www.beijing.com",
        "wechat": "北京大学官方公众号",
        "show_switch": 1,
        "icon_image": "www.qiniu.com/assets/img/qrcode.png",
        "weigh": 0,
        "province_id": 11,
        "city_id": 1101,
        "level_id": 2
        }
        ]
        })
     */
    public function getMyLikeUniversity()
    {
        $userId = $this->auth->id;

        $page = $this->request->param('page', 1, 'int');
        $size = $this->request->param('size', 10, 'int');

        $likeUniversity = new LikeUniversity();
        $likeUniversityList = $likeUniversity->listByUserId($userId, $page, $size);
        foreach ($likeUniversityList as $k => $v) {
            $likeUniversityList[$k]['des_image'] = $this->qiNiu.$v['des_image'];
            $likeUniversityList[$k]['icon_image'] = $this->qiNiu.$v['icon_image'];
        }

        $this->success('获取关注的学校成功', $likeUniversityList);
    }


    /**
     * 关注/取关大学
     * @ApiTitle    (关注/取关大学)
     * @ApiSummary  (关注/取关大学)
     * @ApiMethod   (POST)
     * @ApiHeaders  (name="token", type="string", required=true, description="请求的Token")
     * @ApiParams   (name="university_id", type="integer", required=true, description="大学ID")
     * @ApiReturnParams   (name="code", type="integer", required=true, sample="0")
     * @ApiReturnParams   (name="msg", type="string", required=true, sample="返回成功")
     * @ApiReturnParams   (name="data", type="object", sample="{'user_id':'int','user_name':'string','profile':{'email':'string','age':'integer'}}", description="扩展数据返回")
     * @ApiReturn   ()
     */
    public function likeUniversity()
    {
        $userId = $this->auth->id;

        $universityId = $this->request->param('university_id', 0, 'int');
        if(!$universityId) {
            $this->error('您的操作有误');
        }

        $likeUniversity = new LikeUniversity();
        $likeUniversityInfo = $likeUniversity->infoById($userId, $universityId);
        if($likeUniversityInfo) {
            if($likeUniversity->updateOne($likeUniversityInfo['id'], ['deletetime' => time()])) {
                $this->success('取关大学成功');
            } else {
                $this->error('取关大学失败');
            }
        } else {
            $insertData = [
                'user_id' => $userId,
                'university_id' => $universityId,
                'createtime' => time(),
                'updatetime' => time(),
            ];
            if($likeUniversity->addOne($insertData)) {
                $this->success('关注大学成功');
            } else {
                $this->error('关注大学失败');
            }
        }
    }


    /**
     * 获取大学列表（搜索）
     * @ApiTitle    (获取大学列表（搜索）)
     * @ApiSummary  (获取大学列表（搜索）)
     * @ApiMethod   (GET)
     * @ApiParams   (name="page", type="integer", required=true, description="页码")
     * @ApiParams   (name="size", type="integer", required=true, description="分页数：默认10")
     * @ApiParams   (name="word", type="string", required=false, description="关键词")
     * @ApiParams   (name="level_id", type="integer", required=false, description="排名ID")
     * @ApiParams   (name="city_id", type="integer", required=false, description="城市ID")
     * @ApiReturnParams   (name="code", type="integer", required=true, sample="0")
     * @ApiReturnParams   (name="msg", type="string", required=true, sample="返回成功")
     * @ApiReturnParams   (name="data", type="object", sample="{'user_id':'int','user_name':'string','profile':{'email':'string','age':'integer'}}", description="扩展数据返回")
     * @ApiReturn   ({
        "code": 1,
        "msg": "获取大学列表成功",
        "time": "1586846824",
        "data": [
        {
        "id": 2,
        "name": "天津大学",
        "des_content": "1",
        "des_image": "www.qiniu.com/uploads/20200413/39270b1276af4c6af020cc85eb80fb29.jpg",
        "updatetime": 1586768302,
        "createtime": 1586768302,
        "url": "www.tianjin.com",
        "wechat": "天津大学官方公众号",
        "show_switch": 1,
        "icon_image": "www.qiniu.com/assets/img/qrcode.png",
        "weigh": 2,
        "province_id": 12,
        "city_id": 1201,
        "deletetime": null,
        "level_id": 3
        }
        ]
        })
     */
    public function getUniversityList()
    {
        $page = $this->request->param('page', 1, 'int');
        $size = $this->request->param('size', 10, 'int');

        $word = $this->request->param('word', '', 'string');
        if(mb_strlen($word) > 255) {
            $this->error('您的提交有误');
        }

        $levelId = $this->request->param('level_id', 0, 'int');
        $cityId = $this->request->param('city_id', 0, 'int');
        if (empty($word) && empty($levelId) && empty($cityId)) $levelId = 2;
        $where['show_switch'] = ['=', 1];

        if($levelId) {
            $where['level_id'] = ['=', $levelId];
        }

        if($cityId) {
            $where['city_id'] = ['=', $cityId];
        }

        if($word) {
            $where['name'] = ['like', '%'.$word.'%'];
        }

        $university = new UniversityModel();
        $universityList = $university->listByWhere($page, $size, $where);
        foreach ($universityList as $k => $v) {
            $universityList[$k]['des_image'] = $this->qiNiu.$v['des_image'];
            $universityList[$k]['icon_image'] = $this->qiNiu.$v['icon_image'];
        }
        $this->success('获取大学列表成功', $universityList);
    }



    /**
     * 获取大学的详细信息
     * @ApiTitle    (获取大学的详细信息)
     * @ApiSummary  (获取大学的详细信息)
     * @ApiMethod   (GET)
     * @ApiParams   (name="university_id", type="integer", required=true, description="大学ID")
     * @ApiReturnParams   (name="code", type="integer", required=true, sample="0")
     * @ApiReturnParams   (name="msg", type="string", required=true, sample="返回成功")
     * @ApiReturnParams   (name="data", type="object", sample="{'user_id':'int','user_name':'string','profile':{'email':'string','age':'integer'}}", description="扩展数据返回")
     * @ApiReturn   ({
        "code": 1,
        "msg": "获取大学的详细信息成功",
        "time": "1586846977",
        "data": {
        "id": 1,
        "name": "北京大学",
        "des_content": "1",
        "des_image": "www.qiniu.com/uploads/20200413/39270b1276af4c6af020cc85eb80fb29.jpg",
        "updatetime": 1586767790,
        "createtime": 1586768302,
        "url": "www.beijing.com",
        "wechat": "北京大学官方公众号",
        "show_switch": 1,
        "icon_image": "www.qiniu.com/assets/img/qrcode.png",
        "weigh": 0,
        "province_id": 11,
        "city_id": 1101,
        "deletetime": null,
        "level_id": 2,
        "same_school_total_num": 1,
        "same_city_total_num": 1,
        "same_province_total_num": 1,
        "college_list": [
        {
        "id": 1,
        "university_id": 1,
        "name": "计算机",
        "des_image": "www.qiniu.com/uploads/20200413/39270b1276af4c6af020cc85eb80fb29.jpg",
        "weigh": 0,
        "createtime": 1586768521,
        "updatetime": 1586768484,
        "show_switch": 1,
        "deletetime": null
        }
        ]
        }
        })
     */
    public function getUniversityInfo()
    {
        $userId = $this->auth->id;

        $universityId = $this->request->param('university_id', 0, 'int');
        if(!$universityId) {
            $this->error('您的提交有误');
        }

        // 获取大学数据
        $university = new UniversityModel();
        $universityInfo = $university->infoById($universityId);
        if(!$universityInfo) {
            $this->error('您的提交有误');
        }

        // 获取学长数量
        $student = new Student();
        $studentInfo = $student->infoByUserIdCanShow($userId);
        if(!$studentInfo) {
            $this->error('您的提交有误');
        }
        $sameSchoolTotalNum = $student->countByUniversityIdAndSchoolId($universityId, $studentInfo['school_id']);
        $universityInfo['same_school_total_num'] = $sameSchoolTotalNum;

        $school = new School();
        $schoolInfo = $school->infoById($studentInfo['school_id']);
        $schoolIds = $school->listByCityId($schoolInfo['city_id']);
        $sameCityTotalNum = $student->countByUniversityIdAndSchoolIds($universityId, $schoolIds);
        $universityInfo['same_city_total_num'] = $sameCityTotalNum;
        $schoolIds = $school->listByProvinceId($schoolInfo['province_id']);
        $sameProvinceTotalNum = $student->countByUniversityIdAndSchoolIds($universityId, $schoolIds);
        $universityInfo['same_province_total_num'] = $sameProvinceTotalNum;

        // 图片地址
        $universityInfo['des_image'] = $this->qiNiu.$universityInfo['des_image'];
        $universityInfo['icon_image'] = $this->qiNiu.$universityInfo['icon_image'];
        // 获取学院
        $college = new College();
        $collegeList = $college->listByUniversityId($universityId);
        foreach ($collegeList as $k => $v) {
            $collegeList[$k]['des_image'] = $this->qiNiu.$v['des_image'];
        }
        $universityInfo['college_list'] = $collegeList;

        $likeUniversity = new LikeUniversity();
        $likeUniversityInfo = $likeUniversity->infoById($userId, $universityId);
        $universityInfo['is_like'] = $likeUniversityInfo ? 1 : 0;

        $this->success('获取大学的详细信息成功', $universityInfo);
    }


    /**
     * 获取大学级别985/211等
     * @ApiTitle    (获取大学级别985/211等)
     * @ApiSummary  (获取大学级别985/211等)
     * @ApiMethod   (GET)
     * @ApiReturnParams   (name="code", type="integer", required=true, sample="0")
     * @ApiReturnParams   (name="msg", type="string", required=true, sample="返回成功")
     * @ApiReturnParams   (name="data", type="object", sample="{'user_id':'int','user_name':'string','profile':{'email':'string','age':'integer'}}", description="扩展数据返回")
     * @ApiReturn   ({
        "code": 1,
        "msg": "获取大学级别985/211等成功",
        "time": "1586413893",
        "data": [
        {
        "id": 1,
        "name": "1",
        "weigh": 1,
        "show_switch": 1,
        "createtime": 0,
        "updatetime": 0,
        "deletetime": null
        }
        ]
        })
     */
    public function getUniversityLevel()
    {
        $universityLevel = new UniversityLevel();
        $universityLevelList = $universityLevel->listAll();
        $this->success('获取大学级别985/211等成功', $universityLevelList);
    }



    /**
     * 获取所有的升学方式
     * @ApiTitle    (获取所有的升学方式)
     * @ApiSummary  (获取所有的升学方式)
     * @ApiMethod   (GET)
     * @ApiReturnParams   (name="code", type="integer", required=true, sample="0")
     * @ApiReturnParams   (name="msg", type="string", required=true, sample="返回成功")
     * @ApiReturnParams   (name="data", type="object", sample="{'user_id':'int','user_name':'string','profile':{'email':'string','age':'integer'}}", description="扩展数据返回")
     * @ApiReturn   ({
        "code": 1,
        "msg": "获取所有的升学方式成功",
        "time": "1586420526",
        "data": [
        {
        "id": 1,
        "name": "裸分",
        "weigh": 0,
        "createtime": 0,
        "updatetime": 0,
        "show_switch": 1,
        "deletetime": null
        }
        ]
        })
     */
    public function getAllUp()
    {
        $up = new Up();
        $upList = $up->listAll();
        $this->success('获取所有的升学方式成功', $upList);
    }



    /**
     * 获取选考科目
     * @ApiTitle    (获取选考科目)
     * @ApiSummary  (获取选考科目)
     * @ApiMethod   (GET)
     * @ApiReturnParams   (name="code", type="integer", required=true, sample="0")
     * @ApiReturnParams   (name="msg", type="string", required=true, sample="返回成功")
     * @ApiReturnParams   (name="data", type="object", sample="{'user_id':'int','user_name':'string','profile':{'email':'string','age':'integer'}}", description="扩展数据返回")
     * @ApiReturn   (获取选考科目)
     */
    public function listSubject()
    {
        $subject = new Subject();
        $subjectList = $subject->listAll();
        $this->success('获取选考科目', $subjectList);
    }

}
