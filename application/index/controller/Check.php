<?php
/**
 * Created by PhpStorm.
 * User: tanzhenxing
 * Date: 2018/3/29
 * Time: 16:31
 */
namespace app\index\controller;

use think\Controller;
use app\base\model\Site;
use think\facade\Env;
use app\base\controller\TemplatePath;

class Check extends Controller
{
    /**
     * @throws \think\exception\DbException
     */
    public function template()
    {
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

        // 当前绝对路径
        $root_path = Env::get('root_path');
        $template_full_path = $root_path . 'template' . $path;
        dump($template_full_path);
die;


        $this->template_path = $template_path;

    }
}