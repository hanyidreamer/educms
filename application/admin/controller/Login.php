<?php
/**
 * Created by PhpStorm.
 * User: tzx
 * Date: 2016/10/24
 * Time: 22:03
 */
namespace app\admin\controller;

use think\Controller;
use think\Request;
use app\base\model\Admin;
use app\base\model\Site;
use app\base\controller\Site as SiteInfo;
use app\base\controller\Template;
use app\base\model\System;

class Login extends Controller
{
    /**
     * 后台登录页
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function index()
    {
        // 当前网站信息
        $site_data = new SiteInfo();
        $site = $site_data->info();
        $this->assign('site', $site);
        // 后台系统配置信息
        $system_data = System::get(['site_id'=>$site['id']]);
        if(empty($system_data)){
            $system_data = System::get(['site_id'=>0]);
        }
        $this->assign('system', $system_data);

        // 后台模板路径
        $template = new Template();
        $template_path = $template->path();
        return $this->fetch($template_path);
    }

    /**
     * 登陆后台权限判断
     * @param Request $request
     * @throws \think\exception\DbException
     */
    public function check(Request $request)
    {
        // 判断验证码是否正确
        $post_captcha = $request->post('captcha');
        if(empty($post_captcha)){
            $this->error('验证码不能为空');
        }
        if(!captcha_check($post_captcha)){
            //验证失败
            $this->error("验证码错误");
        }

        // 验证用户名和密码是否正确
        $post_username = $request->post('username');
        $post_password = $request->post('password');

        if(empty($post_username)){
            $this->error('用户名不能为空');
        }
        if(empty($post_password)){
            $this->error('密码不能为空');
        }

        // 验证用户名和密码是否正确
        $post_password = md5($post_password);

        // 检查 用户名是否正确
        $admin_info = Admin::get(['username'=>$post_username]);
        $admin_site_id = $admin_info['site_id'];
        if(!empty($admin_info))
        {
            // username 存在 ,判断密码是否正确
            $admin_password_sql['username'] = $post_username;
            $admin_password_sql['password'] = $post_password;
            $admin_password = Admin::get(['username'=>$post_username,'password'=>$post_password]);
            if(!empty($admin_password)){
                // 用户名密码正确，将$username 存session。
                session('admin_username',$post_username);
                session('admin_password',$post_password);
            }else{
                // 密码错误
                $this->error('用户名或密码错误，登陆失败','/admin/login/index');
            }

        } else {
            // 用户名错误
            $this->error('用户名或密码错误，登陆失败','/admin/login/index');
        }

        $my_admin_status = $admin_info['status'];
        // 判断是否拥有网站权利权限
        $get_domain = $this->request->server('HTTP_HOST');
        $get_domain = preg_replace('/www./', '', $get_domain);
        $site_info_list = Site::get(['domain'=>$get_domain]);
        $site_id = $site_info_list['id'];

        if($site_id==$admin_site_id or $my_admin_status==1){
            $this->success('登录成功', '/admin/index/index');
        }else{
            $this->error('您没有管理网站的权限','/admin/login/index');
        }

    }


}