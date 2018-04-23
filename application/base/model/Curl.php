<?php
/**
 * Created by PhpStorm.
 * User: tanzhenxing
 * Date: 2016/11/22
 * Time: 14:35
 */
namespace app\base\model;

use think\Model;

class Curl extends Model
{
    //检测字符串编码的方法,将编码$code转换为utf-8编码!
    function safeEncoding($str){
        $code=mb_detect_encoding($str,array('GB2312','GBK','UTF-8','ASCII'));//检测字符串编码
        if($code=="CP936"){
            $result=$str;
        }else{
            //$result=mb_convert_encoding($str,'UTF-8',$code);//将编码$code转换为utf-8编码
            $result=iconv($code,"UTF-8",$str);
        }
        return $result;
    }

    public function get_info($url,$timeout,$user_agent)
    {
        $ch = curl_init();
        curl_setopt ($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt ( $ch, CURLOPT_USERAGENT,$user_agent);
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
        // curl_setopt($ch, CURLOPT_BINARYTRANSFER, true) ;
        // curl_setopt($ch, CURLOPT_ENCODING, 'gzip,deflate');
        curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $html = curl_exec($ch);
        curl_close($ch);

        // 转换字符编码（gbk to utf8）
        $html=$this->safeEncoding($html);
        return $html;
    }
}