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
     * @param string $id
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
        if(empty($category_data)) {
            // 为空，跳转到404错误页面
            $this->error('文章不存在', '/405.html');
        }
        $this->assign('category',$category_data);

        $category_article_data = new Article();
        $category_article = $category_article_data->where(['category_id'=>$category_data['id']])->select();
        $this->assign('category_article',$category_article);

        return $this->fetch($this->template_path);
    }
}