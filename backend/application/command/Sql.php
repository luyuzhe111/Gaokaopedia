<?php
/**
 * Created by PhpStorm.
 * User: DELL
 * Date: 2019/5/31
 * Time: 9:25
 */

/**
 * Created by PhpStorm.
 * User: DELL
 * Date: 2019/4/9
 * Time: 10:58
 */
namespace app\command;

use app\api\model\Third;
use app\api\model\Withdraw;
use think\console\Input;
use think\console\Output;
use think\console\Command;
use think\Db;

class Sql extends Command{

    protected function configure()
    {
        $this->setName('Sql')->setDescription('');
    }

    protected function execute(Input $input, Output $output)
    {

        $f = true;
        $page = 1;
        $size = 1;
        while ($f) {
            $u = Db::table('t_univs')->order('ID', 'asc')->page($page, $size)->select();
            if(isset($u[0]) && $u[0]) {
                $us = Db::table('fa_university')->where(['name' => ['like', $u[0]['UnivsName'].'%']])->find();
                if($us) {
                    $co = Db::table('t_univsdep')->where(['UnivsID' => ['=', $u[0]['UnivsID']]])->select();
                    foreach ($co as $k => $v) {
                        Db::table('fa_college')->insert(['university_id' => $us['id'], 'name' => $v['DepName'], 'show_switch' => 1]);
                    }
                }
            } else {
                $f = false;
            }
            $page++;
        }
    }
}