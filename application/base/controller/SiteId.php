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

class SiteId extends Controller
{
    public function info($domain)
    {
        $domain = preg_replace('/www./', '', $domain);
        $site_data = Site::get(['domain'=>$domain]);
        $site_id = $site_data['id'];
        return $site_id;
    }
}