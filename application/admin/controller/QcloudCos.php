<?php
/**
 * Created by PhpStorm.
 * User: tanzhenxing
 * Date: 2016/11/19
 * Time: 16:20
 */
namespace app\admin\controller;

use think\Request;
use app\base\controller\TemplatePath;
use app\base\controller\Base;
use app\base\model\QcloudCos as QcloudCosModel;

class QcloudCos extends Base
{
    public function index(Request $request)
    {
        $title='腾讯云存储';
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

        $post_name= $request->post('name');
        if($post_name==!''){
            $data_sql['name'] =  ['like','%'.$post_name.'%'];
        }

        $qcloud_cos_data = new QcloudCosModel();
        $data_list = $qcloud_cos_data->where(['status'=>1]) -> select();
        $data_count = count($data_list);

        $this->assign('data_list',$data_list);
        $this->assign('data_count',$data_count);

        return $this->fetch($template_path);
    }

    public function add()
    {
        $title='添加 腾讯云存储';
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

    public function insert(Request $request)
    {
        $post_name= $request->post('name');
        $post_bucket_name= $request->post('bucket_name');
        $post_app_id= $request->post('app_id');
        $post_secret_id= $request->post('secret_id');
        $post_secret_key= $request->post('secret_key');
        $post_url= $request->post('url');
        $post_folder= $request->post('folder');
        $post_status= $request->post('status');
        if($post_name==''){
            $this->error('用途不能为空');
        }
        $user = new QcloudCosModel;
        $user['name'] = $post_name;
        $user['bucket_name'] = $post_bucket_name;
        $user['app_id'] = $post_app_id;
        $user['secret_id'] = $post_secret_id;
        $user['secret_key'] = $post_secret_key;
        $user['url'] = $post_url;
        $user['folder'] = $post_folder;
        $user['status'] = $post_status;

        if ($user->save()) {
            $this->success('新增腾讯云存储信息成功', '/admin/qcloudcos/index');
        } else {
            $this->error('操作失败');
        }

    }

    public function edit($id)
    {
        $title='添加 腾讯云存储';
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

        $data_list = QcloudCosModel::get($id);

        $this->assign('data_list',$data_list);

        return $this->fetch($template_path);
    }

    public function save(Request $request)
    {
        $post_id= $request->post('id');
        $post_status= $request->post('status');
        $post_name= $request->post('name');
        $post_bucket_name= $request->post('bucket_name');
        $post_app_id= $request->post('app_id');
        $post_secret_id= $request->post('secret_id');
        $post_secret_key= $request->post('secret_key');
        $post_url= $request->post('url');
        $post_folder= $request->post('folder');
        if($post_id==''){
            $this->error('腾讯云存储id不能为空');
        }
        $user = QcloudCosModel::get($post_id);
        $user['name'] = $post_name;
        $user['bucket_name'] = $post_bucket_name;
        $user['app_id'] = $post_app_id;
        $user['secret_id'] = $post_secret_id;
        $user['secret_key'] = $post_secret_key;
        $user['url'] = $post_url;
        $user['folder'] = $post_folder;
        $user['status'] = $post_status;
        if ($user->save()) {
            $this->success('保存腾讯云存储信息成功', '/admin/qcloudcos/index');
        } else {
            $this->error('操作失败');
        }

    }

    public function delete($id)
    {
        $user = QcloudCosModel::get($id);
        if ($user) {
            $user->delete();
            $this->success('删除腾讯云存储信息成功', '/admin/qcloudcos/index');
        } else {
            $this->error('您要删除的套餐不存在');
        }
    }

}