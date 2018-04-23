<?php
/**
 * Created by PhpStorm.
 * User: tanzhenxing
 * Date: 2017/4/5
 * Time: 15:52
 */
namespace app\base\controller;

use app\common\model\QcloudCos;
use Qcloud\Cos\Client;
use think\Controller;
use think\facade\Env;


class Upload extends Controller
{
    /**
     * 上传默认界面
     * @return mixed
     */
    public function index()
    {
        return $this->fetch();
    }

    /**
     * 上传本地服务器文件
     * @param string $name
     * @return string
     */
    public function upload_file($name = 'file')
    {
        $file = $this->request->file($name);
        $qcloud_file_path = '';
        if(!empty($file)){
            $info = $file->move( 'uploads');
            if($info){
                $file_ext = $info->getExtension();
                $file_ext_array = array('jpg','png','bmp','gif','ico','rar','zip','jpeg','xls','xlsx','doc','docx','pdf','ico');
                if (!in_array($file_ext, $file_ext_array))
                {
                    $this->error('不支持.'.$file_ext.'格式的文件上传！');
                }
                $file_name = $info->getFilename();
                $file_time = date('Ymd',time());
                $qcloud_file_path = 'uploads/'.$file_time.'/'.$file_name;
            }else{
                // 上传失败获取错误信息
                return $file->getError();
            }
        }
        return $qcloud_file_path;
    }

    /**
     * 上传到腾讯云
     * 使用说明：https://cloud.tencent.com/document/product/436/12266
     * @param string $name
     * @return string
     * @throws \think\exception\DbException
     */
    public function qcloud_file($name = 'file')
    {
        // 网站信息
        $site_data = new Site();
        $site_info = $site_data->info();

        $qcloud_file_path = '';
        $upload_local_path = '../runtime/uploads';
        $file = $this->request->file($name);
        // $file_info = $file->getInfo(); // 当前上传的文件信息

        if(!empty($file)){
            $info = $file->move($upload_local_path);
            if($info){
                $file_ext = $info->getExtension();
                $file_ext_array = array('jpg','png','bmp','gif','ico','rar','zip','jpeg','xls','xlsx','doc','docx','pdf','ico');
                if (!in_array($file_ext, $file_ext_array))
                {
                    $this->error('不支持.'.$file_ext.'格式的文件上传！');
                }
                $file_path = $info->getSaveName();
                $root_path = Env::get('root_path');
                $local_file_path = $root_path.'runtime\uploads\\'.$file_path;
                $file_name = $info->getFilename();
                $file_time = date('Ymd',time());
                $qcloud_file_path = 'uploads/'.$file_time.'/'.$file_name;

                // 上传到腾讯云
                $cos_data = new QcloudCos();
                $cos_info = $cos_data->get(['site_id'=>$site_info['id']]);
                if(empty($cos_info)){
                    $cos_info = $cos_data->get(['site_id'=>0]);
                }
                $cosClient = new Client(array('region' => $cos_info['region'],
                    'credentials'=> array(
                        'appId' => $cos_info['app_id'],
                        'secretId'    => $cos_info['secret_id'],
                        'secretKey' => $cos_info['secret_key'])));
                $result = $cosClient->upload(
                    $bucket = $cos_info['bucket_name'].'-'.$cos_info['app_id'],
                    $key = $qcloud_file_path,
                    $body = fopen($local_file_path, 'rb'), // rb表示只读方式打开一个二进制文件
                    $options = array(
                        "ACL"=>'private',
                        'CacheControl' => 'private'));
                if(empty($result['Location'])){
                    $this->error('文件上传不成功');
                }
                $qcloud_file_path = $cos_info['url'].$qcloud_file_path;
            }else{
                // 上传失败获取错误信息
                return $file->getError();
            }
        }
        return $qcloud_file_path;
    }

    /**
     * 上传单个文件到腾讯云存储
     * @param $upload_array
     * @return string
     * @throws \think\exception\DbException
     */
    public function qcloud_upload($upload_array)
    {
        if(empty($upload_array['file_path'])){
            return '';
        }
        // 把图片上传到腾讯云
        $local_file_path = $upload_array['file_path'];
        $local_file_name = $upload_array['title'];

        $site_data = new Site();
        $site_info = $site_data->info();

        $cos_data = new QcloudCos();
        $cos_info = $cos_data->get(['site_id'=>$site_info['id']]);
        if(empty($cos_info)){
            $cos_info = $cos_data->get(['site_id'=>0]);
        }

        $cosClient = new Client(array('region' => $cos_info['region'],
            'credentials'=> array(
                'appId' => $cos_info['app_id'],
                'secretId'    => $cos_info['secret_id'],
                'secretKey' => $cos_info['secret_key'])));

        $file_time = date('Ymd',time());
        $qcloud_file_path = 'uploads/'.$file_time.'/'.$local_file_name;

        $cos_result = $cosClient->upload(
            $bucket = $cos_info['bucket_name'].'-'.$cos_info['app_id'],
            $key = $qcloud_file_path,
            $body = fopen($local_file_path, 'rb'), // rb表示只读方式打开一个二进制文件
            $options = array(
                "ACL"=>'private',
                'CacheControl' => 'private'));
        if(empty($cos_result['Location'])){
            $this->error('文件上传不成功');
        }
        $qcloud_file_url = $cos_info['url'].$qcloud_file_path;

        $upload_array['url'] = $qcloud_file_url;
        unset($upload_array['file_path']);
        return $upload_array;
    }


}