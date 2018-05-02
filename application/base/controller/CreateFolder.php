<?php
/**
 * Created by PhpStorm.
 * User: tanzhenxing
 * Date: 2018/5/2
 * Time: 15:02
 */
namespace app\base\controller;

use think\Controller;

class CreateFolder extends Controller
{
    public function path($dir = '',$mode = '0777')
    {
        if(!file_exists($dir)) {
            if(mkdir($dir,$mode,true)) {
                return 1; // 创建文件夹成功
            }else{
                return 0; // 创建文件夹失败
            }
        } else {
            return 2; // 文件夹已经存在
        }
    }
}