<?php
/**
 * Created by PhpStorm.
 * User: tzx
 * Date: 2016/10/21
 * Time: 13:03
 */
namespace app\api\controller\v1;

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
use app\api\model\PlatformAccountProfit;


class Member extends Controller
{

    public function __construct(Request $request)
    {
        parent::__construct();
        // 解密token 并检查token是否过期
        $post_token= $request->post('token');
        $object_time = new Token();
        $get_time=$object_time->decrypt($post_token);
        $object_check = new Check();
        $object_check->token($get_time);

        // 检查mid是否存在
        $post_mid= $request->post('mid');
        $post_mid=(int)$post_mid;

        $object_mid = new Check();
        $object_mid->member_id($post_mid);

        // 检查sign是否正确
        $post_sign= $request->post('sign');
        $object_sign = new Check();
        $object_sign->sign($post_sign,$post_mid);

    }

    // 会员退出
    public function logout(Request $request)
    {
        $post_mid= $request->post('mid');

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

    // 会员修改密码
    public function change_password(Request $request)
    {
        $post_mid= $request->post('mid');

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
    public function server()
    {
        $server_data=array();
        $server_info_sql['type'] = 1;
        $server_info = MemberServer::where($server_info_sql) -> select();
        foreach ($server_info as $server_list) {
            $server_data[]=array("name"=>$server_list->name,"en_name"=>$server_list->en_name);
        }

        $server=array("code"=>"1","status"=>"query success","server info"=>$server_data);
        return json_encode($server);
    }

    // 会员的交易服务
    public function service(Request $request)
    {
        $post_mid= $request->post('mid');
        $post_account=$request->post('account');
        $post_password=$request->post('password');
        $post_platform=$request->post('platform');
        $post_trade_server=$request->post('trade_server');
        $post_msid=$request->post('msid');

        // 检验帐户aid是否存在
        if(isset($post_mid,$post_account,$post_password,$post_platform,$post_trade_server,$post_msid)){
            $account_info_sql['account'] = $post_account;
            $account_info= TradeAccount::where($account_info_sql) -> find();
            $aid=$account_info['id'];

            // 检验 fin_member_service是否为空
            $member_service_info_sql['aid'] = $aid;
            $member_service_info= MemberService::where($member_service_info_sql) -> find();
            if(isset($member_service_info)){
                $user = new MemberService;
                $user->save([
                    'work_status' => 5,
                ],['aid' => $aid]);
                $out_info=array("status"=>"1","update"=>"success", "content"=>"keep online update!");
                return json_encode($out_info);
            }else{
                $member_service_info = new MemberService();
                $member_service_info->mid = $post_mid;
                $member_service_info->aid = $aid;
                $member_service_info->msid = $post_msid;
                $member_service_info->spid = 1;
                $member_service_info->work_status = 5;
                $member_service_info->save();
                $out_info=array("status"=>"1","update"=>"success", "content"=>"new account keep online update!");
                return json_encode($out_info);
            }

        }

    }

    // 启动/停止会员交易服务
    public function service_status(Request $request)
    {
        $post_work_status= $request->post('work_status');
        $post_work_status=(int)$post_work_status;
        $post_account= $request->post('account');
        $account_info_sql['account'] = $post_account;
        $account_info= TradeAccount::where($account_info_sql) -> find();
        if(!isset($account_info)){
            $json_data=array("code"=>"0","status"=>"query fail","work_status"=>"This account not find");
            return json_encode($json_data);
        }

        $aid=$account_info->id;

        $member_info_sql['aid'] = $aid;
        $member_info= MemberService::where($member_info_sql) -> find();
        $work_status=$member_info['work_status'];
        if($work_status==5){
            $json_data=array("code"=>"0","status"=>"query success","work_status"=>'Service is enabled');
            return json_encode($json_data);
        }
        $member_info->work_status = $post_work_status;
        $member_info->save();

        $json_data=array("code"=>"1","status"=>"query success","work_status"=>$post_work_status);
        return json_encode($json_data);

    }

    // 查询指定帐户的的交易服务状态
    public function service_check(Request $request)
    {
        $post_account= $request->post('account');
        $account_info_sql['account'] = $post_account;
        $account_info= TradeAccount::where($account_info_sql) -> find();
        if(!isset($account_info)){
            $json_data=array("code"=>"0","status"=>"query fail","work_status"=>"This account not find");
            return json_encode($json_data);
        }
        $aid=$account_info->id;

        $member_info_sql['aid'] = $aid;
        $member_info= MemberService::where($member_info_sql) -> find();
        $work_status=$member_info['work_status'];

        $json_data=array("code"=>"1","status"=>"query success","account"=>$post_account,"work_status"=>$work_status);
        return json_encode($json_data);
    }

    // 会员服务套餐包
    public function service_package()
    {
        $service_data=array();
        $service_info_sql['status'] = 1;
        $service_info = MemberServicePackage::where($service_info_sql) -> select();

        foreach ($service_info as $service_list) {
            $service_data[]=array("name"=>$service_list->name,"en_name"=>$service_list->en_name);
        }

        $server=array("code"=>"1","status"=>"query success","service_package"=>$service_data);
        return json_encode($server);
    }

    // 会员中心信息列表
    public function center(Request $request)
    {
        $post_mid= $request->post('mid');

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

            $take_profit = array();

            foreach($total_list  as $total)
            {
                // 当前帐户的入金 profit
               $total_profit[] = $total->profit;
                $the_profit= $total->profit;
                if($the_profit<0){
                    $take_profit[]=$the_profit;
                }
            }
            $total_profit=array_sum($total_profit);
            $take_profit=array_sum($take_profit);

            // 当前帐户的盈利汇总
            $total_order_profit = array();
            $total_order_info_sql['order_type'] =  ['<>','6'];
            $total_order_info_sql['aid'] = $aid;
            $total_order_list = TradeOrder::where($total_order_info_sql) -> select();
            foreach($total_order_list  as $total_order)
            {
                // 当前帐户的盈利 profit
                $total_order_profit[] = $total_order->profit;
            }
            // 盈利总金额
           $total_account_profit=array_sum($total_order_profit);
            // 当前帐户的总盈利率
            $total_all=round(($total_account_profit/$total_profit)*100,2);
            // 帐户总金额
            $total_amount=$total_account_profit+$total_profit;

            // 会员交易服务信息
            $member_service = MemberService::get(['aid' => $aid]);
            if(isset($member_service)){
                $work_status=$member_service['work_status'];
                $msid=$member_service['msid'];
            }else{
                $work_status='0';
                $msid=1;
            }
            // 会员交易服务器信息
            $member_server = MemberServer::get(['id' => $msid]);
            if(isset($member_server)){
                $server_name=$member_server['name'];
            }else{
                $server_name='no server';
            }

            $account_data_info[]=array("account"=>$account,"deposit"=>$total_profit,"take_profit"=>$take_profit,"profit_amount"=>$total_account_profit,"profit_amount_rate"=>$total_all,"total_amount"=>$total_amount,"server_name"=>$server_name,"work_status"=>$work_status);

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



}