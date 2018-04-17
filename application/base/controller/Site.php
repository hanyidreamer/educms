<?php
/**
 * Created by PhpStorm.
 * User: tanzhenxing
 * Date: 2018/4/2
 * Time: 11:15
 */
namespace app\base\controller;

use think\Controller;
use app\common\model\Site as SiteModel;

class Site extends Controller
{
    /**
     * 当前网站信息
     * @throws \think\exception\DbException
     */
    public function info()
    {
        $domain = $this->request->host();
        $domain = preg_replace('/www./','',$domain);
        $data = SiteModel::get(['domain'=>$domain]);
        return $data;
    }
}