<?php
/**
 * Created by PhpStorm.
 * User: tanzhenxing
 * Date: 2017/6/26
 * Time: 15:34
 */
namespace app\admin\controller;

use app\common\model\TemplateCategory as TemplateCategoryModel;

class TemplateCategory extends AdminBase
{
    /**
     * 模板模块
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index()
    {
        $title = $this->request->param('title');
        $data = new TemplateCategoryModel();
        if(empty($title)){
            $site_template = $data->where('status',1)
                ->where('title','like','%'.$title.'%')
                ->select();
        }else{
            $site_template = $data->where('status',1)
                ->select();
        }
        $data_count = count($site_template);
        $this->assign('data_count',$data_count);

        $this->assign('data_list',$site_template);

        return $this->fetch($this->template_path);
    }

    /**
     * 新增
     * @return mixed
     */
    public function create()
    {
        return $this->fetch($this->template_path);
    }

    /**
     * 保存
     * @throws \think\exception\DbException
     */
    public function save()
    {
        $post_data = $this->request->param();
        $allow_field = array('site_id','title','module','sort','status'); // 容许保存的数据库字段
        $data = new TemplateCategoryModel();
        $data->allowField($allow_field)->save($post_data);
        if($data){
            $this->success('添加成功','/admin/template_category/index');
        }else{
            $this->error('添加失败');
        }
    }

    /**
     * 编辑
     * @param $id
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function edit($id)
    {
        $data = TemplateCategoryModel::get($id);
        $this->assign('data',$data);

        return $this->fetch($this->template_path);
    }

    /**
     * 更新
     * @throws \think\exception\DbException
     */
    public function update()
    {
        $post_data = $this->request->param();
        $allow_field = array('title','name','sort','status'); // 容许保存的数据库字段
        $data = TemplateCategoryModel::get($post_data['id']);
        $data->allowField($allow_field)->save($post_data);
        if($data){
            $this->success('保存成功','/admin/template_category/index');
        }else{
            $this->error('保存失败');
        }
    }

    /**
     * 删除
     * @param $id
     * @throws \think\exception\DbException
     */
    public function delete($id)
    {
        $data = TemplateCategoryModel::get($id);
        if ($data){
            $data->delete();
            $this->success('删除成功');
        }else{
            $this->error('删除失败');
        }
    }
}