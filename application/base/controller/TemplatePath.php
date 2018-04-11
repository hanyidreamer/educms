<?php
/**
 * Created by PhpStorm.
 * User: tanzhenxing
 * Date: 2017/6/14
 * Time: 18:29
 */
namespace app\base\controller;

use think\Controller;
use app\base\model\Template;

class TemplatePath extends Controller
{
    /**
     * 前台模板路径
     * @param string $site_id
     * @return string
     * @throws \think\exception\DbException
     */
    public function info($site_id = '')
    {
        $module_name = $this->request->module();
        $controller_name = $this->request->controller();
        $action_name = $this->request->action();
        // 模板名称
        $template_data = Template::get($site_id);
        $template_name = $template_data['folder'];

        // 判断是否为微信浏览器
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
            $client = 'weixin';
        }else{
            // 判断客户端是否为手机访问
            if ($this->request->isMobile()) {
                $client = 'mobile';
            } else {
                $client = 'pc';
            }
        }

        $template_path = '/'.$template_name.'/'.$client.'/'.$module_name.'/'.$controller_name.'/'.$action_name;
        $template_path = strtolower($template_path);

        $template_public_header = '/'.$template_name.'/' . $client .'/public/header';
        $template_public_footer = '/'.$template_name.'/' . $client .'/public/footer';

        $this->assign('public_header',$template_public_header);
        $this->assign('public_footer',$template_public_footer);

        return $template_path;
    }

}