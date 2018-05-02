<?php
/**
 * Created by PhpStorm.
 * User: tzx
 * Date: 2016/10/7
 * Time: 22:06
 */
namespace app\api\controller;

use think\Controller;
use think\Request;
use app\api\model\Token;
use app\api\model\Check;
use app\common\model\TradeAccount;
use app\common\model\TradeAccountDemo;
use app\common\model\TradeServer;
use app\api\model\Profit;
use app\common\model\TradeOrder;
use app\api\model\AccountProfitRate;
use app\common\model\Member;

class Trade extends Controller
{
    // 演示帐户列表
    public function account_demo(Request $request)
    {
        // 解密token 并检查token是否过期
        $post_token = $request->post('token');
        $object_time = new Token();
        $get_time = $object_time->decrypt($post_token);
        $object_check = new Check();
        $object_check->token($get_time);

        // 检查mid是否存在
        $post_mid = $request->post('mid');
        $post_mid=(int)$post_mid;
        $object_mid = new Check();
        $object_mid->member_id($post_mid);

        // 检查sign是否正确
        $post_sign = $request->post('sign');
        $object_sign = new Check();
        $object_sign->sign($post_sign, $post_mid);

        $account_list = array();
        $demo_list = TradeAccountDemo::all();
        foreach ($demo_list as $user) {
            $aid = $user['aid'];
            $account_info_sql['id'] = $aid;
            $account_info = TradeAccount::where($account_info_sql)->find();
            $account_data = $account_info['account'];
            $account_list[] = $account_data;
        }
        $account = array("code" => "1", "status" => "query success", "account_list" => $account_list);
        return json_encode($account);
    }

    // 交易平台服务器列表
    public function server(Request $request)
    {
        // 解密token 并检查token是否过期
        $post_token = $request->post('token');
        $object_time = new Token();
        $get_time = $object_time->decrypt($post_token);
        $object_check = new Check();
        $object_check->token($get_time);

        // 检查mid是否存在
        $post_mid = $request->post('mid');
        $post_mid=(int)$post_mid;
        $object_mid = new Check();
        $object_mid->member_id($post_mid);

        // 检查sign是否正确
        $post_sign = $request->post('sign');
        $object_sign = new Check();
        $object_sign->sign($post_sign, $post_mid);

        $trade_server_info = TradeServer::all();
        $json_info = array("code" => "1", "status" => "query success", "server_list" => $trade_server_info);
        return json_encode($json_info);

    }

    // 平台盈利 百分比
    public function platform_profit(Request $request)
    {
        // 解密token 并检查token是否过期
        $post_token = $request->post('token');
        $object_time = new Token();
        $get_time = $object_time->decrypt($post_token);
        $object_check = new Check();
        $object_check->token($get_time);

        // 检查mid是否存在
        $post_mid = $request->post('mid');
        $post_mid=(int)$post_mid;
        $object_mid = new Check();
        $object_mid->member_id($post_mid);

        // 检查sign是否正确
        $post_sign = $request->post('sign');
        $object_sign = new Check();
        $object_sign->sign($post_sign, $post_mid);

        $object_account = new Profit();
        $object_account->profit_list();
    }

    // 指定帐户的交易记录
    public function account_profit(Request $request)
    {
        // 解密token 并检查token是否过期
        $post_token = $request->post('token');
        $object_time = new Token();
        $get_time = $object_time->decrypt($post_token);
        $object_check = new Check();
        $object_check->token($get_time);

        // 检查mid是否存在
        $post_mid = $request->post('mid');
        $post_mid=(int)$post_mid;
        $object_mid = new Check();
        $object_mid->member_id($post_mid);

        // 检查sign是否正确
        $post_sign = $request->post('sign');
        $object_sign = new Check();
        $object_sign->sign($post_sign, $post_mid);

        $post_account = $request->post('account');
        $account_info_sql['account'] = $post_account;
        $account_info = TradeAccount::where($account_info_sql)->find();
        $aid = $account_info->id;

        // 入金总额
        $total_profit = array();
        $total_info_sql['order_type'] = 6;
        $total_info_sql['aid'] = $aid;
        $total_list = TradeOrder::where($total_info_sql)->select();
        foreach ($total_list as $total) {
            // 当前帐户的入金 profit
            $total_profit[] = $total->profit;
        }
        $total_profit = array_sum($total_profit);


        $info_sql['aid'] = $aid;
        $info_sql['order_type'] =  ['<','3'];
        $info_list = TradeOrder::where($info_sql)->select();
        $account_count=count($info_list);

        $close_time_list = array();
        $trade_lots_list = array();
        $profit_rate_list = array();
        $total_account_profit = array();
        foreach ($info_list as $user) {
            $close_time_list[] = $close_time = $user->close_time;
            $profit = $user->profit;
            $trade_lots_list[] = $user->trade_lots;
            $profit_rate_list[] = round(($profit/$total_profit) * 100, 2);
            $total_account_profit[] = $user->profit;
        }
        // 计算盈利增长率
        $count_num=count($profit_rate_list);
        function sum_profit($a,$b){

            if($a==0){
                return $b[$a];
            }else{
                $c=$a-1;
                return  $b[$a]+sum_profit($c,$b);
            }

        }
        $sum_profit_rate_list = array();
        for($i=0;$i<$count_num;$i++){
            $sum_profit_rate_list[]=sum_profit($i,$profit_rate_list);
        }

        $average_profit_rate = array_sum($profit_rate_list);
        $average_profit_rate=round(($average_profit_rate/$account_count),2);
        $account_profit = array_sum($total_account_profit);
        $account_profit_rate = round(($account_profit/$total_profit) * 100, 2);
        $average_profit=round(($account_profit/$account_count),2);

        $json_info = array("code" => "1", "status" => "query success", "account_profit" => $account_profit,"average_profit" => $average_profit,"average_profit_rate" => $average_profit_rate, "account_profit_rate" => $account_profit_rate, "close_time_list" => $close_time_list, "profit_rate_list" => $sum_profit_rate_list, "trade_lots_list" => $trade_lots_list);
        return json_encode($json_info);

    }

    // 指定帐户的盈利百分比
    public function account_profit_rate(Request $request)
    {
        // 解密token 并检查token是否过期
        $post_token = $request->post('token');
        $object_time = new Token();
        $get_time = $object_time->decrypt($post_token);
        $object_check = new Check();
        $object_check->token($get_time);

        // 检查mid是否存在
        $post_mid = $request->post('mid');
        $post_mid=(int)$post_mid;
        $object_mid = new Check();
        $object_mid->member_id($post_mid);

        // 检查sign是否正确
        $post_sign = $request->post('sign');
        $object_sign = new Check();
        $object_sign->sign($post_sign, $post_mid);

        $post_account = $request->post('account');
        $account_info_sql['account'] = $post_account;
        $account_info = TradeAccount::where($account_info_sql)->find();
        $aid = $account_info->id;

        $object_account = new AccountProfitRate();
        $object_account->account_profit_list($aid);
    }

    // 交易平台 服务器 提交表单和 服务器列表
    public function add_server(Request $request)
    {
        $trade_server = new TradeServer;
        $list = $trade_server->all();
        $this->assign('list', $list);
        $this->assign('count', count($list));
        return $this->fetch();

    }

    // 新增 交易平台 服务器
    public function insert_server(Request $request)
    {
        // 解密token 并检查token是否过期
        $post_token = $request->post('token');
        $object_time = new Token();
        $get_time = $object_time->decrypt($post_token);
        $object_check = new Check();
        $object_check->token($get_time);

        // 检查mid是否存在
        $post_mid = $request->post('mid');
        $post_mid=(int)$post_mid;
        $object_mid = new Check();
        $object_mid->member_id($post_mid);

        // 检查sign是否正确
        $post_sign = $request->post('sign');
        $object_sign = new Check();
        $object_sign->sign($post_sign, $post_mid);

        // 接收post 数据
        $post_platform = $request->post('platform');
        $post_server_name = $request->post('server_name');
        $post_short_name = $request->post('short_name');
        $post_ip = $request->post('ip');

        // 创建 数据 数组
        $trade_server = array();
        if ($post_platform == '') {
            return 'platform is null';
        } else {
            $trade_server['platform'] = $post_platform;
        }
        if ($post_server_name == '') {
            return 'server name is null';
        } else {
            $trade_server['server_name'] = $post_server_name;
        }
        if ($post_server_name == '') {
            return 'short name is null';
        } else {
            $trade_server['short_name'] = $post_short_name;
        }
        if ($post_server_name == '') {
            return 'ip is null';
        } else {
            $trade_server['ip'] = $post_ip;
        }
        // 引用teacher数据表对应的模型
        $server = new TradeServer;
        // 向teacher表中插入数据并判断是否插入成功
        $server->data($trade_server)->save();
        return 'server info saved';
    }

    // 批量添加 交易平台订单数据 表单
    public function add_order(Request $request)
    {
        $trade_order = new TradeOrder;
        $list = $trade_order->all();
        $this->assign('list', $list);
        $this->assign('count', count($list));
        return $this->fetch();
    }

    // 批量保存 交易平台订单数据 json格式
    public function insert_order(Request $request)
    {
        // 解密token 并检查token是否过期
        $post_token = $request->post('token');
        $object_time = new Token();
        $get_time = $object_time->decrypt($post_token);
        $object_check = new Check();
        $object_check->token($get_time);

        // 检查mid是否存在
        $post_mid = $request->post('mid');
        $post_mid=(int)$post_mid;
        $object_mid = new Check();
        $object_mid->member_id($post_mid);

        // 检查sign是否正确
        $post_sign = $request->post('sign');
        $object_sign = new Check();
        $object_sign->sign($post_sign, $post_mid);

        $post_json = $request->post('json');
        // 检查是否为json格式的数据
        $json_data = json_decode($post_json, true);
        if ($json_data == '') {
            $account = array("code" => "0", "status" => "fail,you post a error json data. ");
            return json_encode($account);
        }


        // 查询交易平台服务器是否存在
        $server_num = count($json_data["server_info"]);
        for ($i = 0; $i < $server_num; $i++) {
            $platform = $json_data['server_info'][$i]['platform'];
            $server_name = $json_data['server_info'][$i]['server_name'];
            $short_name = $json_data['server_info'][$i]['short_name'];
            $ip = $json_data['server_info'][$i]['ip'];
        }
        $server_name_info_sql['server_name'] = $server_name;
        $server_name_info = TradeServer::where($server_name_info_sql)->find();
        if(isset($server_name_info)){
            $tsid=$server_name_info->id;
        }else{
            // 保存平台服务器信息到数据库
            $server_user = new TradeServer;
            $server_user->platform = $platform;
            $server_user->server_name = $server_name;
            $server_user->short_name = $short_name;
            $server_user->ip = $ip;
            $server_user->save();
            // 获取自增id
            $tsid=$server_user->id;
        }

        // 判断 account是否存在
        $num = count($json_data["account_info"]);
        for ($ii = 0; $ii < $num; $ii++) {
            $account = $json_data['account_info'][$ii]['account'];
            $password = $json_data['account_info'][$ii]['password'];
            $account_type = $json_data['account_info'][$ii]['account_type'];
            $account_status = $json_data['account_info'][$ii]['status'];
        }
        $account_info_sql['account'] = $account;
        $account_info = TradeAccount::where($account_info_sql)->find();
        if (isset($account_info)) {
            $aid = $account_info->id;
            $mid = $account_info->mid;
        } else {
            // 添加一个新会员帐户
            $username=$short_name.'_'.$account;
            $member_password='202cb962ac59075b964b07152d234b70';
            $member_info=array();
            $member_info['username'] = $username;
            $member_info['password'] = $member_password;
            $member_insert=new Member;
            $member_insert->data($member_info)->save();
            // 获取自增id
            $mid=$member_insert->id;
            // 增加 新account
            $account_user = new TradeAccount;
            $account_user->mid = $mid;
            $account_user->tsid = $tsid;
            $account_user->msid = 1;
            $account_user->account = $account;
            $account_user->password = $password;
            $account_user->account_type = $account_type;
            $account_user->status = $account_status;
            $account_user->save();
            $aid=$member_insert->id;
        }

        // 添加订单记录
        $order_num = count($json_data["order_list"]);
        for ($iii = 0; $iii < $order_num; $iii++) {
            $order_id = $json_data['order_list'][$iii]['order_id'];
            $order_type = $json_data['order_list'][$iii]['order_type'];
            $trade_symbol = $json_data['order_list'][$iii]['trade_symbol'];
            $trade_lots = $json_data['order_list'][$iii]['trade_lots'];
            $order_time = $json_data['order_list'][$iii]['order_time'];
            $order_price = $json_data['order_list'][$iii]['order_price'];
            $close_time = $json_data['order_list'][$iii]['close_time'];
            $close_price = $json_data['order_list'][$iii]['close_price'];
            $stop_loss = $json_data['order_list'][$iii]['stop_loss'];
            $take_profit = $json_data['order_list'][$iii]['take_profit'];
            $commission = $json_data['order_list'][$iii]['commission'];
            $taxes = $json_data['order_list'][$iii]['taxes'];
            $swap= $json_data['order_list'][$iii]['swap'];
            $profit = $json_data['order_list'][$iii]['profit'];
            $comment = $json_data['order_list'][$iii]['comment'];
            $status = $json_data['order_list'][$iii]['status'];

            // 判断 order_id 是否存在
            $order_id_info_sql['order_id'] = $order_id;
            $order_id_info = TradeOrder::where($order_id_info_sql)->find();
            //$this_id=$order_id_info->order_id;
            if(isset($order_id_info)){
                echo 'order id:'.$order_id.' is use <br />';
            } else{
            $order_user = new TradeOrder;
            $order_user->mid = $mid;
            $order_user->aid = $aid;
            $order_user->order_id = $order_id;
            $order_user->order_type = $order_type;
            $order_user->trade_symbol = $trade_symbol;
            $order_user->trade_lots = $trade_lots;
            $order_user->order_time = $order_time;
            $order_user->order_price = $order_price;
            $order_user->close_time = $close_time;
            $order_user->close_price = $close_price;
            $order_user->stop_loss = $stop_loss;
            $order_user->take_profit = $take_profit;
            $order_user->commission = $commission;
            $order_user->taxes = $taxes;
            $order_user->swap = $swap;
            $order_user->profit = $profit;
            $order_user->comment = $comment;
            $order_user->status = $status;
            $order_user->save();
                echo 'order id:'.$order_id.' update success <br />';
            }

        }


    }

}