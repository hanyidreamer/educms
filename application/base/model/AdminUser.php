<?php

namespace app\base\model;

use think\Model;

class AdminUser extends Model
{
    // 通过微信open_id 获取用户信息
    public function getInfoByOpenid($open_id){
        return $this->get(['weixin_openid'=>$open_id]);
    }

    //方法一
    public function getDepartmentIdAttr($value)
    {
        return $this->belongsTo('AdminDepartment','department_id','id')->where('id',$value)->value('name');
    }
}
