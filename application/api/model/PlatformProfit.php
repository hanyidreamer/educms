<?php
/**
 * Created by PhpStorm.
 * User: tzx
 * Date: 2016/10/9
 * Time: 18:12
 */
namespace app\api\model;

use think\Model;
use app\common\model\TradeOrder;


class PlatformProfit extends Model
{
    public function profit_list()
    {
        // 入金汇总（本金汇总）
        $total_profit = array();
        $total_info_sql['order_type'] = 6;
        $total_list = TradeOrder::where($total_info_sql) -> select();
        foreach($total_list  as $total)
        {
            // 当前帐户的入金 profit
            $total_profit[] = $total->profit;
        }
        $total_profit=array_sum($total_profit);

        // 盈利汇总
        $total_order_profit = array();
        $total_order_info_sql['order_type'] =  ['<','3'];
        $total_order_list = TradeOrder::where($total_order_info_sql) -> select();
        foreach($total_order_list  as $total_order)
        {
            // 当前帐户的入金 profit
            $total_order_profit[] = $total_order->profit;
        }
        $total_order_profit=array_sum($total_order_profit);
        // 平台总盈利率
        $total_all=round(($total_order_profit/$total_profit)*100,2);

        // 平台本年盈利 比
        $year_profit = array();
        $year_time = time();
        $year_time=date('Y',$year_time);
        $year_info_sql['order_type'] =  ['<','3'];
        $year_info_sql['close_time'] =  ['like',$year_time.'%'];
        $year_list = TradeOrder::where($year_info_sql) -> select();
        foreach($year_list  as $year_order)
        {
            // 当前帐户的入金 profit
            $year_profit[] = $year_order->profit;
        }
        $total_year_profit=array_sum($year_profit);
        // 平台本年盈利率
        $total_year=round(($total_year_profit/$total_profit)*100,2);


        // 平台本月盈利 比
        $month_profit = array();
        $month_time = time();
        $month_time=date('Y-m',$month_time);
        $month_info_sql['order_type'] =  ['<','3'];
        $month_info_sql['close_time'] =  ['like',$month_time.'%'];
        $month_list = TradeOrder::where($month_info_sql) -> select();
        foreach($month_list  as $month_order)
        {
            // 当前帐户的入金 profit
            $month_profit[] = $month_order->profit;
        }
        $total_month_profit=array_sum($month_profit);
        // 平台本月盈利率
        $total_month=round(($total_month_profit/$total_profit)*100,2);

        // 平台本周盈利 比
        $week_profit = array();
        $week_time = time();
        $week_time=date('Y-m-d',$week_time);
        $week_info_sql['order_type'] =  ['<','3'];
        $week_info_sql['close_time'] =  ['like',$week_time.'%'];
        $week_list = TradeOrder::where($week_info_sql) -> select();
        foreach($week_list  as $week_order)
        {
            // 当前帐户的入金 profit
            $week_profit[] = $week_order->profit;
        }
        $total_week_profit=array_sum($week_profit);
        // 平台本周盈利率
        $total_week=round(($total_week_profit/$total_profit)*100,2);

        // 平台今日盈利 比
        $today_profit = array();
        $today_time = time();
        $today_time=date('Y-m-d',$today_time);
        $today_info_sql['order_type'] =  ['<','3'];
        $today_info_sql['close_time'] =  ['like',$today_time.'%'];
        $today_list = TradeOrder::where($today_info_sql) -> select();
        foreach($today_list  as $today_order)
        {
            // 当前帐户的入金 profit
            $today_profit[] = $today_order->profit;
        }
        $total_today_profit=array_sum($today_profit);
        // 平台今日盈利率
        $total_today=round(($total_today_profit/$total_profit)*100,2);


        $profit_data=array("total_all"=>$total_all,"total_year"=>$total_year,"total_month"=>$total_month,"total_week"=>$total_week,"total_today"=>$total_today);
        return $profit_data;
    }

}