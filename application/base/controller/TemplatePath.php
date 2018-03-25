<?php
/**
 * Created by PhpStorm.
 * User: tanzhenxing
 * Date: 2017/6/14
 * Time: 18:29
 */
namespace app\base\controller;

use think\Controller;
use think\Request;
use app\base\model\Site;
use app\base\model\Template;

class TemplatePath extends Controller
{
    /**
     * @param $module_name
     * @param $controller_name
     * @param $action_name
     * @return string
     * @throws \think\exception\DbException
     */
    public function info($module_name,$controller_name,$action_name)
    {
        $get_domain = $this->request->server('HTTP_HOST');
        $get_domain = preg_replace('/www./', '', $get_domain);
        $domain = Site::get(['domain'=>$get_domain]);
        $site_id = $domain['id'];
        $template_data = Template::get($site_id);
        $template_name = $template_data['folder'];

        // 判断是否为微信浏览器
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
                $template_path = $template_name.'/weixin/'.$module_name.'/'.$controller_name.'/'.$action_name;
                $template_path = strtolower($template_path);
        }else{
            // 判断客户端是否为手机访问
            if ($this->request->isMobile()) {
                $template_path = $template_name.'/mobile/'.$module_name.'/'.$controller_name.'/'.$action_name;
                $template_path = strtolower($template_path);
            } else {
                $template_path = $template_name.'/pc/'.$module_name.'/'.$controller_name.'/'.$action_name;
                $template_path = strtolower($template_path);
            }
        }

        return $template_path;
    }

    /**
     * @return string
     * @throws \think\exception\DbException
     */
    public function public_path()
    {
        $get_domain = $this->request->server('HTTP_HOST');
        $get_domain = preg_replace('/www./', '', $get_domain);
        $domain = Site::get(['domain'=>$get_domain]);
        $site_id = $domain['id'];
        $template_data = Template::get($site_id);
        $template_name = $template_data['folder'];

        // 判断是否为微信浏览器
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
            $template_path = $template_name.'/weixin/public';
            $template_path = strtolower($template_path);
        }else{
            // 判断客户端是否为手机访问
            if ($this->request->isMobile()) {
                $template_path = $template_name.'/mobile/public';
                $template_path = strtolower($template_path);
            } else {
                $template_path = $template_name.'/pc/public';
                $template_path = strtolower($template_path);
            }
        }
        return $template_path;
    }

    public function admin_path($controller_name,$action_name)
    {
        // 判断是否为微信浏览器
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
            $template_path = '/admin/weixin/'.$controller_name.'/'.$action_name;
            $template_path = strtolower($template_path);
        }else{
            // 判断客户端是否为手机访问
            if (request()->isMobile()) {
                $template_path = '/admin/mobile/'.$controller_name.'/'.$action_name;
                $template_path = strtolower($template_path);
            } else {
                $template_path = '/admin/pc/'.$controller_name.'/'.$action_name;
                $template_path = strtolower($template_path);
            }
        }
        return $template_path;
    }

    /**
     * @return string
     * @throws \think\exception\DbException
     */
    public function admin_public_path()
    {
        $get_domain = $this->request->server('HTTP_HOST');
        $get_domain = preg_replace('/www./', '', $get_domain);
        $domain = Site::get(['domain'=>$get_domain]);
        $site_id = $domain['id'];
        $template_data = Template::get($site_id);
        $template_name = $template_data['folder'];

        // 判断是否为微信浏览器
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
            $template_path = '/admin/weixin/public';
            $template_path = strtolower($template_path);
        }else{
            // 判断客户端是否为手机访问
            if (request()->isMobile()) {
                $template_path ='/admin/mobile/public';
                $template_path = strtolower($template_path);
            } else {
                $template_path = '/admin/pc/public';
                $template_path = strtolower($template_path);
            }
        }
        return $template_path;
    }

}