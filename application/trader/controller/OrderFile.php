<?php
/**
 * Created by PhpStorm.
 * User: tanzhenxing
 * Date: 2018/5/2
 * Time: 14:21
 */
namespace app\trader\controller;

use think\Controller;

class OrderFile extends Controller
{
    /**
     * 检查订单文件信息
     * @param string $url
     */
    public function info($url = '')
    {
        $account = $_GET["id"];
        $filename = 'statement.htm';
        $file_path = './'.$account.'/'.$filename;

        $my_file_info = array();

// 判断文件是否存在
        if(file_exists($file_path)){
            // 文件大小
            $file_site = filesize($file_path);

            // 文件修改时间
            $update_time=filemtime($file_path);

            $file_data = file_get_contents($file_path,FILE_USE_INCLUDE_PATH);
            $file_info = mb_convert_encoding($file_data, 'utf-8', 'gbk');
            $file_info = str_replace(' bgcolor=#E0E0E0', '', $file_info);
            // 获取成交订单列表
            preg_match_all('/<tr align=right>(.*?)<\/tr>/is', $file_info, $order_list, PREG_PATTERN_ORDER);
            $order_num = count($order_list[1]);

            // 判断文件是否完整
            preg_match('/<\/div><\/body><\/html>/isU', $file_info, $get_file_info);
            if($get_file_info[0] == '</div></body></html>'){
                // echo '文件完整';
                $my_file_info=['code'=>"1",'full'=>"1",'file_size'=>"$file_site",'order_num'=>"$order_num",'update_time'=>"$update_time"];
            }else{
                //echo '文件不完整';
                $my_file_info=['code'=>"1",'full'=>"0",'file_size'=>"$file_site",'order_num'=>"$order_num",'update_time'=>"$update_time"];
            }

        }else{
            // 0 为 文件不存在;
            $my_file_info=['code'=>"0"];
        }

        echo json_encode($my_file_info);
    }
}