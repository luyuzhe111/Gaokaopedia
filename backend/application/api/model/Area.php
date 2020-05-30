<?php


namespace app\api\model;

use think\Model;

class Area extends Model
{
    protected $name = 'area';

    public function listByCode($code)
    {
        return $this->where(['cityCode' => ['=', $code]])->select();
    }
}