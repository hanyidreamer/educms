<?php
/**
 * Created by PhpStorm.
 * User: tanzhenxing
 * Date: 2017/6/26
 * Time: 15:35
 */
namespace app\admin\controller;

use think\Request;
use app\base\model\WechatOfficialAccounts as WechatOfficialAccountsModel;
use app\base\controller\TemplatePath;
use app\base\controller\Base;
use app\base\controller\SiteId;
use app\base\controller\Upload;

class WechatOfficialAccounts extends Base
{
    public function index(Request $request)
    {
        // 给当页面标题赋值
        $title = '微信公众号';
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
        $post_title = $request->param('title');
        $data = new WechatOfficialAccountsModel;
        if(!empty($post_title)){
            $data_list = $data->where(['status' => 1, 'title' => ['like','%'.$post_title.'%']])->select();
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
        $title = '添加公众号';
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
        $post_qrcode = '';
        $file_qrcode = $request->file('qrcode');
        if(!empty($file_qrcode)){
            $local_qrcode = $file_qrcode->getInfo('tmp_name');
            $qrcode_filename = $file_qrcode->getInfo('name');
            $qrcode_file_info = new Upload();
            $post_qrcode = $qrcode_file_info->qcloud_file($local_qrcode,$qrcode_filename);
        }

        $post_icon = '';
        $file_thumb = $request->file('icon');
        if(!empty($file_thumb)){
            $local_thumb = $file_thumb->getInfo('tmp_name');
            $thumb_filename = $file_thumb->getInfo('name');
            $thumb_file_info = new Upload();
            $post_icon = $thumb_file_info->qcloud_file($local_thumb,$thumb_filename);
        }

        $post_site_id = $request->post('site_id');
        $post_title = $request->post('title');
        $post_wechat_id = $request->post('wechat_id');
        $post_original_id = $request->post('original_id');
        $post_app_id = $request->post('app_id');
        $post_app_secret = $request->post('app_secret');
        $post_desc = $request->post('desc');
        $post_username = $request->post('username');
        $post_password = $request->post('password');
        $post_password = md5($post_password);
        $post_merchant_id = $request->post('merchant_id');
        $post_api_key = $request->post('api_key');
        $post_access_token = $request->post('access_token');
        $post_refresh_token = $request->post('refresh_token');
        $post_expires_time_token = $request->post('expires_time_token');
        $post_status = $request->post('status');

        if($post_title==''){
            $this->error('标题不能为空');
        }
        $user = new WechatOfficialAccountsModel;
        $user['site_id'] = $post_site_id;
        $user['title']    = $post_title;
        $user['wechat_id'] = $post_wechat_id;
        $user['original_id'] = $post_original_id;
        $user['app_id'] = $post_app_id;
        $user['app_secret'] = $post_app_secret;
        $user['desc'] = $post_desc;
        $user['qrcode'] = $post_qrcode;
        $user['icon'] = $post_icon;
        $user['username'] = $post_username;
        $user['password'] = $post_password;
        $user['merchant_id'] = $post_merchant_id;
        $user['api_key'] = $post_api_key;
        $user['access_token'] = $post_access_token;
        $user['refresh_token'] = $post_refresh_token;
        $user['expires_time_token'] = $post_expires_time_token;
        $user['status'] = $post_status;

        if ($user->save()) {
            $this->success('新增成功', '/admin/wechat_official_accounts/index');
        } else {
            $this->error('操作失败');
        }

    }

    public function edit($id)
    {
        $title = '编辑公众号';
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

        // 获取信息
        $data_list = WechatOfficialAccountsModel::get($id);
        $this->assign('data',$data_list);

        return $this->fetch($template_path);
    }

    public function update(Request $request)
    {
        $post_qrcode = '';
        $file_qrcode = $request->file('qrcode');
        if(!empty($file_qrcode)){
            $local_qrcode = $file_qrcode->getInfo('tmp_name');
            $qrcode_filename = $file_qrcode->getInfo('name');
            $qrcode_file_info = new Upload();
            $post_qrcode = $qrcode_file_info->qcloud_file($local_qrcode,$qrcode_filename);
        }

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
        $post_title = $request->post('title');
        $post_wechat_id = $request->post('wechat_id');
        $post_original_id = $request->post('original_id');
        $post_app_id = $request->post('app_id');
        $post_app_secret = $request->post('app_secret');
        $post_desc = $request->post('desc');
        $post_username = $request->post('username');
        $post_password = $request->post('password');
        $post_password = md5($post_password);
        $post_merchant_id = $request->post('merchant_id');
        $post_api_key = $request->post('api_key');
        $post_access_token = $request->post('access_token');
        $post_refresh_token = $request->post('refresh_token');
        $post_expires_time_token = $request->post('expires_time_token');
        $post_status = $request->post('status');

        if($post_title==''){
            $this->error('标题不能为空');
        }
        $user = WechatOfficialAccountsModel::get($post_id);
        $user['site_id'] = $post_site_id;
        $user['title']    = $post_title;
        $user['wechat_id'] = $post_wechat_id;
        $user['original_id'] = $post_original_id;
        $user['app_id'] = $post_app_id;
        $user['app_secret'] = $post_app_secret;
        $user['desc'] = $post_desc;
        if(!empty($post_icon)){
            $user['icon'] = $post_icon;
        }
        if(!empty($post_qrcode)){
            $user['qrcode'] = $post_qrcode;
        }
        $user['username'] = $post_username;
        if(!empty($post_password)){
            $user['password'] = $post_password;
        }
        $user['merchant_id'] = $post_merchant_id;
        $user['api_key'] = $post_api_key;
        $user['access_token'] = $post_access_token;
        $user['refresh_token'] = $post_refresh_token;
        $user['expires_time_token'] = $post_expires_time_token;
        $user['status'] = $post_status;

        if ($user->save()) {
            $this->success('保存成功', '/admin/wechat_official_accounts/index');
        } else {
            $this->error('操作失败');
        }
    }

    public function delete($id)
    {
        $user = WechatOfficialAccountsModel::get($id);
        if ($user) {
            $user->delete();
            $this->success('删除成功', '/admin/wechat_official_accounts/index');
        } else {
            $this->error('删除失败');
        }
    }

}