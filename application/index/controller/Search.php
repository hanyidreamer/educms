<?php
/**
 * Created by PhpStorm.
 * User: tanzhenxing
 * Date: 2017/6/23
 * Time: 5:17
 */
namespace app\index\controller;

use think\Request;
use app\base\Controller\Base;
use app\base\model\Site;
use app\base\controller\SiteId;
use app\base\Controller\TemplatePath;
use app\base\model\CourseCategory;
use app\base\model\Course;

class Search extends Base
{
    public function index(Request $request,$mid = '')
    {
        // 当前方法不同终端的模板路径
        $module_name = Request::instance()->module();
        $controller_name = Request::instance()->controller();
        $action_name = Request::instance()->action();
        $template_path_info = new TemplatePath();
        $template_path = $template_path_info->info($module_name,$controller_name,$action_name);
        $template_public = $template_path_info->public_path();
        $template_public_header = $template_public.'/header';
        $template_public_footer = $template_public.'/footer';
        $this->assign('public_header',$template_public_header);
        $this->assign('public_footer',$template_public_footer);

        // 获取网站id
        $get_domain = Request::instance()->server('HTTP_HOST');
        $this->assign('domain',$get_domain);
        $site_id_data = new SiteId();
        $site_id = $site_id_data->info($get_domain);

        $get_domain = Request::instance()->server('HTTP_HOST');
        $get_domain = preg_replace('/www./', '', $get_domain);
        $site_info = Site::get(['domain'=>$get_domain]);
        $this->assign('title',$site_info['title']);
        $this->assign('description',$site_info['desc']);
        $this->assign('keywords',$site_info['keywords']);
        $this->assign('site',$site_info);
        $this->assign('mid',$mid);
        $this->assign('site_id',$site_id);

        // 顶部导航
        // 一级分类
        $nav_level_one_info = new CourseCategory();
        $nav_level_one_data = $nav_level_one_info->where(['site_id'=>$site_id,'parent_id'=>0,'nav_status'=>1])->select();
        // 二级分类
        foreach ($nav_level_one_data as $data){
            $category_id = $data['id'];
            $parent_id= $data['parent_id'];
            $sub_nav_info = new CourseCategory();
            $category_data = $sub_nav_info->where(['parent_id'=>$category_id])->select();
            $data['sub_list'] = $category_data;
        }
        $this->assign('nav',$nav_level_one_data);

        // 搜索结果
        $post_title = $request->param('title');
        $data = new Course;
        if(!empty($post_title)){
            $data_list = $data->where(['site_id'=>$site_id,'status' => 1, 'title' => ['like','%'.$post_title.'%']])->select();
        }else{
            $data_list = $data->where(['site_id'=>$site_id,'status'=>1])->select();
        }
        $data_count = count($data_list);
        $this->assign('data_count',$data_count);

        $this->assign('data_list',$data_list);


        return $this->fetch($template_path);
    }
}