<?php
/**
 * Created by PhpStorm.
 * User: tzx
 * Date: 2016/10/21
 * Time: 13:19
 */
namespace app\api\controller\v1;

use think\Controller;
use think\Request;
use app\api\model\Token;
use app\api\model\Check;
use app\api\model\Sign;
use app\common\model\Member as MemberModel;
use app\common\model\MemberAuthentication;
use app\api\model\Send;
use app\common\model\SiteSlide;
use app\common\model\TradeAccount;
use app\common\model\TradeAccountDemo;
use app\common\model\TradeOrder;
use app\common\model\MemberServer;


class Index extends Controller
{

    // 用户登陆 前置检查项
    public function __construct(Request $request)
    {
        parent::__construct();
        // 解密token 并检查token是否过期
        $post_token= $request->post('token');
        $object_time = new Token();
        $get_time=$object_time->decrypt($post_token);
        $object_check = new Check();
        $object_check->token($get_time);
    }

    // 用户登陆
    public function login(Request $request)
    {
        $post_token= $request->post('token');

        // 验证用户名是否正确
        $post_username = $request->post('username');
        $object_username = new Check();
        $object_username->username($post_username);

        // 验证密码是否正确
        $post_password = $request->post('password');
        $object_password = new Check();
        $object_password->password($post_username,$post_password);

        //登陆成功，返回Json数据，sign签名
        $member_info_sql['username'] = $post_username;
        $member_info = MemberModel::where($member_info_sql) -> find();
        $mid=$member_info->id;
        $username=$member_info->username;
        $sign_str=$mid.$username;
        $object_sign = new Sign();
        $get_sign=$object_sign->make_sign($sign_str);

        // 将用户成功登陆信息 写入到 fin_member_authentication表
        $authentication_info_sql['mid'] = $mid;
        $authentication_info = MemberAuthentication::where($authentication_info_sql) -> find();
        if(isset($authentication_info)){
            $authentication_info->token = $post_token;
            $authentication_info->status = 1;
            if (false !== $authentication_info->save()) {
                $out_info=array("code"=>"1","status"=>"login success","mid"=> "".$mid."", "username"=>$username, "sign"=> $get_sign);
                return json_encode($out_info);
            } else {
                return $authentication_info->getError();
            }
        } else{
            $authentication_info = new MemberAuthentication();
            $authentication_info-> token = $post_token;
            $authentication_info-> mid = $mid;
            $authentication_info-> sign = $get_sign;
            $authentication_info-> status = 1;
            $authentication_info-> save();
            $out_info=array("code"=>"1","status"=>"login success","mid"=> "".$mid."", "username"=>$username, "sign"=> $get_sign);
            return json_encode($out_info);
        }
    }

    // 用户注册
    public function register(Request $request)
    {
        $post_tel= $request->post('tel');
        // 验证手机号码是否正确
        $get_tel=preg_match('/^1[34578]\d{9}$/',$post_tel);
        if($get_tel==false){
            $json_data=array("code"=>"0","status"=>"tel num is error");
            $out_data=json_encode($json_data);
            return $out_data;
        }
        $post_password= $request->post('password');
        $post_password=md5($post_password);
        $post_sms_code= $request->post('sms_code');
        $post_nickname= $request->post('nickname');
        $ip=$request->ip();

        // 缓存验证码
        $sms_file_name='../runtime/sms_code/'.$post_tel.'.txt';

        if(file_exists($sms_file_name)){
            $sms_file = file_get_contents($sms_file_name);
            $sms_file=(int)$sms_file;
            if($post_sms_code==$sms_file){
                // 把会员注册信息写入到数据库
                $user = new MemberModel;
                $user->agent_id = 1;
                $user->username = $post_tel;
                $user->nickname = $post_nickname;
                $user->password = $post_password;
                $user->tel = $post_tel;
                $user->type = 1;
                $user->ip = $ip;
                $register_time=date('Y-m-d H:m:s',time());
                $user->expiration_time = $register_time;
                $user->status = 1;
                $user->save();
                $mid=$user->id;
                $username=$user->username;

                $sign_str=$mid.$username;
                $object_sign = new Sign();
                $get_sign=$object_sign->make_sign($sign_str);

                $member_info=array("mid"=>$mid, "username"=>$username, "sign"=>$get_sign, "ip"=>$ip, "time"=>$register_time);

                // 删除缓存文件
                unlink($sms_file_name);
                // 返回json
                $json_data=array("code"=>"1","status"=>"member register success","member_info"=>$member_info);
                $out_data=json_encode($json_data);
                return $out_data;
            }else{
                // 手机短信验证码不正确
                $json_data=array("code"=>"0","status"=>"sms code is error");
                $out_data=json_encode($json_data);
                return $out_data;
            }

        }else{
            $json_data=array("code"=>"0","status"=>"tel num is error");
            $out_data=json_encode($json_data);
            return $out_data;
        }
    }

    // 手机短信接口
    public function sms(Request $request)
    {
        $post_tel= $request->post('tel');
        $sms_file_name='../runtime/sms_code/'.$post_tel.'.txt';
        // 生成随机验证码
        $sms_code=rand(1000,9999);

        // 验证手机号码是否正确
        $get_tel=preg_match('/^1[34578]\d{9}$/',$post_tel);
        if($get_tel==false){
            $json_data=array("code"=>"0","status"=>"tel num is error");
            $out_data=json_encode($json_data);
            return $out_data;
        }

        // 判断手机号是否存在
        $member_info_sql['tel'] = $post_tel;
        $member_info= MemberModel::where($member_info_sql) -> find();
        if(isset($member_info)){
            $json_data=array("code"=>"0","status"=>"This tel num is used");
            $out_data=json_encode($json_data);
            return $out_data;
        }

        // 同一个手机号码一分钟只发送一次验证码
        if(file_exists($sms_file_name))
        {
            $sms_file_time=filemtime($sms_file_name);
            $now_time=time();
            if($now_time-$sms_file_time<60){
                $json_data=array("code"=>"0","status"=>"get sms code  too often");
                $out_data=json_encode($json_data);
                return $out_data;
            }
        }

        // 发送手机验证码
        if($post_tel!==''){
            $Send = new Send;
            $result = $Send->sms([
                'param'  => ['code'=>"$sms_code",'product'=>'[Firdc]'],
                'mobile'  => $post_tel,
                'template'  => 'SMS_9700361',
            ]);
            if($result == true){
                // 写入缓存文件
                file_put_contents($sms_file_name,$sms_code);
                // 输出 json 数据
                $json_data=array("code"=>"1","status"=>"send sms code success","sms_code"=>$sms_code);
                $out_data=json_encode($json_data);
                echo $out_data;
            }
        }

    }

    // 检测用户名是否存在
    public function check_username(Request $request)
    {
        $post_username= $request->post('username');
        // 判断 $post_mid 是否为空
        if($post_username==''){
            $out_info=array("code"=>"0","status"=>"username is null.");
            return json_encode($out_info);
        }

        // 判断用户名是否存在
        $member_info_sql['username'] = $post_username;
        $member_info= MemberModel::where($member_info_sql) -> find();
        if(isset($member_info)){
            $json_data=array("code"=>"0","status"=>"username unuseble");
            $out_data=json_encode($json_data);
            return $out_data;
        }else{
            $json_data=array("code"=>"1","status"=>"username can use");
            $out_data=json_encode($json_data);
            return $out_data;
        }

    }

    // 检测手机号是否存在
    public function check_tel(Request $request)
    {
        $post_tel= $request->post('tel');
        // 验证手机号码是否正确
        $get_tel=preg_match('/^1[34578]\d{9}$/',$post_tel);
        if($get_tel==false){
            $json_data=array("code"=>"0","status"=>"tel num is error");
            $out_data=json_encode($json_data);
            return $out_data;
        }
        // 判断手机号是否存在
        $member_info_sql['tel'] = $post_tel;
        $member_info= MemberModel::where($member_info_sql) -> find();
        if(isset($member_info)){
            $json_data=array("code"=>"0","status"=>"tel unuseble");
            $out_data=json_encode($json_data);
            return $out_data;
        }else{
            $json_data=array("code"=>"1","status"=>"tel can use");
            $out_data=json_encode($json_data);
            return $out_data;
        }
    }

    // 首页幻灯片
    public  function site_slide()
    {
        $site_slide = array();
        $site_slide_info_sql['status'] = 1;
        $site_slide_list = SiteSlide::where($site_slide_info_sql) -> select();
        foreach ($site_slide_list as $user) {
            $title = $user['title'];
            $description = $user['description'];
            $thumb = 'https://www.firdc.com'.$user['thumb'];
            $url = $user['url'];
            $site_slide[] = array("title" => $title, "description" => $description, "thumb" => $thumb, "url" => $url);
        }
        $account = array("code" => "1", "status" => "query success", "account list" => $site_slide);
        return json_encode($account);
    }

    // 首页交易帐户信息列表
    public function home()
    {
        // 读取缓存文件
        $cache_time=time();
        $cache_date=date('Y-m-d',$cache_time);
        $cache_file="../runtime/json/home/".$cache_date.".json";
        if(file_exists($cache_file)){
            $cache_file_time=filemtime($cache_file);
            $cache_file_date=date('Y-m-d',$cache_file_time);
            $cache_file_size=filesize($cache_file);
            if($cache_date==$cache_file_date and $cache_file_size>100){
                $json_data = file_get_contents($cache_file);
                return $json_data;
            }
        }

        // 演示帐户 信息列表
        $home_account_list = array();

        // 盈利计算方法
        function sum_profit($c_num,$d_profit){

            if($c_num==0){
                return $d_profit[$c_num];
            }else{
                $c_profit=$c_num-1;
                return  $d_profit[$c_num]+sum_profit($c_profit,$d_profit);
            }
        }

        $account_info_sql['status'] = 1;
        $account_info = TradeAccountDemo::where($account_info_sql)->select();
        $count_num=count($account_info);

        foreach($account_info as $account)
        {
            $aid = $account->aid;

            // 把计算出来的结果放去到数组中
            $order_data = array();
            for($i=0;$i<$count_num;$i++){

                // 入金汇总（本金汇总）
                $total_profit = array();
                $total_info_sql['order_type'] = 6;
                $total_info_sql['aid'] = $aid;
                $total_list = TradeOrder::where($total_info_sql) -> select();
                foreach($total_list  as $total)
                {
                    // 当前帐户的入金
                    $total_profit[] = $total->profit;
                }
                // 入金总和
                $total_profit=array_sum($total_profit);

                $close_time_list = array();
                $trade_lots_list = array();
                $profit_rate_list = array();

                // 本年 相关数据
                $year_profit = array();
                $year_time = time();
                $year_time=date('Y',$year_time);
                $year_info_sql['order_type'] =  ['<','3'];
                $year_info_sql['close_time'] =  ['like',$year_time.'%'];
                $year_info_sql['aid'] = $aid;
                $year_list = TradeOrder::where($year_info_sql) -> select();

                $order_count = count($year_list);

                foreach($year_list  as $year_order)
                {
                    // 当前帐户的盈利
                    $profit=$year_order->profit;
                    $year_profit[] = $profit;
                    $close_time_list[]=$year_order->close_time;
                    $trade_lots_list[]=$year_order->trade_lots;
                    $profit_rate_list[] = round(($profit/$total_profit)*100,2);

                }
                // 盈利总和
                $total_year_profit=array_sum($year_profit);

                // 本年盈利率
                $profit_rate=round(($total_year_profit/$total_profit)*100,2);
                $average_profit=round(($total_year_profit/$order_count),2);
                $average_profit_rate = array_sum($profit_rate_list);
                $average_profit_rate=round(($average_profit_rate/$order_count),2);

                // 把计算出来的结果放去到数组中
                $draw_down_list = array();
                $sum_profit_rate_list = array();
                for($i=0;$i<$order_count;$i++){
                    $draw_down_list[]=(sum_profit($i,$year_profit))+$total_profit;
                    $sum_profit_rate_list[]=(sum_profit($i,$profit_rate_list));

                }

                //  计算最大盈利和最小盈利
                $max_profit_num = array_search(max($draw_down_list), $draw_down_list);
                $min_profit_num = array_search(min($draw_down_list), $draw_down_list);
                $max_profit = $draw_down_list[$max_profit_num];
                $min_profit = $draw_down_list[$min_profit_num];

                // 本年 最大回撤率
                $draw_down=round((($max_profit-$min_profit)/$max_profit)*100,2);


                // 帐户信息
                $trade_account_info_sql['id'] = $aid;
                $trade_account_info = TradeAccount::where($trade_account_info_sql) -> find();
                $account = $trade_account_info['account'];
                $msid = $trade_account_info['msid'];

                // 指定的会员服务器使用人数
                $account_count_info_sql['msid'] = $msid;
                $account_count_info = TradeAccount::where($account_count_info_sql) -> select();
                $msid_count=count($account_count_info);

                // 会员服务器信息
                $server_info_sql['id'] = $msid;
                $member_server_info = MemberServer::where($server_info_sql) -> find();
                $name = $member_server_info['name'];

                $order_data[]=array("account"=>$account,"server_id"=>$msid, "server_name"=>$name, "use_number"=>$msid_count, "total_profit"=>$total_year_profit, "profit_rate"=>$profit_rate, "order_count"=>$order_count, "draw_down"=>$draw_down,"average_profit"=>$average_profit,"average_profit_rate"=>$average_profit_rate,"close_time_list"=>$close_time_list,"profit_rate_list"=>$sum_profit_rate_list,"trade_lots_list"=>$trade_lots_list);


            }
            $home_account_list[] = array("account_info"=>$order_data);
        }
        $json_data= array("code"=>"1", "status"=>"query success", "account_list"=>$home_account_list);

        $out_data=json_encode($json_data);
        // 缓存查询结果1天
        file_put_contents($cache_file,$out_data);
        return $out_data;

    }

}