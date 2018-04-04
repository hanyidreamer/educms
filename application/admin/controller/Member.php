<?php
/**
 * Created by PhpStorm.
 * User: tzx
 * Date: 2016/10/26
 * Time: 11:55
 */
namespace app\admin\controller;

use think\Request;
use app\base\model\Member as MemberModel;
use app\base\controller\Upload;

class Member extends AdminBase
{
    /**
     * @param Request $request
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function index(Request $request)
    {
        $site_id = $this->site_id;

        // 找出列表数据
        $pages=15;
        $post_title = $request->param('title');
        $data = new MemberModel;
        if(!empty($post_title)){
            $data_list = $data->where(['status' => 1, 'title' => ['like','%'.$post_title.'%']])->order('id desc') -> paginate($pages);
        }else{
            $data_list = $data->where(['site_id'=>$site_id,'status'=>1])->order('id desc') -> paginate($pages);
        }
        $data_count = count($data_list);
        $this->assign('data_count',$data_count);

        $this->assign('data_list',$data_list);

        return $this->fetch($this->template_path);
    }

    /**
     * @return mixed
     */
    public function create()
    {
        return $this->fetch($this->template_path);
    }

    /**
     * @param Request $request
     */
    public function save(Request $request)
    {
        // 获取icon文件
        $file_icon = $request->file('icon');
        if(!empty($file_icon)){
            $local_icon = $file_icon->getInfo('tmp_name');
            $icon_filename = $file_icon->getInfo('name');
            $icon_file_info = new Upload();
            $post_icon=$icon_file_info->qcloud_file($local_icon,$icon_filename);
        }
        $post_site_id = $request->post('site_id');
        $post_username= $request->post('username');
        $post_password= $request->post('password');
        $post_nickname= $request->post('nickname');
        $post_tel= $request->post('tel');
        $post_sex= $request->post('sex');
        $post_weixinhao =$request->post('weixinhao');
        $post_qq=$request->post('qq');
        $post_email=$request->post('email');
        $post_status= $request->post('status');
        if($post_username=='' or $post_password==''){
            $this->error('会员的用户名和密码不能为空');
        }
        $user = new MemberModel;
        if(!empty($post_icon)){
        $user['icon'] = $post_icon;
    }
        if($post_password!==''){
            $post_password=md5($post_password);
            $user['password'] = $post_password;
        }
        $user['site_id'] = $post_site_id;
        $user['username'] = $post_username;
        $user['password'] = $post_password;
        $user['nickname'] = $post_nickname;
        $user['tel'] = $post_tel;
        $user['sex'] = $post_sex;
        $user['weixinhao'] = $post_weixinhao;
        $user['qq'] = $post_qq;
        $user['email'] = $post_email;
        $user['status'] = $post_status;

        if ($user->save()) {
            $this->success('新增会员成功', '/admin/member/index');
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
        $data_list = MemberModel::get($id);
        $this->assign('data_list',$data_list);
        return $this->fetch($this->template_path);
    }

    /**
     * @param Request $request
     * @throws \think\exception\DbException
     */
    public function update(Request $request)
    {
        // 获取icon文件
        $file_icon = $request->file('icon');
        if(!empty($file_icon)){
            $local_icon = $file_icon->getInfo('tmp_name');
            $icon_filename = $file_icon->getInfo('name');
            $icon_file_info = new Upload();
            $post_icon=$icon_file_info->qcloud_file($local_icon,$icon_filename);
        }

        $post_id= $request->post('id');
        $post_tel= $request->post('tel');
        $post_password= $request->post('password');
        if($post_password!==''){
            $post_password=md5($post_password);
        }
        $post_nickname= $request->post('nickname');
        $post_sex= $request->post('sex');
        $post_status= $request->post('status');
        $post_weixinhao =$request->post('weixinhao');
        $post_qq=$request->post('qq');
        $post_email=$request->post('email');
        if($post_id==''){
            $this->error('用户不能为空');
        }

        $user = MemberModel::get($post_id);
        if(!empty($post_icon)){
            $user['icon'] = $post_icon;
        }
        if($post_password!==''){
            $user['password'] = $post_password;
        }
        $user['nickname'] = $post_nickname;
        $user['sex'] = $post_sex;
        $user['tel'] = $post_tel;
        $user['weixinhao'] = $post_weixinhao;
        $user['qq'] = $post_qq;
        $user['email'] = $post_email;
        $user['status'] = $post_status;
        if ($user->save()) {
            $this->success('保存会员信息成功', '/admin/member/index');
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
        $user = MemberModel::get($id);
        if ($user) {
            $user->delete();
            $this->success('删除会员成功', '/admin/member/index');
        } else {
            $this->error('您要删除的会员不存在');
        }
    }

}