<?php
/**
 * Created by PhpStorm.
 * User: tanzhenxing
 * Date: 2017/4/17
 * Time: 16:48
 */
namespace app\trade\model;

use app\index\model\TradeAccountShow;
use app\index\model\TradeAccount;

class GetAccount
{
    public function account_list($type,$site_id)
    {
        $account_show_list_check = TradeAccountShow::get(['type'=>$type,'site_id'=>$site_id]);
        if(empty($account_show_list_check)){
            $site_id=4;
        }

        $account_show_list = TradeAccountShow::get(['type'=>$type,'site_id'=>$site_id]);
        $account_sql['id'] = $account_show_list['aid'];
        $account_list = TradeAccount::get($account_sql['id']);
        $account=$account_list['account'];
        return $account;
    }
}