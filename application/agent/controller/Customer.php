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

        // 用户名
        $agent_username = session('agent_username');
        $agent_data = Agent::get(['username'=>$agent_username]);
        $this->assign('agent',$agent_data);

        // 当前用户所属的交易账户
        $data = new TradeAccount();
        $customer = $data->where(['agent_id'=>$agent_data['id']])->select();

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
        // 顶部菜单
        $right_menu = array('status'=>false,'menu_title'=>'','menu_url'=>'');
        $this->assign('right_menu',$right_menu);

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

    /**
     * @param $id
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function edit($id)
    {
        // 顶部菜单
        $right_menu = array('status'=>false,'menu_title'=>'添加客户','menu_url'=>'/agent/customer/create');
        $this->assign('right_menu',$right_menu);

        // 交易服务器信息
        $trade_server_data = new TradeServer();
        $trade_server = $trade_server_data->where(['category'=>'live','status'=>1])->select();
        $this->assign('trade_server',$trade_server);

        // 当前客户资料
        $my_trade_account = TradeAccount::get($id);
        $this->assign('my_trade_account',$my_trade_account);
        $my_trade_server = TradeServer::get($my_trade_account['tsid']);
        $this->assign('my_trade_server',$my_trade_server);
        return $this->fetch($this->template_path);
    }

    /**
     * @throws \think\exception\DbException
     */
    public function update()
    {
        $post_data = $this->request->param();
        $data_array = array('tsid','account','password','nickname');

        $data = TradeAccount::get($post_data['id']);
        $data_save = $data->allowField($data_array)->save($post_data);
        if($data_save){
            $this->success('修改成功','/agent/customer/');
        }else{
            $this->error('保存失败');
        }
    }

    /**
     * @param $id
     * @throws \think\exception\DbException
     */
    public function delete($id)
    {
        $data = TradeAccount::get($id);
        if ($data) {
            $data->delete();
            $this->success('删除成功', '/agent/customer/index');
        } else {
            $this->error('删除失败');
        }
    }
}