<?php

namespace app\api\controller;

use think\Controller;
use app\base\model\AdminUser as AdminUserModel;

class AdminUser extends Controller
{
    public function adminUserInfo($open_id = '')
    {
        $user_data = new AdminUserModel();
        $user_info = $user_data->getInfoByOpenid($open_id);
        return $user_info;
    }

    public function adminUserWithDepartment($open_id){
        $user_data = new AdminUserModel();
        $user_info = $user_data->getInfoByOpenid($open_id);
        return $user_info->toArray();
    }

}
