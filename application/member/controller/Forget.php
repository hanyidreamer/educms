<?php
/**
 * Created by PhpStorm.
 * User: tanzhenxing
 * Date: 2017/6/22
 * Time: 17:08
 */
namespace app\member\controller;

use app\base\controller\Base;
use app\common\model\Member;

class Forget extends Base
{
    /**
     * @return mixed
     */
    public function index()
    {
        return $this->fetch($this->template_path);
    }

    /**
     *
     */
    public function password()
    {
        $post_mobile = $this->request->param('mobile');
        $post_password = $this->request->param('password');
        $post_password = md5($post_password);
        $post_password2 = $this->request->param('password2');
        $post_password2 = md5($post_password2);
        if($post_password != $post_password2){
            $this->error('两次输入的密码不一致！');
        }
        $post_sms_code = $this->request->param('sms_code');

        $sms_code = session('sms_code');
        if($post_sms_code != $sms_code){
            $this->error('短信验证码不正确');
        }

        $member_data = new Member;
        $member_data->where(['site_id'=>$this->site_id,'tel'=>$post_mobile])->update(['password' => $post_password]);

        $this->success('密码修改成功','/member/login/index');
    }
}