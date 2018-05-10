<?php
/**
 * Created by PhpStorm.
 * User: tzx
 * Date: 2016/10/25
 * Time: 15:13
 */
namespace app\admin\controller;

use think\Request;
use app\common\model\TradeAccount as TradeAccountModel;
use app\common\model\Member;
use app\common\model\MemberServer;
use app\common\model\TradeServer;

class TradeAccount extends AdminBase
{
    /**
     * @param Request $request
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index(Request $request)
    {
        $post_account= $request->post('account');
        if(!empty($post_account)){
            $trade_data = new TradeAccountModel();
            $data_list = $trade_data->where(['account'=>$post_account]) -> select();
        }else{
            $data_list = TradeAccountModel::all();
        }

        $data_count = count($data_list);
        foreach($data_list as $data)
        {
            $data->id;
            $mid=$data->mid;
            $member_list = Member::get($mid);
            $username=$member_list['username'];
            $data->username=$username;

            $tsid=$data->tsid;
            $trade_list = TradeServer::get($tsid);
            $trade_platform=$trade_list['platform'];
            $data->trade_platform=$trade_platform;
            $trade_server_name=$trade_list['server_name'];
            $data->trade_server_name=$trade_server_name;

            $msid=$data->msid;
            $ms_list = MemberServer::get($msid);
            $member_server=$ms_list['name'];
            $data->member_server_name=$member_server;

            $data->account;
            $data->password;
            $data->account_type;
            $data->status;
            $data->update_time;
        }
        $this->assign('data_list',$data_list);
        $this->assign('data_count',$data_count);

        return $this->fetch();
    }

    /**
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function add()
    {
        $member_data = new MemberServer;
        $member_list = $member_data->where(['status'=>1]) -> select();
        $this->assign('member_list',$member_list);

        $trade_data = new TradeServer();
        $trade_list = $trade_data->where(['status'=>1]) -> select();
        $this->assign('trade_list',$trade_list);

        return $this->fetch();
    }

    /**
     * @param Request $request
     * @throws \think\exception\DbException
     */
    public function insert(Request $request)
    {
        $post_username= $request->post('username');
        $member_list = Member::get(['username'=>$post_username]);
        $mid=$member_list['id'];

        $post_member_server_name= $request->post('member_server_name');
        $ms_list = MemberServer::get(['en_name'=>$post_member_server_name]);
        $msid=$ms_list['id'];

        $post_trade_platform= $request->post('trade_platform');
        $post_trade_server_name= $request->post('trade_server_name');
        $trade_list = TradeServer::get(['platform'=>$post_trade_platform,'server_name'=>$post_trade_server_name]);
        $tsid=$trade_list['id'];

        $post_account= $request->post('account');
        $post_password= $request->post('password');
        $post_account_type= $request->post('account_type');
        $post_status= $request->post('status');
        if($post_account=='' or $post_password==''){
            $this->error('交易账户和密码不能为空');
        }
        $user = new TradeAccountModel;
        $user['mid'] = $mid;
        $user['tsid'] = $tsid;
        $user['msid'] = $msid;
        $user['account'] = $post_account;
        $user['password'] = $post_password;
        $user['account_type'] = $post_account_type;
        $user['status'] = $post_status;

        if ($user->save()) {
            $this->success('新增交易账户成功', '/admin/tradeaccount/index');
        } else {
            $this->error('操作失败');
        }

    }

    /**
     * @param $id
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function edit($id)
    {
        $data_list = TradeAccountModel::get($id);

        $mid=$data_list['mid'];
        $member_list = Member::get($mid);
        $username=$member_list['username'];
        $data_list['username']=$username;

        $tsid = $data_list['tsid'];
        $trade_list = TradeServer::get($tsid);
        $platform = $trade_list['platform'];
        $data_list['trade_platform'] = $platform;
        $server_name = $trade_list['server_name'];
        $data_list['trade_server_name'] = $server_name;

        $msid = $data_list['msid'];
        $ms_list = MemberServer::get($msid);
        $ms_name = $ms_list['name'];
        $data_list['member_server_name'] = $ms_name;

        $this->assign('data_list',$data_list);
        return $this->fetch();
    }

    /**
     * @param Request $request
     * @throws \think\exception\DbException
     */
    public function save(Request $request)
    {
        $post_id= $request->post('id');

        $post_username= $request->post('username');
        $member_list = Member::get(['username'=>$post_username]);
        $mid=$member_list['id'];

        $post_trade_platform= $request->post('trade_platform');
        $post_trade_server_name= $request->post('trade_server_name');
        $trade_list = TradeServer::get(['platform'=>$post_trade_platform,'server_name'=>$post_trade_server_name]);
        $tsid=$trade_list['id'];

        $post_member_server_name= $request->post('member_server_name');
        $ms_list = MemberServer::get(['name'=>$post_member_server_name]);
        $msid=$ms_list['id'];

        $post_account= $request->post('account');
        $post_password= $request->post('password');
        $post_account_type= $request->post('account_type');
        $post_status= $request->post('status');
        if($post_id==''){
            $this->error('账户id不能为空');
        }

        $user = TradeAccountModel::get($post_id);
        $user['mid'] = $mid;
        $user['tsid'] = $tsid;
        $user['msid'] = $msid;
        $user['account'] = $post_account;
        $user['password'] = $post_password;
        $user['account_type'] = $post_account_type;
        $user['status'] = $post_status;
        if ($user->save()) {
            $this->success('保存交易账户信息成功', '/admin/tradeaccount/index');
        } else {
            $this->error('操作失败');
        }

    }

    /**
     * @param $id
     * @throws \think\exception\DbException
     */
    public function delete($id)
    {
        $user = TradeAccountModel::get($id);
        if ($user) {
            $user->delete();
            $this->success('删除交易账户成功', '/admin/tradeaccount/index');
        } else {
            $this->error('您要删除的交易账户不存在');
        }
    }


}