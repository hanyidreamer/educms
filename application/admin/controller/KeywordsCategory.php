<?php
/**
 * Created by PhpStorm.
 * User: tanzhenxing
 * Date: 2017/4/14
 * Time: 22:14
 */
namespace app\admin\controller;

use think\Request;
use app\base\model\KeywordsCategory as KeywordsCategoryModel;


class KeywordsCategory extends AdminBase
{
    /**
     * @param Request $request
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index(Request $request)
    {
        $post_name= $request->post('title');
        if($post_name==!''){
            $data_sql['title'] =  ['like','%'.$post_name.'%'];
        }
        $data_sql['status'] = 1;
        $keywords_data = new KeywordsCategoryModel();
        $data_list = $keywords_data->where($data_sql) -> select();
        $data_count = count($data_list);
        foreach($data_list as $data)
        {
            $data->id;
            $data->parent_id;
            $data->title;
            $data->status;
            $data->update_time;
        }
        $this->assign('data_list',$data_list);
        $this->assign('data_count',$data_count);
        return $this -> fetch();
    }

    /**
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function add()
    {
        $category_sql['parent_id'] = 0;
        $category_data = new KeywordsCategoryModel();
        $category_list = $category_data->where($category_sql) -> select();
        foreach($category_list as $data)
        {
            $data->id;
            $data->title;
        }
        $this->assign('category_list',$category_list);
        $title='添加关键词分类';
        $this->assign('title',$title);

        return $this->fetch();
    }

    /**
     * @param Request $request
     */
    public function insert(Request $request)
    {

        $post_title= $request->post('title');
        $post_category= $request->post('category');
        $post_status= $request->post('status');

        if($post_title==''){
            $this->error('分类名称不能为空');
        }
        $user = new KeywordsCategoryModel;
        $user['title'] = $post_title;
        $user['parent_id '] = $post_category;

        $user['status'] = $post_status;

        if ($user->save()) {
            $this->success('新增分类成功', '/admin/keywords_category/index');
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
        $title='编辑文章分类';
        $this->assign('title',$title);

        $category_sql['parent_id'] = 0;
        $category_data = new KeywordsCategoryModel();
        $category_list = $category_data->where($category_sql) -> select();
        foreach($category_list as $data)
        {
            $data->id;
            $data->title;
        }
        $this->assign('category_list',$category_list);

        $data_sql['id'] = $id;
        $data_list = KeywordsCategoryModel::get($id);
        $this->assign('data_list',$data_list);
        return $this->fetch();
    }

    /**
     * @param Request $request
     * @throws \think\exception\DbException
     */
    public function save(Request $request)
    {
        $post_id= $request->post('id');
        $post_title= $request->post('title');
        $post_parent_id= $request->post('parent_id');

        $post_status= $request->post('status');

        if($post_title==''){
            $this->error('分类名称不能为空');
        }

        $user = KeywordsCategoryModel::get($post_id);
        $user['title'] = $post_title;
        $user['parent_id'] = $post_parent_id;
        $user['status'] = $post_status;

        if ($user->save()) {
            $this->success('保存分类信息成功', '/admin/keywords_category/index');
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
        $user = KeywordsCategoryModel::get($id);
        if ($user) {
            $user->delete();
            $this->success('删除分类成功', '/admin/keywords_category/index');
        } else {
            $this->error('您要删除的分类不存在');
        }
    }

}