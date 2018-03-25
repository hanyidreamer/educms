<?php
/**
 * Created by PhpStorm.
 * User: tanzhenxing
 * Date: 2017/6/26
 * Time: 15:16
 */
namespace app\admin\controller;

use think\Request;
use app\base\model\Admin as AdminModel;
use app\base\model\AdminCategory;
use app\base\controller\TemplatePath;
use app\base\controller\Base;
use app\base\controller\SiteId;
use app\base\controller\Upload;

class Admin extends Base
{
    public function index(Request $request)
    {
        $title = '管理员列表';
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
        $data = new AdminModel;
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
            $admin_category = AdminCategory::get($category_id);
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
        $title = '添加管理员';
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
        $category_data = new AdminCategory();
        $category = $category_data->where(['site_id'=>$site_id])->select();
        $this->assign('category',$category);

        return $this->fetch($template_path);
    }

    public function save(Request $request)
    {
        // 获取 略缩图 icon文件
        $post_icon = '';
        $file_thumb = $request->file('icon');
        if(!empty($file_thumb)){
            $local_thumb = $file_thumb->getInfo('tmp_name');
            $thumb_filename = $file_thumb->getInfo('name');
            $thumb_file_info = new Upload();
            $post_icon = $thumb_file_info->qcloud_file($local_thumb,$thumb_filename);
        }

        $post_site_id = $request->param('site_id');
        $post_category_id = $request->param('category_id');
        $post_username = $request->param('username');
        $post_password = $request->param('password');
        $post_password = md5($post_password);
        $post_nickname = $request->param('nickname');
        $post_tel = $request->param('tel');
        $post_qq = $request->param('qq');
        $post_weixinhao = $request->param('weixinhao');
        $post_email = $request->param('email');
        $post_ip = $request->ip();
        $post_status = $request->param('status');

        if(empty($post_username)){
            $this->error('用户名不能为空');
        }

        $admin_username = AdminModel::get(['username'=>$post_username]);
        if(!empty($admin_username)){
            $this->error('您填写的用户名已经被注册，请更换');
        }
        $admin_tel = AdminModel::get(['tel'=>$post_tel]);
        if(!empty($admin_tel)){
            $this->error('您填写的手机号码已经被注册，请更换');
        }


        $data = new AdminModel;
        $data['icon'] = $post_icon;
        $data['site_id'] = $post_site_id;
        $data['category_id'] = $post_category_id;
        $data['username'] = $post_username;
        $data['password'] = $post_password;
        $data['nickname'] = $post_nickname;
        $data['tel'] = $post_tel;
        $data['qq'] = $post_qq;
        $data['weixinhao'] = $post_weixinhao;
        $data['email'] = $post_email;
        $data['ip'] = $post_ip;

        $data['status'] = $post_status;
        if ($data->save()) {
            $this->success('保存成功','/admin/admin/index');
        } else {
            $this->error('操作失败');
        }
    }

    public function edit($id)
    {
        $title = '编辑管理员';
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
        $categorg_id_info = AdminModel::get($id);
        $categorg_id = $categorg_id_info['category_id'];

        // 获取信息
        $data_list = AdminModel::get($id);
        $this->assign('data',$data_list);

        // 获取网站分类列表
        $category_data = new AdminCategory();
        $category = $category_data->where(['site_id'=>$site_id])->select();
        $this->assign('category',$category);

        $my_categorg_data = AdminCategory::get($categorg_id);
        $my_categorg_title = $my_categorg_data['title'];
        $this->assign('my_category_id',$categorg_id);
        $this->assign('my_categorg_title',$my_categorg_title);

        return $this->fetch($template_path);
    }

    public function update(Request $request)
    {
        // 获取 分类略缩图 thumb文件
        $post_icon = '';
        $file_thumb = $request->file('icon');
        if(!empty($file_thumb)){
            $local_thumb = $file_thumb->getInfo('tmp_name');
            $thumb_filename = $file_thumb->getInfo('name');
            $thumb_file_info = new Upload();
            $post_icon = $thumb_file_info->qcloud_file($local_thumb,$thumb_filename);
        }

        $post_id = $request->post('id');
        $post_site_id = $request->post('site_id');
        $post_category_id = $request->post('category_id');
        $post_username = $request->post('username');
        $post_password = $request->post('password');
        $post_password = md5($post_password);
        $post_nickname = $request->param('nickname');
        $post_tel = $request->param('tel');
        $post_qq = $request->param('qq');
        $post_weixinhao = $request->param('weixinhao');
        $post_email = $request->param('email');
        $post_ip = $request->ip();
        $post_status = $request->param('status');
        if(empty($post_username)){
            $this->error('用户名不能为空');
        }

        $user = AdminModel::get($post_id);
        if(!empty($post_icon)){
            $user['icon'] = $post_icon;
        }
        $user['site_id'] = $post_site_id;
        $user['category_id'] = $post_category_id;
        if(!empty($post_username)){
            $user['username'] = $post_username;
        }
        $user['password'] = $post_password;
        $user['nickname'] = $post_nickname;
        $user['tel'] = $post_tel;
        $user['qq'] = $post_qq;
        $user['nickname'] = $post_nickname;
        $user['weixinhao'] = $post_weixinhao;
        $user['email'] = $post_email;
        $user['ip'] = $post_ip;
        $user['status'] = $post_status;

        if ($user->save()) {
            $this->success('更新成功', '/admin/admin/index');
        } else {
            $this->error('操作失败');
        }

    }

    public function delete($id)
    {
        $data = AdminModel::get($id);
        if ($data) {
            $data->delete();
            $this->success('删除广告成功', '/admin/admin/index');
        } else {
            $this->error('您要删除的广告不存在');
        }
    }

}