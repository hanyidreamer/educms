<?php
/**
 * Created by PhpStorm.
 * User: tanzhenxing
 * Date: 2017/8/28
 * Time: 14:15
 */

namespace app\article\controller;

use app\common\model\Article as ArticleModel;
use app\base\controller\Base;

class Article extends Base
{
    /**
     * 查看文章
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function view()
    {
        $id = $this->request->param('id');

        // 文章内容  判断id是否为数字
        if(is_numeric($id)){
            // id 为数字，按照id查询对应的文章数据
            $article_data = ArticleModel::get($id);
            // 判断查询结果是否为空
            if(!empty($article_data)){
                // 不为空
                $article_unique_code = $article_data['unique_code'];
                if(!empty($article_unique_code)){
                    // 不为空,301重定向到自定义文章后缀的网址
                    $this->redirect('/article/'.$article_unique_code,301);
                }
                else{
                    // 为空,没有定义文章后缀的网址
                    $this->error('文章不存在','/404.html');
                }
            }
            else{
                // 为空，跳转到404错误页面
                $this->error('文章不存在','/404.html');
            }
        }
        // id 不为数字，按照unique_code查询对应的文章数据
        else{
            $article_data = ArticleModel::get(['unique_code'=>$id]);
            // 判断查询结果是否为空
            if(!empty($article_data)){
                $this->assign('data',$article_data);
            }
            else{
                // 为空，跳转到404错误页面
                $this->error('文章不存在','/404.html');
            }
        }


        return $this->fetch($this->template_path);
    }

}