<?php
/**
 * Created by PhpStorm.
 * User: tzx
 * Date: 2016/10/26
 * Time: 14:46
 */
namespace app\admin\controller;

use think\Request;
use app\common\model\Member;
use app\common\model\MemberService as MemberServiceModel;
use app\common\model\MemberServer;
use app\common\model\MemberServicePackage;
use app\common\model\TradeAccount;

class MemberService extends AdminBase
{
    /**
     * @param Request $request
     * @return mixed|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index(Request $request)
    {
        $post_username= $request->post('username');
        if($post_username==!''){
            $data_test_sql['username'] =  ['like','%'.$post_username.'%'];
            $member_data = new Member();
            $member_test_list = $member_data->where($data_test_sql) -> select();
            $data_count = count($member_test_list);

            if($data_count==0){
                return '您查询的用户:['.$post_username.']不存在！';
            }

                $member_data=array();
                foreach ($member_test_list as $member){
                    $member_id=$member->id;
                    $member_data_sql['mid'] =  $member_id;
                    $member_service_data = new MemberServiceModel;
                    $data_list = $member_service_data->where($member_data_sql) -> select();

                    foreach ($data_list as $data){
                        $data->id;
                        $mid=$data->mid;
                        $member_list = Member::get($mid);
                        $data->username=$member_list['username'];

                        $aid=$data->aid;
                        $account_list = TradeAccount::get($aid);
                        $data->account=$account_list['account'];
                        $data->password=$account_list['password'];

                        $msid=$data->msid;
                        $ms_server_list = MemberServer::get($msid);
                        $data->server=$ms_server_list['name'];

                        $spid=$data->spid;
                        $package_list = MemberServicePackage::get($spid);
                        $data->package=$package_list['name'];

                        $data->work_status;
                        $data->aid_status;
                        $member_data[]=$data;
                    }


                }

            $this->assign('data_list',$member_data);
            $this->assign('data_count',$data_count);
            return $this->fetch();
        }else{
            $data_sql['status'] = 1;
            $member_service_data = new MemberServiceModel();
            $data_list = $member_service_data->where($data_sql) -> select();
            $data_count = count($data_list);
            foreach($data_list as $data)
            {
                $data->id;
                $mid=$data->mid;
                $member_list = Member::get($mid);
                $data->username=$member_list['username'];

                $aid=$data->aid;
                $account_list = TradeAccount::get($aid);
                $data->account=$account_list['account'];
                $data->password=$account_list['password'];

                $msid=$data->msid;
                $ms_server_list = MemberServer::get($msid);
                $data->server=$ms_server_list['name'];

                $spid=$data->spid;
                $package_sql['id'] = $spid;
                $package_list = MemberServicePackage::get($spid);
                $data->package=$package_list['name'];

                $data->work_status;
                $data->aid_status;
            }


            $this->assign('data_list',$data_list);
            $this->assign('data_count',$data_count);

            return $this->fetch();
        }

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
        $post_username= $request->post('username');
        $post_password= $request->post('password');
        $post_nickname= $request->post('nickname');
        $post_tel= $request->post('tel');
        $post_type= $request->post('type');
        $post_status= $request->post('status');
        if($post_username=='' or $post_password==''){
            $this->error('会员的用户名和密码不能为空');
        }
        $user = new MemberServiceModel;
        $user['username'] = $post_username;
        $user['password'] = $post_password;
        $user['nickname'] = $post_nickname;
        $user['tel'] = $post_tel;
        $user['type'] = $post_type;
        $user['status'] = $post_status;

        if ($user->save()) {
            $this->success('新增会员成功', '/admin/memberservice/index');
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
        $data_list = MemberServiceModel::get($id);
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
        $post_tel= $request->post('tel');
        $post_password= $request->post('password');
        $post_password=md5($post_password);
        $post_nickname= $request->post('nickname');
        $post_type= $request->post('type');
        $post_status= $request->post('status');
        if($post_id==''){
            $this->error('用户不能为空');
        }

        $user = MemberServiceModel::get($post_id);
        $user['password'] = $post_password;
        $user['nickname'] = $post_nickname;
        $user['tel'] = $post_tel;
        $user['type'] = $post_type;
        $user['status'] = $post_status;
        if ($user->save()) {
            $this->success('保存会员信息成功', '/admin/memberservice/index');
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
        $user = MemberServiceModel::get($id);
        if ($user) {
            $user->delete();
            $this->success('删除会员服务成功', '/admin/memberservice/index');
        } else {
            $this->error('您要删除的会员服务不存在');
        }
    }

}