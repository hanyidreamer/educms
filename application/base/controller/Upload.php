<?php
/**
 * Created by PhpStorm.
 * User: tanzhenxing
 * Date: 2017/4/5
 * Time: 15:52
 */
namespace app\base\controller;

use Qcloud_cos\Cosapi;


class Upload extends Base
{
    /**
     * 上传默认界面
     * @return mixed
     */
    public function index()
    {
        return $this->fetch();
    }

    // 上传程序服务器文件
    public function upload_file($file)
    {
        // 获取上传的文件信息
        //$file = $my_request->file('file');

        if ($file) {
            // 移动到框架应用根目录/public/uploads/ 目录下
            $info = $file->validate(['ext' => 'jpg,png,ico,gif'])->move('./uploads/file');
            if ($info) {
                $filename= $info->getSaveName();
                $filename=str_replace('\\','/',$filename);
                $filename='/uploads/file/'.$filename;
            } else {
                $filename='';
                // 上传失败获取错误信息
                $this->error($info->getError());
            }
        }else{
            $filename='';
        }
        return $filename;
    }

    /**
     * 上传文件到腾讯云
     * @param $local_file_name
     * @param $filename
     * @return string
     */
    public function qcloud_file($local_file_name,$filename)
    {
        $web_url = 'https://waihui-10080712.file.myqcloud.com';
        $time = time();
        $date = date('Y-m-d',$time);
        $bucketName = 'waihui';  // bucket名称
        $srcPath = $local_file_name;  // 本地文件路径
        $dstFolder = '/upload/'.$date.'/'; // 上传的文件夹
        $dstPath = $dstFolder.$time.$filename;  // 上传的文件路径

        Cosapi::setTimeout(3600);

        //创建文件夹
        Cosapi::createFolder($bucketName, $dstFolder);

        //上传文件
        $bizAttr = ""; // 目录属性信息，业务自行维护
        $insertOnly = 0; // 是否覆盖同名文件:0 覆盖,1:不覆盖
        $sliceSize = 3 * 1024 * 1024;  // 分片大小
        $uploadRet = Cosapi::upload($bucketName, $srcPath, $dstPath,$bizAttr,$sliceSize, $insertOnly);
        $code = $uploadRet['code']; // 状态码 0 为成功
        $message=$uploadRet['message']; // 返回值 success 为成功
        $access_url=$uploadRet['data']['access_url'];  // 生成的资源可访问的url(仅文件有效)
        $resource_path=$uploadRet['data']['resource_path']; // 资源路径
        $source_url=$uploadRet['data']['source_url'];  // 资源url
        $url=$uploadRet['data']['url'];

        $out_url=$web_url.$resource_path;

        return $out_url;
       // dump($uploadRet);

    }

}