<?php


namespace app\api\model;

use think\Model;

class School extends Model
{
    protected $name = 'school';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';


    // 根据城市ID获取多个学校ID集
    public function listByCityId($cityId)
    {
        return $this->where(['city_id' => ['=', $cityId], 'show_switch' => ['=', 1]])->useSoftDelete($this->deleteTime)->column('id');
    }

    public function listAllByCityId($cityId)
    {
        return $this->where(['city_id' => ['=', $cityId], 'show_switch' => ['=', 1]])->order('weigh', 'desc')->useSoftDelete($this->deleteTime)->select();
    }

    // 根据学校ID获取学校信息
    public function infoById($id)
    {
        return $this->where(['id' => ['=', $id], 'show_switch' => ['=', 1]])->useSoftDelete($this->deleteTime)->find();
    }

    // 根据省ID获取多个学校ID集
    public function listByProvinceId($provinceId)
    {
        return $this->where(['province_id' => ['=', $provinceId], 'show_switch' => ['=', 1]])->useSoftDelete($this->deleteTime)->column('id');
    }

    // 添加一个学校
    public function addOne($data)
    {
        return $this->insert($data);
    }
}