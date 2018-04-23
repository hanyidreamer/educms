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
     * @param string $template_file_id
     * @return string
     * @throws \think\exception\DbException
     */
    public function path($template_file_id = '')
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

        // 判断是否为指定模板
        if(is_numeric($template_file_id) and !empty($template_file_id)){
            $template_file_data = TemplateFile::get($template_file_id);
            // 模板标识
            $template_name_data = TemplateModel::get(['id'=>$template_file_data['template_id']]);
            $template_name = $template_name_data['unique_code'];
            $template_category = TemplateCategory::get(['id' => $template_file_data['category_id']]);
            $template_category_module = $template_category['module'];
            $template_path = '/' . $template_name . '/' . $template_category_module . '/' . $template_file_data['controller_name'] . '/' . $template_file_data['template_file_name'];
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