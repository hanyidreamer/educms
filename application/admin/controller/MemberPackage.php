<?php
/**
 * Created by PhpStorm.
 * User: tzx
 * Date: 2016/10/26
 * Time: 14:45
 */
namespace app\admin\controller;

use think\Request;
use app\common\model\Member as MemberModel;
use app\common\model\MemberServicePackage;

class MemberPackage extends Base
{
    public function index(Request $request)
    {
        $post_username= $request->post('name');
        if($post_username==!''){
            $data_sql['name'] =  ['like','%'.$post_username.'%'];
        }
        $data_sql['status'] = 1;
        $member_service_data = new MemberServicePackage();
        $data_list = $member_service_data->where($data_sql) -> select();
        $data_count = count($data_list);
        $this->assign('data_list',$data_list);
        $this->assign('data_count',$data_count);

        return $this->fetch();
    }

    public function add()
    {
        return $this->fetch();
    }

    public function insert(Request $request)
    {
        $post_name= $request->post('name');
        $post_en_name= $request->post('en_name');
        $post_status= $request->post('status');
        if($post_name==''){
            $this->error('套餐名称不能为空');
        }
        $user = new MemberServicePackage;
        $user['name'] = $post_name;
        $user['en_name'] = $post_en_name;
        $user['status'] = $post_status;

        if ($user->save()) {
            $this->success('新增套餐成功', '/admin/memberpackage/index');
        } else {
            $this->error('操作失败');
        }

    }

    public function edit($id)
    {
        $service_list = MemberServicePackage::get(['status'=>1]);
        $this->assign('service_list',$service_list);

        $data_list = MemberModel::get($id);
        $this->assign('data_list',$data_list);

        return $this->fetch();
    }

    public function save(Request $request)
    {
        $post_id= $request->post('id');
        $post_status= $request->post('status');
        $post_en_name= $request->post('en_name');
        if($post_id==''){
            $this->error('用户不能为空');
        }
        $user = MemberServicePackage::get($post_id);
        $user['en_name'] = $post_en_name;
        $user['status'] = $post_status;
        if ($user->save()) {
            $this->success('保存套餐信息成功', '/admin/memberpackage/index');
        } else {
            $this->error('操作失败');
        }

    }

    public function delete($id)
    {
        $user = MemberServicePackage::get($id);
        if ($user) {
            $user->delete();
            $this->success('删除套餐成功', '/admin/memberpackage/index');
        } else {
            $this->error('您要删除的套餐不存在');
        }
    }

}