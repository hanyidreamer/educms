<?php
/**
 * Created by PhpStorm.
 * User: tanzhenxing
 * Date: 2017/6/26
 * Time: 15:30
 */
namespace app\admin\controller;

use think\Request;
use think\Session;
use app\base\controller\Base;
use app\base\controller\TemplatePath;
use app\base\model\Site as SiteModel;
use app\base\model\Admin;
use app\base\controller\Upload;

class Site extends Base
{
    public function index()
    {
        // 给当页面标题赋值
        $title = '网站基本信息设置';
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

        // 获取admin_id
        $admin_username = Session::get('username');
        $site_admin_data = Admin::get(['username'=>$admin_username]);
        $admin_id = $site_admin_data['id'];

        // 获取网站id
        $get_domain = Request::instance()->server('HTTP_HOST');
        $this->assign('domain',$get_domain);


        $site_info_sql['admin_id'] = $admin_id;
        $site_info_sql['status'] = 1;
        $site_data = new SiteModel();
        $site_info = $site_data->where($site_info_sql) -> select();
        $site_count = count($site_info);

        foreach ($site_info as $data)
        {
            $admin_id = $data['admin_id'];
            $site_admin_data = Admin::get($admin_id);
            $site_admin = $site_admin_data['nickname'];
            $data->site_admin = $site_admin;
        }

        $this->assign('site_info',$site_info);
        $this->assign('site_count',$site_count);

        return $this->fetch($template_path);
    }


    public function create()
    {
        //增加网站
        $title = '新增网站';
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
        // 获取icon文件
        $file_icon = $request->file('icon');
        if(!empty($file_icon)){
            $local_icon = $file_icon->getInfo('tmp_name');
            $icon_filename = $file_icon->getInfo('name');
            $icon_file_info = new Upload();
            $post_icon=$icon_file_info->qcloud_file($local_icon,$icon_filename);
        }

        // 获取logo文件
        $file_logo = $request->file('logo');
        if(!empty($file_logo)){
            $local_logo = $file_logo->getInfo('tmp_name');
            $logo_filename = $file_logo->getInfo('name');
            $logo_file_info = new Upload();
            $post_logo=$logo_file_info->qcloud_file($local_logo,$logo_filename);
        }

        // 获取 分类略缩图 thumb文件
        $file_thumb = $request->file('thumb');
        if(!empty($file_thumb)){
            $local_thumb = $file_thumb->getInfo('tmp_name');
            $thumb_filename = $file_thumb->getInfo('name');
            $thumb_file_info = new Upload();
            $post_thumb=$thumb_file_info->qcloud_file($local_thumb,$thumb_filename);
        }

        // 获取 二维码图片文件
        $file_qrcode = $request->file('qrcode');
        if(!empty($file_qrcode)){
            $local_qrcode = $file_qrcode->getInfo('tmp_name');
            $qrcode_filename = $file_qrcode->getInfo('name');
            $qrcode_file_info = new Upload();
            $post_qrcode = $qrcode_file_info->qcloud_file($local_qrcode,$qrcode_filename);
        }


        $post_domain = $request->post('domain');
        $post_admin_id = $request->post('admin_id');
        $post_title= $request->post('title');
        $post_desc = $request->post('desc');
        $post_keywords= $request->post('keywords');

        $post_home_title = $request->post('home_title');
        $post_template_id = $request->post('template_id');
        $post_stats = $request->post('stats');
        $post_icp = $request->post('icp');
        $post_copyright = $request->post('copyright');

        $post_tel = $request->post('tel');
        $post_phone = $request->post('phone');
        $post_qq = $request->post('qq');
        $post_email = $request->post('email');
        $post_address = $request->post('address');

        $post_status= $request->post('status');
        if($post_domain=='' or $post_title==''){
            $this->error('网站域名和名称不能为空');
        }
        $user = new SiteModel;
        $user['domain'] = $post_domain;
        $user['title'] = $post_title;
        $user['desc'] = $post_desc;
        $user['keywords'] = $post_keywords;
        if(!empty($post_icon)){
            $user['icon'] = $post_icon;
        }
        if(!empty($post_thumb)){
            $user['thumb'] = $post_thumb;
        }
        if(!empty($post_logo)){
            $user['logo'] = $post_logo;
        }
        if(!empty($post_qrcode)){
            $user['qrcode'] = $post_qrcode;
        }

        $user['home_title'] = $post_home_title;
        $user['template_id'] = $post_template_id;
        $user['stats'] = $post_stats;
        $user['icp'] = $post_icp;
        $user['copyright'] = $post_copyright;
        $user['admin_id'] = $post_admin_id;

        $user['tel'] = $post_tel;
        $user['phone'] = $post_phone;
        $user['qq'] = $post_qq;
        $user['email'] = $post_email;
        $user['address'] = $post_address;

        $user['status'] = $post_status;

        if ($user->save()) {
            $this->success('新增网站成功', '/admin/site/index');
        } else {
            $this->error('操作失败');
        }
    }


    public function read($id)
    {
        // 查看页
    }


    public function edit($id)
    {
        // 编辑网站
        $title = '编辑网站';
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

        // 获取网站信息
        $site_info = SiteModel::get($id);
        $this->assign('site',$site_info);

        return $this->fetch($template_path);
    }


    public function update(Request $request, $id)
    {
        // 获取icon文件
        $file_icon = $request->file('icon');
        if(!empty($file_icon)){
            $local_icon = $file_icon->getInfo('tmp_name');
            $icon_filename = $file_icon->getInfo('name');
            $icon_file_info = new Upload();
            $post_icon=$icon_file_info->qcloud_file($local_icon,$icon_filename);
        }

        // 获取logo文件
        $file_logo = $request->file('logo');
        if(!empty($file_logo)){
            $local_logo = $file_logo->getInfo('tmp_name');
            $logo_filename = $file_logo->getInfo('name');
            $logo_file_info = new Upload();
            $post_logo=$logo_file_info->qcloud_file($local_logo,$logo_filename);
        }

        // 获取 分类略缩图 thumb文件
        $file_thumb = $request->file('thumb');
        if(!empty($file_thumb)){
            $local_thumb = $file_thumb->getInfo('tmp_name');
            $thumb_filename = $file_thumb->getInfo('name');
            $thumb_file_info = new Upload();
            $post_thumb=$thumb_file_info->qcloud_file($local_thumb,$thumb_filename);
        }

        // 获取 二维码图片文件
        $file_qrcode = $request->file('qrcode');
        if(!empty($file_qrcode)){
            $local_qrcode = $file_qrcode->getInfo('tmp_name');
            $qrcode_filename = $file_qrcode->getInfo('name');
            $qrcode_file_info = new Upload();
            $post_qrcode = $qrcode_file_info->qcloud_file($local_qrcode,$qrcode_filename);
        }


        $post_domain = $request->post('domain');
        $post_admin_id = $request->post('admin_id');
        $post_title= $request->post('title');
        $post_desc = $request->post('desc');
        $post_keywords= $request->post('keywords');

        $post_home_title = $request->post('home_title');
        $post_template_id = $request->post('template_id');
        $post_stats = $request->post('stats');
        $post_icp = $request->post('icp');
        $post_copyright = $request->post('copyright');

        $post_tel = $request->post('tel');
        $post_phone = $request->post('phone');
        $post_qq = $request->post('qq');
        $post_email = $request->post('email');
        $post_address = $request->post('address');

        $post_status= $request->post('status');
        if($post_domain=='' or $post_title==''){
            $this->error('网站域名和名称不能为空');
        }

        $user = SiteModel::get($id);
        $user['domain'] = $post_domain;
        $user['title'] = $post_title;
        $user['desc'] = $post_desc;
        $user['keywords'] = $post_keywords;
        if(!empty($post_icon)){
            $user['icon'] = $post_icon;
        }
        if(!empty($post_thumb)){
            $user['thumb'] = $post_thumb;
        }
        if(!empty($post_logo)){
            $user['logo'] = $post_logo;
        }
        if(!empty($post_qrcode)){
            $user['qrcode'] = $post_qrcode;
        }

        $user['home_title'] = $post_home_title;
        $user['template_id'] = $post_template_id;
        $user['stats'] = $post_stats;
        $user['icp'] = $post_icp;
        $user['copyright'] = $post_copyright;
        $user['admin_id'] = $post_admin_id;

        $user['tel'] = $post_tel;
        $user['phone'] = $post_phone;
        $user['qq'] = $post_qq;
        $user['email'] = $post_email;
        $user['address'] = $post_address;
        $user['status'] = $post_status;


        if ($user->save()) {
            $this->success('更新网站成功', '/admin/site/index');
        } else {
            $this->error('操作失败');
        }

    }


    public function delete($id)
    {
        $user = SiteModel::get($id);
        if ($user) {
            $user->delete();
            $this->success('删除网站成功', '/admin/site/index');
        } else {
            $this->error('您要删除的网站不存在');
        }
    }
}