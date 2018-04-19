<?php
/**
 * Created by PhpStorm.
 * User: tanzhenxing
 * Date: 2017/6/26
 * Time: 15:34
 */
namespace app\admin\controller;

use think\Request;
use app\common\model\Template as TemplateModel;
use app\common\model\TemplateCategory;

class Template extends AdminBase
{
    /**
     * 模板管理
     * @param Request $request
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index(Request $request)
    {
        $post_title = $request->param('title');
        $data = new TemplateModel;
        if(!empty($post_title)){
            $data_list = $data->where(['status' => 1,'site_id'=>$this->site_id])
                ->where('title','like','%'.$post_title.'%')
                ->select();
        }else{
            $data_list = $data->where(['site_id'=>$this->site_id,'status'=>1])->select();
        }
        $data_count = count($data_list);
        $this->assign('data_count',$data_count);

        foreach ($data_list as $data){
            $category_id = $data['category_id'];
            $category_data = TemplateCategory::get($category_id);
            $category_title = $category_data['title'];
            $data['category_title'] = $category_title;
        }

        $this->assign('data_list',$data_list);

        return $this->fetch($this->template_path);
    }

    /**
     * 新增
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function create()
    {
        // 获取分类列表
        $category_data = new TemplateCategory();
        $category = $category_data->where(['status'=>1])->select();
        $this->assign('category',$category);

        return $this->fetch($this->template_path);
    }

    /**
     * 保存
     * @param Request $request
     */
    public function save(Request $request)
    {
        $post_category_id = $request->param('category_id');
        $post_sort = $request->param('sort');
        $post_title = $request->param('title');
        $post_tag = $request->param('tag');
        $post_status = $request->param('status');

        if(empty($post_title)){
            $this->error('名称不能为空');
        }

        $data = new TemplateModel();
        $data['site_id'] = $this->site_id;
        $data['title'] = $post_title;
        $data['sort'] = $post_sort;
        $data['category_id'] = $post_category_id;
        $data['tag'] = $post_tag;
        $data['status'] = $post_status;
        if ($data->save()) {
            $this->success('保存成功','/admin/template/index');
        } else {
            $this->error('操作失败');
        }
    }

    /**
     * 编辑
     * @param $id
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function edit($id)
    {
        $site_id = $this->site_id;
        // 获取当前分类id
        $category_id_info = TemplateModel::get($id);
        $category_id = $category_id_info['category_id'];

        $data_list = TemplateModel::get($id);
        $this->assign('data',$data_list);

        // 获取网站分类列表
        $category_data = new TemplateCategory();
        $category = $category_data->where(['status'=>1])->select();
        $this->assign('category',$category);

        $my_category_data = TemplateCategory::get($category_id);
        $my_category_title = $my_category_data['title'];
        $this->assign('my_category_id',$category_id);
        $this->assign('my_category_title',$my_category_title);

        return $this->fetch($this->template_path);
    }

    /**
     * 更新
     * @param Request $request
     * @throws \think\exception\DbException
     */
    public function update(Request $request)
    {
        $post_id = $request->param('id');
        $post_category_id = $request->param('category_id');
        $post_sort = $request->param('sort');
        $post_title = $request->param('title');
        $post_tag = $request->param('tag');
        $post_status = $request->param('status');
        if(empty($post_title)){
            $this->error('标题不能为空');
        }

        $data = TemplateModel::get($post_id);
        $data['category_id'] = $post_category_id;
        $data['sort'] = $post_sort;
        if(!empty($post_title)){
            $data['title'] = $post_title;
        }
        $data['tag'] = $post_tag;
        $data['status'] = $post_status;

        if ($data->save()) {
            $this->success('更新成功', '/admin/template/index');
        } else {
            $this->error('操作失败');
        }

    }

    /**
     * 删除
     * @param $id
     * @throws \think\exception\DbException
     */
    public function delete($id)
    {
        $data = TemplateModel::get($id);
        if ($data) {
            $data->delete();
            $this->success('删除广告成功', '/admin/template/index');
        } else {
            $this->error('您要删除的广告不存在');
        }
    }
}
