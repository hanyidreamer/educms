<?php
/**
 * Created by PhpStorm.
 * User: tanzhenxing
 * Date: 2017/6/21
 * Time: 10:43
 */
namespace app\member\controller;

use think\Request;
use think\Session;
use think\Cookie;
use app\index\controller\Base;

class Index extends Base
{
    // 会员中心
    public function index(){
        $username = $this->username ;
        $this->assign('username',$username);

        $template_path = $this->template_path;

        // 判断用户是否登录
        if(empty($username)){
            $this->error('请先登录','/member/login/index');
        }

        return $this->fetch($template_path);
    }
    // 注销登录
    public function logout(){
        Session::delete('username');
        Session::delete('password');
        Cookie::delete('password');
        Cookie::delete('username');
        $this->success('正在退出', '/member/login/index');
    }
}
