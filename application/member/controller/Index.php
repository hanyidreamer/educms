<?php
/**
 * Created by PhpStorm.
 * User: tanzhenxing
 * Date: 2017/6/21
 * Time: 10:43
 */
namespace app\member\controller;

use think\facade\Session;
use think\facade\Cookie;
use app\base\controller\Base;

class Index extends Base
{
    // 会员中心
    /**
     * @return mixed
     */
    public function index(){
        // 判断用户是否登录
        if(empty(session('username'))){
            $this->error('请先登录','/member/login/index');
        }
        return $this->fetch($this->template_path);
    }
    // 注销登录

    /**
     *
     */
    public function logout(){
        Session::delete('username');
        Session::delete('password');
        Cookie::delete('password');
        Cookie::delete('username');
        $this->success('正在退出', '/member/login/index');
    }
}
