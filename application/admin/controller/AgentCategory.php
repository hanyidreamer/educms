<?php
/**
 * Created by PhpStorm.
 * User: tanzhenxing
 * Date: 2017/6/26
 * Time: 15:17
 */
namespace app\admin\controller;

use think\Request;
use app\base\model\AgentCategory as AgentCategoryModel;

class AgentCategory extends AdminBase
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
        // 找出列表数据
        $post_title = $request->param('title');
        $data = new AgentCategoryModel;
        if(!empty($post_title)){
            $data_list = $data->where(['status' => 1, 'title' => ['like','%'.$post_title.'%']])->select();
        }else{
            $data_list = $data->where(['status'=>1])->select();
        }
        $data_count = count($data_list);
        $this->assign('data_count',$data_count);

        $this->assign('data_list',$data_list);

        return $this->fetch($this->template_path);
    }

    /**
     * @return mixed
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function create()
    {
        // 获取网站分类列表
        $category_data = new AgentCategoryModel();
        $category = $category_data->where(['status'=>1])->select();
        $this->assign('category',$category);

        return $this->fetch($this->template_path);
    }

    /**
     * @param Request $request
     */
    public function save(Request $request)
    {
        $post_level = $request->param('level');
        $post_title = $request->param('title');
        $post_status = $request->param('status');

        if(empty($post_title)){
            $this->error('标题不能为空');
        }

        $data = new AgentCategoryModel;
        $data['title'] = $post_title;
        $data['level'] = $post_level;
        $data['status'] = $post_status;
        if ($data->save()) {
            $this->success('保存成功','/admin/agent_category/index');
        } else {
            $this->error('操作失败');
        }
    }

    /**
     * @param $id
     * @return mixed
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */
    public function edit($id)
    {
        // 获取当前分类
        $category_data = AgentCategoryModel::get($id);
        $this->assign('data',$category_data);

        return $this->fetch($this->template_path);
    }

    /**
     * @param Request $request
     * @throws \think\exception\DbException
     */
    public function update(Request $request)
    {
        $post_id = $request->post('id');
        $post_title = $request->post('title');
        $post_level = $request->post('level');
        $post_status= $request->post('status');
        if(empty($post_title)){
            $this->error('标题不能为空');
        }

        $user = AgentCategoryModel::get($post_id);
        if(!empty($post_title)){
            $user['title'] = $post_title;
        }
        $user['level'] = $post_level;
        $user['status'] = $post_status;

        if ($user->save()) {
            $this->success('更新成功', '/admin/agent_category/index');
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
        $data = AgentCategoryModel::get($id);
        if ($data) {
            $data->delete();
            $this->success('删除成功', '/admin/agent_category/index');
        } else {
            $this->error('删除失败');
        }
    }
}