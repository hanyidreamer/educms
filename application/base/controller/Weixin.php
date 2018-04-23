<?php
/**
 * Created by PhpStorm.
 * User: tanzhenxing
 * Date: 2017/9/11
 * Time: 14:13
 */
namespace app\base\controller;

use think\Controller;
use app\common\model\MemberWeixin;
use app\common\model\WechatOfficialAccounts;
use app\base\model\Curl;

class Weixin extends Controller
{
    /**
     * 微信授权登录
     * @param $site_id
     * @param $mid
     * @return mixed
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */
    public function info($site_id,$mid)
    {
        // 微信公众号配置信息
        $official_accounts_info = WechatOfficialAccounts::get(['site_id'=>$site_id]);
        $app_id = $official_accounts_info['app_id'];
        $app_secret = $official_accounts_info['app_secret'];
        $get_token = $this->request->param('token');
        $code = $this->request->param('code');
        $state = $this->request->param('state');
        $timeout = 30;
        $user_agent = 'Mozilla/5.0 (iPhone; CPU iPhone OS 10_3_2 like Mac OS X) AppleWebKit/603.2.4 (KHTML, like Gecko) Mobile/14F89 MicroMessenger/6.5.9 NetType/4G Language/zh_CN';

        $redirect_url = $this->request->url(true);

        if(empty($code) and empty($state)) {
            $question_mark = '?';
            $check_url = stripos($redirect_url,$question_mark);
            $token = time();
            if($check_url){
                $redirect_url = $redirect_url.'&token='.$token;
            }else{
                $redirect_url = $redirect_url.'?token='.$token;
            }
            $scope = 'snsapi_base';
            $code_url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$app_id."&redirect_uri=".$redirect_url."&response_type=code&scope=".$scope."&state=".$scope."#wechat_redirect";
            header("Location: ".$code_url);
            die;
        }

        // 判断token 是否过期
        $token_time = time() - $get_token;
        if(!empty($code) and !empty($state) and !empty($get_token) and $token_time>3){
            // 清除url无用参数
            $redirect_url = preg_replace('/\?token=(.*)/','',$redirect_url);
            // 重新生成token
            $token = time();
            $redirect_url = $redirect_url.'?token='.$token;
            $scope = 'snsapi_base';
            $code_url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$app_id."&redirect_uri=".$redirect_url."&response_type=code&scope=".$scope."&state=".$scope."#wechat_redirect";
            header("Location: ".$code_url);
            die;
        }

        // 获取微信access_token
        $token_url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=".$app_id."&secret=".$app_secret."&code=".$code."&grant_type=authorization_code";
        $weixin_user_data = new Curl();
        $access_token_data = $weixin_user_data->get_info($token_url,$timeout,$user_agent);
        $access_token_info = json_decode($access_token_data);
        $access_token = $access_token_info->access_token;
        // $expires_in = $access_token_info->expires_in;
        // $refresh_token = $access_token_info->refresh_token;
        $openid = $access_token_info->openid;
        // $scope = $access_token_info->scope;

        // 判断数据库中openid是否存在，如果不存在，跳转到微信登录授权url
        if($state=='snsapi_base'){
            $member_weixin_info = MemberWeixin::get(['openid'=>$openid]);
            if(empty($member_weixin_info)){
                $scope = 'snsapi_userinfo';
                $redirect_url = preg_replace('/\?token=(.*)/','',$redirect_url);
                // 重新生成token
                $token = time();
                $redirect_url = $redirect_url.'?token='.$token;
                $code_url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$app_id."&redirect_uri=".$redirect_url."&response_type=code&scope=".$scope."&state=".$scope."#wechat_redirect";
                header("Location: ".$code_url);
                die;
            }else{
                return $openid;
            }
        }

        $user_info_url = "https://api.weixin.qq.com/sns/userinfo?access_token=".$access_token."&openid=".$openid."&lang=zh_CN";
        $user_data = $weixin_user_data->get_info($user_info_url,$timeout,$user_agent);
        $user_info = json_decode($user_data);

        $openid = $user_info->openid;
        $nickname = $user_info->nickname;
        $sex = $user_info->sex;
        $city = $user_info->city;
        $province = $user_info->province;
        $country = $user_info->country;
        $headimgurl = $user_info->headimgurl;
        // $privilege = $user_info->privilege;
        // $unionid = $user_info->unionid;
        // 把微信用户信息保存到数据库中

        $member_weixin_data = new MemberWeixin();
        $member_weixin_data['site_id'] = $site_id;
        if(empty($mid)){
            $mid = 0;
        }
        $member_weixin_data['mid'] = $mid;
        $member_weixin_data['openid'] = $openid;
        $member_weixin_data['nickname'] = $nickname;
        $member_weixin_data['sex'] = $sex;
        $member_weixin_data['city'] = $city;
        $member_weixin_data['province'] = $province;
        $member_weixin_data['country'] = $country;
        $member_weixin_data['headimgurl'] = $headimgurl;
        $member_weixin_data['status'] = 1;
        $member_weixin_data->save();
        return $openid;
    }
}