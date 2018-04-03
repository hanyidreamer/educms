<?php
/**
 * Created by PhpStorm.
 * User: tanzhenxing
 * Date: 2017/6/14
 * Time: 17:23
 */
namespace app\base\controller;

use think\Controller;
use app\base\model\Site;
use app\base\model\Admin;

class Base extends Controller
{
    protected $site_id;
    protected $template_path;
    /**
     * 检查客户端信息
     * @throws \think\exception\DbException
     */
    protected function initialize()
    {
        // 获取当前域名是否授权
        $get_domain = $this->request->server('HTTP_HOST');
        $get_domain = preg_replace('/www./', '', $get_domain);
        $site_info = Site::get(['domain'=>$get_domain]);

        // 显示未授权域名的提示信息
        if(empty($site_info)) {
            $this->error('欢迎使用培训分销系统，您的网站还没有开通，请联系电话：13450232305');
        }
        // 获取site_id
        $site_id = $site_info['id'];
        $this->site_id = $site_id;
        $site_admin_id = $site_info['admin_id'];

        // 判断是否有管理网站的权限
        $admin_username = session('username');
        $admin_data = Admin::get(['username'=>$admin_username]);
        $my_admin_id = $admin_data['id'];

        if($site_admin_id != $my_admin_id){
            $this->error('您没有管理该网站的权限','/admin/login/index');
        }

        // 当前方法不同终端的模板路径
        // $module_name = $this->request->module();
        $controller_name = $this->request->controller();
        $action_name = $this->request->action();
        $template_path_info = new TemplatePath();
        $template_path = $template_path_info->admin_path($controller_name,$action_name);
        $template_public = $template_path_info->admin_public_path();
        $template_public_header = $template_public.'/header';
        $template_public_footer = $template_public.'/footer';
        $this->assign('public_header',$template_public_header);
        $this->assign('public_footer',$template_public_footer);

        $this->template_path = $template_path;
    }
}