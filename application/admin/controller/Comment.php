<?php
/**
 * Created by PhpStorm.
 * User: tanzhenxing
 * Date: 2017/5/6
 * Time: 15:26
 */
namespace app\admin\controller;

use think\Request;
use app\common\model\Comment as ModelComment;
use app\common\model\Member;

class Comment extends AdminBase
{
    /**
     * @param Request $request
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function index(Request $request)
    {
        $site_id = $this->site_id;
        $post_username= $request->post('title');
        if($post_username==!''){
            $data_sql['title'] =  ['like','%'.$post_username.'%'];
        }

        // 分页数量
        $pages=15;
        $data_sql['site_id'] = $site_id;
        $data_sql['status'] = 1;
        $comment_info = new ModelComment();
        $data_list = $comment_info->where($data_sql) -> paginate($pages);
        $data_count = count($data_list);

        foreach($data_list as $data)
        {
            $mid = $data->mid;
            $member_info = Member::get($mid);
            $member_username=$member_info['username'];
            $data->username=$member_username;
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
        $member_info_sql['status'] = 1;
        $my_member_info = new Member();
        $member_info = $my_member_info->where($member_info_sql) ->limit(50) ->order('id desc') -> select();
        $this->assign('member_info',$member_info);

        return $this->fetch();
    }

    /**
     * @param Request $request
     */
    public function insert(Request $request)
    {
        $post_site_id=$request->post('site_id');
        $post_mid=$request->post('mid');
        $post_comment=$request->post('comment');
        $post_type= $request->post('type');
        $post_status= $request->post('status');
        if($post_site_id=='' or $post_mid==''){
            $this->error('会员的用户名和密码不能为空');
        }
        $user = new ModelComment;
        $user['site_id'] = $post_site_id;
        $user['mid'] = $post_mid;
        $user['type'] = $post_type;
        $user['comment'] = $post_comment;
        $user['status'] = $post_status;

        if ($user->save()) {
           $this->success('新增评论成功', '/admin/comment/index');
        } else {
            $this->error('操作失败');
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
        $member_info_sql['status'] = 1;
        $my_member_info = new Member();
        $member_info = $my_member_info->where($member_info_sql) ->limit(50) ->order('id desc') -> select();
        $this->assign('member_info',$member_info);

        $data_list = ModelComment::get($id);
        $this->assign('data_list',$data_list);

        $mid = $data_list['mid'];
        $this->assign('mid',$mid);


        return $this->fetch();
    }

    /**
     * @param Request $request
     * @throws \think\exception\DbException
     */
    public function save(Request $request)
    {
        $post_id= $request->post('id');
        $post_site_id= $request->post('site_id');
        $post_mid= $request->post('mid');
        $post_comment=$request->post('comment');
        $post_type= $request->post('type');
        $post_status= $request->post('status');
        if($post_site_id==''){
            $this->error('网站id不能为空');
        }

        $user = ModelComment::get($post_id);
        $user['site_id'] = $post_site_id;
        $user['mid'] = $post_mid;
        $user['comment'] = $post_comment;
        $user['type'] = $post_type;
        $user['status'] = $post_status;
        if ($user->save()) {
            $this->success('保存点评信息成功', '/admin/comment/index');
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
        $user = ModelComment::get($id);
        if ($user) {
            $user->delete();
             $this->success('删除评论成功', '/admin/comment/index');
        } else {
            $this->error('您要删除的会员不存在');
        }
    }
}