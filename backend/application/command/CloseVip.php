<?php
/**
 * Created by PhpStorm.
 * User: DELL
 * Date: 2019/5/31
 * Time: 9:25
 */
namespace app\command;

use app\api\model\Student;
use think\console\Input;
use think\console\Output;
use think\console\Command;


class CloseVip extends Command{

    protected function configure()
    {
        $this->setName('CloseVip')->setDescription('关闭vip已经TimeOut的vip用户, 每1分钟执行一次');
    }

    protected function execute(Input $input, Output $output)
    {
        $student = new Student();

        $where['vip_endtime'] = ['<', time()];
        $studentList = $student->listByWhere($where);

        $userIds = [];
        foreach ($studentList as $v) {
            $userIds[] = $v['user_id'];
        }

        if($userIds) {
            $student->updateByIds($userIds, ['vip_level' => 0, 'vip_endtime' => null, 'updatetime' => time()]);
        }
    }
}