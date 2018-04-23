<?php
/**
 * Created by PhpStorm.
 * User: tanzhenxing
 * Date: 2018/4/17
 * Time: 10:38
 */
namespace app\base\controller;

use think\Controller;

class ImgUrl extends Controller
{
    /**
     * 微信图片转换
     */
    public function wei_xin()
    {
        $url = $this->request->baseUrl();
        $img_url = preg_replace('/\/wx_img/','',$url);
        $img_url = 'http://mmbiz.qpic.cn'.$img_url;
        $img_data = $this->curl($img_url);
        header('content-type: image/png');
        echo $img_data;
        exit;
    }

    /**
     * QQ新闻图片转换
     */
    public function qq_news()
    {
        $url = $this->request->baseUrl();
        $img_url = preg_replace('/\/qq_img/','',$url);
        $img_url = 'http://inews.gtimg.com'.$img_url;
        $img_data = $this->curl($img_url);
        header('content-type: image/png');
        echo $img_data;
        exit;
    }

    public function curl($img_url)
    {
        $defaultUserAgent = 'Mozilla/5.0 (X11; Linux i686) AppleWebKit/535.19 (KHTML, like Gecko) Ubuntu/10.10 Chromium/18.0.1025.151 Chrome/18.0.1025.151 Safari/535.19';
        $_header = array(
            "User-Agent: {$defaultUserAgent}"
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $img_url);
        curl_setopt($ch, CURLOPT_HTTPGET, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30 );
        curl_setopt($ch, CURLOPT_HTTPHEADER, $_header);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        $result = curl_exec($ch);
        return $result;
    }
}