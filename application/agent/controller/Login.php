<?php
/**
 * Created by PhpStorm.
 * User: tanzhenxing
 * Date: 2018/5/8
 * Time: 16:23
 */

namespace app\agent\controller;

use app\common\model\Agent;
use think\Controller;
use app\base\controller\Template;

class Login extends Controller
{
    /**
     * 后台登录页
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function index()
    {
        // 后台模板路径
        $public_array = array('public_header'=>'/public/agent_header','public_footer'=>'/public/agent_footer');
        $template = new Template();
        $template_path = $template->path($public_array);

        return $this->fetch($template_path);
    }

    /**
     * 登陆后台权限判断
     * @param Request $request
     * @throws \think\exception\DbException
     */
    public function check()
    {
        // 验证用户名和密码是否正确
        $post_username = $this->request->post('username');
        $post_password = $this->request->post('password');

        if(empty($post_username)){
            $this->error('用户名不能为空');
        }
        if(empty($post_password)){
            $this->error('密码不能为空');
        }

        // 验证用户名和密码是否正确
        $post_password = md5($post_password);

        // 检查 用户名是否正确
        $admin_info = Agent::get(['username'=>$post_username]);

        if(!empty($admin_info))
        {
            // username 存在 ,判断密码是否正确
            $admin_password_sql['username'] = $post_username;
            $admin_password_sql['password'] = $post_password;
            $admin_password = Agent::get(['username'=>$post_username,'password'=>$post_password]);
            if(!empty($admin_password)){
                // 用户名密码正确，将$username 存session。
                session('agent_username',$post_username);
                session('agent_password',$post_password);
            }else{
                // 密码错误
                $this->error('用户名或密码错误，登陆失败','/agent/login/index');
            }

        } else {
            // 用户名错误
            $this->error('用户名或密码错误，登陆失败','/agent/login/index');
        }

        $this->success('登录成功', '/agent/index/index');

    }

}