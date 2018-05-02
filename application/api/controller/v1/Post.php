<?php
/**
 * Created by PhpStorm.
 * User: tzx
 * Date: 2016/10/21
 * Time: 13:07
 */
namespace app\api\controller\v1;

use think\Controller;

class Post extends Controller
{
    // 注册
    public function register()
    {
        return $this->fetch();
    }

    // 登陆
    public function login()
    {
        return $this->fetch();
    }

    // 注销
    public function logout()
    {
        return $this->fetch();
    }


    public function profit()
    {
        return $this->fetch();
    }

    public function member_server()
    {
        return $this->fetch();
    }

    public function service_package()
    {
        return $this->fetch();
    }

    public function member_service()
    {
        return $this->fetch();
    }

    public function account_demo()
    {
        return $this->fetch();
    }

    public function trade_server()
    {
        return $this->fetch();
    }
    public function change_password()
    {
        return $this->fetch();
    }

    public function platform_profit()
    {
        return $this->fetch();
    }

    public function account_profit()
    {
        return $this->fetch();
    }

    public function account_profit_rate()
    {
        return $this->fetch();
    }

    public function service_status()
    {
        return $this->fetch();
    }

    public function service_check()
    {
        return $this->fetch();
    }

    public function order_insert()
    {
        return $this->fetch();
    }

    public function center()
    {
        return $this->fetch();
    }

    public function home()
    {
        return $this->fetch();
    }
    // 首页幻灯片
    public function site_slide()
    {
        return $this->fetch();
    }
    // 短信验证码
    public function sms()
    {
        return $this->fetch();
    }
    // 检测用户名
    public function check_username()
    {
        return $this->fetch();
    }

    // 检测 手机号
    public function check_tel()
    {
        return $this->fetch();
    }

    public function add_server()
    {
        return $this->fetch();
    }

    // 最大回撤率 DrawDown
    public function draw_down()
    {
        return $this->fetch();
    }

}