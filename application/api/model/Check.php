<?php

namespace app\api\model;

use think\Model;

class Check extends Model
{
    // 验证token参数
    public function token($get_time)
    {
        // 检查token 时间有效期
        $get_time=(int)$get_time;
        $get_time=date('Y-m', $get_time);
        $today = time();
        $today=date('Y-m', $today);
        if($get_time==$today)
        {
            // token 有效
        } else {
            $out_info=array("code"=>"0","status"=>"token is error or expired.");
            echo json_encode($out_info);
            exit;
        }
    }

   // 验证sign参数
    public function sign($post_sign,$post_mid)
    {
        // 判断 $post_mid 是否为空
        if($post_mid==''){
            $out_info=array("code"=>"0","status"=>"mid is null.");
            echo json_encode($out_info);
            exit;
        }
        // 判断 $post_mid 是否为整数
        if(!is_int($post_mid)){
            $out_info=array("code"=>"0","status"=>"mid is error.");
            echo json_encode($out_info);
            exit;
        }
        // 判断 $post_sign 是否为空
        if($post_sign==''){
            $out_info=array("code"=>"0","status"=>"sign is null.");
            echo json_encode($out_info);
            exit;
        }
        $member_info_sql['mid'] = $post_mid;
        $member_info = MemberAuthentication::where($member_info_sql) -> find();
        if(isset($member_info)){
            // mid 存在,取出sign
            $sign=$member_info->sign;
        }else{
            $out_info=array("code"=>"0","status"=>"sign is not exist.");
            echo json_encode($out_info);
            exit;
        }
        // 判断sign 是否正确
        if($sign==$post_sign)
        {
            // sign 正确
        } else {
            $out_info=array("code"=>"0","status"=>"sign is error.");
            echo json_encode($out_info);
            exit;
        }
    }

    // 验证fin_member_authentication表 mid 是否存在
    public function mid($post_mid)
    {
        // 判断 $post_mid 是否为空
        if($post_mid==''){
            $out_info=array("code"=>"0","status"=>"mid is null.");
            echo json_encode($out_info);
            exit;
        }
        // 判断 $post_mid 是否为整数
        if(!is_int($post_mid)){
            $out_info=array("code"=>"0","status"=>"mid is error.");
            echo json_encode($out_info);
            exit;
        }
        $member_info_sql['mid'] = $post_mid;
        $member_info = Member::where($member_info_sql) -> find();
        if(isset($member_info))
        {
            // mid 存在
        } else {
            // mid 不存在
            $out_info=array("code"=>"0","status"=>"mid is not exist");
            echo json_encode($out_info);
            exit;
        }
    }

    //  验证Member表 id 是否存在
    public function member_id($post_mid)
    {
        // 判断 $post_mid 是否为空
        if($post_mid==''){
            $out_info=array("code"=>"0","status"=>"mid is null.");
            return json_encode($out_info);
        }
        // 判断 $post_mid 是否为整数
        if(!is_int($post_mid)){
            $out_info=array("code"=>"0","status"=>"mid 4 is error.");
            return json_encode($out_info);
        }
        $client_info_sql['id'] = $post_mid;
        $client_info = Member::where($client_info_sql) -> find();
        if(isset($client_info)) {
            // member id 存在
        } else {
            // member id 不存在
            $out_info=array("code"=>"0","status"=>"member's id is not exist");
            return json_encode($out_info);
        }
    }

    // 验证用户名是否存在
    public function username($post_username)
    {
        // 判断 $post_username 是否为空
        if($post_username==''){
            $out_info=array("code"=>"0","status"=>"username is null.");
            echo json_encode($out_info);
            exit;
        }
        // 检查用户名是否存在数据库中
        $client_info_sql['username'] = $post_username;
        $client_info = Member::where($client_info_sql) -> find();
        if(isset($client_info))
        {
            // username 存在
        } else {
            // username 不存在
            $out_info=array("code"=>"0","status"=>"username is not exist.");
            echo json_encode($out_info);
            exit;
        }
    }

    // 验证用户名和密码是否正确
    public function password($post_username,$post_password)
    {
        // 判断 $post_username ,$post_password是否为空
        if($post_username=='' or $post_password==''){
            $out_info=array("code"=>"0","status"=>"username or password is null.");
            echo json_encode($out_info);
            exit;
        }
        // 验证用户名和密码是否和数据库中的一致
        $post_password = md5($post_password);
        $client_info_sql['username'] = $post_username;
        $client_info_sql['password'] = $post_password;
        $client_info = Member::where($client_info_sql) -> find();
        if(isset($client_info))
        {
            // password 正确
        } else {
            // password 不正确
            $out_info=array("code"=>"0","status"=>"login fail,password is error!");
            echo json_encode($out_info);
            exit;
        }
    }

}