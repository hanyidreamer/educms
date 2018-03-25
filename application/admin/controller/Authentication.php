<?php
/**
 * Created by PhpStorm.
 * User: tzx
 * Date: 2016/10/25
 * Time: 14:31
 */
namespace app\admin\controller;

use think\Request;
use app\index\model\MemberAuthentication;
use app\index\model\Member;

class Authentication extends Base
{
    public function index()
    {
        $member_info_sql['status'] = ['<>','0'];
        $member_auth_info = new MemberAuthentication();
        $data_list = $member_auth_info->where($member_info_sql) -> select();
        $data_count=count($data_list);
        foreach($data_list as $data)
        {
            $data->id;
            $mid=$data->mid;
            $member_info_sql['id'] = $mid;
            $member_list = Member::get($mid);
            $data->username=$member_list['username'];

            $data->token;
            $data->sign;
            $data->ip;
            $data->status;
            $data->update_time;

        }
        $this->assign('data_count',$data_count);
        $this->assign('data_list',$data_list);
        return $this->fetch();
    }

    public function add()
    {
        $title = '新增 授权';
        $this->assign('title',$title);
        return $this->fetch();
    }

    public function insert(Request $request)
    {
        $post_ip = $request->ip();
        $post_mid= $request->post('mid');
        $post_client= $request->post('client');
        $post_token= $request->post('token');
        $post_sign= $request->post('sign');
        $post_status= $request->post('status');
        if($post_mid=='' or $post_token==''){
            $this->error('会员的用户名和token不能为空');
        }

        $user = new MemberAuthentication;
        $user['mid'] = $post_mid;
        $user['client'] = $post_client;
        $user['token'] = $post_token;
        $user['sign'] = $post_sign;
        $user['ip'] = $post_ip;
        $user['status'] = $post_status;

        if ($user->save()) {
            $this->success('新增会员api授权成功', '/admin/authentication/index');
        } else {
            $this->error('会员的用户名和token不能为空');
        }

    }

    public function edit($id)
    {
        $data_sql['id'] = $id;
        $data_list = MemberAuthentication::get($id);

        $this->assign('user',$data_list);
        return $this->fetch();
    }

    public function save(Request $request)
    {
        $post_id= $request->post('id');
        $post_ip = $request->ip();
        $post_mid= $request->post('mid');
        $post_client= $request->post('client');
        $post_token= $request->post('token');
        $post_sign= $request->post('sign');
        $post_status= $request->post('status');
        if($post_id==''){
            $this->error('用户不能为空');
        }

        $user = MemberAuthentication::get($post_id);
        $user['ip'] = $post_ip;
        $user['mid'] = $post_mid;
        $user['client'] = $post_client;
        $user['token'] = $post_token;
        $user['sign'] = $post_sign;
        $user['status'] = $post_status;
        if ($user->save()) {
            $this->success('保存会员信息成功', '/admin/authentication/index');
        } else {
            $this->error('操作失败');
        }

    }

    public function delete($id)
    {
        $user = MemberAuthentication::get($id);
        if ($user) {
            $user->delete();
            $this->success('删除会员授权信息成功', '/admin/authentication/index');
        } else {
            $this->error('您要删除的信息不存在');
        }
    }


}