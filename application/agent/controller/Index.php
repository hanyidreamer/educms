<?php
/**
 * Created by PhpStorm.
 * User: tzx
 * Date: 2016/10/14
 * Time: 17:07
 */
namespace app\agent\controller;

use think\Controller;
use think\Request;
use think\Session;
use think\Cookie;
use app\common\model\Agent;
use app\common\model\Member;
use app\common\model\TradeAccount;
use app\common\model\MemberService;
use app\common\model\TradeOrder;


class Index extends Controller
{
    // 验证域名是否合法
    public function _initialize() {
        $get_domain=$_SERVER['HTTP_HOST'];
        if($get_domain=='firdc.com' or $get_domain=='www.firdc.com'){
            header("HTTP/1.1 301 Moved Permanently");
            header("Location: https://www.firdc.com/mobile/member/index.html");
        }
        // $get_domain = preg_replace('/http:\/\//','',$get_domain);
        // $get_domain = preg_replace('/https:\/\//','',$get_domain);
        $agent_info_sql['domain'] = $get_domain;
        $agent_info = Agent::where($agent_info_sql) -> find();
        if(isset($agent_info)){
            $domain=$agent_info->domain;
            if($get_domain==$domain){
                $agent_status=$agent_info->status;
                if($agent_status==0){
                    echo '服务已经到期，请联系管理员';
                    exit;
                }
            }else{
                header("HTTP/1.1 301 Moved Permanently");
                header("Location: https://www.firdc.com/mobile/member/index.html");
            }
        }else{
            header("HTTP/1.1 301 Moved Permanently");
            header("Location: https://www.firdc.com/mobile/member/index.html");
        }
    }

    public function index()
    {
        $get_domain=$_SERVER['HTTP_HOST'];
        header("HTTP/1.1 301 Moved Permanently");
        header("Location: http://".$get_domain."/agent/index/login");
    }

    // 注册模块
    public function register(Request $request)
    {
        $request->post('username');
        return 'register';

    }

    // 登陆模块
    public function login(Request $request)
    {
        $post_username = $request->post('username');
        $post_password = $request->post('password');
        $post_password = md5($post_password);
        if($post_username==''){
            return $this->fetch();
        }
        // 查询代理会员的agent_id
        $get_domain=$_SERVER['HTTP_HOST'];
        $agent_info_sql['domain'] = $get_domain;
        $agent_info = Agent::where($agent_info_sql) -> find();
        $agent_id=$agent_info->id;

        // 查询用户信息
        if($post_username!=='' and $post_password!==''){
            $agent_member_info_sql['username'] = $post_username;
            $agent_member_info_sql['agent_id'] = $agent_id;
            $agent_member_info = Member::where($agent_member_info_sql) -> find();
            if(isset($agent_member_info)){
                $mid=$agent_member_info->id;
                $username=$agent_member_info->username;
                $password=$agent_member_info->password;
                if($post_username==$username and $password==$password){


                    // 设置session值
                    Session::set('mid',$mid);
                    Session::set('username',$username);
                    Session::set('password',$password);

                    $this->success('登陆成功', 'index/member');
                }else{
                    return '密码错误';
                }

            }else{
                return '用户名错误';
            }

        }



        return $this->fetch();
    }

    // 注销模块
    public function logout(Request $request)
    {
        return 'logout';
    }
    // 找回密码
    public function forget_password(Request $request)
    {
        return 'forget_password';
    }
    // 会员中心模块
    public function member(Request $request)
    {
        $mid=Session::get('mid');
        $trade_account_info_sql['mid'] = $mid;
        $trade_account_info = TradeAccount::where($trade_account_info_sql) -> select();
        foreach($trade_account_info  as $account_info)
        {
            $account_info->account;
            $account_info->password;
            $account_time=date('Y-m-d',$account_info->update_time);
            $account_info->account_time=$account_time;
            $aid=$account_info->id;

            $member_service_info_sql['aid'] = $aid;
            $member_service_info = MemberService::where($member_service_info_sql) -> find();
            $work_status=$member_service_info->work_status;
            $account_info->work_status=$work_status;

            // 盈利汇总
            $total_order_profit = array();
            $total_order_info_sql['order_type'] =  ['<','3'];
            $total_order_info_sql['aid'] = $aid;
            $total_order_list = TradeOrder::where($total_order_info_sql) -> select();
            foreach($total_order_list  as $total_order)
            {
                // 当前帐户的入金 profit
                $total_order_profit[] = $total_order->profit;
            }
            $total_order_profit=array_sum($total_order_profit);
            $account_info->profit=$total_order_profit;

        }

        $this->assign('trade_account_info', $trade_account_info);
        return $this->fetch();
    }

}