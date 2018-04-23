<?php
/**
 * Created by PhpStorm.
 * User: tanzhenxing
 * Date: 2016/11/11
 * Time: 9:11
 */
namespace app\admin\controller;

use think\Request;
use app\common\model\TradeOrder as TradeOrderModel;
use app\common\model\TradeAccount;
use app\common\model\TradeServer;
use app\common\model\Member;

class TradeOrder extends AdminBase
{
    /**
     * @param Request $request
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function index(Request $request)
    {
        $pages=20;

        $post_account= $request->post('account');
        $account_list = TradeAccount::get(['account'=>$post_account]);
        $aid=$account_list['id'];

        if($aid==!''){
            $data_sql['aid'] =  $aid;
        }

        $trade_data = new TradeOrderModel();
        $data_list = $trade_data->where(['status'=>1]) -> paginate($pages);
        $data_count = count($data_list);
        foreach($data_list as $data)
        {
            $data->id;

            $mid=$data->mid;
            $member_list = Member::get($mid);
            $username=$member_list['username'];
            $data->username=$username;

            $aid=$data->aid;
            $aid_list = TradeAccount::get($aid);
            $account=$aid_list['account'];
            $data->account=$account;

            $data->order_id;
            $data->order_type;
            $data->trade_symbol;
            $data->trade_lots;
            $data->open_time;
            $data->open_price;
            $data->close_time;
            $data->close_price;
            $data->stop_loss;
            $data->take_profit;
            $data->commission;
            $data->taxes;
            $data->swap;
            $data->profit;
            $data->comment;
            $data->status;
            $data->update_time;
        }
        $this->assign('data_list',$data_list);
        $this->assign('data_count',$data_count);

        return $this->fetch();
    }

    /**
     * @return mixed
     */
    public function add()
    {
        return $this->fetch();
    }

    /**
     * @param Request $request
     */
    public function insert(Request $request)
    {
        $post_platform= $request->post('platform');
        $post_server_name= $request->post('server_name');
        $post_short_name= $request->post('short_name');
        $post_ip= $request->post('ip');
        $post_status= $request->post('status');
        if($post_platform=='' or $post_server_name==''){
            $this->error('交易平台名称和交易服务器名称不能为空');
        }
        $user = new TradeServer;
        $user['platform'] = $post_platform;
        $user['server_name'] = $post_server_name;
        $user['short_name'] = $post_short_name;
        $user['ip'] = $post_ip;
        $user['status'] = $post_status;

        if ($user->save()) {
            $this->success('新增交易服务器成功', '/admin/tradeserver/index');
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
        $data_list = TradeServer::get($id);

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
        $post_platform= $request->post('platform');
        $post_server_name= $request->post('server_name');
        $post_short_name= $request->post('short_name');
        $post_ip= $request->post('ip');
        $post_status= $request->post('status');
        if($post_id==''){
            $this->error('用户不能为空');
        }

        $user = TradeServer::get($post_id);
        $user['platform'] = $post_platform;
        $user['server_name'] = $post_server_name;
        $user['short_name'] = $post_short_name;
        $user['ip'] = $post_ip;
        $user['status'] = $post_status;
        if ($user->save()) {
            $this->success('交易服务器信息保存成功', '/admin/tradeserver/index');
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
        $user = TradeOrderModel::get($id);
        if ($user) {
            $user->delete();
            $this->success('删除交易订单成功', '/admin/tradeorder/index');
        } else {
            $this->error('您要删除的交易订单不存在');
        }
    }

}