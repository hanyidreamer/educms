<?php
/**
 * Created by PhpStorm.
 * User: tanzhenxing
 * Date: 2017/5/9
 * Time: 9:46
 */
namespace app\api\controller\v2;

use think\Controller;
use think\Request;

class Index extends Controller
{
    // 域名检查 是否为接口域名
    public function __construct(Request $request)
    {
        parent::__construct();
        // 获取当前域名
        $domain = Request::instance()->server('HTTP_HOST');
        if($domain != 'api.qianbailang.com'){
            echo '欢迎使用外汇API接口，联系电话：13450232305';
            exit;
        }
    }

    public function index()
    {
        echo 'api';
    }

    public function key()
    {
        echo 'key';
    }
}