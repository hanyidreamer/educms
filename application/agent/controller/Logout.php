<?php
/**
 * Created by PhpStorm.
 * User: tanzhenxing
 * Date: 2018/5/8
 * Time: 16:23
 */
namespace app\agent\controller;

use think\Controller;

class Logout extends Controller
{
    /**
     * 注销登录
     */
    public function index()
    {
        session('agent_username',null);
        session('agent_password',null);
        $this->success('正在退出', '/agent/login/index');
    }
}