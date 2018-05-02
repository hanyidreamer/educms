<?php
/**
 * Created by PhpStorm.
 * User: tanzhenxing
 * Date: 2018/4/26
 * Time: 9:46
 */
namespace app\index\controller;

use think\Controller;
use think\facade\Env;

class Error extends Controller
{
    public function _empty()
    {
        header("HTTP/1.1 404 Not Found");
        header("Status: 404 Not Found");
        $file_path = Env::get('root_path').'web/404.html';
        $html = file_get_contents($file_path);
        echo $html;
        exit;
    }
}
