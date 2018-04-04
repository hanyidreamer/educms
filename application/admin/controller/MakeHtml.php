<?php
/**
 * Created by PhpStorm.
 * User: tanzhenxing
 * Date: 2017/4/12
 * Time: 22:52
 */
namespace app\admin\controller;

use think\Request;

class MakeHtml extends AdminBase
{
    public function index()
    {
        return $this->fetch();
    }

    // 生成首页
    public function make_home(Request $request)
    {
        // 获取首页网址

        // 获取首页html代码

        // 把首页html代码保存到 网站根目录/index.html

        // 返回生成结果


    }

    // 生成列表页（分类页）
    public function make_list()
    {
        // 获取列表网址清单

        // 获取列表html代码

        // 把列表html代码保存到 列表目录/列表标识码index.html

        // 返回生成结果
    }

    // 生成文章页
    public function make_article()
    {
        // 获取文章网址列表

        // 获取文章html代码

        // 把首页html代码保存到 文章目录/文章唯一标识码.html

        // 返回生成结果
    }
}