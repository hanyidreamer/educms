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
use app\base\controller\TemplatePath;
use app\base\controller\Base;

class AgentCategory extends Base
{
    public function index(Request $request)
    {
        // 给当页面标题赋值
        $title = '代理分类';
        $this->assign('title',$title);

        // 当前方法不同终端的模板路径
        $controller_name = Request::instance()->controller();
        $action_name = Request::instance()->action();
        $template_path_info = new TemplatePath();
        $template_path = $template_path_info->admin_path($controller_name,$action_name);
        $template_public = $template_path_info->admin_public_path();
        $template_public_header = $template_public.'/header';
        $template_public_footer = $template_public.'/footer';
        $this->assign('public_header',$template_public_header);
        $this->assign('public_footer',$template_public_footer);

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

        return $this->fetch($template_path);
    }

    public function create()
    {
        // 新增
        $title = '新增';
        $this->assign('title',$title);

        // 当前方法不同终端的模板路径
        $controller_name = Request::instance()->controller();
        $action_name = Request::instance()->action();
        $template_path_info = new TemplatePath();
        $template_path = $template_path_info->admin_path($controller_name,$action_name);
        $template_public = $template_path_info->admin_public_path();
        $template_public_header = $template_public.'/header';
        $template_public_footer = $template_public.'/footer';
        $this->assign('public_header',$template_public_header);
        $this->assign('public_footer',$template_public_footer);

        // 获取网站分类列表
        $category_data = new AgentCategoryModel();
        $category = $category_data->where(['status'=>1])->select();
        $this->assign('category',$category);

        return $this->fetch($template_path);
    }

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

    public function edit($id)
    {
        $title = '编辑';
        $this->assign('title',$title);

        // 当前方法不同终端的模板路径
        $controller_name = Request::instance()->controller();
        $action_name = Request::instance()->action();
        $template_path_info = new TemplatePath();
        $template_path = $template_path_info->admin_path($controller_name,$action_name);
        $template_public = $template_path_info->admin_public_path();
        $template_public_header = $template_public.'/header';
        $template_public_footer = $template_public.'/footer';
        $this->assign('public_header',$template_public_header);
        $this->assign('public_footer',$template_public_footer);

        // 获取当前分类
        $category_data = AgentCategoryModel::get($id);
        $this->assign('data',$category_data);

        return $this->fetch($template_path);
    }

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