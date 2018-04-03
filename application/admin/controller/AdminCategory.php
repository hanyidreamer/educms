<?php
/**
 * Created by PhpStorm.
 * User: tanzhenxing
 * Date: 2017/6/26
 * Time: 15:16
 */
namespace app\admin\controller;

use think\Request;
use app\base\model\AdminCategory as AdminCategoryModel;

class AdminCategory extends AdminBase
{
    /**
     * 管理员类型
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index()
    {
        $site_id = $this->site_id;
        // 找出列表数据
        $post_title = $this->request->param('title');
        $data = new AdminCategoryModel;
        if(!empty($post_title)){
            $data_list = $data->where(['status' => 1, 'title' => ['like','%'.$post_title.'%']])->select();
        }else{
            $data_list = $data->where(['site_id'=>$site_id,'status'=>1])->select();
        }
        $data_count = count($data_list);
        $this->assign('data_count',$data_count);

        $this->assign('data_list',$data_list);

        return $this->fetch($this->template_path);
    }

    /**
     * 新增管理员类型
     * @return mixed
     */
    public function create()
    {
        return $this->fetch($this->template_path);
    }

    /**
     * 保存数据
     * @param Request $request
     */
    public function save(Request $request)
    {
        $post_site_id = $request->param('site_id');
        $post_title = $request->param('title');
        $post_level = $request->param('level');
        $post_status = $request->param('status');
        if($post_title==''){
            $this->error('分类名称不能为空');
        }
        $data = new AdminCategoryModel;
        $data['site_id'] = $post_site_id;
        $data['title'] = $post_title;
        $data['level'] = $post_level;
        $data['status'] = $post_status;
        if ($data->save()) {
            $this->success('保存成功','/admin/admin_category/index');
        } else {
            $this->error('操作失败');
        }

    }

    /**
     * 编辑管理员类型
     * @param $id
     * @return mixed
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */
    public function edit($id)
    {
        // 获取网站信息
        $data_list = AdminCategoryModel::get($id);
        $this->assign('data',$data_list);

        return $this->fetch($this->template_path);
    }

    /**
     * 更新数据
     * @param Request $request
     * @throws \think\exception\DbException
     */
    public function update(Request $request)
    {
        $post_id = $request->post('id');
        $post_site_id = $request->post('site_id');
        $post_title = $request->post('title');
        $post_level = $request->post('level');
        $post_status= $request->post('status');
        if(empty($post_title)){
            $this->error('分类名称不能为空');
        }

        $user = AdminCategoryModel::get($post_id);
        $user['site_id'] = $post_site_id;
        $user['title'] = $post_title;
        $user['level'] = $post_level;
        $user['status'] = $post_status;
        if ($user->save()) {
            $this->success('保存成功', '/admin/admin_category/index');
        } else {
            $this->error('操作失败');
        }
    }

    /**
     * 删除数据
     * @param $id
     * @throws \think\exception\DbException
     */
    public function delete($id)
    {
        $data = AdminCategoryModel::get($id);
        if ($data) {
            $data->delete();
            $this->success('删除广告分类成功', '/admin/admin_category/index');
        } else {
            $this->error('您要删除的广告分类不存在');
        }
    }

}