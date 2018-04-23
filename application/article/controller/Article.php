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
use app\common\model\ArticleCategory;

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
        if(is_numeric($id)) {
            $article_data = ArticleModel::get($id);
            $this->redirect('/article/'.$article_data['unique_code'],301);
        }

        // 获取当前文章数据
        $article_data = ArticleModel::get(['unique_code'=>$id,'site_id'=>$this->site_id]);
        if(empty($article_data)) {
            // 为空，跳转到404错误页面
            $this->error('文章不存在', '/405.html');
        }
        
        $this->assign('article',$article_data);

        // 当前分类信息
        $category = ArticleCategory::get($article_data['category_id']);
        // 判断是否设置跳转链接
        if(!empty($category['redirect_url'])){
            $category['link'] = $category['redirect_url'];
        }else{
            $category['link'] = '/article_category/'.$category['unique_code'];
        }
        $this->assign('in_category',$category);

        // 当前分类所有子分类
        if($category['parent_id'] == 0){
            $my_parent_id = $category['id'];
        }else{
            $my_parent_id = $category['parent_id'];
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