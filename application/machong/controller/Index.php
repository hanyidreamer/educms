<?php
/**
 * Created by PhpStorm.
 * User: tanzhenxing
 * Date: 2018/4/4
 * Time: 16:07
 */
namespace app\machong\controller;

use app\machong\model\Ad;
use app\machong\model\Article;
use app\machong\model\ArticleCate;
use app\machong\model\Setting;
use think\Db;

class Index extends Base
{
    /**
     * 麻涌旅游首页
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index()
    {
        $data = new Setting();
        // 标题
        $title_data = $data->where(['valuename'=>'title'])->find();
        $title = $title_data['valuetxt'];
        $this->assign('title',$title);

        // 描述
        $keywords_data = $data->where(['valuename'=>'keywords'])->find();
        $keywords = $keywords_data['valuetxt'];
        $this->assign('keywords',$keywords);

        // 关键词
        $description_data = $data->where(['valuename'=>'Description'])->find();
        $description = $description_data['valuetxt'];
        $this->assign('description',$description);

        // 幻灯片数据
        $slide_type = 'flash';
        $ad = new Ad();
        $slide = $ad->where(['type'=>$slide_type])->order('sort','asc')->select();
        $this->assign('slide',$slide);
       return $this->fetch();
    }

    /**
     * 分类列表
     * @param $id
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function category($id)
    {
        $category = new ArticleCate();
        $category_info = $category->find($id);
        $this->assign('category_info',$category_info);
        // 判断子分类是否为空
        $sub_category = $category->where(['pid'=>$category_info['id'],'status'=>1])->select();
        $count_sub_category = count($sub_category);

        if(!empty($count_sub_category)){
            // 子分类不为空,显示子分类列表
            $this->assign('category',$sub_category);
            // index 普通文章列表 index_newspic 图文列表 techan2 特产列表
            if($category_info['tpl']=='index'){
                $template = 'index_list';
            }else{
                $template = $category_info['tpl'];
            }
        }else{
            // 子分类为空，显示当前分类所属的文章列表
            $article = new Article();
            $article_data = $article->where(['pid'=>$category_info['id']])->order('sort','desc')->select();
            $template = 'category';
            $this->assign('category',$article_data);
        }

        return $this->fetch($template);
    }

    // 文章详细页
    public function article()
    {

    }

    /**
     * 搜索页
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function search()
    {
        $keyword = $this->request->param('keyword');
        if (!empty($keyword)){
            //$search_data = new Article;
            // $search = $search_data->where('title' ,'like','%'.$keyword.'%')->select();
            $search = Db::name('article')->where('title' ,'like','%'.$keyword.'%')->where('status','=',1)->select();
        }else {
            $search = '';
        }
        $this->assign('search',$search);
        return $this->fetch();
    }

    /**
     * 搜索首页
     * @return mixed
     */
    public function search_index()
    {
        return $this->fetch();
    }
}