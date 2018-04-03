<?php
/**
 * Created by PhpStorm.
 * User: tanzhenxing
 * Date: 2018/4/2
 * Time: 12:34
 */
namespace app\common\controller;

use think\Controller;
use QCloud\Cos\Api;
use app\base\model\QcloudCos as QcloudCosModel;


class QcloudCos extends Controller
{
    /**
     * @param $upload_tmp_name
     * @param $upload_name
     * @param $upload_size
     * @return null|string|string[]
     * @throws \think\exception\DbException
     */
    public function upload($upload_tmp_name,$upload_name,$upload_size)
    {
        // 读取数据库中腾讯云配置信息
        $config_info = QcloudCosModel::get(1);
        $my_url = $config_info['my_url']; // 自定义域名网址
        $bucket_name = $config_info['bucket_name'];
        // cos文件上传路径
        $year = date("Y");
        $month = date("m");
        $date = date("d");
        $time_rand_number = rand(1,99999999);
        $time = time().$time_rand_number;
        $folder_path = 'images/'.$year.'/'.$month.'/'.$date.'/';

        $config = array(
            'app_id' => $config_info['app_id'],
            'secret_id' => $config_info['secret_id'],
            'secret_key' => $config_info['secret_key'],
            'region' => $config_info['region'],
            'timeout' => $config_info['timeout']
        );
        $cosApi = new Api($config);


        // 查询目录是否存在
        $folder_path_info = $cosApi->statFolder($bucket_name, $folder_path);
        if($folder_path_info['code']==0){
            // echo '目录'.$folder_path.'已经存在！';
        }else{
            // 创建文件夹
            $create_folder = $cosApi->createFolder($bucket_name, $folder_path);
            if($create_folder['code']==0){
                // echo '创建目录：'.$folder_path.' 成功！';
            }else{
                return '创建目录：'.$folder_path.' 失败！';
            }
        }

        // 上传文件
        $src_path = $upload_tmp_name; // 本地文件路径
        // cos 文件路径
        $dst_path = preg_replace('/(.*)\./','.',$upload_name);
        $dst_path = $folder_path.$time.$dst_path;
        $biz_attr = '';  // 文件属性
        if($upload_size>1048576){
            $slice_size = 1 * 1024 * 1024; // 文件分片大小
        }else{
            $slice_size = '';
        }
        $insert_only = 1; //0：覆盖；1：不覆盖
        $upload_file_info = $cosApi->upload($bucket_name, $src_path, $dst_path,$biz_attr,$slice_size,$insert_only);
        $upload_code = $upload_file_info['code']; // 上传状态，0为成功
        $resource_path = $upload_file_info['data']['resource_path']; // cos 文件路径

        $file_url = preg_replace('/\/10080712\/waihui\//',$my_url,$resource_path);

        if($upload_code==0){
            $file_info = $cosApi->stat($bucket_name, $dst_path);
            if($file_info['code']==0){
                return $file_url;
            }else{
                return '上传文件失败！';
            }
        }

    }

}