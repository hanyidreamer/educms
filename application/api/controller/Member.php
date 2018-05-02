<?php
/**
 * Created by PhpStorm.
 * User: tzx
 * Date: 2016/10/7
 * Time: 14:31
 */
namespace app\api\controller;

use think\Controller;
use think\Request;
use app\api\model\Token;
use app\api\model\Sign;
use app\api\model\Check;
use app\common\model\Member as MemberModel;
use app\common\model\MemberAuthentication;
use app\common\model\MemberServer;
use app\common\model\MemberServicePackage;
use app\common\model\MemberService;
use app\common\model\TradeAccount;
use app\common\model\TradeServer;
use app\common\model\TradeOrder;
use app\api\model\MemberProfitRate;
use app\api\model\PlatformProfit;
use app\common\model\TradeAccountDemo;
use app\api\model\AccountProfitRate;
use app\api\model\PlatformAccountProfit;
use app\mobile\model\Send;


class Member extends Controller
{

    public function __construct(Request $request)
    {
        parent::__construct();
        $post_token= $request->post('token');
        $object_time = new Token();
        $get_time=$object_time->decrypt($post_token);
        $object_check = new Check();
        $object_check->token($get_time);

    }
    // 解密token 并检查token是否过期

    // 会员登陆验证
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

    // 会员注销验证
    public function logout(Request $request)
    {

        // 检查mid是否存在
        $post_mid= $request->post('mid');
        $post_mid=(int)$post_mid;
        $object_mid = new Check();
        $object_mid->member_id($post_mid);

        // 检查sign是否正确
        $post_sign= $request->post('sign');
        $object_sign = new Check();
        $object_sign->sign($post_sign,$post_mid);

        // 将用户注销信息 写入到 fin_member_authentication表
        $authentication_info_sql['mid'] = $post_mid;
        $authentication_info = MemberAuthentication::where($authentication_info_sql) -> find();
        if(!isset($authentication_info))
        {
            $out_info=array("code"=>"0","status"=>"logout fail,non-existent this client!");
            return json_encode($out_info);
        }
        // 过滤重复退出
        $status=$authentication_info->status;
        $token=$authentication_info->token;
        if($status=='2' and $token=='')
        {
            $out_info=array("code"=>"2","status"=>"Member already exit!");
            return json_encode($out_info);
        }
        // 将注销信息保存到数据库
        $authentication_info->token = '';
        $authentication_info->status = 2;
        if (false !== $authentication_info->save()) {
            $out_info=array("code"=>"2","status"=>"logout success");
            return json_encode($out_info);
        }
        else {
            return $authentication_info->getError();
        }
    }

    // 会员注册
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

    // 会员修改密码
    public function change_password(Request $request)
    {
        // 检查mid是否存在
        $post_mid= $request->post('mid');
        $post_mid=(int)$post_mid;
        $object_mid = new Check();
        $object_mid->member_id($post_mid);

        // 检查sign是否正确
        $post_sign= $request->post('sign');
        $object_sign = new Check();
        $object_sign->sign($post_sign,$post_mid);

        $post_password= $request->post('password');

        if(isset($post_password)){
            $object_check = new Sign();
            $password=$object_check->make_sign($post_password);

            $user = new MemberModel;
            $user->save([
                'password'  => $password,
            ],['id' => $post_mid]);

            $out_info=array("code"=>"1","status"=>"change password success","mid"=> "".$post_mid."", "password"=>$password);
            return json_encode($out_info);

        }
    }

    // 会员交易服务器信息
    public function server(Request $request)
    {

        // 检查mid是否存在
        $post_mid= $request->post('mid');
        $post_mid=(int)$post_mid;
        $object_mid = new Check();
        $object_mid->member_id($post_mid);

        // 检查sign是否正确
        $post_sign= $request->post('sign');
        $object_sign = new Check();
        $object_sign->sign($post_sign,$post_mid);

        $server_info_sql['type'] = 1;
        $server_info = MemberServer::where($server_info_sql) -> select();
        $server=array("code"=>"1","status"=>"query success","server info"=>$server_info);
        return json_encode($server);
    }

    // 会员的交易服务
    public function service(Request $request)
    {

        // 检查mid是否存在
        $post_mid= $request->post('mid');
        $post_mid=(int)$post_mid;
        $object_mid = new Check();
        $object_mid->member_id($post_mid);

        // 检查sign是否正确
        $post_sign= $request->post('sign');
        $object_sign = new Check();
        $object_sign->sign($post_sign,$post_mid);

        $post_mid = $request->post('mid');
        $post_account=$request->post('account');
        $post_password=$request->post('password');
        $post_trade_server=$request->post('trade_server');
        $post_messages=$request->post('messages');
        $post_member_server=$request->post('member_server');
        $post_lots=$request->post('lots');
        $post_work=$request->post('work');
        $post_work_status=$request->post('work_status');

        // 检验帐户aid是否存在
        if(isset($post_mid,$post_account,$post_password,$post_trade_server,$post_member_server)){
            $account_info_sql['account'] = $post_account;
            $account_info= TradeAccount::where($account_info_sql) -> find();

            if(isset($account_info)){
                $aid=$account_info->id;
                $msid=$account_info->msid;

            }else{

                // 获取tsid
                $trade_server_info_sql['server_name'] = $post_trade_server;
                $trade_server_info = TradeServer::where($trade_server_info_sql) -> find();
                if(isset($trade_server_info)){
                    $tsid=$trade_server_info->id;
                }else{
                    return 'post_server is error!';
                }

                // 获取msid
                $member_server_info_sql['en_name'] = $post_member_server;
                $member_server_info = MemberServer::where($member_server_info_sql) -> find();
                if(isset($member_server_info)){
                    $msid=$member_server_info->id;
                }else{
                    return 'post_expert is error!';
                }

                $user = new TradeAccount;

                $user->data([
                    'mid'  =>  $post_mid,
                    'tsid' =>  $tsid,
                    'msid'  =>  $msid,
                    'account' =>  $post_account,
                    'password'  =>  $post_password,
                    'account_type' =>  1,
                    'status'  =>  2,
                ]);
                $user->save();

                $aid=$user->id;
            }

            // 检验 fin_member_service是否为空
            $member_service_info_sql['aid'] = $aid;
            $member_service_info= MemberService::where($member_service_info_sql) -> find();
            if(isset($member_service_info)){
                $user = new MemberService;
                $user->save([
                    'work'  => $post_work,
                    'work_status' => $post_work_status,
                    'messages' => $post_messages,
                    'lots' => $post_lots,
                ],['aid' => $aid]);
                $out_info=array("status"=>"1","update"=>"success", "content"=>"keep online update!");
                return json_encode($out_info);

            }else{
                $member_service_info = new MemberService();
                $member_service_info->mid = $post_mid;
                $member_service_info->aid = $aid;
                $member_service_info->msid = $msid;
                $member_service_info->spid = 1;
                $member_service_info->work = $post_work;
                $member_service_info->work_status = $post_work_status;
                $member_service_info->messages = $post_messages;
                $member_service_info->lots = $post_lots;
               // $member_service_info->symbol = $post_symbol;
                $member_service_info->save();
                $out_info=array("status"=>"1","update"=>"success", "content"=>"keep online update!");
                return json_encode($out_info);
            }

        }

    }

    // 指定帐户的的交易服务状态
    public function service_status(Request $request)
    {

        // 检查mid是否存在
        $post_mid= $request->post('mid');
        $post_mid=(int)$post_mid;
        $object_mid = new Check();
        $object_mid->member_id($post_mid);

        // 检查sign是否正确
        $post_sign= $request->post('sign');
        $object_sign = new Check();
        $object_sign->sign($post_sign,$post_mid);

        $post_account= $request->post('account');
        $account_info_sql['account'] = $post_account;
        $account_info= TradeAccount::where($account_info_sql) -> find();
        $aid=$account_info->id;

        $member_info_sql['aid'] = $aid;
        $member_info= MemberService::where($member_info_sql) -> find();
        $work_status=$member_info->work_status;
        $lots=$member_info->lots;

        $json_data=array("code"=>"1","status"=>"query success","work status"=>$work_status,"lots"=>$lots);
        return json_encode($json_data);

    }

    // 会员服务套餐包
    public function service_package(Request $request)
    {

        // 检查mid是否存在
        $post_mid= $request->post('mid');
        $post_mid=(int)$post_mid;
        $object_mid = new Check();
        $object_mid->member_id($post_mid);

        // 检查sign是否正确
        $post_sign= $request->post('sign');
        $object_sign = new Check();
        $object_sign->sign($post_sign,$post_mid);

        $server_info = MemberServicePackage::all();
        $server=array("code"=>"1","status"=>"query success","service_package"=>$server_info);
        return json_encode($server);
    }

    // 会员中心信息列表
    public function center(Request $request)
    {

        // 检查mid是否存在
        $post_mid= $request->post('mid');
        $post_mid=(int)$post_mid;
        $object_mid = new Check();
        $object_mid->member_id($post_mid);

        // 检查sign是否正确
        $post_sign= $request->post('sign');
        $object_sign = new Check();
        $object_sign->sign($post_sign,$post_mid);

        // 读取缓存文件
        $cache_time=time();
        $cache_date=date('Y-m-d-H',$cache_time);
        $cache_file="../runtime/json/center/".$post_mid."_".$cache_date.".json";
        if(file_exists($cache_file)){
            $cache_file_time=filemtime($cache_file);
            $cache_file_date=date('Y-m-d-H',$cache_file_time);
            $cache_file_size=filesize($cache_file);
            if($cache_date==$cache_file_date and $cache_file_size>10){
                $json_data = file_get_contents($cache_file);
                echo $json_data;
                exit;
            }
        }

        // 获取盈利
        $object_member = new MemberProfitRate();
        $member_profit_rate=$object_member->member_profit_list($post_mid);

        $account_data_info = array();

        $account_info_sql['mid'] = $post_mid;
        $account_info= TradeAccount::where($account_info_sql) -> select();

        foreach ($account_info as $user) {
            $aid=$user->id;
            $account = $user->account;

            // 入金汇总（本金汇总）
            $total_profit = array();
            $total_info_sql['order_type'] = 6;
            $total_info_sql['aid'] = $aid;
            $total_list = TradeOrder::where($total_info_sql) -> select();
            $count_total_list=count($total_list);
            if($count_total_list=='0'){
                continue;
            }

            foreach($total_list  as $total)
            {
                // 当前帐户的入金 profit
                $total_profit[] = $total->profit;
            }
           $total_profit=array_sum($total_profit);

            // 当前帐户的盈利汇总
            $total_order_profit = array();
            $total_order_info_sql['order_type'] =  ['<>','6'];
            $total_info_sql['aid'] = $aid;
            $total_order_list = TradeOrder::where($total_order_info_sql) -> select();
            foreach($total_order_list  as $total_order)
            {
                // 当前帐户的入金 profit
                $total_order_profit[] = $total_order->profit;
            }

            $total_account_profit=array_sum($total_order_profit);
            // 当前帐户的总盈利率
            $total_all=round(($total_account_profit/$total_profit)*100,2);

            // 交易服务信息
            $member_service = MemberService::get(['aid' => $aid]);
            $work_status=$member_service['work_status'];
            if($work_status==''){
                $work_status='0';
            }
            $lots=$member_service['lots'];
            if($lots==''){
                $lots='0';
            }

            $account_data_info[]=array("account"=>$account,"profit"=>$total_account_profit,"rate"=>$total_all,"work_status"=>$work_status,"lots"=>$lots);

        }

        $json_data=array("code"=>"1","status"=>"query success","member_profit_rate"=>$member_profit_rate,"account_info"=>$account_data_info);
        $out_data=json_encode($json_data);
        // 缓存查询结果1天
        file_put_contents($cache_file,$out_data);
        echo $out_data;


    }

    // 会员首页
    public function home(Request $request)
    {

        // 检查mid是否存在
        $post_mid= $request->post('mid');
        $post_mid=(int)$post_mid;
        $object_mid = new Check();
        $object_mid->member_id($post_mid);

        // 检查sign是否正确
        $post_sign= $request->post('sign');
        $object_sign = new Check();
        $object_sign->sign($post_sign,$post_mid);

        // 读取缓存文件
        $cache_time=time();
        $cache_date=date('Y-m-d',$cache_time);
        $cache_file="./static/json/home/".$cache_date.".json";
        if(file_exists($cache_file)){
            $cache_file_time=filemtime($cache_file);
            $cache_file_date=date('Y-m-d',$cache_file_time);
            $cache_file_size=filesize($cache_file);
            if($cache_date==$cache_file_date and $cache_file_size>1024){
                $json_data = file_get_contents($cache_file);
                echo $json_data;
                exit;
            }
        }

        // 平台盈利百分比
        $object_account = new PlatformProfit();
        $platform_profit=$object_account->profit_list();

        // 盈利计算方法
        function sum_profit($c_num,$d_profit){

            if($c_num==0){
                return $d_profit[$c_num];
            }else{
                $c_profit=$c_num-1;
                return  $d_profit[$c_num]+sum_profit($c_profit,$d_profit);
            }
        }

        // 演示帐户 信息列表
        $home_account_list = array();

        $demo_list = TradeAccountDemo::all();
        foreach ($demo_list as $demo_user) {
            $aid=$demo_user['aid'];
            $account_info_sql['id'] = $aid;
            $account_info= TradeAccount::where($account_info_sql) -> find();
            $account_list=$account_info['account'];

            // 当前帐户的入金总额
            $total_profit = array();
            $total_info_sql['order_type'] = 6;
            $total_info_sql['aid'] = $aid;
            $total_list = TradeOrder::where($total_info_sql) -> select();
            foreach($total_list  as $total)
            {
                // 当前帐户的入金 profit
                $total_profit[] = $total->profit;
            }
            $total_profit=array_sum($total_profit);

            $close_time_list = array();
            $trade_lots_list = array();
            $profit_rate_list = array();
            $total_account_profit = array();

            $info_sql['aid'] = $aid;
            $info_sql['order_type'] =  ['<','3'];
            $info_list= TradeOrder::where($info_sql) -> select();
           $account_count=count($info_list);

            foreach ($info_list as $user) {
                $close_time_list[]=$close_time=$user->close_time;
                $profit=$user->profit;
                $trade_lots_list[]=$user->trade_lots;
                $profit_rate_list[] = round(($profit/$total_profit)*100,2);
                $total_account_profit[] =$user->profit;
            }

            // 计算盈利增长率
            $count_num=count($profit_rate_list);

            $sum_profit_rate_list = array();
            for($i=0;$i<$count_num;$i++){
                $sum_profit_rate_list[]=sum_profit($i,$profit_rate_list);
            }


            $average_profit_rate = array_sum($profit_rate_list);
            $average_profit_rate=round(($average_profit_rate/$account_count),2);

            $account_profit=array_sum($total_account_profit);
            $account_profit_rate=round(($account_profit/$total_profit)*100,2);
            $average_profit=round(($account_profit/$account_count),2);

            // 帐户盈利率
            $demo_account = new PlatformAccountProfit();
            $demo_account_profit=$demo_account->account_profit_list($aid);

            $home_account_list[] = array("account"=>$account_list,"profit_rate"=>$demo_account_profit,"total_profit"=>$account_profit,"average_profit"=>$average_profit,"average_profit_rate"=>$average_profit_rate,"total_rate"=>$account_profit_rate,"close_time_list"=>$close_time_list,"profit_rate_list"=>$sum_profit_rate_list,"trade_lots_list"=>$trade_lots_list);

        }

        $json_data=array("code"=>"1","status"=>"query success","platform_profit"=>$platform_profit,"account_info"=>$home_account_list);
        $out_data=json_encode($json_data);
        // 缓存查询结果1天
        file_put_contents($cache_file,$out_data);
        echo $out_data;
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
                $json_data=array("code"=>"1","status"=>"send sms success","sms_code"=>$sms_code);
                $out_data=json_encode($json_data);
                echo $out_data;
            }
        }

    }

    // check_username
    public function check_username(Request $request)
    {
        $post_username= $request->post('username');
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

    // check_tel
    public function check_tel(Request $request)
    {
        $post_username= $request->post('tel');
        // 判断手机号是否存在
        $member_info_sql['tel'] = $post_username;
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

}