<?php
/**
 * Created by PhpStorm.
 * User: tanzhenxing
 * Date: 2017/6/23
 * Time: 11:11
 */
namespace app\member\controller;

use think\Controller;
use think\Request;
use think\Session;
use app\member\controller\WeixinOauth;
use app\base\model\Member;
use app\base\model\Membership;
use app\base\model\MemberWeixin;
use app\base\model\WechatOfficialAccounts;
use app\base\model\Curl;

class WeixinUser extends Controller
{
    // 获取微信用户openid
    public function login($site_id,$mid)
    {
            $official_accounts_info = WechatOfficialAccounts::get(['site_id'=>$site_id]);
            $app_id = $official_accounts_info['app_id'];
            $app_secret = $official_accounts_info['app_secret'];
            $redirect_url = Request::instance()->url(true);
            $code = Request::instance()->param('code');
            $state = Request::instance()->param('state');
            $user_info = Request::instance()->param('user_info');
            $request = Request::instance();
            $script_uri = $request->server('SCRIPT_URI');
            $request_uri = $request->server('REQUEST_URI');
            $server_name = $request->server('SERVER_NAME');
            $request_url = 'http://'.$server_name.$request_uri;
            $question_mark = '?';
            $check_url = stripos($request_url,$question_mark);

            $timeout = 30;
            $user_agent = 'Mozilla/5.0 (iPhone; CPU iPhone OS 10_3_2 like Mac OS X) AppleWebKit/603.2.4 (KHTML, like Gecko) Mobile/14F89 MicroMessenger/6.5.9 NetType/4G Language/zh_CN';

            if(empty($code) and empty($state)){
                // code 为空则跳转
                if($user_info=='all'){
                    $scope = 'snsapi_userinfo';
                    $state = 'all';
                }else{
                    $scope = 'snsapi_base'; // 不出现登录授权界面
                    if($check_url){
                        $redirect_url = $redirect_url.'&user_info=base';
                    }else{
                        $redirect_url = $redirect_url.'?&user_info=base';
                    }

                }

                $code_url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$app_id."&redirect_uri=".$redirect_url."&response_type=code&scope=".$scope."&state=".$state."#wechat_redirect";
                header("Location: ".$code_url);
            }else{
                // 获取微信access_token
                $token_url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=".$app_id."&secret=".$app_secret."&code=".$code."&grant_type=authorization_code";
                $weixin_user_data = new Curl();
                $access_token_data = $weixin_user_data->get_info($token_url,$timeout,$user_agent);
                $access_token_info = json_decode($access_token_data);
                $access_token = $access_token_info->access_token;
                $expires_in = $access_token_info->expires_in;
                $refresh_token = $access_token_info->refresh_token;
                $openid = $access_token_info->openid;
                $scope = $access_token_info->scope;

                if($scope=='snsapi_base'){
                    // 判断数据库中openid是否存在，如果不存在，添加新记录
                    $member_weixin_info = MemberWeixin::get(['openid'=>$openid]);
                    if(empty($member_weixin_info)){
                        if($check_url){
                            $redirect_url = $redirect_url.'&user_info=all';
                        }else{
                            $redirect_url = $redirect_url.'?&user_info=all';
                        }
                        $redirect_url = str_replace('code=','pre_code=',$redirect_url);

                        header("Location: ".$redirect_url);
                    }else{
                        $this->assign('member_weixin',$member_weixin_info);
                        // 写入Session 数据
                        Session::set('weixin_user',$openid);
                    }

                }else{
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
                    if(!empty($openid)){
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
                    }
                    // 写入Session 数据
                    Session::set('weixin_user',$openid);

                }
                return $openid;
            }
    }

}