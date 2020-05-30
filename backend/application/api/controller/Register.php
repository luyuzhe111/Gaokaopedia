<?php

namespace app\api\controller;

use app\api\model\Student;
use app\common\controller\Api;
use think\Log;

/**
 * 注册相关
 */
class Register extends Api
{


    // 无需登录的接口,*表示全部
    protected $noNeedLogin = [''];
    // 无需鉴权的接口,*表示全部
    protected $noNeedRight = ['*'];


    /**
     * 检查用户是否身份认证
     * @ApiTitle    (检查用户是否身份认证)
     * @ApiSummary  (检查用户是否身份认证)
     * @ApiMethod   (GET)
     * @ApiHeaders  (name="token", type="string", required=true, description="请求的Token")
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
    public function isRegister()
    {
        $userId = $this->auth->id;

        $student = new Student();
        $studentInfo = $student->infoByUserIdCanShow($userId);
        if($studentInfo) {
            $this->success('已经注册', $studentInfo);
        }

        $this->error('需要注册');
    }


    /**
     * 用户提交身份认证
     * @ApiTitle    (用户提交身份认证)
     * @ApiSummary  (用户提交身份认证)
     * @ApiMethod   (POST)
     * @ApiHeaders  (name="token", type="string", required=true, description="请求的Token")
     * @ApiParams  (name="level", type="integer", required=true, description="级别:1高中 2大学")
     * @ApiParams  (name="nickname", type="string", required=true, description="姓名")
     * @ApiParams  (name="head_image", type="string", required=true, description="头像")
     * @ApiParams  (name="school_id", type="integer", required=true, description="高中学校ID")
     * @ApiParams  (name="email", type="string", required=true, description="邮箱")
     * @ApiParams  (name="starttime", type="string", required=false, description="入学年份：2020-10-11")
     * @ApiParams  (name="endtime", type="string", required=false, description="毕业年份：2020-10-11")
     * @ApiParams  (name="subject_ids", type="string", required=false, description="选考科目ID集：1,2,3")
     * @ApiParams  (name="up_id", type="integer", required=false, description="升学方式ID")
     * @ApiParams  (name="university_id", type="integer", required=false, description="大学ID")
     * @ApiParams  (name="college_id", type="integer", required=false, description="学院ID")
     * @ApiParams  (name="graduated_id", type="integer", required=false, description="专业ID")
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
    public function register()
    {
        $userId = $this->auth->id;

        $student = new Student();
        $studentInfo = $student->infoByUserIdCanShow($userId);
        if($studentInfo) {
            $this->success('已经注册', $studentInfo);
        }

        $level = $this->request->param('level', 0, 'int');
        if(!$level) {
            $this->error('请您选择您的身份（高中/大学）');
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

        $schoolId = $this->request->param('school_id', 0, 'int');
        if(!$schoolId) {
            $this->error('请选择您的高中');
        }

        $email = $this->request->param('email', '', 'email');
        if(!$email) {
            $this->error('请填写正确的邮箱');
        }

        if($level == 1) {
            $startTime = $this->request->param('starttime', '', 'string');
            if(!$startTime) {
                $this->error('请您选择正确的入学年份');
            }

            $insertData = [
                'user_id' => $userId,
                'level' => $level,
                'nickname' => $nickname,
                'head_image' => $headImage,
                'email' => $email,
                'school_id' => $schoolId,
                'createtime' => time(),
                'updatetime' => time(),
                'starttime' => strtotime($startTime),
                'show_switch' => 1
            ];
        } else {
            $endTime = $this->request->param('endtime', '', 'string');
            if(!$endTime) {
                $this->error('请您选择正确的毕业年份');
            }

            $subjectIds = $this->request->param('subject_ids', '', 'string');
            if(!$subjectIds) {
                $this->error('请您选择正确的选考科目');
            }

            $upId = $this->request->param('up_id', '', 'int');
            if(!$upId) {
                $this->error('请您选择正确的升学方式');
            }

            $universityId = $this->request->param('university_id', 0, 'int');
            if(!$universityId) {
                $this->error('请您选择正确的就读大学');
            }

            $collegeId = $this->request->param('college_id', 0, 'int');
            if(!$collegeId) {
                $this->error('请您选择正确的学院');
            }

            $graduatedId = $this->request->param('graduated_id', 0, 'int');
            if(!$graduatedId) {
                $this->error('请您选择正确的专业');
            }

            $insertData = [
                'user_id' => $userId,
                'level' => $level,
                'nickname' => $nickname,
                'head_image' => $headImage,
                'email' => $email,
                'school_id' => $schoolId,
                'createtime' => time(),
                'updatetime' => time(),
                'endtime' => strtotime($endTime),
                'subject_ids' => $subjectIds,
                'up_id' => $upId,
                'university_id' => $universityId,
                'college_id' => $collegeId,
                'graduated_id' => $graduatedId,
                'vip_level' => 0,
                'vip_endtime' => time(),
                'show_switch' => 1,
            ];
        }

        $student = new Student();
        $res = $student->addOne($insertData);
        if(!$res) {
            $this->error('您的身份认证提交失败，请检查您的输入信息');
        }
        $this->success('您的身份认证已经提交，请等待审核');
    }


}
