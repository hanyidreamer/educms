<?php
/**
 * Created by PhpStorm.
 * User: tanzhenxing
 * Date: 2018/4/17
 * Time: 11:07
 */
namespace app\base\controller;

use think\Controller;

class ArticleData extends Controller
{
    /**
     * 文章中的数据处理，替换文章中的外部图片
     * @param $article_data
     * @return null|string|string[]
     */
    public function info($article_data)
    {
        // 修改微信图片url
        $article_data = preg_replace('/http:\/\/mmbiz.qpic.cn\//is','/wx_img/',$article_data);
        $article_data = preg_replace('/https:\/\/mmbiz.qpic.cn\//is','/wx_img/',$article_data);
        // 修改QQ新闻图片url
        $article_data = preg_replace('/http:\/\/inews.gtimg.com\//is','/qq_img/',$article_data);
        $article_data = preg_replace('/https:\/\/inews.gtimg.com\//is','/qq_img/',$article_data);
        return $article_data;
    }

    /**
     * 文章中的首张图片
     * @param $article_data
     * @return mixed
     */
    public function first_img($article_data)
    {
        // 匹配文章中的第一张图片
        preg_match('/<img.*?src="(.*?)".*?>/is',$article_data,$first_img);
        return $first_img[1];
    }
}