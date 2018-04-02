<?php
/**
 * Created by PhpStorm.
 * User: tanzhenxing
 * Date: 2017/6/30
 * Time: 16:06
 */
namespace app\index\controller;

use app\base\controller\Site;
use think\Controller;
use app\base\model\CourseCategory;

class Base extends Controller
{
    protected $site_id;
    protected $template_path;

    /**
     * 前台基本类
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    protected function initialize()
    {
        // 网站基本信息
        $site_data = new Site();
        $site_info = $site_data->info();
        if(empty($site_info)){
            $this->error('欢迎使用培训分销系统，您的网站还没有开通，请联系电话：13450232305');
        }
        // 获取当前网站id
        $this->site_id = $site_info['id'];

        // 网站基本信息
        $this->assign('site',$site_info);

        // 检查模板信息

        $template_data = $check_data->template();

        // 顶部导航
        // 一级分类
        $nav_level_one_info = new CourseCategory();
        $nav_level_one_data = $nav_level_one_info->where(['site_id'=>$site_info['id'],'parent_id'=>0,'nav_status'=>1])->select();
        // 二级分类
        foreach ($nav_level_one_data as $data){
            $category_id = $data['id'];
            $sub_nav_info = new CourseCategory();
            $category_data = $sub_nav_info->where(['parent_id'=>$category_id])->select();
            $data['sub_list'] = $category_data;
        }
        $this->assign('nav',$nav_level_one_data);



    }

}