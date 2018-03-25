<?php
/**
 * Created by PhpStorm.
 * User: tanzhenxing
 * Date: 2017/6/26
 * Time: 15:18
 */
namespace app\admin\controller;

use think\Request;
use app\base\model\Area as AreaModel;
use app\base\controller\TemplatePath;
use app\base\controller\Base;

class Area extends Base
{
    public function index(Request $request)
    {
        // 给当页面标题赋值
        $title = '广告列表';
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
        $data = new AreaModel;
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
        $title = '类目';
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

        return $this->fetch($template_path);
    }

    public function save(Request $request)
    {
        $post_sort = $request->param('sort');
        $post_title = $request->param('title');
        $post_english_title = $request->param('english_title');
        $post_status = $request->param('status');
        if(empty($post_title)){
            $this->error('名称不能为空');
        }
        $data = new AreaModel;
        $data['title'] = $post_title;
        $data['sort'] = $post_sort;
        $data['english_title'] = $post_english_title;
        $data['status'] = $post_status;
        if ($data->save()) {
            $this->success('保存成功','/admin/area/index');
        } else {
            $this->error('操作失败');
        }
    }

    public function edit($id)
    {
        $title = '编辑类目';
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

        // 获取广告信息
        $data_list = AreaModel::get($id);
        $this->assign('data',$data_list);


        return $this->fetch($template_path);
    }

    public function update(Request $request)
    {
        $post_id = $request->post('id');
        $post_sort = $request->post('sort');
        $post_title = $request->post('title');
        $post_english_title = $request->post('english_title');
        $post_status= $request->post('status');
        if(empty($post_title)){
            $this->error('名称不能为空');
        }

        $user = AreaModel::get($post_id);
        $user['sort'] = $post_sort;
        if(!empty($post_title)){
            $user['title'] = $post_title;
        }
        $user['english_title'] = $post_english_title;
        $user['status'] = $post_status;

        if ($user->save()) {
            $this->success('更新成功', '/admin/area/index');
        } else {
            $this->error('操作失败');
        }

    }

    public function delete($id)
    {
        $data = AreaModel::get($id);
        if ($data) {
            $data->delete();
            $this->success('删除成功', '/admin/area/index');
        } else {
            $this->error('删除失败');
        }
    }
}