<?php

namespace app\admin\controller;

use app\base\model\Admin;
use app\base\model\Site;

class Index extends AdminBase
{
    /**
     * 后台默认首页
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index()
    {
        $admin_username = session('username');
        $this->assign('admin_username',$admin_username);
        $admin_info = Admin::get(['username'=>$admin_username]);
        $admin_id = $admin_info['id'];

        $site_info_sql['admin_id'] = $admin_id;
        $my_site_info = new Site();
        $site_info = $my_site_info->where($site_info_sql) -> select();
        $this->assign('site_info',$site_info);

        if($admin_id==1){
            return $this->fetch($this->template_path);
        }
        else{
            $template_path = preg_replace('/\/index\/index/','/index/index2',$this->template_path);
            return $this->fetch($template_path);
        }

    }

}