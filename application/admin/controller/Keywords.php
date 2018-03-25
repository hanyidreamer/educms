<?php
/**
 * Created by PhpStorm.
 * User: tanzhenxing
 * Date: 2017/4/14
 * Time: 20:04
 */
namespace app\admin\controller;

use think\Request;
use app\index\model\Keywords as KeywordsModel;
use app\index\model\KeywordsCategory;

class Keywords extends Base
{
    public function index(Request $request)
    {
        $title='关键词管理';
        $this->assign('title',$title);

        $post_title= $request->post('keyword');
        if($post_title==!''){
            $data_sql['keyword'] =  ['like','%'.$post_title.'%'];
        }
        // 分页数量
        $pages=15;
        $keywords_data = new KeywordsModel;
        $data_list = $keywords_data->where(['status'=>1])->order('id asc')  -> paginate($pages);
        $data_count = count($data_list);
        foreach($data_list as $data)
        {
            $category_id=$data->category_id;
            $category_list = KeywordsCategory::get($category_id);
            $data->category=$category_list['title'];

            $data->article_id;
            $data->keyword;
            $data->pc;
            $data->mobile;
            $data->status;
            $data->update_time;
        }
        $this->assign('data_list',$data_list);
        $this->assign('data_count',$data_count);
        return $this -> fetch();
    }

    public function add()
    {
        $title='添加关键词';
        $this->assign('title',$title);

        $keywords_data = new KeywordsCategory;
        $category_list = $keywords_data->where(['status'=>1]) -> select();

        $this->assign('category_list',$category_list);
        return $this->fetch();
    }

    public function insert(Request $request)
    {
        $post_category_id= $request->post('category');
        $post_keyword= $request->post('keyword');
        $post_pc= $request->post('pc');
        $post_mobile = $request->post('mobile');
        $post_article_id= $request->post('article_id');
        $post_status= $request->post('status');

        $keyword_data = KeywordsModel::get(['keyword'=>$post_keyword]);

        if(!empty($keyword_data)){
            $this->error('关键词已经存在');
        }
        if(empty($post_keyword)){
            $this->error('关键词不能为空');
        }

        $user = new KeywordsModel;
        $user['category_id'] = $post_category_id;
        $user['keyword'] = $post_keyword;
        $user['pc'] = $post_pc;
        $user['mobile'] = $post_mobile;
        $user['article_id'] = $post_article_id;
        $user['status'] = $post_status;

        if ($user->save()) {
            $this->success('新增关键词成功', '/admin/keywords/index');
        } else {
            $this->error('操作失败');
        }
    }

    public function edit($id)
    {
        $title='编辑关键词';
        $this->assign('title',$title);

        $category_data = new KeywordsCategory();
        $category_list = $category_data->where(['status'=>1]) -> select();
        foreach($category_list as $data)
        {
            $data->id;
            $data->title;
        }
        $this->assign('category_list',$category_list);

        $data_list = KeywordsModel::get($id);

        $this->assign('data_list',$data_list);
        return $this->fetch();
    }

    public function save(Request $request)
    {
        $post_id= $request->post('id');
        $post_category_id= $request->post('category');
        $post_keyword= $request->post('keyword');
        $post_pc= $request->post('pc');
        $post_mobile = $request->post('mobile');
        $post_article_id= $request->post('article_id');
        $post_status= $request->post('status');


        if($post_keyword==""){
            $this->error('关键词不能为空');
        }
        $user = KeywordsModel::get($post_id);
        $user['category_id'] = $post_category_id;
        $user['keyword'] = $post_keyword;
        $user['pc'] = $post_pc;
        $user['mobile'] = $post_mobile;
        $user['article_id'] = $post_article_id;
        $user['status'] = $post_status;

        if ($user->save()) {
            $this->success('修改关键词成功', '/admin/keywords/index');
        } else {
            $this->error('操作失败');
        }
    }

    public function delete($id)
    {
        $user = KeywordsModel::get($id);
        $user['status'] = 0;
        if ($user->save()) {
            $this->success('关键词已删除', '/admin/keywords/recycle');
        } else {
            $this->error('操作失败');
        }
    }

    public function recycle(Request $request){
        $title='关键词回收站';
        $this->assign('title',$title);

        $post_title= $request->post('keyword');
        if($post_title==!''){
            $data_sql['keyword'] =  ['like','%'.$post_title.'%'];
        }
        // 分页数量
        $pages=15;
        $keywords_data = new KeywordsModel();
        $data_list = $keywords_data->where(['status'=>0])->order('id asc')  -> paginate($pages);
        $data_count = count($data_list);
        foreach($data_list as $data)
        {
            $category_id = $data->category_id;
            $category_list = KeywordsCategory::get($category_id);
            $data->category = $category_list['title'];
        }
        $this->assign('data_list',$data_list);
        $this->assign('data_count',$data_count);
        return $this -> fetch();
    }

    // 恢复网站
    public function recovery($id){
        $user = KeywordsModel::get($id);
        $user['status'] = 1;
        if ($user->save()) {
            $this->success('关键词已恢复', '/admin/keywords/recycle');
        } else {
            $this->error('操作失败');
        }
    }

    //永久删除
    public function del($id)
    {
        $user = KeywordsModel::get($id);
        if ($user) {
            $user->delete();
            $this->success('关键词已经永久删除', '/admin/keywords/recycle');
        } else {
            $this->error('您要删除的文章不存在');
        }
    }

}