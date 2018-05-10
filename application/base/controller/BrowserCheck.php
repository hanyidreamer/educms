<?php
/**
 * Created by PhpStorm.
 * User: tanzhenxing
 * Date: 2017/9/11
 * Time: 10:05
 */
namespace app\base\controller;

use think\controller;

class BrowserCheck extends Controller
{
    public function info()
    {
        // 判断是否为微信浏览器
        if(strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
            return 'wechat';
        }
        // 判断是否为手机浏览器
        $mobile_browser = $this->request->isMobile();
        if($mobile_browser){
            return 'mobile';
        }else{
            return 'pc';
        }
    }
}