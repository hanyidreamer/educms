<?php
/**
 * Created by PhpStorm.
 * User: tanzhenxing
 * Date: 2017/6/23
 * Time: 10:57 APP SECRET
 */
namespace app\member\controller;

use think\Controller;
use app\base\model\Curl;

class WeixinOauth extends Controller
{
    /**
     * 生成OAuth2的URL （基本信息，不需要点击登录）
     * @param $app_id
     * @param $redirect_url
     * @param $scope
     * @param $state
     * @return string
     */
    public function oauth2_authorize($app_id,$redirect_url,$scope,$state)
    {
        $url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$app_id."&redirect_uri=".$redirect_url."&response_type=code&scope=".$scope."&state=".$state."#wechat_redirect";
        return $url;
    }

    //生成OAuth2的code （需要点击登录）
    public function oauth2_authorize_code($app_id,$redirect_url,$scope,$state)
    {
        $url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$app_id."&redirect_uri=".$redirect_url."&response_type=code&scope=".$scope."&state=".$state."#wechat_redirect";
        $timeout = 30;
        $user_agent = 'Mozilla/5.0 (iPhone; CPU iPhone OS 10_3_2 like Mac OS X) AppleWebKit/603.2.4 (KHTML, like Gecko) Mobile/14F89 MicroMessenger/6.5.9 NetType/4G Language/zh_CN';
        $data = new Curl();
        $res = $data->get_info($url,$timeout,$user_agent);
        return $res;
    }

    //生成OAuth2的Access Token
    public function oauth2_access_token($app_id,$app_secret,$code)
    {
        $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=".$app_id."&secret=".$app_secret."&code=".$code."&grant_type=authorization_code";
        $timeout = 30;
        $user_agent = 'Mozilla/5.0 (iPhone; CPU iPhone OS 10_3_2 like Mac OS X) AppleWebKit/603.2.4 (KHTML, like Gecko) Mobile/14F89 MicroMessenger/6.5.9 NetType/4G Language/zh_CN';
        $data = new Curl();
        $res = $data->get_info($url,$timeout,$user_agent);
        return $res;
    }

    //获取用户基本信息（OAuth2 授权的 Access Token 获取 未关注用户，Access Token为临时获取）
    public function oauth2_get_user_info($access_token, $openid)
    {
        $url = "https://api.weixin.qq.com/sns/userinfo?access_token=".$access_token."&openid=".$openid."&lang=zh_CN";
        $timeout = 30;
        $user_agent = 'Mozilla/5.0 (iPhone; CPU iPhone OS 10_3_2 like Mac OS X) AppleWebKit/603.2.4 (KHTML, like Gecko) Mobile/14F89 MicroMessenger/6.5.9 NetType/4G Language/zh_CN';
        $data = new Curl();
        $res = $data->get_info($url,$timeout,$user_agent);
        return $res;
    }

    //获取用户基本信息
    public function get_user_info($access_token,$openid)
    {
        $url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$access_token."&openid=".$openid."&lang=zh_CN";
        $timeout = 30;
        $user_agent = 'Mozilla/5.0 (iPhone; CPU iPhone OS 10_3_2 like Mac OS X) AppleWebKit/603.2.4 (KHTML, like Gecko) Mobile/14F89 MicroMessenger/6.5.9 NetType/4G Language/zh_CN';
        $data = new Curl();
        $res = $data->get_info($url,$timeout,$user_agent);
        return $res;
    }

}