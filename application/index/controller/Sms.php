<?php
/**
 * Created by PhpStorm.
 * User: tanzhenxing
 * Date: 2017/6/21
 * Time: 21:08
 */
namespace app\index\controller;

use think\Controller;
use think\facade\Session;
use app\base\model\Curl;

class Sms extends Controller
{
    public function send($mobile)
    {
        // 生成随机4位数字的验证码
        $code_num = rand(1000,9999);
        $uid = 'bozhundianzi01';
        $password = 'bfa0e2dde8dbbbeb7dba0ec986ca588c';
        $template_number = '100006';
        $text = '{"code":"您的验证码是：'.$code_num.'"}';

        // 判断手机号是否合法
        if(is_numeric($mobile)){
            session::set('mobile',$mobile);
            session::set('sms_code',$code_num);
        }
        // 发送验证码到指定手机号
        $sms_url = "http://api.sms.cn/sms/?ac=send&uid=$uid&pwd=$password&template=$template_number&mobile=$mobile&content=$text";

        // curl基础信息配置
        $timeout = 300;
        $user_agent = 'Mozilla/4.0+(compatible;+MSIE+6.0;+Windows+NT+5.1;+SV1)';
        $sms_data = new Curl();
        $back_text = $sms_data->get_info($sms_url, $timeout, $user_agent);

        echo $sms_url;

        // $back_text = file_get_contents($sms_url);
        echo $back_text;
    }
}