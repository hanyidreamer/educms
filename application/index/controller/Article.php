<?php
/**
 * Created by PhpStorm.
 * User: tanzhenxing
 * Date: 2017/8/28
 * Time: 14:15
 */

namespace app\index\controller;

use think\Request;
use app\base\controller\TemplatePath;
use app\base\controller\SiteId;
use app\base\model\Site;
use app\base\model\CourseCategory;
use app\base\model\ArticleCategory;
use app\base\model\Article as ArticleModel;

class Article extends Base
{
    public function category($mid='',$id='')
    {
        $username = $this->username ;
        $this->assign('username',$username);

        $site_id = $this->site_id;
        $template_path = $this->template_path;

        $this->assign('mid',$mid);


        // 文章内容  判断id是否为数字
        if(is_numeric($id)){
            // id 为数字，按照id查询对应的文章数据
            $article_data = ArticleCategory::get($id);
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
            $article_data = ArticleCategory::get(['unique_code'=>$id]);
            $category_id = $article_data['id'];
            // 判断查询结果是否为空
            if(!empty($article_data)){
                $this->assign('data',$article_data);
                // 资讯列表
                $article_info = new ArticleModel();
                $article_data = $article_info->where(['site_id'=>$site_id,'category_id'=>$category_id])-> order('sort asc') ->limit(10) ->select();
                $this->assign('article',$article_data);
            }
            else{
                // 为空，跳转到404错误页面
                $this->error('文章不存在','/404.html');
            }
        }


        return $this->fetch($template_path);
    }

    public function view($mid='',$id='')
    {
        $username = $this->username ;
        $this->assign('username',$username);

        $site_id = $this->site_id;
        $template_path = $this->template_path;

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


        return $this->fetch($template_path);
    }

}