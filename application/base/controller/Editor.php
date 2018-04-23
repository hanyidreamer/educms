<?php
/**
 * Created by PhpStorm.
 * User: tanzhenxing
 * Date: 2018/4/16
 * Time: 21:59
 */
namespace app\base\controller;

use think\Controller;
use Ueditor\Ueditor;

class Editor extends Controller
{
    /**
     * 百度编辑器
     * @param string $action
     * @param string $params
     * @return \think\response\Json
     * @throws \think\exception\DbException
     */
    public function index($action = '', $params = '')
    {
        $ueditor = new Ueditor();

        switch ($action) {
            case 'config':
                $result = $ueditor->getConfig();
                break;

            /* 上传图片 */
            case 'uploadimage':
                $upload_array = $ueditor->uploadImage()->saveImage();
                $qcloud_file = new Upload();
                $result = $qcloud_file->qcloud_upload($upload_array);
                break;
            /* 上传涂鸦 */
            case 'uploadscrawl':
                $upload_array = $ueditor->uploadImage()->saveScrawl();
                $qcloud_file = new Upload();
                $result = $qcloud_file->qcloud_upload($upload_array);
                break;
            /* 上传视频 */
            case 'uploadvideo':
                $upload_array = $ueditor->uploadImage()->saveVideo();
                $qcloud_file = new Upload();
                $result = $qcloud_file->qcloud_upload($upload_array);
                break;
            /* 上传文件 */
            case 'uploadfile':
                $upload_array = $ueditor->uploadImage()->saveFile();
                $qcloud_file = new Upload();
                $result = $qcloud_file->qcloud_upload($upload_array);
                break;

            /* 列出图片 */
            case 'listimage':
                $start = $params('start');
                $size = $params( 'size');
                $result = $ueditor->listImage()->getListImage($start, $size);

                break;
            /* 列出文件 */
            case 'listfile':
                $start = $params('start');
                $size = $params('size');
                $result = $ueditor->listFile()->getListFile($start, $size);
                break;

            /* 抓取远程文件 */
            case 'catchimage':
                $upload_array = $ueditor->catchImage()->crawlerImage();
                $count_list = count($upload_array['list']);
                for($i=0;$i<$count_list;$i++){
                    $qcloud_file = new Upload();
                    $upload_array['list'][$i] = $qcloud_file->qcloud_upload($upload_array['list'][$i]);
                }
                $result = $upload_array;
                break;

            default:
                $result = [
                    'state'=> '请求地址出错'
                ];
                break;
        }

        return json($result);
    }

}