<?php
/**
 * Created by PhpStorm.
 * User: tanzhenxing
 * Date: 2017/6/14
 * Time: 18:29
 */
namespace app\base\controller;

use think\Controller;
use app\common\model\Template as TemplateModel;
use app\common\model\TemplateFile;
use app\common\model\TemplateCategory;

class Template extends Controller
{
    /**
     * 当前模板路径
     * @return string
     * @throws \think\exception\DbException
     */
    public function path()
    {
        // 当前操作位置
        $module_name = $this->request->module();  // 模块名称
        $controller_name = $this->request->controller(); // 控制器名称
        $action_name = $this->request->action(); // 操作方法名称

        // 网站信息
        $site = new Site();
        $site_data = $site->info();
        // 网站模板配置信息
        $site_template = TemplateModel::get($site_data['template_id']);
        $resource_path = '/resource/' . $site_template['unique_code'] . '/';
        $this->assign('resource_path',$resource_path);

        // 判断当前模块的模板文件是否在数据库中指定
        $my_template_category = TemplateCategory::get(['module'=>$module_name]);
        $my_template = TemplateFile::get(['template_id'=>$site_data['template_id'],'category_id'=>$my_template_category['id'] ,'controller_name'=>$controller_name,'action_name'=>$action_name]);
        if(!empty($my_template['id'])){
            // 模板标识
            $template_name_data = TemplateModel::get(['id'=>$my_template['template_id']]);
            $template_name = $template_name_data['unique_code'];
            $template_category = TemplateCategory::get(['id' => $my_template['category_id']]);
            $template_category_module = $template_category['module'];
            $template_path = '/' . $template_name . '/' . $template_category_module . '/' . $my_template['controller_name'] . '/' . $my_template['template_file_name'];
            if($module_name == 'admin'){
                $site_template_name = 'public';
                $template_path = '/' .$site_template_name .'/' .$module_name .'/' .$controller_name.'/'.$my_template['template_file_name'];
                $template_path = strtolower($template_path);
                $template_public_header = '/' .$site_template_name .'/' . $module_name .'/public/header';
                $template_public_footer = '/' .$site_template_name .'/' . $module_name .'/public/footer';
                $this->assign('public_header',$template_public_header);
                $this->assign('public_footer',$template_public_footer);
            }
        }else{
            $template_name_data = TemplateModel::get(['id'=>$site_data['template_id']]);
            $template_name = $template_name_data['unique_code'];

            // 是否根据客户端信息，设置不同的模板
            $client_data = new BrowserCheck();
            $client = $client_data->info();

            $client_path = '';
            $template_pc = $template_name_data['pc'];
            if($client == 'pc' and $template_pc == 1){
                $client_path = $client . '/';
            }
            $template_mobile = $template_name_data['mobile'];
            if($client == 'mobile' and $template_mobile == 1){
                $client_path = $client . '/';
            }
            $template_wechat = $template_name_data['wechat'];
            if($client == 'wechat' and $template_wechat == 1){
                $client_path = $client . '/';
            }

            // 判断是否为Admin模块
            if($module_name == 'admin'){
                $site_template_name = 'public';
                $template_path = '/' .$site_template_name .'/' .$module_name .'/' .$controller_name.'/'.$action_name;
                $template_path = strtolower($template_path);
                $template_public_header = '/' .$site_template_name .'/' . $module_name .'/public/header';
                $template_public_footer = '/' .$site_template_name .'/' . $module_name .'/public/footer';
                $this->assign('public_header',$template_public_header);
                $this->assign('public_footer',$template_public_footer);
            }else{
                // 当前操作默认模板
                $template_path = '/' . $template_name .'/' . $client_path . $module_name . '/' . $controller_name . '/' . $action_name;
                $template_public_header = '/' .$template_name .'/' . $client_path . '/public/header';
                $template_public_footer = '/' .$template_name .'/' . $client_path . '/public/footer';
                $this->assign('public_header',$template_public_header);
                $this->assign('public_footer',$template_public_footer);
            }
        }
        $template_path = strtolower($template_path);

        return $template_path;
    }

}