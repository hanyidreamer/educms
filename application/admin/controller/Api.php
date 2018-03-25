<?php
/**
 * Created by PhpStorm.
 * User: tanzhenxing
 * Date: 2016/11/2
 * Time: 15:05
 */
namespace app\admin\controller;

use think\Request;
use app\base\model\Api as ApiModel;
use app\base\model\ApiCategory;
use app\base\controller\TemplatePath;
use app\base\controller\Base;
use app\base\controller\SiteId;

class Api extends Base
{
    public function index(Request $request)
    {
        $title = 'API列表';
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

        // 找出列表数据
        $post_username = $request->param('username');
        $data = new ApiModel;
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
            $admin_category = ApiCategory::get($category_id);
            $category_title = $admin_category['title'];
            $data['category_title'] = $category_title;
        }

        $data_count = count($data_list);
        $this->assign('data_count',$data_count);

        $this->assign('data_list',$data_list);

        return $this->fetch($template_path);
    }

    public function create()
    {
        // 新增
        $title = '添加接口';
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

        // 获取分类列表
        $category_data = new ApiCategory();
        $category = $category_data->where(['site_id'=>$site_id])->select();
        $this->assign('category',$category);

        return $this->fetch($template_path);
    }

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


        $data = new ApiModel;
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

    public function edit($id)
    {
        $title = '编辑接口';
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

        // 获取当前分类id
        $categorg_id_info = ApiModel::get($id);
        $categorg_id = $categorg_id_info['category_id'];

        // 获取信息
        $data_list = ApiModel::get($id);
        $this->assign('data',$data_list);

        // 获取网站分类列表
        $category_data = new ApiCategory();
        $category = $category_data->where(['site_id'=>$site_id])->select();
        $this->assign('category',$category);

        $my_categorg_data = ApiCategory::get($categorg_id);
        $my_categorg_title = $my_categorg_data['title'];
        $this->assign('my_category_id',$categorg_id);
        $this->assign('my_categorg_title',$my_categorg_title);

        return $this->fetch($template_path);
    }

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

        $user = ApiModel::get($post_id);
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

    public function delete($id)
    {
        $data = ApiModel::get($id);
        if ($data) {
            $data->delete();
            $this->success('删除广告成功', '/admin/api/index');
        } else {
            $this->error('您要删除的广告不存在');
        }
    }

}