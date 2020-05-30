<?php


namespace app\api\model;

use think\Model;

class Province extends Model
{
    protected $name = 'province';


    // 获取全部的省
    public function listAll()
    {
        return $this->select();
    }
}