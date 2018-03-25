<?php
/**
 * Created by PhpStorm.
 * User: tanzhenxing
 * Date: 2017/6/21
 * Time: 10:51
 */
namespace app\member\controller;

use think\Session;
use think\Cookie;
use think\Request;
use app\index\controller\Base;
use app\base\model\Member;
use app\base\controller\BrowserCheck;
use app\member\controller\WeixinUser;


class Login extends Base
{
    public function index(){
        $username = $this->username ;
        $this->assign('username',$username);

        $site_id = $this->site_id;

        $mid = $this->mid;
        $this->assign('mid',$mid);

        // 判断是否为微信浏览器
        $user_browser = new BrowserCheck();
        $user_browser_info = $user_browser->info();
        if($user_browser_info=='wechat_browser'){

        }
        $template_path = $this->template_path;
        return $this->fetch($template_path);
    }

    public function check(Request $request)
    {
        $post_site_id = $request->param('site_id');
        // 验证用户名和密码是否正确
        $post_username = $request->param('username');
        $post_password = $request->param('password');

        if(empty($post_username)){
            $this->error('用户名不能为空');
        }
        if(empty($post_password)){
            $this->error('密码不能为空');
        }

        // 验证用户名和密码是否正确
        $post_password = md5($post_password);


        // 验证用户名是否为手机号
        if (is_numeric($post_username) and strlen($post_username)==11) {
            $member_data = Member::get(['tel'=>$post_username,'site_id'=>$post_site_id]);
        }else{
            $member_data = Member::get(['username'=>$post_username]);
        }

        if(!empty($member_data))
        {
            // username 存在 ,判断密码是否正确
            if (is_numeric($post_username) and strlen($post_username)==11) {
                $member_password = Member::get(['tel'=>$post_username,'site_id'=>$post_site_id,'password'=>$post_password]);
                $username = $member_password['username'];
                $password = $member_password['password'];
            }else{
                $member_password = Member::get(['username'=>$post_username,'password'=>$post_password]);
                $username = $member_password['username'];
                $password = $member_password['password'];
            }

            if(!empty($member_password)){
                // 用户名密码正确，将$username 存session。
                Session::set('username',$username);
                Session::set('password',$password);
                Cookie::set('username',$username);
                Cookie::set('password',$password);
            }else{
                // 密码错误
                $this->error('用户名或密码错误，登陆失败');
            }

        } else {
            // 用户名错误
            $this->error('用户名或密码错误，登陆失败');
        }

        $this->success('登录成功', '/member/index/index');
    }

    public function index_wx(){
        $username = $this->username ;
        $this->assign('username',$username);

        $site_id = $this->site_id;

        $mid = $this->mid;
        $this->assign('mid',$mid);

        // 判断是否为微信浏览器
        $user_browser = new BrowserCheck();
        $user_browser_info = $user_browser->info();
        if($user_browser_info=='wechat_browser'){
            $weixin_user_info = new WeixinUser();
            $openid = $weixin_user_info->login($site_id,$mid);
            $this->assign('openid',$openid);
        }
        $template_path = $this->template_path;
        return $this->fetch($template_path);
    }

    public function check_wx(Request $request){
        $post_site_id = $request->param('site_id');
        // 验证用户名和密码是否正确
        $post_mobile = $request->param('mobile');
        $post_code = $request->param('code');
        var_dump($post_code);
        var_dump($post_mobile);
        var_dump($post_site_id);

    }

}