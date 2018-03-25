<?php
/**
 * Created by PhpStorm.
 * User: tzx
 * Date: 2016/10/25
 * Time: 15:43
 */
namespace app\admin\controller;

use think\Request;
use think\Session;
use app\base\model\Site;
use app\base\model\Admin;
use app\base\controller\Base;
use app\base\controller\Upload;
use app\base\controller\SiteId;
use app\base\controller\TemplatePath;

class SiteInfo extends Base
{
    // 我的网站列表
    public function index()
    {
        $title='网站列表';
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

        $admin_username = Session::get('username');
        $admin_info = Admin::get(['username'=>$admin_username]);
        $admin_id = $admin_info['id'];

        $site_info_sql['admin_id'] = $admin_id;
        $site_info_data = new Site();
        $site_info = $site_info_data->where($site_info_sql) ->paginate(10);
        $site_count = count($site_info);

        $this->assign('site_info',$site_info);
        $this->assign('site_count',$site_count);

        return $this->fetch($template_path);
    }

    // 编辑网站基本信息
    public function info()
    {
        $title = '网站基本信息';
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

        $site_info = Site::get($site_id);
        // 获取站长名称
        $admin_id = $site_info['admin_id'];
        $admin_info = Admin::get($admin_id);

        $site_info['admin_username'] = $admin_info['username'];

        $this->assign('site_info',$site_info);
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
        if(!empty($file_logo)) {
            $local_logo = $file_logo->getInfo('tmp_name');
            $logo_filename = $file_logo->getInfo('name');
            $logo_file_info = new Upload();
            $post_logo = $logo_file_info->qcloud_file($local_logo, $logo_filename);
        }

        // 获取 网站略缩图 thumb文件
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

        $post_id= $request->post('id');
        $post_title= $request->post('title');
        $post_home_title= $request->post('home_title');
        $post_desc= $request->post('desc');
        $post_keywords= $request->post('keywords');

        $post_stats = $request->post('stats');
        $post_icp = $request->post('icp');
        $post_copyright = $request->post('copyright');

        if($post_title=='' or $post_id==''){
            $this->error('域名和网站名称不能为空');
        }

        $user = Site::get($post_id);
        $user['title'] = $post_title;
        $user['home_title'] = $post_home_title;
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

        $user['stats'] = $post_stats;
        $user['icp'] = $post_icp;
        $user['copyright'] = $post_copyright;

        if ($user->save()) {
            $this->success('保存网站信息成功', '/admin/site_info/info');
        } else {
            $user->getError();
        }

    }

    public function contact()
    {
        $title = '网站联系方式';
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
        $site_info = Site::get($site_id);

        $this->assign('site_info',$site_info);
        return $this->fetch($template_path);
    }

    public function contact_save(Request $request)
    {
        $post_id= $request->post('id');
        $post_tel= $request->post('tel');
        $post_qq= $request->post('qq');
        $post_email= $request->post('email');
        $post_address= $request->post('address');

        $user = Site::get($post_id);
        $user['tel'] = $post_tel;
        $user['qq'] = $post_qq;
        $user['email'] = $post_email;
        $user['address'] = $post_address;
        if ($user->save()) {
            $this->success('保存网站信息成功', '/admin/site_info/contact');
        } else {
            $user->getError();
        }
    }

}