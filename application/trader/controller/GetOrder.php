<?php
/**
 * Created by PhpStorm.
 * User: tanzhenxing
 * Date: 2017/4/17
 * Time: 10:53
 */
namespace app\trade\model;

use think\Db;

class GetOrder
{
    public function close_order_list($account,$order_num)
    {
        $order_table_name = "account_".$account;
        // 最近成交的订单
        $order_record_list = Db::connect('trade_order')->query("select * from $order_table_name where status = 1 and close_time != '0000-00-00 00:00:00' order by close_time desc limit $order_num");

        return $order_record_list;
    }

    public function close_order_count($account)
    {
        $order_table_name = "account_".$account;
        // 最近成交的订单
        $order_record_count = Db::connect('trade_order')->query("select count(*) from $order_table_name where status = 1 and close_time != '0000-00-00 00:00:00'");
        $count = $order_record_count[0]['count(*)'];
        return $count;
    }

    public function open_order_list($account)
    {
        $order_table_name = "account_".$account;
        // 最近成交的订单
        $order_record_list = Db::connect('trade_order')->query("select * from $order_table_name where close_time = '0000-00-00 00:00:00'  and order_type != 'balance' order by open_time desc");

        return $order_record_list;
    }

    public function open_order_count($account)
    {
        $order_table_name = "account_".$account;
        // 最近成交的订单
        $order_record_count = Db::connect('trade_order')->query("select count(*) from $order_table_name where close_time = '0000-00-00 00:00:00'  and order_type != 'balance'");
        $count = $order_record_count[0]['count(*)'];
        return $count;
    }

    public function one_day($account)
    {
        $order_table_name = "account_".$account;
        // 最近成交的订单
        // SELECT * FROM `account_30019200` WHERE DATE_SUB(CURDATE(), INTERVAL 1 DAY) <= DATE(`close_time`);

        $today = date('Y-m-d',time()-3600*24);
        $order_record_count = Db::connect('trade_order')->query("SELECT * FROM  $order_table_name WHERE  `close_time` LIKE  '%$today%'");
        $count = $order_record_count[0]['count(*)'];
        return $count;
    }

    public function week()
{
    // $order_table_name = "account_".$account;
    // 最近成交的订单
    // SELECT * FROM `account_30019200` WHERE DATE_SUB(CURDATE(), INTERVAL 7 DAY) <= DATE(`close_time`);

    $order_record_count = Db::connect('trade_order')->query('SELECT * FROM `account_30019200` WHERE DATE_SUB(CURDATE(), INTERVAL 7 DAY) <= DATE(`close_time`);');
    $count = $order_record_count[0]['count(*)'];
    return $count;
}

    public function month($account)
    {
        $order_table_name = "account_".$account;
        // 最近成交的订单
        $order_record_count = Db::connect('trade_order')->query("select count(*) from $order_table_name where status = 0");
        $count = $order_record_count[0]['count(*)'];
        return $count;
    }

}