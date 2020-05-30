<?php


namespace app\api\model;

use think\Model;

class City extends Model
{
    protected $name = 'city';

    public function listByCode($code)
    {
        return $this->where(['provinceCode' => ['=', $code]])->select();
    }
}