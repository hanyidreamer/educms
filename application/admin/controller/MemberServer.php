<?php
/**
 * Created by PhpStorm.
 * User: tzx
 * Date: 2016/10/26
 * Time: 14:45
 */
namespace app\admin\controller;

use think\Request;
use app\common\model\MemberServer as MemberServerModel;
use app\common\model\TradeServer;

class MemberServer extends Base
{
    public function index(Request $request)
    {
        $post_username= $request->post('name');
        if($post_username==!''){
            $data_sql['name'] =  ['like','%'.$post_username.'%'];
        }
        $data_sql['status'] = 1;
        $member_data = new MemberServerModel;
        $data_list = $member_data->where($data_sql) -> select();
        $data_count = count($data_list);
        foreach($data_list as $data)
        {
            $server_info = TradeServer::get($data['tsid']);
            $data['server_name'] = $server_info['server_name'];
            $data['platform'] = $server_info['platform'];
        }
        $this->assign('data_list',$data_list);
        $this->assign('data_count',$data_count);

        return $this->fetch();
    }

    public function add()
    {
        $trade_server_data = new TradeServer();
        $trade_server_list = $trade_server_data->where(['status'=>1]) -> select();
        $this->assign('trade_server_list',$trade_server_list);
        return $this->fetch();
    }

    public function insert(Request $request)
    {
        $post_name= $request->post('name');
        $post_en_name= $request->post('en_name');
        $post_ip= $request->post('ip');
        $post_server_name= $request->post('server_name');
        $post_platform= $request->post('platform');
        $post_type= $request->post('type');
        $post_status= $request->post('status');
        if($post_name=='' or $post_en_name==''){
            $this->error('服务器名称不能为空');
        }

        $trade_server_list = TradeServer::get(['platform'=>$post_platform,'server_name'=>$post_server_name]);
        if($trade_server_list==''){
            $trade_user = new TradeServer;
            $trade_user['platform'] = $post_platform;
            $trade_user['server_name'] = $post_server_name;
            $trade_user['ip'] = $post_ip;
            $trade_user['status'] = $post_status;
            if ($trade_user->save()) {
                // 新增交易服务器成功
                $tsid = $trade_user['id'];
            } else {
                $tsid = '';
                $this->error('操作失败');
            }
        }else{
            $tsid=$trade_server_list['id'];
        }

        $user = new MemberServerModel;
        $user['tsid'] = $tsid;
        $user['name'] = $post_name;
        $user['en_name'] = $post_en_name;
        $user['ip'] = $post_ip;
        $user['type'] = $post_type;
        $user['status'] = $post_status;

        if ($user->save()) {
            $this->success('新增会员成功', '/admin/memberserver/index');
        } else {
            $this->error('操作失败');
        }

    }

    public function edit($id)
    {
        $data_list = MemberServerModel::get($id);

        $tsid=$data_list['tsid'];
        $trade_server_list = TradeServer::get($tsid);
        $this->assign('trade_server_list',$trade_server_list);

        $this->assign('data_list',$data_list);
        return $this->fetch();
    }

    public function save(Request $request)
    {
        $post_id= $request->post('id');
        $post_name= $request->post('name');
        $post_en_name= $request->post('en_name');
        $post_ip= $request->post('ip');
        $post_platform= $request->post('platform');
        $post_server_name= $request->post('server_name');
        $post_type= $request->post('type');
        $post_status= $request->post('status');
        if($post_id==''){
            $this->error('用户不能为空');
        }

        $trade_server_list = TradeServer::get(['platform'=>$post_platform,'server_name'=>$post_server_name]);
        $tsid = $trade_server_list['id'];

        $user = MemberServerModel::get($post_id);
        $user['tsid'] = $tsid;
        $user['name'] = $post_name;
        $user['en_name'] = $post_en_name;
        $user['ip'] = $post_ip;
        $user['type'] = $post_type;
        $user['status'] = $post_status;
        if ($user->save()) {
            $this->success('会员服务器信息保存成功', '/admin/memberserver/index');
        } else {
            $this->error('操作失败');
        }

    }

    public function delete($id)
    {
        $user = MemberServerModel::get($id);
        if ($user) {
            $user->delete();
            $this->success('删除会员服务器成功', '/admin/memberserver/index');
        } else {
            $this->error('操作失败');
        }
    }

}