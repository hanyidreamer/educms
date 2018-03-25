<?php
/**
 * Created by PhpStorm.
 * User: tzx
 * Date: 2016/10/25
 * Time: 21:32
 */
namespace app\admin\controller;

use think\Request;
use app\base\model\Sms as SmsModel;
use app\base\model\SmsCategory;
use app\base\controller\TemplatePath;
use app\base\controller\Base;
use app\base\controller\SiteId;

class Sms extends Base
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

        // 获取网站id
        $get_domain = Request::instance()->server('HTTP_HOST');
        $this->assign('domain',$get_domain);
        $site_id_data = new SiteId();
        $site_id = $site_id_data->info($get_domain);

        // 找出广告列表数据
        $post_title = $request->param('title');
        $data = new SmsModel;
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

    // 增加网站
    public function add()
    {
        $title = '新增短信接口';
        $this->assign('title',$title);
        return $this->fetch();
    }

    public function edit($id)
    {
        $data_list = SmsModel::get($id);
        $this->assign('data_list',$data_list);
        return $this->fetch();
    }

    public function save(Request $request)
    {
        $post_id = $request->post('id');
        $post_site_id = $request->post('site_id');
        $post_name = $request->post('name');
        $post_platform = $request->post('platform');
        $post_sign = $request->post('sign');
        $post_app_key = $request->post('app_key');
        $post_secret_key = $request->post('secret_key');
        $post_sign_name = $request->post('sign_name');
        $post_status = $request->post('status');

        if($post_name=='' or $post_id==''){
            $this->error('短信接口名称不能为空');
        }

        $user = SmsModel::get($post_id);
        $user['name'] = $post_name;
        $user['site_id']    = $post_site_id;
        $user['platform'] = $post_platform;
        $user['sign'] = $post_sign;
        $user['app_key']    = $post_app_key;
        $user['secret_key'] = $post_secret_key;
        $user['sign_name'] = $post_sign_name;
        $user['status'] = $post_status;
        if ($user->save()) {
            $this->success('保存短信接口信息成功', '/admin/sms/index');
        } else {
            $this->error('操作失败');
        }

    }

    // 新增短信接口
    public function insert(Request $request)
    {
        $post_name = $request->post('name');
        $post_site_id = $request->post('site_id');
        $post_platform = $request->post('platform');
        $post_sign = $request->post('sign');
        $post_app_key = $request->post('app_key');
        $post_secret_key = $request->post('secret_key');
        $post_sign_name= $request->post('sign_name');
        $post_status = $request->post('status');


        if($post_name=='' or $post_app_key==''){
            $this->error('平台名称和app_key不能为空');
        }
        $user = new SmsModel;
        $user['name'] = $post_name;
        $user['site_id']    = $post_site_id;
        $user['platform'] = $post_platform;
        $user['sign'] = $post_sign;
        $user['app_key']    = $post_app_key;
        $user['secret_key'] = $post_secret_key;
        $user['sign_name'] = $post_sign_name;
        $user['status'] = $post_status;

        if ($user->save()) {
            $this->success('新增短息接口成功', '/admin/sms/index');
        } else {
            $this->error('操作失败');
        }
    }

    // 删除
    public function delete($id)
    {
        $user = SmsModel::get($id);
        if ($user) {
            $user->delete();
            $this->success('删除成功', '/admin/sms/index');
        } else {
            $this->error('删除的信息不存在');
        }
    }


}