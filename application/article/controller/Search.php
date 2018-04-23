<?php
/**
 * Created by PhpStorm.
 * User: tanzhenxing
 * Date: 2018/4/20
 * Time: 22:40
 */
namespace app\article\controller;

use app\base\controller\Base;
use app\common\model\Article;

class Search extends Base
{
    /**
     * @throws \think\exception\DbException
     */
    public function index()
    {
        $post_data = $this->request->param();
        $this->assign('search_keyword',$post_data['keyword']);
        $article_data = new Article();
        $article = $article_data->where('title','like','%'.$post_data['keyword'].'%')
            ->where(['site_id'=>$this->site_id,'status'=>1])->paginate(5);
        foreach ($article as $article_list){
            // 生成当前文章链接
            if(!empty($article_list['redirect_url'])){
                $article_list['link'] = $article_list['redirect_url'];
            }else{
                $article_list['link'] = '/article/'.$article_list['unique_code'];
            }
            if(empty($article_list['thumb'])){
                $article_list['thumb'] = 'https://wximg-10001398.cossh.myqcloud.com/uploads/20180420/1524237262911207.png';
            }
        }
        $page = $article->render();
        $this->assign('page', $page);
        $this->assign('search_article',$article);

        return $this->fetch($this->template_path);
    }
}