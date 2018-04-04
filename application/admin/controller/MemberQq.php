<?php
/**
 * Created by PhpStorm.
 * User: tanzhenxing
 * Date: 2017/6/26
 * Time: 15:25
 */
namespace app\admin\controller;

use think\Request;
use app\base\model\MemberQq as MemberQqModel;
use app\base\model\Member;

class MemberQq extends AdminBase
{
    /**
     * @param Request $request
     * @return mixed
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index(Request $request)
    {
        $site_id = $this->site_id;
        // 找出列表数据
        $post_username = $request->param('username');
        $data = new MemberQqModel;
        if(!empty($post_username)){
            $data_list = $data->where([
                'site_id'=>$site_id,
                'status' => 1,
                'username' => ['like','%'.$post_username.'%']
            ])
                ->select();
        }else{
            $data_list = $data->where(['site_id'=>$site_id,'status'=>1])->select();
        }

        foreach ($data_list as $data){
            $category_id = $data['category_id'];
            $admin_category = Member::get($category_id);
            $category_title = $admin_category['title'];
            $data['category_title'] = $category_title;
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
        $post_site_id = $request->param('site_id');
        $post_category_id = $request->param('category_id');
        $post_title = $request->param('title');
        $post_url = $request->param('url');
        $post_desc = $request->param('desc');
        $post_status = $request->param('status');

        if(empty($post_title)){
            $this->error('接口名称不能为空');
        }


        $data = new MemberQqModel;
        $data['site_id'] = $post_site_id;
        $data['category_id'] = $post_category_id;
        $data['title'] = $post_title;
        $data['url'] = $post_url;
        $data['desc'] = $post_desc;

        $data['status'] = $post_status;
        if ($data->save()) {
            $this->success('保存成功','/admin/api/index');
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
        // 获取信息
        $data_list = MemberQqModel::get($id);
        $this->assign('data',$data_list);

        return $this->fetch($this->template_path);
    }

    /**
     * @param Request $request
     * @throws \think\exception\DbException
     */
    public function update(Request $request)
    {
        $post_id = $request->post('id');
        $post_site_id = $request->post('site_id');
        $post_category_id = $request->post('category_id');
        $post_title = $request->post('title');
        $post_url = $request->param('url');
        $post_desc = $request->param('desc');
        $post_status = $request->param('status');
        if(empty($post_title)){
            $this->error('名称不能为空');
        }

        $user = MemberQqModel::get($post_id);
        $user['site_id'] = $post_site_id;
        $user['category_id'] = $post_category_id;

        $user['title'] = $post_title;
        $user['url'] = $post_url;
        $user['desc'] = $post_desc;
        $user['status'] = $post_status;

        if ($user->save()) {
            $this->success('更新成功', '/admin/api/index');
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
        $data = MemberQqModel::get($id);
        if ($data) {
            $data->delete();
            $this->success('删除广告成功', '/admin/api/index');
        } else {
            $this->error('您要删除的广告不存在');
        }
    }

}