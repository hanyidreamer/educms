<?php
/**
 * Created by PhpStorm.
 * User: tanzhenxing
 * Date: 2018/5/8
 * Time: 15:32
 */
namespace app\agent\controller;


use app\common\model\Agent;
use app\common\model\TradeAccount;
use app\common\model\TradeServer;

class Customer extends AgentBase
{
    /**
     * @throws \think\exception\DbException
     */
    public function index()
    {
        // 顶部菜单
        $right_menu = array('status'=>true,'menu_title'=>'添加客户','menu_url'=>'/agent/customer/create');
        $this->assign('right_menu',$right_menu);

        // 当前代理的账户
        $agent_username = session('agent_username');
        $customer = '';
        if(!empty($agent_username)){
            $agent_data = Agent::get(['username'=>$agent_username]);
            $data = new TradeAccount();
            $customer = $data->where(['agent_id'=>$agent_data['id']])->select();
        }
        $this->assign('customer',$customer);

        return $this->fetch($this->template_path);

    }

    /**
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function create()
    {
        // 交易服务器信息
        $trade_server_data = new TradeServer();
        $trade_server = $trade_server_data->where(['category'=>'live','status'=>1])->select();
        $this->assign('trade_server',$trade_server);

        return $this->fetch($this->template_path);
    }

    /**
     * @throws \think\exception\DbException
     */
    public function save()
    {
        $agent_username = session('agent_username');
        if(empty($agent_username)){
            $agent_id = 0;
        }
        $agent_data = Agent::get(['username'=>$agent_username]);
        $agent_id = $agent_data['id'];

        $post_data = $this->request->param();
        $post_data['agent_id'] = $agent_id;
        $post_data['status'] = 1;

        $data_array = array('agent_id','tsid','account','password','nickname','status');

        $data = new TradeAccount();
        $data_save = $data->allowField($data_array)->save($post_data);
        if($data_save){
            $this->success('保存成功','/agent/customer/');
        }else{
            $this->error('保存失败');
        }



    }

    public function edit($id)
    {

    }

    public function update()
    {
        
    }
}