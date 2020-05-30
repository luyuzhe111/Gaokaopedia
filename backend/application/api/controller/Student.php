<?php

namespace app\api\controller;

use app\api\model\Article as ArticleModel;
use app\api\model\ArticleType;
use app\api\model\College;
use app\api\model\Graduated;
use app\api\model\LikeStudent;
use app\api\model\School;
use app\api\model\Student as StudentModel;
use app\api\model\Subject;
use app\api\model\University as UniversityModel;
use app\api\model\Up;
use app\common\controller\Api;
use app\api\model\User as UserModel;
use think\Log;

/**
 * 学长相关
 */
class Student extends Api
{

    // 无需登录的接口,*表示全部
    protected $noNeedLogin = [''];
    // 无需鉴权的接口,*表示全部
    protected $noNeedRight = ['*'];

    /**
     * 获取学长列表和搜索
     * @ApiTitle    (获取学长列表和搜索)
     * @ApiSummary  (获取学长列表和搜索)
     * @ApiMethod   (GET)
     * @ApiHeaders  (name="token", type="string", required=true, description="请求的Token")
     * @ApiParams   (name="page", type="integer", required=true, description="页码")
     * @ApiParams   (name="size", type="integer", required=true, description="分页数：默认10")
     * @ApiParams   (name="type", type="integer", required=false, description="1高中同校 2高中同市 3高中同省")
     * @ApiParams   (name="university_id", type="integer", required=false, description="大学ID")
     * @ApiParams   (name="graduated_id", type="integer", required=false, description="专业ID")
     * @ApiParams   (name="college_id", type="integer", required=false, description="学院ID")
     * @ApiParams   (name="up_id", type="integer", required=false, description="升学方式ID")
     * @ApiParams   (name="word", type="string", required=false, description="学长姓名")
     * @ApiReturnParams   (name="code", type="integer", required=true, sample="0")
     * @ApiReturnParams   (name="msg", type="string", required=true, sample="返回成功")
     * @ApiReturnParams   (name="data", type="object", sample="{'user_id':'int','user_name':'string','profile':{'email':'string','age':'integer'}}", description="扩展数据返回")
     * @ApiReturn   ({
        "code": 1,
        "msg": "获取学长列表成功",
        "time": "1586845996",
        "data": [
        {
        "id": 3,
        "user_id": 1,
        "level": "2",
        "nickname": "我是大学生",
        "head_image": "www.qiniu.com/uploads/20200413/39270b1276af4c6af020cc85eb80fb29.jpg",
        "school_id": 11,
        "subject_ids": "3,2,1",
        "up_id": 1,
        "college_id": 1,
        "university_id": 1,
        "graduated_id": 2,
        "createtime": 1586769562,
        "updatetime": 1586769562,
        "starttime": null,
        "endtime": 1586769446,
        "deletetime": null,
        "show_switch": 1,
        "email": "563771383@qq.com",
        "vip_level": "0",
        "vip_endtime": null,
        "university_name": "北京大学",
        "college_name": "计算机",
        "graduated_name": "计算机",
        "is_like": 0,
        "up_name": "裸分"
        }
        ]
        })
     */
    public function getStudentList()
    {
        $userId = $this->auth->id;

        $page = $this->request->param('page', 1, 'int');
        $size = $this->request->param('size', 10, 'int');
        $type = $this->request->param('type', 0, 'int');
        $universityId = $this->request->param('university_id', 0, 'int');
        $word = $this->request->param('word', '', 'string');
        $graduatedId = $this->request->param('graduated_id', 0, 'int');
        $collegeId = $this->request->param('college_id', 0, 'int');
        $upId = $this->request->param('up_id', 0, 'int');

        $where['show_switch'] = ['=', 1];
        $where['level'] = ['=', 2];
        $student = new StudentModel();
        // 获取学校
        $studentInfo = $student->infoByUserIdCanShow($userId);
        if(!$studentInfo) {
            $this->error('您的提交有误');
        }

        if($type) { // 学校限制
            if(!in_array($type, [1, 2, 3])) {
                $this->error('您的提交有误');
            }
        } else {
            $type = bcadd($studentInfo['vip_level'], 1, 0);
        }

        $schoolIds = [];
        // 获取学长
        if($type == 1) {
            $schoolIds = [$studentInfo['school_id']];
        } elseif ($type == 2) {
            if(bcadd($studentInfo['vip_level'], 1, 0) < $type) {
                $this->error('需要买市卡');
            }
            $school = new School();
            $schoolInfo = $school->infoById($studentInfo['school_id']);
            $schoolIds = $school->listByCityId($schoolInfo['city_id']);
        } elseif ($type == 3) {
            if(bcadd($studentInfo['vip_level'], 1, 0) < $type) {
                $this->error('需要买省卡');
            }
            $school = new School();
            $schoolInfo = $school->infoById($studentInfo['school_id']);
            $schoolIds = $school->listByProvinceId($schoolInfo['province_id']);
        }

        if($schoolIds) {
            $where['school_id'] = ['in', $schoolIds];
        }

        if($universityId) {
            $where['university_id'] = ['=', $universityId];
        }

        if($graduatedId) {
            $where['graduated_id'] = ['=', $graduatedId];
        }

        if($collegeId) {
            $where['college_id'] = ['=', $collegeId];
        }

        if($upId) {
            $where['up_id'] = ['=', $upId];
        }

        if($word && mb_strlen($word) <= 255) {
            $where['nickname'] = ['like', '%'.$word.'%'];
        }
        Log::write(json_encode($where), 'debug');

        $studentList = $student->listByUniversityIdAndSchoolIds($where, $page, $size);
        if(!$studentList) {
            $this->success('获取学长列表成功', $studentList);
        }

        // 获取所有大学
        $university = new UniversityModel();
        $universityList = $university->listAll();
        $universityListObj = [];
        foreach ($universityList as $v) {
            $universityListObj[$v['id']] = $v;
        }

        // 获取我关注的
        $likeStudent = new LikeStudent();
        $likeStudentList = $likeStudent->listAllByUserId($userId);
        $likeStudentListObj = [];
        foreach ($likeStudentList as $v) {
            $likeStudentListObj[$v['userb_id']] = $v;
        }

        // 获取所有的学院
        $college = new College();
        $collegeList = $college->listAll();
        $collegeListObj = [];
        foreach ($collegeList as $v) {
            $collegeListObj[$v['id']] = $v;
        }

        // 获取所有的专业
        $graduated = new Graduated();
        $graduatedList = $graduated->listAll();
        $graduatedListObj = [];
        foreach($graduatedList as $v) {
            $graduatedListObj[$v['id']] = $v;
        }

        // 获取所有的升学方式
        $up = new Up();
        $upList = $up->listAll();
        $upListObj = [];
        foreach ($upList as $v) {
            $upListObj[$v['id']] = $v;
        }

        foreach ($studentList as $k => $v) {
            if(isset($universityListObj[$v['university_id']])) {
                $studentList[$k]['university_name'] = $universityListObj[$v['university_id']]['name'];
            } else {
                $studentList[$k]['university_name'] = '';
            }

            if(isset($collegeListObj[$v['college_id']])) {
                $studentList[$k]['college_name'] = $collegeListObj[$v['college_id']]['name'];
            } else {
                $studentList[$k]['college_name'] = '';
            }

            if(isset($graduatedListObj[$v['graduated_id']])) {
                $studentList[$k]['graduated_name'] = $graduatedListObj[$v['graduated_id']]['name'];
            } else {
                $studentList[$k]['graduated_name'] = '';
            }

            if(isset($likeStudentListObj[$v['user_id']])) {
                $studentList[$k]['is_like'] = 1;
            } else {
                $studentList[$k]['is_like'] = 0;
            }

            if(isset($upListObj[$v['up_id']])) {
                $studentList[$k]['up_name'] = $upListObj[$v['up_id']]['name'];
            } else {
                $studentList[$k]['up_name'] = '';
            }

            $studentList[$k]['head_image'] = $this->qiNiu.$v['head_image'];
        }

        $this->success('获取学长列表成功', $studentList);
    }



    /**
     * 获取学长详细信息
     * @ApiTitle    (获取学长详细信息)
     * @ApiSummary  (获取学长详细信息)
     * @ApiMethod   (GET)
     * @ApiHeaders  (name="token", type="string", required=true, description="请求的Token")
     * @ApiParams   (name="user_id", type="integer", required=true, description="学长用户ID")
     * @ApiReturnParams   (name="code", type="integer", required=true, sample="0")
     * @ApiReturnParams   (name="msg", type="string", required=true, sample="返回成功")
     * @ApiReturnParams   (name="data", type="object", sample="{'user_id':'int','user_name':'string','profile':{'email':'string','age':'integer'}}", description="扩展数据返回")
     * @ApiReturn   ({
        "code": 1,
        "msg": "获取学长详细信息成功",
        "time": "1586846113",
        "data": {
        "id": 3,
        "user_id": 1,
        "level": "2",
        "nickname": "我是大学生",
        "head_image": "www.qiniu.com/uploads/20200413/39270b1276af4c6af020cc85eb80fb29.jpg",
        "school_id": 11,
        "subject_ids": "3,2,1",
        "up_id": 1,
        "college_id": 1,
        "university_id": 1,
        "graduated_id": 2,
        "createtime": 1586769562,
        "updatetime": 1586769562,
        "starttime": null,
        "endtime": 1586769446,
        "deletetime": null,
        "show_switch": 1,
        "email": "563771383@qq.com",
        "vip_level": "0",
        "vip_endtime": null,
        "university_name": "北京大学",
        "college_name": "计算机",
        "end_year": "1970-01-01",
        "school_name": "北京第一高中",
        "subject_names": [
        "化学",
        "物理",
        "生物"
        ],
        "up_name": "裸分",
        "is_like": 0
        }
        })
     */
    public function getStudentInfo()
    {
        $myId = $this->auth->id;

        $userId = $this->request->param('user_id', 0, 'int');
        if(!$userId) {
            $this->error('您的操作有误');
        }

        $student = new StudentModel();
        $studentInfo = $student->infoByUserIdCanShow($userId);
        if(!$studentInfo) {
            $this->error('您的操作有误');
        }

        $university =new UniversityModel();
        $universityInfo = $university->infoById($studentInfo['university_id']);
        if($universityInfo) {
            $studentInfo['university_name'] = $universityInfo['name'];
        } else {
            $studentInfo['university_name'] = '';
        }

        $college = new College();
        $collegeInfo = $college->infoById($studentInfo['college_id']);
        if($collegeInfo) {
            $studentInfo['college_name'] = $collegeInfo['name'];
        } else {
            $studentInfo['college_name'] = '';
        }

        $endYear = date("Y-m-d", $studentInfo['endtime']);
        $studentInfo['end_year'] = $endYear;

        $startYear = date("Y-m-d", $studentInfo['starttime']);
        $studentInfo['start_year'] = $startYear;

        $school = new School();
        $schoolInfo = $school->infoById($studentInfo['school_id']);
        if($schoolInfo) {
            $studentInfo['school_name'] = $schoolInfo['name'];
        } else {
            $studentInfo['school_name'] = '';
        }

        $subject = new Subject();
        $subjectList = $subject->listByIds($studentInfo['subject_ids']);
        if($subjectList) {
            $subjectNameArr = [];
            foreach ($subjectList as $v) {
                $subjectNameArr[] = $v['name'];
            }
            $studentInfo['subject_names'] = $subjectNameArr;
        } else {
            $studentInfo['subject_names'] = [];
        }

        $up = new Up();
        $upInfo = $up->infoById($studentInfo['up_id']);
        if($upInfo) {
            $studentInfo['up_name'] = $upInfo['name'];
        } else {
            $studentInfo['up_name'] = '';
        }

        // 获取我关注的
        $likeStudent = new LikeStudent();
        $likeStudentInfo= $likeStudent->infoById($myId, $userId);
        if($likeStudentInfo) {
            $studentInfo['is_like'] = 1;
        } else {
            $studentInfo['is_like'] = 0;
        }

        $studentInfo['head_image'] = $this->qiNiu.$studentInfo['head_image'];

        $this->success('获取学长详细信息成功', $studentInfo);
    }


    /**
     * 关注/取关学长
     * @ApiTitle    (关注/取关学长)
     * @ApiSummary  (关注/取关学长)
     * @ApiMethod   (POST)
     * @ApiHeaders  (name="token", type="string", required=true, description="请求的Token")
     * @ApiParams   (name="userb_id", type="integer", required=true, description="学长用户ID")
     * @ApiReturnParams   (name="code", type="integer", required=true, sample="0")
     * @ApiReturnParams   (name="msg", type="string", required=true, sample="返回成功")
     * @ApiReturnParams   (name="data", type="object", sample="{'user_id':'int','user_name':'string','profile':{'email':'string','age':'integer'}}", description="扩展数据返回")
     * @ApiReturn   ({
        "code": 1,
        "msg": "取关成功",
        "time": "1586846157",
        "data": null
        })
     */
    public function likeStudent()
    {
        $useraId = $this->auth->id;

        $userbId = $this->request->param('userb_id', 0, 'int');
        if(!$userbId) {
            $this->error('您的操作有误');
        }

        $likeStudent = new LikeStudent();
        $likeStudentInfo = $likeStudent->infoById($useraId, $userbId);
        if($likeStudentInfo) {
            if($likeStudent->updateOne($likeStudentInfo['id'], ['deletetime' => time()])) {
                $this->success('取关成功');
            } else {
                $this->error('取关失败');
            }
        } else {
            $insertData = [
                'usera_id' => $useraId,
                'userb_id' => $userbId,
                'createtime' => time(),
                'updatetime' => time(),
            ];
            if($likeStudent->addOne($insertData)) {
                $this->success('关注成功');
            } else {
                $this->error('关注失败');
            }
        }
    }



    /**
     * 获取个人信息
     * @ApiTitle    (获取个人信息)
     * @ApiSummary  (获取个人信息)
     * @ApiMethod   (GET)
     * @ApiHeaders  (name="token", type="string", required=true, description="请求的Token")
     * @ApiReturnParams   (name="code", type="integer", required=true, sample="0")
     * @ApiReturnParams   (name="msg", type="string", required=true, sample="返回成功")
     * @ApiReturnParams   (name="data", type="object", sample="{'user_id':'int','user_name':'string','profile':{'email':'string','age':'integer'}}", description="扩展数据返回")
     * @ApiReturn   ({
        "code": 1,
        "msg": "获取个人信息成功",
        "time": "1586846291",
        "data": {
        "id": 3,
        "user_id": 1,
        "level": "2",
        "nickname": "我是大学生",
        "head_image": "www.qiniu.com/uploads/20200413/39270b1276af4c6af020cc85eb80fb29.jpg",
        "school_id": 11,
        "subject_ids": "3,2,1",
        "up_id": 1,
        "college_id": 1,
        "university_id": 1,
        "graduated_id": 2,
        "createtime": 1586769562,
        "updatetime": 1586769562,
        "starttime": null,
        "endtime": 1586769446,
        "deletetime": null,
        "show_switch": 1,
        "email": "563771383@qq.com",
        "vip_level": "0",
        "vip_endtime": null
        }
        })
     */
    public function getMyInfo()
    {
        $userId = $this->auth->id;

        $student = new StudentModel();
        $studentInfo = $student->infoByUserIdCanShow($userId);
        if(!$studentInfo) {
            $this->error('您的操作有误');
        }

        $subject = new Subject();
        $subjectList = $subject->listByIds($studentInfo['subject_ids']);
        $studentInfo['subject_list'] = $subjectList;

        $up = new Up();
        $studentInfo['up_info'] = $up->infoById($studentInfo['up_id']);

        $college = new College();
        $studentInfo['college_info'] = $college->infoById($studentInfo['college_id']);

        $universityModel = new UniversityModel();
        $studentInfo['university_info'] = $universityModel->infoById($studentInfo['university_id']);

        $school = new School();
        $studentInfo['school_info'] = $school->infoById($studentInfo['school_id']);

        $studentInfo['head_image'] = $this->qiNiu.$studentInfo['head_image'];

        $studentInfo['endtime'] = date('Y', $studentInfo['endtime']);

        $graduated = new Graduated();
        $studentInfo['graduated_info'] = $graduated->infoById($studentInfo['graduated_id']);
        $this->success('获取个人信息成功', $studentInfo);
    }


    /**
     * 获取我喜欢的学长
     * @ApiTitle    (获取我喜欢的学长)
     * @ApiSummary  (获取我喜欢的学长)
     * @ApiMethod   (GET)
     * @ApiHeaders  (name="token", type="string", required=true, description="请求的Token")
     * @ApiParams   (name="page", type="integer", required=true, description="分页码")
     * @ApiParams   (name="size", type="integer", required=true, description="每页数")
     * @ApiReturnParams   (name="code", type="integer", required=true, sample="0")
     * @ApiReturnParams   (name="msg", type="string", required=true, sample="返回成功")
     * @ApiReturnParams   (name="data", type="object", sample="{'user_id':'int','user_name':'string','profile':{'email':'string','age':'integer'}}", description="扩展数据返回")
     * @ApiReturn   ({
        "code": 1,
        "msg": "获取我喜欢的学长成功",
        "time": "1586484363",
        "data": [
        {
        "id": 1,
        "usera_id": 1,
        "userb_id": 1,
        "createtime": 1586309985,
        "updatetime": 1586309985,
        "deletetime": null,
        "user_id": 1,
        "level": "1",
        "nickname": "谢百川",
        "head_image": "www.a.jpg",
        "school_id": 1,
        "subject_ids": "",
        "up_id": 1,
        "college_id": 1,
        "university_id": 1,
        "graduated_id": 1,
        "starttime": 1286640000,
        "endtime": null,
        "show_switch": 1,
        "email": "1",
        "university_name": "哈哈",
        "college_name": "计算机",
        "graduated_name": "软件工程",
        "up_name": "裸分"
        }
        ]
        })
     */
    public function getMyLikeStudent(){
        $userId = $this->auth->id;

        $page = $this->request->param('page', 0, 'int');
        $size = $this->request->param('size', 0, 'int');

        $likeStudent = new LikeStudent();
        $likeStudentList = $likeStudent->listByUserId($userId, $page, $size);
        if(!$likeStudentList) {
            $this->success('获取我喜欢的学长成功', $likeStudentList);
        }

        $university = new UniversityModel();
        $universityList = $university->listAll();
        $universityListObj = [];
        foreach ($universityList as $v) {
            $universityListObj[$v['id']] = $v;
        }

        // 获取所有的学院
        $college = new College();
        $collegeList = $college->listAll();
        $collegeListObj = [];
        foreach ($collegeList as $v) {
            $collegeListObj[$v['id']] = $v;
        }

        // 获取所有的专业
        $graduated = new Graduated();
        $graduatedList = $graduated->listAll();
        $graduatedListObj = [];
        foreach($graduatedList as $v) {
            $graduatedListObj[$v['id']] = $v;
        }

        // 获取所有的升学方式
        $up = new Up();
        $upList = $up->listAll();
        $upListObj = [];
        foreach ($upList as $v) {
            $upListObj[$v['id']] = $v;
        }

        foreach ($likeStudentList as $k => $v) {
            if(isset($universityListObj[$v['university_id']])) {
                $likeStudentList[$k]['university_name'] = $universityListObj[$v['university_id']]['name'];
            } else {
                $likeStudentList[$k]['university_name'] = '';
            }

            if(isset($collegeListObj[$v['college_id']])) {
                $likeStudentList[$k]['college_name'] = $collegeListObj[$v['college_id']]['name'];
            } else {
                $likeStudentList[$k]['college_name'] = '';
            }

            if(isset($graduatedListObj[$v['graduated_id']])) {
                $likeStudentList[$k]['graduated_name'] = $graduatedListObj[$v['graduated_id']]['name'];
            } else {
                $likeStudentList[$k]['graduated_name'] = '';
            }

            if(isset($upListObj[$v['up_id']])) {
                $likeStudentList[$k]['up_name'] = $upListObj[$v['up_id']]['name'];
            } else {
                $likeStudentList[$k]['up_name'] = '';
            }

            $likeStudentList[$k]['head_image'] = $this->qiNiu.$v['head_image'];
        }

        $this->success('获取我喜欢的学长成功', $likeStudentList);
    }


    /**
     * 修改头像或姓名
     * @ApiTitle    (修改头像或姓名)
     * @ApiSummary  (修改头像或姓名)
     * @ApiMethod   (POST)
     * @ApiHeaders  (name="token", type="string", required=true, description="请求的Token")
     * @ApiParams  (name="nickname", type="string", required=true, description="姓名")
     * @ApiParams  (name="head_image", type="string", required=true, description="头像")
     * @ApiReturnParams   (name="code", type="integer", required=true, sample="0")
     * @ApiReturnParams   (name="msg", type="string", required=true, sample="返回成功")
     * @ApiReturnParams   (name="data", type="object", sample="{'user_id':'int','user_name':'string','profile':{'email':'string','age':'integer'}}", description="扩展数据返回")
     * @ApiReturn   ({
        "code": 0,
        "msg": "需要注册",
        "time": "1586307199",
        "data": null
        })
     */
    public function updateStudentInfo()
    {
        $userId = $this->auth->id;

        $student = new StudentModel();
        $studentInfo = $student->infoByUserIdCanShow($userId);
        if(!$studentInfo) {
            $this->error('还没有注册');
        }

        $nickname = $this->request->param('nickname', '', 'string');
        if(!$nickname || mb_strlen($nickname) > 255) {
            $this->error('请填写您的姓名');
        }

        $headImage = $this->request->param('head_image', '', 'string');
        if(!$headImage) {
            $this->error('请您上传一张头像');
        }
        $pathInfo = pathinfo($headImage);
        $imageExtArr = ['jpg', 'jpeg', 'png'];
        if(!isset($pathInfo['extension']) || !in_array($pathInfo['extension'], $imageExtArr)) {
            $this->error('请选择正确的图片格式');
        }

        $res = $student->updateOne($userId, ['nickname' => $nickname, 'head_image' => $headImage]);
        if(!$res) {
            $this->error('对不起，您的信息修改失败');
        }
        $this->success('修改成功', []);
    }
}