<?php
/**
 * Created by PhpStorm.
 * User: tanzhenxing
 * Date: 2018/4/12
 * Time: 14:34
 */
namespace app\article\controller;

use app\base\controller\Base;
use app\common\model\ArticleCategory;
use app\common\model\Article;

class Category extends Base
{
    /**
     * 文章分类
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function view()
    {
        $id = $this->request->param('id');
        if(is_numeric($id)) {
            $category_data = ArticleCategory::get($id);
            $this->redirect('/article_category/'.$category_data['unique_code'],301);
        }
        $category_data = ArticleCategory::get(['unique_code'=>$id,'site_id'=>$this->site_id]);
        // 判断当前分类是否存在，不存在就跳转到404错误页面
        if(empty($category_data)) {
            $this->error('文章不存在', '/405.html');
        }
        // 判断当前分类是否设置了跳转链接
        if(!empty($category_data['redirect_url'])){
            $this->redirect($category_data['redirect_url'],301);
        }

        // 生成当前分类链接
        if(!empty($category_data['redirect_url'])){
            $category_data['link'] = $category_data['redirect_url'];
        }else{
            $category_data['link'] = '/article_category/'.$category_data['unique_code'];
        }
        $this->assign('in_category',$category_data);

        // 当前分类所属的文章
        $category_article_data = new Article();
        $category_article = $category_article_data->where(['category_id'=>$category_data['id']])->paginate(5);
        $page = $category_article->render();
        $this->assign('page', $page);
        // 获取总记录数
        $article_count = $category_article->total();
        $this->assign('article_count', $article_count);
        foreach ($category_article as $article){
            // 生成当前文章链接
            if(!empty($article['redirect_url'])){
                $article['link'] = $article['redirect_url'];
            }else{
                $article['link'] = '/article/'.$article['unique_code'];
            }
        }
        $this->assign('in_category_article',$category_article);

        // 当前分类所有子分类
        if($category_data['parent_id'] == 0){
            $my_parent_id = $category_data['id'];
        }else{
            $my_parent_id = $category_data['parent_id'];
        }
        $sub_category_data = new ArticleCategory();
        $sub_category = $sub_category_data->where(['parent_id'=>$my_parent_id])->select();
        foreach ($sub_category as $item){
            // 判断是否设置跳转链接
            if(!empty($item['redirect_url'])){
                $item['link'] = $item['redirect_url'];
            }else{
                $item['link'] = '/article_category/'.$item['unique_code'];
            }
        }
        $this->assign('sub_category',$sub_category);


        return $this->fetch($this->template_path);
    }
}