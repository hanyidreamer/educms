<?php
/**
 * Created by PhpStorm.
 * User: tanzhenxing
 * Date: 2018/5/2
 * Time: 22:05
 */
namespace app\article\controller;

use think\Controller;
use app\base\controller\Template;

class SinglePage extends Controller
{
    /**
     * 模板：http://xy.jyptchaxun.com/special/bd/index.html
     * @throws \think\exception\DbException
     */
    public function index()
    {
        $template = new Template();
        $template_path = $template->path();
        return $this->fetch($template_path);
    }

    /**
     * @throws \think\exception\DbException
     */
    public function view()
    {
        $template = new Template();
        $template_path = $template->path();
        return $this->fetch($template_path);
    }
}