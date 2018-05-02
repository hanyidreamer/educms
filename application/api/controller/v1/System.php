<?php
/**
 * Created by PhpStorm.
 * User: tzx
 * Date: 2016/10/7
 * Time: 16:07
 */
namespace app\api\controller\v1;

use think\Controller;

class System extends Controller
{
    // 打印服务器Unix 时间戳
    public function get_time()
    {
        $time=time();
        $json_info=array("code"=>"1","status"=>"success","time"=>$time);
        echo json_encode($json_info);
    }
}