<?php
/**
 * Created by PhpStorm.
 * User: tanzhenxing
 * Date: 2016/11/2
 * Time: 14:48
 */
namespace app\admin\controller;

use app\base\controller\ArticleData;
use think\Request;
use app\common\model\Article as ArticleModel;
use app\common\model\ArticleCategory;
use app\common\model\Admin;
use app\base\controller\Upload;


class Article extends AdminBase
{
    /**
     * 文章列表
     * @param Request $request
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function index(Request $request)
    {
        $post_title= $request->post('title');
        // 分页数量
        $pages=15;
        $article_list = new ArticleModel();
        if($post_title==!''){
            $data_list =$article_list->where(['site_id'=>$this->site_id,'status'=>1])
                ->where('title','like','%'.$post_title.'%')
                ->order('id', 'desc') -> paginate($pages);
        }else{
            $data_list =$article_list->where(['site_id'=>$this->site_id,'status'=>1])->order('id', 'desc') -> paginate($pages);
        }


        $data_count = count($data_list);
        foreach($data_list as $data)
        {
            $category_id=$data->category_id;
            $category_list = ArticleCategory::get($category_id);
            $data->category=$category_list['title'];
        }
        $this->assign('data_list',$data_list);
        $this->assign('data_count',$data_count);
        return $this -> fetch($this->template_path);
    }

    /**
     * 新增文章
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function create()
    {
        $article_category_info = new ArticleCategory();
        $category_list = $article_category_info->where(['site_id'=>$this->site_id]) -> select();
        $this->assign('category_list',$category_list);
        return $this->fetch($this->template_path);
    }

    /**
     * 保存文章信息
     * @param Request $request
     * @throws \think\exception\DbException
     */
    public function save(Request $request)
    {
        $upload = new Upload();
        $post_thumb = $upload->qcloud_file('thumb');

        $admin_username= session('admin_username');
        $admin_list = Admin::get(['username'=>$admin_username]);

        $post_category= $request->post('category');
        $post_template_id = $request->post('template_id');
        if($post_template_id==""){
            $category_list = ArticleCategory::get($post_category);
            $post_template_id = $category_list['article_template_id'];
        }
        $post_redirect_url= $request->post('redirect_url');
        $post_related_articles= $request->post('related_articles');

        $post_title= $request->post('title');
        $post_short_title= $request->post('short_title');


        $post_unique_code= $request->post('unique_code');
        if($post_unique_code==""){
            $post_unique_code= 'a'.time().rand(1000,9999);
        }
        $post_keywords= $request->post('keywords');
        $post_description = $request->post('description');

        $post_click= $request->post('click');
        $post_sort = $request->post('sort');
        $post_author= $request->post('author');
        $article_body = $request->post('myVent');
        $article_data = new ArticleData();
        $post_body = $article_data->info($article_body);
        $article_first_img = $article_data->first_img($post_body);

        $post_status= $request->post('status');
        if($post_title=='' or $post_category==''){
            $this->error('文章标题和分类不能为空');
        }
        $data = new ArticleModel;
        $data['title'] = $post_title;
        $data['category_id'] = $post_category;
        $data['mid'] = $admin_list['id'];
        $data['site_id'] = $this->site_id;
        $data['short_title'] = $post_short_title;
        $data['unique_code'] = $post_unique_code;
        $data['keywords'] = $post_keywords;
        $data['description'] = $post_description;
        if(!empty($post_thumb)){
            $data['thumb'] = $post_thumb;
        }else{
            $data['thumb'] = $article_first_img;
        }
        $data['click'] = $post_click;
        $data['sort'] = $post_sort;
        $data['author'] = $post_author;
        $data['template_id'] = $post_template_id;
        $data['redirect_url'] = $post_redirect_url;
        $data['related_articles'] = $post_related_articles;

        $data['body'] = $post_body;
        $data['status'] = $post_status;

        if ($data->save()) {
            $this->success('新增文章成功', '/admin/article/index');
        } else {
            $this->error('操作失败');
        }

    }

    /**
     * @param $id
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function edit($id)
    {
        $article_info = new ArticleModel();
        $data_list = $article_info -> get($id);
        $this->assign('data_list',$data_list);

        $my_category_id = $data_list['category_id'];
        $this->assign('my_category_id',$my_category_id);

        $article_category_info = new ArticleCategory();
        $category_list = $article_category_info->where(['status'=>1,'site_id'=>$this->site_id]) -> select();

        $this->assign('category_list',$category_list);

        return $this->fetch($this->template_path);
    }

    /**
     * 更新文章信息
     * @param Request $request
     * @throws \think\exception\DbException
     */
    public function update(Request $request)
    {
        $upload = new Upload();
        $post_thumb = $upload->qcloud_file('thumb');

        $admin_username= session('username');
        $admin_list = Admin::get(['username'=>$admin_username]);
        $mid=$admin_list['id'];

        $post_id= $request->post('id');

        $post_category= $request->post('category');
        $post_template_id= $request->post('template_id');
        if($post_template_id==""){
            $category_list = ArticleCategory::get($post_category);
            $post_template_id=$category_list['article_template_id'];
        }
        $post_redirect_url= $request->post('redirect_url');
        $post_related_articles= $request->post('related_articles');

        $post_title= $request->post('title');
        $post_short_title= $request->post('short_title');


        $post_unique_code= $request->post('unique_code');
        if($post_unique_code==""){
            $post_unique_code= 'a'.time().rand(1000,9999);
        }
        $post_keywords= $request->post('keywords');
        $post_description = $request->post('description');

        $post_click= $request->post('click');
        $post_sort = $request->post('sort');
        $post_author= $request->post('author');
        $article_body = $request->post('myVent');
        $article_data = new ArticleData();
        $post_body = $article_data->info($article_body);
        $article_first_img = $article_data->first_img($post_body);

        $post_status= $request->post('status');
        if($post_title=='' or $post_category==''){
            $this->error('文章标题和分类不能为空');
        }
        $data = ArticleModel::get($post_id);
        $data['title'] = $post_title;
        $data['category_id'] = $post_category;
        $data['mid'] = $mid;
        $data['short_title'] = $post_short_title;
        $data['unique_code'] = $post_unique_code;
        $data['keywords'] = $post_keywords;
        $data['description'] = $post_description;

        if(!empty($post_thumb)){
            $data['thumb'] = $post_thumb;
        }else{
            $data['thumb'] = $article_first_img;
        }
        $data['click'] = $post_click;
        $data['sort'] = $post_sort;
        $data['author'] = $post_author;
        $data['template_id'] = $post_template_id;
        $data['redirect_url'] = $post_redirect_url;
        $data['related_articles'] = $post_related_articles;

        $data['body'] = $post_body;
        $data['status'] = $post_status;
        if ($data->save()) {
            $this->success('保存文章内容成功', '/admin/article/index');
        } else {
            $this->error('操作失败');
        }

    }

    /**
     * @param $id
     * @throws \think\exception\DbException
     */
    public function delete($id)
    {
        $user = ArticleModel::get($id);
        $user['status'] = 0;
        if ($user->save()) {
            $this->success('文章已删除', '/admin/article/recycle');
        } else {
            $this->error('操作失败');
        }
    }

    /**
     * @param Request $request
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function recycle(Request $request)
    {
        $post_title= $request->post('title');
        // 分页数量
        $pages=15;
        $article_info = new ArticleModel();
        if($post_title==!''){
            $data_list = $article_info->where(['status'=>0])
                ->where('title','like','%'.$post_title.'%')
                ->order('id','desc')  -> paginate($pages);
        }else{
            $data_list = $article_info->where(['status'=>0])->order('id','desc')  -> paginate($pages);
        }

        $data_count = count($data_list);
        foreach($data_list as $data)
        {
            $category_id=$data->category_id;
            $category_list = ArticleCategory::get(['id'=>$category_id]);
            $data->category=$category_list['title'];
        }
        $this->assign('data_list',$data_list);
        $this->assign('data_count',$data_count);
        return $this -> fetch($this->template_path);
    }

    /**
     * 恢复网站
     * @param $id
     * @throws \think\exception\DbException
     */
    public function recovery($id){
        $user = ArticleModel::get($id);
        $user['status'] = 1;
        if ($user->save()) {
            $this->success('文章已恢复', '/admin/article/recycle');
        } else {
            $this->error('操作失败');
        }
    }

    /**
     * 永久删除
     * @param $id
     * @throws \think\exception\DbException
     */
    public function del($id)
    {
        $user = ArticleModel::get($id);
        if ($user) {
            $user->delete();
            $this->success('文章已经永久删除', '/admin/article/recycle');
        } else {
            $this->error('您要删除的文章不存在');
        }
    }

}