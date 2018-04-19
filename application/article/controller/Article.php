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
        if(is_numeric($id)) {
            $article_data = ArticleModel::get($id);
            $this->redirect('/article/'.$article_data['unique_code'],301);
        }

        // 获取当前文章数据
        $article_data = ArticleModel::get(['unique_code'=>$id]);
        if(empty($article_data)) {
            // 为空，跳转到404错误页面
            $this->error('文章不存在', '/405.html');
        }
        
        $this->assign('data',$article_data);
        return $this->fetch($this->template_path);
    }

}