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

class Login extends Controller
{
    /**
     * 后台登录页
     * @return mixed
     */
    public function index()
    {
        // 后台模板路径
        $template = new AdminTemplate();
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

        if(!empty($admin_info))
        {
            // username 存在 ,判断密码是否正确
            $admin_password_sql['username'] = $post_username;
            $admin_password_sql['password'] = $post_password;
            $admin_password = Admin::get(['username'=>$post_username,'password'=>$post_password]);
            if(!empty($admin_password)){
                // 用户名密码正确，将$username 存session。
                session('username',$post_username);
                session('password',$post_password);
                cookie('username',$post_username);
                cookie('password',$post_password);
            }else{
                // 密码错误
                $this->error('用户名或密码错误，登陆失败','/admin/login/index');
            }

        } else {
            // 用户名错误
            $this->error('用户名或密码错误，登陆失败','/admin/login/index');
        }

        $my_admin_id = $admin_info['id'];
        $my_admin_status = $admin_info['status'];
        // 判断是否拥有网站权利权限
        $get_domain = $this->request->server('HTTP_HOST');
        $get_domain = preg_replace('/www./', '', $get_domain);
        $site_info_list = Site::get(['domain'=>$get_domain]);
        $admin_id = $site_info_list['admin_id'];

        if($my_admin_id==$admin_id or $my_admin_status==1){
            $this->success('登录成功', '/admin/index/index');
        }else{
            $this->error('您没有管理网站的权限','/admin/login/index');
        }

    }


}