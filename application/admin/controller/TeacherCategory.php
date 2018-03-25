<?php
/**
 * Created by PhpStorm.
 * User: tanzhenxing
 * Date: 2017/6/26
 * Time: 15:34
 */
namespace app\admin\controller;

use think\Request;
use app\base\model\TeacherCategory as TeacherCategoryModel;
use app\base\controller\TemplatePath;
use app\base\controller\Base;
use app\base\controller\SiteId;

class TeacherCategory extends Base
{
    public function index(Request $request)
    {
        $title = '广告分类列表';
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

        // 获取网站id
        $get_domain = Request::instance()->server('HTTP_HOST');
        $this->assign('domain',$get_domain);
        $site_id_data = new SiteId();
        $site_id = $site_id_data->info($get_domain);

        // 找出广告列表数据
        $post_title = $request->param('title');
        $data = new TeacherCategoryModel;
        if(!empty($post_title)){
            $data_list = $data->where([
                'site_id'=>$site_id,
                'status' => 1,
                'title' => ['like','%'.$post_title.'%']
            ])
                ->select();
        }else{
            $data_list = $data->where(['site_id'=>$site_id,'status'=>1])->select();
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

        // 获取网站id
        $get_domain = Request::instance()->server('HTTP_HOST');
        $this->assign('domain',$get_domain);
        $site_id_data = new SiteId();
        $site_id = $site_id_data->info($get_domain);
        $this->assign('site_id',$site_id);

        return $this->fetch($template_path);
    }

    public function save(Request $request)
    {
        $post_site_id = $request->param('site_id');
        $post_title = $request->param('title');
        $post_desc = $request->param('desc');
        $post_keywords = $request->param('keywords');
        $post_status = $request->param('status');
        if($post_title==''){
            $this->error('分类名称不能为空');
        }
        $data = new TeacherCategoryModel;
        $data['site_id'] = $post_site_id;
        $data['title'] = $post_title;
        $data['desc'] = $post_desc;
        $data['keywords'] = $post_keywords;
        $data['status'] = $post_status;
        if ($data->save()) {
            $this->success('保存成功','/admin/teacher_category/index');
        } else {
            $this->error('操作失败');
        }

    }

    public function edit($id)
    {
        $title = '编辑广告分类';
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

        // 获取网站id
        $get_domain = Request::instance()->server('HTTP_HOST');
        $this->assign('domain',$get_domain);
        $site_id_data = new SiteId();
        $site_id = $site_id_data->info($get_domain);
        $this->assign('site_id',$site_id);

        // 获取网站信息
        $data_list = TeacherCategoryModel::get($id);
        $this->assign('data',$data_list);

        return $this->fetch($template_path);
    }

    public function update(Request $request)
    {
        $post_id = $request->post('id');
        $post_site_id = $request->post('site_id');
        $post_title = $request->post('title');
        $post_keywords = $request->post('keywords');
        $post_desc = $request->post('desc');
        $post_status= $request->post('status');
        if(empty($post_title)){
            $this->error('分类名称不能为空');
        }

        $user = TeacherCategoryModel::get($post_id);
        $user['site_id'] = $post_site_id;
        $user['title'] = $post_title;
        $user['keywords'] = $post_keywords;
        $user['desc'] = $post_desc;
        $user['status'] = $post_status;
        if ($user->save()) {
            $this->success('保存成功', '/admin/teacher_category/index');
        } else {
            $this->error('操作失败');
        }
    }

    public function delete($id)
    {
        $data = TeacherCategoryModel::get($id);
        if ($data) {
            $data->delete();
            $this->success('删除分类成功', '/admin/teacher_category/index');
        } else {
            $this->error('您要删除的分类不存在');
        }
    }
}