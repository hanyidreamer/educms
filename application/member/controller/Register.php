<?php
/**
 * Created by PhpStorm.
 * User: tanzhenxing
 * Date: 2017/6/21
 * Time: 10:52
 */
namespace app\member\controller;

use think\Request;
use app\index\controller\Base;
use app\base\model\Member;
use app\base\model\Membership;
use app\base\model\MemberWeixin;
use app\base\controller\Weixin;
use app\base\controller\BrowserCheck;

class Register extends Base
{
    /**
     * @return mixed
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */
    public function index()
    {
        // 判断是否为微信浏览器
        $user_browser = new BrowserCheck();
        $user_browser_info = $user_browser->info();
        if($user_browser_info=='wechat_browser'){
            $weixin_user_info = new Weixin();
            $openid = $weixin_user_info->info($this->site_id,session('mid'));
            $this->assign('openid',$openid);
            // 获取会员信息
            $member_weixin_info = MemberWeixin::get(['openid'=>$openid]);
            $member_weixin_id = $member_weixin_info['id'];

            $member_info = Member::get(['weixin_id'=>$member_weixin_id]);
            if(!empty($member_info)){
                $member_info['name'] = $member_info['real_name'];

                $this->assign('member_data',$member_info);
            }else{
                $member_weixin_info['name'] = $member_weixin_info['nickname'];
                $this->assign('member_data',$member_weixin_info);
            }
        }

        return $this->fetch($this->template_path);
    }

    /**
     * @param Request $request
     */
    public function save(Request $request)
    {
        // 判断是否为微信浏览器
        $user_browser = new BrowserCheck();
        $user_browser_info = $user_browser->info();
        if($user_browser_info!='wechat_browser'){
            // 判断验证码是否正确
            $post_captcha = $request->post('captcha');
            if(empty($post_captcha)){
                $this->error('图形验证码不能为空');
            }
            if(!captcha_check($post_captcha)){
                //验证失败
                $this->error("图形验证码错误");
            }
        }

        // $post_open_id = $request->param('open_id');
        $post_mobile = $request->param('mobile');
        $post_password = $request->param('password');
        $post_password = md5($post_password);
        $post_sms_code = $request->param('sms_code');
        $post_mid = $request->param('mid');
        if(empty($post_mid) or !is_numeric($post_mid)){
            $post_mid = '0';
        }

        $sms_code = session('sms_code');
        if($post_sms_code != $sms_code){
            $this->error('短信验证码不正确','/member/register/index');
        }

        $ip = $request->ip();

        // 将会员资料写入到数据库中
        $member_data = new Member();
        $member_data['site_id'] = $this->site_id;
        $member_data['username'] = 'tel_'.$this->site_id . $post_mobile;
        $member_data['password'] = $post_password;
        $member_data['tel'] = $post_mobile;
        $member_data['ip'] = $ip;
        $member_data['status'] = 1;
        $member_data->save();

        // 把分销上下级关系写入到数据库中
        $membership_data = new Membership();
        $membership_data['site_id'] = $this->site_id;
        $membership_data['mid'] = session('mid');
        $membership_data['father_id'] = $post_mid;
        $membership_data['status'] = 1;

        if($user_browser_info=='wechat_browser'){
            if ($membership_data->save()) {
                $this->success('会员注册成功', '/member/pay_vip/index');
            } else {
                $this->error('会员注册失败','/member/pay_vip/index');
            }
        }
        // 不是微信浏览器的跳转方式
        if ($membership_data->save()) {
            $this->success('会员注册成功', '/member/login/index');
        } else {
            $this->error('会员注册失败','/member/register/index');
        }

    }

}