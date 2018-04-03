<?php
/**
 * Created by PhpStorm.
 * User: tanzhenxing
 * Date: 2018/4/2
 * Time: 11:15
 */
namespace app\base\controller;

use think\Controller;
use \app\base\model\Site as SiteModel;

class Site extends Controller
{
    /**
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