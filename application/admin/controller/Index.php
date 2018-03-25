<?php

namespace app\admin\controller;

use think\Session;
use think\Request;
use app\base\model\Admin;
use app\base\model\Site;
use app\base\controller\Base;
use app\base\controller\TemplatePath;

class Index extends Base
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
        $title = '后台管理系统';
        $this->assign('title',$title);

        // 当前方法不同终端的模板路径
        $controller_name = $this->request->controller();
        $action_name = $this->request->action();
        $template_path_info = new TemplatePath();
        $template_path = $template_path_info->admin_path($controller_name,$action_name);

        $get_domain = $this->request->server('HTTP_HOST');
        $this->assign('domain',$get_domain);

        $admin_username = session('username');
        $this->assign('admin_username',$admin_username);
        $admin_info = Admin::get(['username'=>$admin_username]);
        $admin_id = $admin_info['id'];

        $site_info_sql['admin_id'] = $admin_id;
        $my_site_info = new Site();
        $site_info = $my_site_info->where($site_info_sql) -> select();
        $this->assign('site_info',$site_info);


        $admin_type = $admin_info['category_id'];
        if($admin_type==1){
            return $this->fetch($template_path);
        }
        else{
            $template_path = preg_replace('/\/index\/index/','/index/index2',$template_path);
            return $this->fetch($template_path);
        }

    }

}