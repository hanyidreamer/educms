<?php
/**
 * Created by PhpStorm.
 * User: tanzhenxing
 * Date: 2018/5/2
 * Time: 16:57
 */
namespace app\trader\controller;

use app\common\model\TradeAccount;
use app\base\controller\TraderAdminBase;

class Account extends TraderAdminBase
{
    /**
     * 账户列表
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index()
    {
        $trader_data = new TradeAccount();
        $account_data = $trader_data->where(['status'=>1])->select();
        $this->assign('account',$account_data);

        $this->fetch($this->template_path);
    }
}