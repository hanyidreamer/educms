<?php
/**
 * Created by PhpStorm.
 * User: tanzhenxing
 * Date: 2017/6/22
 * Time: 11:37
 */
namespace app\base\controller;

use think\Controller;
use app\base\model\Site;

class SiteData extends Controller
{
    /**
     * @throws \think\exception\DbException
     */
    public function info()
    {
        $domain = $this->request->host();
        $domain = preg_replace('/www./', '', $domain);
        $site_data = Site::get(['domain'=>$domain]);
        return $site_data;
    }
}