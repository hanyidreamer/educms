<?php
/**
 * Created by PhpStorm.
 * User: tanzhenxing
 * Date: 2017/6/14
 * Time: 18:29
 */
namespace app\base\controller;

use think\Controller;
use app\base\model\Template as TemplateModel;
use app\base\model\SiteTemplate;
use app\base\model\TemplateCategory;

class Template extends Controller
{
    /**
     * 模板路径
     * @return string
     * @throws \think\exception\DbException
     */
    public function path()
    {
        // 读取后台模板信息
        // 模板路径
        $module_name = $this->request->module();  // 模块名称
        $controller_name = $this->request->controller(); // 控制器名称
        $action_name = $this->request->action(); // 操作方法名称

        // 网站信息
        $site = new Site();
        $site_data = $site->info();

        // 网站模板配置信息
        $site_template = new SiteTemplate();
        $site_template_data = $site_template->get(['site_id'=>$site_data['id']]);

        // 模板分类信息
        $template_category = new TemplateCategory();
        $template_category_data = $template_category->get(['module' => $module_name]);

        // 模板信息
        $template = new TemplateModel();
        $admin_template = $template->get(['category_id' => $template_category_data['id'],'site_id' => $site_data['id']]);

        if(empty($admin_template)){
            $template_tag_name = 'common'; // 模板标签
            $site_template_data = $site_template->get(['site_id'=>0]);
            $site_template_name = $site_template_data['name']; // 网站模板名称
        }else{
            $template_tag_name = $admin_template['tag'];
            $site_template_name = $site_template_data['name'];
        }

        // 判断模板类型
        if($template_tag_name == 'common'){
            $site_template_name = 'public';
            $template_path = '/' .$site_template_name .'/' .$module_name .'/' .$controller_name.'/'.$action_name;
            $template_path = strtolower($template_path);
            $template_public_header = '/' .$site_template_name .'/' . $module_name .'/public/header';
            $template_public_footer = '/' .$site_template_name .'/' . $module_name .'/public/footer';
            $this->assign('public_header',$template_public_header);
            $this->assign('public_footer',$template_public_footer);
        }else{
            $client_data = new BrowserCheck();
            $client = $client_data->info();
            $template_path = '/' .$site_template_name .'/'.$client.'/' .$module_name .'/' .$controller_name.'/'.$action_name;
            $template_path = strtolower($template_path);
            $template_public_header = '/' .$site_template_name .'/'.$client. '/public/header';
            $template_public_footer = '/' .$site_template_name .'/'.$client. '/public/footer';
            $this->assign('public_header',$template_public_header);
            $this->assign('public_footer',$template_public_footer);
        }

        return $template_path;
    }

}