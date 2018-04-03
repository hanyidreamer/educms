<?php
/**
 * Created by PhpStorm.
 * User: tanzhenxing
 * Date: 2017/6/26
 * Time: 15:17
 */
namespace app\admin\controller;

use app\base\model\AdminLog as AdminLogModel;
use app\base\model\Admin;
use app\base\controller\TemplatePath;
use app\base\controller\Base;
use app\base\controller\Site;

class AdminLog extends Base
{
    /**
     * 日志列表
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index()
    {
        $title = '日志列表';
        $this->assign('title',$title);

        // 当前方法不同终端的模板路径
        $controller_name = $this->request->controller();
        $action_name = $this->request->action();
        $template_path_info = new TemplatePath();
        $template_path = $template_path_info->admin_path($controller_name,$action_name);
        $template_public = $template_path_info->admin_public_path();
        $template_public_header = $template_public.'/header';
        $template_public_footer = $template_public.'/footer';
        $this->assign('public_header',$template_public_header);
        $this->assign('public_footer',$template_public_footer);

        // 获取网站id
        $get_domain = $this->request->server('HTTP_HOST');
        $this->assign('domain',$get_domain);
        $site_id_data = new Site();
        $site_id = $site_id_data->info();

        // 找出广告列表数据
        $post_title = $this->request->param('title');
        $data = new AdminLogModel;
        if(!empty($post_title)){
            $data_list = $data->where(['status' => 1, 'title' => ['like','%'.$post_title.'%']])->select();
        }else{
            $data_list = $data->where(['site_id'=>$site_id,'status'=>1])->select();
        }
        $data_count = count($data_list);
        $this->assign('data_count',$data_count);

        foreach ($data_list as $data){
            $admin_id = $data['admin_id'];
            $admin_data = Admin::get($admin_id);
            $admin_username = $admin_data['username'];
            $data['username'] = $admin_username;
        }

        $this->assign('data_list',$data_list);

        return $this->fetch($template_path);
    }
}