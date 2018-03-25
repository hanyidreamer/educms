<?php
/**
 * Created by PhpStorm.
 * User: tanzhenxing
 * Date: 2017/6/30
 * Time: 16:06
 */
namespace app\index\controller;

use app\base\controller\BrowserCheck;
use think\Controller;
use think\facade\Env;
use think\Request;
use think\Session;
use app\base\model\Site;
use app\base\model\CourseCategory;
use app\base\controller\TemplatePath;
use app\member\controller\WeixinUser;

class Base extends Controller
{
    /**
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    protected function initialize()
    {
        // 获取当前域名是否授权
        $get_domain = $this->request->server('HTTP_HOST');
        $get_domain = preg_replace('/www./', '', $get_domain);

        $this->assign('domain',$get_domain);
        $site_info = Site::get(['domain'=>$get_domain]);
        // 显示未授权域名的提示信息
        if(empty($site_info)) {
            $this->error('欢迎使用培训分销系统，您的网站还没有开通，请联系电话：13450232305');
        }

        // 获取网站id
        $site_id = $site_info['id'];
        $this->site_id = $site_id;
        $this->assign('site_id',$site_id);

        $mid = $this->request->param('mid');
        if(empty($mid) and !is_numeric($mid)){  // 判断mid 是否为整数
            $mid = 0;
        }
        $this->mid = $mid;

        // 获取网站基本信息
        $this->assign('title',$site_info['title']);
        $this->assign('description',$site_info['desc']);
        $this->assign('keywords',$site_info['keywords']);
        $this->assign('site',$site_info);

        // 顶部导航
        // 一级分类
        $nav_level_one_info = new CourseCategory();
        $nav_level_one_data = $nav_level_one_info->where(['site_id'=>$site_id,'parent_id'=>0,'nav_status'=>1])->select();
        // 二级分类
        foreach ($nav_level_one_data as $data){
            $category_id = $data['id'];
            // $parent_id= $data['parent_id'];
            $sub_nav_info = new CourseCategory();
            $category_data = $sub_nav_info->where(['parent_id'=>$category_id])->select();
            $data['sub_list'] = $category_data;
        }
        $this->assign('nav',$nav_level_one_data);


        // 当前方法不同终端的模板路径
        $module_name = $this->request->module();
        $controller_name = $this->request->controller();
        $action_name = $this->request->action();
        $template_path_info = new TemplatePath();
        $template_path = $template_path_info->info($module_name,$controller_name,$action_name);
        $template_public = $template_path_info->public_path();
        $template_public_header = $template_public.'/header';
        $template_public_footer = $template_public.'/footer';
        $this->assign('public_header',$template_public_header);
        $this->assign('public_footer',$template_public_footer);
        $this->assign('controller_name',$controller_name);

        $this->template_path = $template_path;

        // 用户登录信息
        $username = session('username');
        $this->username = $username;

    }

}