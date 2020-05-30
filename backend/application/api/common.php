<?php


function handleOrder($orderInfo) {
    try {

        $orderInfo['other'] = json_decode($orderInfo['other'], true);
        if($orderInfo['type'] == 1) { // 打赏
            $user = new \app\api\model\User();
            $userInfo = $user->infoAndLockById($orderInfo['other']['user_id']);
            if(!$userInfo) {
                return false;
            }
            $insertData = [
                'user_id' => $orderInfo['other']['user_id'],
                'money' => $orderInfo['money'],
                'memo' => '收到打赏',
                'createtime' => time(),
            ];
            $userMoneyLog = new \app\api\model\UserMoneyLog();
            $res = $userMoneyLog->addOne($insertData);
            if(!$res) {
                return false;
            }

            $updateData = [
                'money' => bcadd($userInfo['money'], $orderInfo['money'], 2)
            ];
            $res = $user->updateOne($orderInfo['other']['user_id'], $updateData);
            if(!$res) {
                return false;
            }
            return true;
        } elseif ($orderInfo['type'] == 2) { // 购买vip

            $student = new \app\api\model\Student();
            $studentInfo = $student->infoByUserIdCanShow($orderInfo['other']['user_id']);
            if(!$student) {
                return false;
            }

            //添加明细
            $insertData = [
                'user_id' => $orderInfo['other']['user_id'],
                'money' => $orderInfo['money'],
                'memo' => '购买VIP',
                'createtime' => time()
            ];

            if($studentInfo['vip_endtime']) {
                if($studentInfo['vip_level'] == $orderInfo['other']['vip_level']) {
                    if($studentInfo['vip_endtime'] >= time()) {
                        $vipEndTime = bcadd($studentInfo['vip_endtime'], bcmul($orderInfo['other']['keep'], bcmul(3600, 24, 0), 0), 0);
                        //添加明细
                        $insertData = [
                            'user_id' => $orderInfo['other']['user_id'],
                            'money' => $orderInfo['money'],
                            'memo' => '续费VIP',
                            'createtime' => time()
                        ];
                    } else {
                        $vipEndTime = bcadd(bcmul($orderInfo['other']['keep'], bcmul(3600, 24, 0), 0), time(), 0);
                    }
                } else {
                    $vipEndTime = bcadd(bcmul($orderInfo['other']['keep'], bcmul(3600, 24, 0), 0), time(), 0);
                }
            } else {
                $vipEndTime = bcadd(bcmul($orderInfo['other']['keep'], bcmul(3600, 24, 0), 0), time(), 0);
            }

            $userMoneyLog = new \app\api\model\UserMoneyLog();
            $res1 = $userMoneyLog->addOne($insertData);

            $updateData = [
                'vip_level' => $orderInfo['other']['vip_level'],
                'vip_endtime' => $vipEndTime,
            ];
            $res = $student->updateOne($studentInfo['user_id'], $updateData);

            if(!$res) {
                return false;
            }
            return true;
        } else {
            return false;
        }
    } catch (\Exception $e) {
        return false;
    }

}