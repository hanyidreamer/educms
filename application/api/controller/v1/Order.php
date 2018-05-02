<?php
/**
 * Created by PhpStorm.
 * User: tzx
 * Date: 2016/10/8
 * Time: 21:30
 */
namespace app\api\controller\v1;

use think\Controller;
use think\Request;
use app\api\model\Check;
use app\api\model\Token;
use app\common\model\TradeAccount;
use app\common\model\TradeOrder;

class Order extends Controller
{
    public function __construct(Request $request)
    {
        // 解密token 并检查token是否过期
        $post_token= $request->post('token');
        $object_time = new Token();
        $get_time=$object_time->decrypt($post_token);
        $object_check = new Check();
        $object_check->token($get_time);

        // 检查mid是否存在
        $post_mid= $request->post('mid');
        $post_mid=(int)$post_mid;
        $this->post_mid = $post_mid;
        $object_mid = new Check();
        $object_mid->member_id($post_mid);

        // 检查sign是否正确
        $post_sign= $request->post('sign');
        $object_sign = new Check();
        $object_sign->sign($post_sign,$post_mid);

    }

    // 插入交易记录
    public function insert(Request $request)
    {
        $post_mid= $this->post_mid;

        // 获取 account的 aid
        $post_account= $request->post('account');
        if($post_account==''){
            $out_info=array("code"=>"0","status"=>"account is null.");
            return json_encode($out_info);
        }
        $account_info_sql['account'] = $post_account;
        $account_info= TradeAccount::where($account_info_sql) -> find();
        if($account_info==''){
            $out_info=array("code"=>"0","status"=>"account is error.");
            return json_encode($out_info);
        }

        $aid=$account_info->id;

        $post_order_id=$request->post('order_id');
        $post_order_type=$request->post('order_type');
        $post_trade_symbol=$request->post('trade_symbol');
        $post_trade_lots=$request->post('trade_lots');
        $post_order_time=$request->post('order_time');
        $post_order_price=$request->post('order_price');
        $post_close_time=$request->post('close_time');
        $post_close_price=$request->post('close_price');
        $post_stop_loss=$request->post('stop_loss');
        $post_take_profit=$request->post('take_profit');
        $post_commission=$request->post('commission');
        $post_taxes=$request->post('taxes');
        $post_swap=$request->post('swap');
        $post_profit=$request->post('profit');
        $post_comment=$request->post('comment');
        $post_status=$request->post('status');

        // 判断订单号是否为空
        if($post_order_id=='' or $post_order_id==0){
            $json_data=array("code"=>"0","status"=>"order_id is null");
            return json_encode($json_data);
        }

        // 判断订单号是否存在
        $account_order_info_sql['order_id'] = $post_order_id;
        $account_order_info= TradeOrder::where($account_order_info_sql) -> find();
        if(isset($account_order_info)){
            $out_info=array("code"=>"0","status"=>"order_id is error.");
            return json_encode($out_info);
        }else{
            // 插入订单交易记录
            $trade_order_info = new TradeOrder();
            $trade_order_info->mid = $post_mid;
            $trade_order_info->aid = $aid;
            $trade_order_info->order_id = $post_order_id;
            $trade_order_info->order_type = $post_order_type;
            $trade_order_info->trade_symbol = $post_trade_symbol;
            $trade_order_info->trade_lots = $post_trade_lots;
            $trade_order_info->order_time = $post_order_time;
            $trade_order_info->order_price = $post_order_price;
            $trade_order_info->close_time = $post_close_time;
            $trade_order_info->close_price = $post_close_price;
            $trade_order_info->stop_loss = $post_stop_loss;
            $trade_order_info->take_profit = $post_take_profit;
            $trade_order_info->commission = $post_commission;
            $trade_order_info->taxes = $post_taxes;
            $trade_order_info->swap = $post_swap;
            $trade_order_info->profit = $post_profit;
            $trade_order_info->comment = $post_comment;
            $trade_order_info->status = $post_status;

            if ($trade_order_info->save()) {
                $json_data=array("code"=>"1","status"=>"order insert success");
                return json_encode($json_data);
            } else {
                return $trade_order_info->getError();
            }
        }

    }

}