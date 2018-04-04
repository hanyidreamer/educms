<?php
/**
 * Created by PhpStorm.
 * User: tanzhenxing
 * Date: 2018/4/4
 * Time: 9:39
 */
namespace app\admin\controller;

use think\Controller;

class AdminTemplate extends Controller
{
    public function path()
    {
        // 后台模板路径
        $module_name = $this->request->module();
        $controller_name = $this->request->controller();
        $action_name = $this->request->action();

        $template_path = '/' . $module_name .'/pc/'.$controller_name.'/'.$action_name;
        $template_path = strtolower($template_path);

        $template_public_header = '/' . $module_name .'/pc/public/header';
        $template_public_footer = '/' . $module_name .'/pc/public/footer';
        $this->assign('public_header',$template_public_header);
        $this->assign('public_footer',$template_public_footer);
        return $template_path;
    }
}