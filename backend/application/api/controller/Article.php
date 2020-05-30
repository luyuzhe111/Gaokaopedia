<?php

namespace app\api\controller;

use app\api\model\ArticleType;
use app\api\model\LikeArticle;
use app\api\model\LikeStudent;
use app\api\model\LookArticle;
use app\api\model\School;
use app\api\model\Student;
use app\common\controller\Api;
use app\api\model\Article as ArticleModel;
use think\Log;

/**
 * 文章相关
 */
class Article extends Api
{

    // 无需登录的接口,*表示全部
    protected $noNeedLogin = ['getArticleType', 'getArticleInfo', 'getArticleList'];
    // 无需鉴权的接口,*表示全部
    protected $noNeedRight = ['*'];


    /**
     * 获取大学或学院或学长的文章
     * @ApiTitle    (获取大学或学院或学长的文章)
     * @ApiSummary  (获取大学或学院或学长的文章)
     * @ApiMethod   (GET)
     * @ApiParams   (name="university_id", type="integer", required=false, description="大学ID")
     * @ApiParams   (name="college_id", type="integer", required=false, description="大学ID")
     * @ApiParams   (name="user_id", type="integer", required=false, description="学长ID")
     * @ApiParams   (name="page", type="integer", required=true, description="分页码")
     * @ApiParams   (name="size", type="integer", required=true, description="分页数 默认10")
     * @ApiParams   (name="type_id", type="integer", required=true, description="文章的分类ID")
     * @ApiReturnParams   (name="code", type="integer", required=true, sample="0")
     * @ApiReturnParams   (name="msg", type="string", required=true, sample="返回成功")
     * @ApiReturnParams   (name="data", type="object", sample="{'user_id':'int','user_name':'string','profile':{'email':'string','age':'integer'}}", description="扩展数据返回")
     * @ApiReturn   ({
        "code": 1,
        "msg": "获取大学的文章成功",
        "time": "1586332327",
        "data": [
        {
        "id": 1,
        "createtime": 1586309985,
        "updatetime": 1586309985,
        "show_switch": 1,
        "title": "文章标题",
        "article_type_id": 1,
        "show_type": "1",
        "des_content": "内容",
        "des_images": "图片集例：1.jpg,2.jpg",
        "weigh": 0,
        "user_id": 1,
        "deletetime": null,
        "level": "1",
        "nickname": "谢百川（发布人信息）",
        "head_image": "www.a.jpg（头像）",
        "school_id": 1,
        "subject_ids": "",
        "up_id": 0,
        "college_id": 0,
        "university_id": 1,
        "graduated_id": 0,
        "starttime": 1286640000,
        "endtime": null,
        "email": "1",
        "article_type_name": "经验"
        }
        ]
        })
     */
    public function getArticleList()
    {
        $myId = $this->auth->id;
//        $myId = 25;

        $universityId = $this->request->param('university_id', 0, 'int');
//        $universityId = 892;

        $collegeId = $this->request->param('college_id', 0, 'int');

        $page = $this->request->param('page', 1, 'int');
        $size = $this->request->param('size', 10, 'int');
        $typeId = $this->request->param('type_id', 0, 'int');
        $userId = $this->request->param('user_id', 0, 'int');

        $where['a.show_switch'] = ['=', 1];

        $student = new Student();
        $studentInfo = $student->infoByUserIdCanShow($myId);
        if(!$studentInfo) {
            $this->error('您的提交有误');
        }

        $type = bcadd($studentInfo['vip_level'], 1, 0);
        $schoolIds = [];
        // 获取学长
        if($type == 1) {
            $schoolIds = [$studentInfo['school_id']];
        } elseif ($type == 2) {
            $school = new School();
            $schoolInfo = $school->infoById($studentInfo['school_id']);
            $schoolIds = $school->listByCityId($schoolInfo['city_id']);
        } elseif ($type == 3) {
            $school = new School();
            $schoolInfo = $school->infoById($studentInfo['school_id']);
            $schoolIds = $school->listByProvinceId($schoolInfo['province_id']);
        }

        if($universityId) {
            $where['b.university_id'] = ['=', $universityId];
        }

        if($collegeId) {
            $where['b.college_id'] = ['=', $collegeId];
        }

        if($typeId) {
            $where['a.article_type_id'] = ['=', $typeId];
        }

        if($userId) {
            $where['a.user_id'] = ['=', $userId];
        }

        $where['a.deletetime'] = null;

        // 获取文章
        $article = new ArticleModel();
        $articleList = $article->listByWhere($where, $page, $size, $schoolIds);
        if(!$articleList) {
            $this->success('获取大学或学院的文章成功', $articleList);
        }

        $articleType = new ArticleType();
        $articleTypeList = $articleType->listAll();
        $articleTypeListObj = [];
        foreach ($articleTypeList as $k => $v) {
            $articleTypeListObj[$v['id']] = $v;
        }

        foreach ($articleList as $k => $v) {
            if(isset($articleTypeListObj[$v['article_type_id']])) {
                $articleList[$k]['article_type_name'] = $articleTypeListObj[$v['article_type_id']]['name'];
            } else {
                $articleList[$k]['article_type_name'] = '';
            }

            if($v['des_images']) {
                $desImagesArr = explode(',', $v['des_images']);
                foreach ($desImagesArr as $kk => $vv) {
                    $desImagesArr[$kk] = $this->qiNiu.$vv;
                }
                $articleList[$k]['des_images'] = $desImagesArr;
            } else {
                $articleList[$k]['des_images'] = [];
            }

            $articleList[$k]['head_image'] = $this->qiNiu.$v['head_image'];
        }
        $this->success('获取大学或学院的文章成功', $articleList);
    }



    /**
     * 获取文章的详细信息
     * @ApiTitle    (获取文章的详细信息)
     * @ApiSummary  (获取文章的详细信息)
     * @ApiMethod   (GET)
     * @ApiParams   (name="article_id", type="integer", required=false, description="大学ID")
     * @ApiReturnParams   (name="code", type="integer", required=true, sample="0")
     * @ApiReturnParams   (name="msg", type="string", required=true, sample="返回成功")
     * @ApiReturnParams   (name="data", type="object", sample="{'user_id':'int','user_name':'string','profile':{'email':'string','age':'integer'}}", description="扩展数据返回")
     * @ApiReturn   ({
        "code": 1,
        "msg": "获取文章的详细信息成功",
        "time": "1586843495",
        "data": {
        "id": 3,
        "createtime": 1586769562,
        "updatetime": 1586769562,
        "show_switch": 1,
        "title": "全是经验",
        "article_type_id": 1,
        "show_type": "1",
        "des_content": "经验经验经验经验经验经验经验经验经验经验经验经验经验经验经验经验经验经验经验经验经验经验经验经验经验经验经验经验经验经验经验经验",
        "des_images": [
        "www.qiniu.com/uploads/20200413/39270b1276af4c6af020cc85eb80fb29.jpg"
        ],
        "weigh": 7,
        "user_id": 1,
        "deletetime": null,
        "level": "2",
        "nickname": "我是大学生",
        "head_image": "www.qiniu.com/uploads/20200413/39270b1276af4c6af020cc85eb80fb29.jpg",
        "school_id": 11,
        "subject_ids": "3,2,1",
        "up_id": 1,
        "college_id": 1,
        "university_id": 1,
        "graduated_id": 2,
        "starttime": null,
        "endtime": 1586769446,
        "email": "563771383@qq.com",
        "vip_level": "0",
        "vip_endtime": null,
        "is_like": 0,
        "date": "1970-01-01"
        }
        })
     */
    public function getArticleInfo()
    {
        $userId = $this->auth->id;

        $articleId = $this->request->param('article_id', 0, 'int');
        if(!$articleId) {
            $this->error('您的提交有误');
        }

        $article = new ArticleModel();
        $articleInfo = $article->infoById($articleId);
        if(!$articleInfo) {
            $this->error('文章不存在');
        }

        // 记录浏览
        $lookArticle = new LookArticle();
        $lookArticleInfo = $lookArticle->infoById($userId, $articleId);
        if($lookArticleInfo) {
            $updateData = ['updatetime' => time()];
            $lookArticle->updateOne($lookArticleInfo['id'], $updateData);
        } else {
            $insertData = [
                'user_id' => $userId,
                'article_id' => $articleId,
                'createtime' => time(),
                'updatetime' => time(),
            ];
            $lookArticle->addOne($insertData);
        }

        $likeArticle = new LikeArticle();
        $likeArticleInfo = $likeArticle->infoById($userId, $articleId);
        if(!$likeArticleInfo) {
            $articleInfo['is_like'] = 0;
        } else {
            $articleInfo['is_like'] = 1;
        }

        $articleInfo['date'] = date('Y-m-d', strtotime($articleInfo['createtime']));
        $articleInfo['head_image'] = $this->qiNiu.$articleInfo['head_image'];
        if($articleInfo['des_images']) {
            $desImagesArr = explode(',', $articleInfo['des_images']);
            foreach ($desImagesArr as $kk => $vv) {
                $desImagesArr[$kk] = $this->qiNiu.$vv;
            }
            $articleInfo['des_images'] = $desImagesArr;
        } else {
            $articleInfo['des_images'] = [];
        }

        $articleInfo['createtime'] = date('Y-m-d', $articleInfo['createtime']);
        $this->success('获取文章的详细信息成功', $articleInfo);
    }


    /**
     * 关注/取关文章
     * @ApiTitle    (关注/取关文章)
     * @ApiSummary  (关注/取关文章)
     * @ApiMethod   (POST)
     * @ApiHeaders  (name="token", type="string", required=true, description="请求的Token")
     * @ApiParams   (name="article_id", type="integer", required=true, description="文章ID")
     * @ApiReturnParams   (name="code", type="integer", required=true, sample="0")
     * @ApiReturnParams   (name="msg", type="string", required=true, sample="返回成功")
     * @ApiReturnParams   (name="data", type="object", sample="{'user_id':'int','user_name':'string','profile':{'email':'string','age':'integer'}}", description="扩展数据返回")
     * @ApiReturn   ({
        "code": 1,
        "msg": "取关成功",
        "time": "1586421214",
        "data": null
        })
     */
    public function likeArticle()
    {
        $userId = $this->auth->id;

        $articleId = $this->request->param('article_id', 0, 'int');
        if(!$articleId) {
            $this->error('您的操作有误');
        }

        $likeArticle = new LikeArticle();
        $likeArticleInfo = $likeArticle->infoById($userId, $articleId);
        if($likeArticleInfo) {
            if($likeArticle->updateOne($likeArticleInfo['id'], ['deletetime' => time()])) {
                $this->success('取关成功');
            } else {
                $this->error('取关失败');
            }
        } else {
            $insertData = [
                'user_id' => $userId,
                'article_id' => $articleId,
                'createtime' => time(),
                'updatetime' => time(),
            ];
            if($likeArticle->addOne($insertData)) {
                $this->success('关注成功');
            } else {
                $this->error('关注失败');
            }
        }
    }


    /**
     * 清空文章浏览记录
     * @ApiTitle    (清空文章浏览记录)
     * @ApiSummary  (清空文章浏览记录)
     * @ApiMethod   (POST)
     * @ApiHeaders  (name="token", type="string", required=true, description="请求的Token")
     * @ApiReturnParams   (name="code", type="integer", required=true, sample="0")
     * @ApiReturnParams   (name="msg", type="string", required=true, sample="返回成功")
     * @ApiReturnParams   (name="data", type="object", sample="{'user_id':'int','user_name':'string','profile':{'email':'string','age':'integer'}}", description="扩展数据返回")
     * @ApiReturn   ()
     */
    public function delLookArticle()
    {
        $userId = $this->auth->id;

        $lookArticle = new LookArticle();
        $res = $lookArticle->updateSomeByUserId($userId, ['deletetime' => time()]);
        if($res) {
            $this->success('清空文章浏览记录成功');
        } else {
            $this->error('清空文章浏览记录失败，请您重试');
        }
    }


    /**
     * 清空文章浏览记录
     * @ApiTitle    (清空文章浏览记录)
     * @ApiSummary  (清空文章浏览记录)
     * @ApiMethod   (POST)
     * @ApiHeaders  (name="token", type="string", required=true, description="请求的Token")
     * @ApiReturnParams   (name="code", type="integer", required=true, sample="0")
     * @ApiReturnParams   (name="msg", type="string", required=true, sample="返回成功")
     * @ApiReturnParams   (name="data", type="object", sample="{'user_id':'int','user_name':'string','profile':{'email':'string','age':'integer'}}", description="扩展数据返回")
     * @ApiReturn   ({
    "code": 1,
    "msg": "获取文章查看历史成功",
    "time": "1587618709",
    "data": [
    {
    "id": 8,
    "user_id": 1,
    "article_id": 8,
    "createtime": 1586770381,
    "updatetime": 1586844971,
    "deletetime": null,
    "level": "2",
    "nickname": "我是大学生",
    "head_image": "/uploads/20200413/39270b1276af4c6af020cc85eb80fb29.jpg",
    "school_id": 11,
    "subject_ids": "3,2,1",
    "up_id": 1,
    "college_id": 1,
    "university_id": 1,
    "graduated_id": 2,
    "starttime": null,
    "endtime": 1586769446,
    "show_switch": 1,
    "email": "563771383@qq.com",
    "vip_level": "0",
    "vip_endtime": null,
    "title": "还是经验",
    "article_type_id": 1,
    "show_type": "2",
    "des_content": "经验经验经验经验经验经验经验经验经验经验经验经验经验经验经验经验经验经验经验经验经验经验经验经验",
    "des_images": "/uploads/20200413/39270b1276af4c6af020cc85eb80fb29.jpg",
    "weigh": 8
    }
    ]
    })
     */
    public function getLookArticleList()
    {
        $page = $this->request->param('page', 1, 'int');
        $size = $this->request->param('size', 10, 'int');
        $userId = $this->auth->id;

        $lookArticle = new LookArticle();
        $lookArticleList = $lookArticle->listById($userId, $page, $size);

        $likeStudent = new LikeStudent();
        $likeStudentList = $likeStudent->listAllByUserId($userId);
        $likeStudentListObj = [];
        foreach ($likeStudentList as $v) {
            $likeStudentListObj[$v['userb_id']] = $v;
        }

        $articleType = new ArticleType();
        $articleTypeList = $articleType->listAll();
        $articleTypeListObj = [];
        foreach ($articleTypeList as $k => $v) {
            $articleTypeListObj[$v['id']] = $v;
        }
        $student = new Student();

        foreach ($lookArticleList as $k => $v) {

            $userInfo = $student->infoByUserId($v['user_id']);
            if($userInfo) {
                $lookArticleList[$k]['nickname'] = $userInfo['nickname'];
                $lookArticleList[$k]['head_image'] = $userInfo['head_image'];
            } else {
                $lookArticleList[$k]['nickname'] = '';
                $lookArticleList[$k]['head_image'] = '';
            }

            if(isset($likeStudentListObj[$v['user_id']])) {
                $lookArticleList[$k]['is_like_user'] = 1;
            } else {
                $lookArticleList[$k]['is_like_user'] = 0;
            }

            if(isset($articleTypeListObj[$v['article_type_id']])) {
                $lookArticleList[$k]['article_type_name'] = $articleTypeListObj[$v['article_type_id']]['name'];
            } else {
                $lookArticleList[$k]['article_type_name'] = '';
            }

            if($v['des_images']) {
                $desImagesArr = explode(',', $v['des_images']);
                foreach ($desImagesArr as $kk => $vv) {
                    $desImagesArr[$kk] = $this->qiNiu.$vv;
                }
                $lookArticleList[$k]['des_images'] = $desImagesArr;
            } else {
                $lookArticleList[$k]['des_images'] = [];
            }

            $lookArticleList[$k]['head_image'] = $this->qiNiu.$v['head_image'];
        }

        $this->success('获取文章查看历史成功', $lookArticleList);
    }




    /**
     * 获取我收藏的文章
     * @ApiTitle    (获取我收藏的文章)
     * @ApiSummary  (获取我收藏的文章)
     * @ApiMethod   (GET)
     * @ApiHeaders  (name="token", type="string", required=true, description="请求的Token")
     * @ApiParams   (name="page", type="integer", required=true, description="分页码")
     * @ApiParams   (name="size", type="integer", required=true, description="分页数")
     * @ApiReturnParams   (name="code", type="integer", required=true, sample="0")
     * @ApiReturnParams   (name="msg", type="string", required=true, sample="返回成功")
     * @ApiReturnParams   (name="data", type="object", sample="{'user_id':'int','user_name':'string','profile':{'email':'string','age':'integer'}}", description="扩展数据返回")
     * @ApiReturn   ({
        "code": 1,
        "msg": "获取我收藏的文章成功",
        "time": "1586844029",
        "data": [
        {
        "id": 8,
        "createtime": 1586770381,
        "updatetime": 1586770381,
        "article_id": 8,
        "user_id": 1,
        "deletetime": null,
        "level": "1",
        "nickname": "我是高中生",
        "head_image": "www.qiniu.com/uploads/20200413/39270b1276af4c6af020cc85eb80fb29.jpg",
        "school_id": 11,
        "subject_ids": "",
        "up_id": 0,
        "college_id": 0,
        "university_id": 0,
        "graduated_id": 0,
        "starttime": 1586769446,
        "endtime": null,
        "show_switch": 0,
        "email": "563771383@qq.com",
        "vip_level": "0",
        "vip_endtime": null,
        "title": "还是经验",
        "article_type_id": 1,
        "show_type": "2",
        "des_content": "经验经验经验经验经验经验经验经验经验经验经验经验经验经验经验经验经验经验经验经验经验经验经验经验",
        "des_images": [
        "www.qiniu.com/uploads/20200413/39270b1276af4c6af020cc85eb80fb29.jpg"
        ],
        "weigh": 8,
        "is_like_user": 1,
        "article_type_name": "经验"
        }
        ]
        })
     */
    public function getMyLikeArticle()
    {
        $userId = $this->auth->id;

        $page = $this->request->param('page', 0, 'int');
        $size = $this->request->param('size', 0, 'int');

        $likeArticle = new LikeArticle();
        $likeArticleList = $likeArticle->listByUserId($userId, $page, $size);

        if(!$likeArticleList) {
            $this->success('获取我收藏的文章成功', $likeArticleList);
        }

        $likeStudent = new LikeStudent();
        $likeStudentList = $likeStudent->listAllByUserId($userId);
        $likeStudentListObj = [];
        foreach ($likeStudentList as $v) {
            $likeStudentListObj[$v['userb_id']] = $v;
        }

        $articleType = new ArticleType();
        $articleTypeList = $articleType->listAll();
        $articleTypeListObj = [];
        foreach ($articleTypeList as $k => $v) {
            $articleTypeListObj[$v['id']] = $v;
        }


        $student = new Student();

        foreach ($likeArticleList as $k => $v) {
            $userInfo = $student->infoByUserId($v['user_id']);
            if($userInfo) {
                $likeArticleList[$k]['nickname'] = $userInfo['nickname'];
                $likeArticleList[$k]['head_image'] = $userInfo['head_image'];
            } else {
                $likeArticleList[$k]['nickname'] = '';
                $likeArticleList[$k]['head_image'] = '';
            }

            if(isset($likeStudentListObj[$v['user_id']])) {
                $likeArticleList[$k]['is_like_user'] = 1;
            } else {
                $likeArticleList[$k]['is_like_user'] = 0;
            }

            if(isset($articleTypeListObj[$v['article_type_id']])) {
                $likeArticleList[$k]['article_type_name'] = $articleTypeListObj[$v['article_type_id']]['name'];
            } else {
                $likeArticleList[$k]['article_type_name'] = '';
            }

            if($v['des_images']) {
                $desImagesArr = explode(',', $v['des_images']);
                foreach ($desImagesArr as $kk => $vv) {
                    $desImagesArr[$kk] = $this->qiNiu.$vv;
                }
                $likeArticleList[$k]['des_images'] = $desImagesArr;
            } else {
                $likeArticleList[$k]['des_images'] = [];
            }

            $likeArticleList[$k]['head_image'] = $this->qiNiu.$v['head_image'];
        }
        $this->success('获取我收藏的文章成功', $likeArticleList);
    }



    /**
     * 添加一个文章
     * @ApiTitle    (添加一个文章)
     * @ApiSummary  (添加一个文章)
     * @ApiMethod   (POST)
     * @ApiHeaders  (name="token", type="string", required=true, description="请求的Token")
     * @ApiParams   (name="title", type="string", required=true, description="标题")
     * @ApiParams   (name="article_type_id", type="integer", required=true, description="分类ID")
     * @ApiParams   (name="show_type", type="integer", required=true, description="1仅校友 2全部")
     * @ApiParams   (name="des_content", type="string", required=true, description="内容")
     * @ApiParams   (name="des_images", type="string", required=true, description="图片集例：a.jpg,b.jpg")
     * @ApiReturnParams   (name="code", type="integer", required=true, sample="0")
     * @ApiReturnParams   (name="msg", type="string", required=true, sample="返回成功")
     * @ApiReturnParams   (name="data", type="object", sample="{'user_id':'int','user_name':'string','profile':{'email':'string','age':'integer'}}", description="扩展数据返回")
     * @ApiReturn   ({
        "code": 1,
        "msg": "您的文章发表成功，请您耐心等待审核",
        "time": "1586844200",
        "data": null
        })
     */
    public function addArticle()
    {
        $userId = $this->auth->id;

        $title = $this->request->param('title', '', 'string');
        if(!$title) {
            $this->error('您的文章标题不能为空');
        }

        if(mb_strlen($title) > 255) {
            $this->error('您的文章标题不能超过255汉字长度');
        }

        $articleTypeId = $this->request->param('article_type_id', 0, 'int');
        if(!$articleTypeId) {
            $this->error('请您选择正确的文章类型');
        }

        $showType = $this->request->param('show_type', 1, 'int');

        $desContent = $this->request->param('des_content', '', 'string');
        if(!$desContent) {
            $this->error('您的文章内容不能为空');
        }

        $desImages = $this->request->param('des_images', '', 'string');
        $imageExtArr = ['jpg', 'jpeg', 'png'];
        if($desImages) {
            $desImagesArr = explode(',', $desImages);
            foreach ($desImagesArr as $k => $v) {
                $pathInfo = pathinfo($v);
                if(!isset($pathInfo['extension']) || !in_array($pathInfo['extension'], $imageExtArr)) {
                    $this->error('请选择正确的图片格式');
                }
            }
        }

        $student = new Student();
        $studentInfo = $student->infoByUserIdCanShow($userId);
        if(!$studentInfo) {
            $this->error('您的操作有误');
        }

        if($studentInfo['level'] == 1) {
            $this->error('高中生不能发布文章哦');
        }

        $insertData = [
            'title' => $title,
            'article_type_id' => $articleTypeId,
            'show_type' => $showType,
            'des_content' => $desContent,
            'des_images' => $desImages,
            'user_id' => $userId,
            'createtime' => time(),
            'updatetime' => time(),
            'show_switch' => 1,
        ];

        $article = new ArticleModel();
        $res = $article->addOne($insertData);
        if(!$res) {
            $this->error('您的文章发表失败，您可以重试一下');
        } else {
            $this->success('您的文章发表成功，请您耐心等待审核');
        }
    }



    /**
     * 获取我发布的文章
     * @ApiTitle    (获取我发布的文章)
     * @ApiSummary  (获取我发布的文章)
     * @ApiMethod   (GET)
     * @ApiHeaders  (name="token", type="string", required=true, description="请求的Token")
     * @ApiParams   (name="page", type="integer", required=true, description="分页码")
     * @ApiParams   (name="size", type="integer", required=true, description="分页数")
     * @ApiReturnParams   (name="code", type="integer", required=true, sample="0")
     * @ApiReturnParams   (name="msg", type="string", required=true, sample="返回成功")
     * @ApiReturnParams   (name="data", type="object", sample="{'user_id':'int','user_name':'string','profile':{'email':'string','age':'integer'}}", description="扩展数据返回")
     * @ApiReturn   ({
        "code": 1,
        "msg": "获取我发布的文章",
        "time": "1586844908",
        "data": [
        {
        "id": 11,
        "createtime": 1586844201,
        "updatetime": 1586844201,
        "show_switch": 0,
        "title": "1",
        "article_type_id": 1,
        "show_type": "1",
        "des_content": "1",
        "des_images": [
        "www.qiniu.com1.jpg",
        "www.qiniu.com2.jpg"
        ],
        "weigh": 0,
        "user_id": 1,
        "deletetime": null,
        "article_type_name": "经验"
        }
        ]
        })
     */
    public function getMyArticle()
    {
        $userId = $this->auth->id;

        $page = $this->request->param('page', 1, 'int');
        $size = $this->request->param('size', 10, 'int');

        $article = new ArticleModel();
        $articleList = $article->listByUserId($userId, $page, $size);
        if(!$articleList) {
            $this->success('获取我发布的文章', $articleList);
        }

        $articleType = new ArticleType();
        $articleTypeList = $articleType->listAll();
        $articleTypeListObj = [];
        foreach ($articleTypeList as $k => $v) {
            $articleTypeListObj[$v['id']] = $v;
        }

        $student = new Student();
        foreach ($articleList as $k => $v) {
            $userInfo = $student->infoByUserId($v['user_id']);
            if($userInfo) {
                $likeArticleList[$k]['nickname'] = $userInfo['nickname'];
                $likeArticleList[$k]['head_image'] = $userInfo['head_image'];
            } else {
                $likeArticleList[$k]['nickname'] = '';
                $likeArticleList[$k]['head_image'] = '';
            }

            if(isset($articleTypeListObj[$v['article_type_id']])) {
                $articleList[$k]['article_type_name'] = $articleTypeListObj[$v['article_type_id']]['name'];
            } else {
                $articleTypeList[$k]['article_type'] = '';
            }

            if($v['des_images']) {
                $desImagesArr = explode(',', $v['des_images']);
                foreach ($desImagesArr as $kk => $vv) {
                    $desImagesArr[$kk] = $this->qiNiu.$vv;
                }
                $articleList[$k]['des_images'] = $desImagesArr;
            } else {
                $articleList[$k]['des_images'] = [];
            }

        }
        $this->success('获取我发布的文章', $articleList);
    }



    /**
     * 获取所有文章类型
     * @ApiTitle    (获取所有文章类型)
     * @ApiSummary  (获取所有文章类型)
     * @ApiMethod   (GET)
     * @ApiReturnParams   (name="code", type="integer", required=true, sample="0")
     * @ApiReturnParams   (name="msg", type="string", required=true, sample="返回成功")
     * @ApiReturnParams   (name="data", type="object", sample="{'user_id':'int','user_name':'string','profile':{'email':'string','age':'integer'}}", description="扩展数据返回")
     * @ApiReturn   ({
        "code": 1,
        "msg": "获取所有文章类型成功",
        "time": "1586484966",
        "data": [
        {
        "id": 1,
        "name": "经验",
        "weigh": 0,
        "createtime": 0,
        "updatetime": 0,
        "show_switch": 1,
        "deletetime": null
        }
        ]
        })
     */
    public function getArticleType()
    {
        $articleType = new ArticleType();
        $articleTypeList = $articleType->listAll();
        $this->success('获取所有文章类型成功', $articleTypeList);
    }



    /**
     * 删除某个文章
     * @ApiTitle    (删除某个文章)
     * @ApiSummary  (删除某个文章)
     * @ApiMethod   (POST)
     * @ApiHeaders  (name="token", type="string", required=true, description="请求的Token")
     * @ApiParams   (name="article_id", type="integer", required=true, description="文章ID")
     * @ApiReturnParams   (name="code", type="integer", required=true, sample="0")
     * @ApiReturnParams   (name="msg", type="string", required=true, sample="返回成功")
     * @ApiReturnParams   (name="data", type="object", sample="{'user_id':'int','user_name':'string','profile':{'email':'string','age':'integer'}}", description="扩展数据返回")
     * @ApiReturn   (删除某个文章)
     */
    public function delArticle()
    {
        $userId = $this->auth->id;
        $articleId = $this->request->param('article_id', 0, 'int');
        if(!$articleId) {
            $this->error('');
        }

        $articleModel = new ArticleModel();

        $articleInfo = $articleModel->infoByIdNoWhere($articleId);
        if(!$articleInfo) {
            $this->error('');
        }

        if($articleInfo['user_id'] != $userId) {
            $this->error('不能删除他人文章');
        }

        $res = $articleModel->delOne($articleId, ['deletetime' => time()]);
        if(!$res) {
            $this->error('删除某个文章失败');
        }
        $this->success('删除某个文章成功');
    }
}