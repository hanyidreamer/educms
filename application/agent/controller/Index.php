<?php
/**
 * Created by PhpStorm.
 * User: tzx
 * Date: 2016/10/14
 * Time: 17:07
 */
namespace app\agent\controller;


use app\common\model\TradeAccount;
use app\common\model\TradeAccountShow;

class Index extends AgentBase
{

    /**
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index()
    {
        // 顶部菜单
        $right_menu = array('status'=>false,'menu_title'=>'','menu_url'=>'');
        $this->assign('right_menu',$right_menu);

        // 交易账户案例
        $trade_account_show = new TradeAccountShow();
        $trade_account_show_data = $trade_account_show -> where(['site_id'=>0])->select();
        foreach ($trade_account_show_data as $item){
            $trade_account = TradeAccount::get($item['aid']);

            $item['account'] = $trade_account['account'];
        }


        return $this->fetch($this->template_path);
    }
}