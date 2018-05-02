<?php
/**
 * Created by PhpStorm.
 * User: tanzhenxing
 * Date: 2017/6/21
 * Time: 10:43
 */
namespace app\member\controller;

use app\common\model\Member;
use think\facade\Session;
use think\facade\Cookie;
use app\base\controller\Base;

class Index extends Base
{
    /**
     * 会员中心
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function index(){
        // 判断用户是否登录
        if(empty(session('username'))){
            $this->error('请先登录','/member/login/index');
        }
        // 当前会员信息
        session('username');
        $member = Member::get(['username'=>session('username')]);
        $this->assign('member',$member);

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
