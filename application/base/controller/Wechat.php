<?php
/**
 * Created by PhpStorm.
 * User: tanzhenxing
 * Date: 2018/5/3
 * Time: 17:33
 */
namespace app\base\controller;

use app\common\model\MemberWeixin;
use app\common\model\Member;

class Wechat extends Base
{
    /**
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */
    public function client()
    {
        $weixin_user_info = new Weixin();
        $openid = $weixin_user_info->info($this->site_id,session('mid'));
        session('open_id',$openid);

        $this->assign('openid',$openid);
        // 获取会员信息
        $member_weixin_info = MemberWeixin::get(['openid'=>$openid]);
        $member_weixin_id = $member_weixin_info['id'];

        $member_info = Member::get(['weixin_id'=>$member_weixin_id]);
        if(!empty($member_info)){
            $member_info['name'] = $member_info['real_name'];

            $this->assign('member',$member_info);
        }else{
            $member_weixin_info['name'] = $member_weixin_info['nickname'];
            $this->assign('member',$member_weixin_info);
        }

        return $openid;
    }
}