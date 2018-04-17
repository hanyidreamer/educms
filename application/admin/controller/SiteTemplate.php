<?php
/**
 * Created by PhpStorm.
 * User: tanzhenxing
 * Date: 2018/4/17
 * Time: 21:48
 */
namespace app\admin\controller;

use app\common\model\SiteTemplate as SiteTemplateModel;

class SiteTemplate extends AdminBase
{
    /**
     * 网站模板配置
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index()
    {
        $title = $this->request->param('title');
        $data = new SiteTemplateModel();
        if(empty($title)){
            $site_template = $data->where('status',1)
                ->where('site_id', $this->site_id)
                ->where('title','like','%'.$title.'%')
                ->select();
        }else{
            $site_template = $data->where('status',1)
                ->where('site_id')
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
        $post_data['site_id'] = $this->site_id;
        $site_template = SiteTemplateModel::get(['site_id'=>$this->site_id]);
        if(!empty($site_template)){
            $this->error('当前网站模板配置已经存在，请不要重复添加');
        }
        $allow_field = array('site_id','title','name','sort','status'); // 容许保存的数据库字段
        $data = new SiteTemplateModel();
        $data->allowField($allow_field)->save($post_data);
        if($data){
            $this->success('添加成功','/admin/site_template/index');
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
        $data = SiteTemplateModel::get($id);
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
        $data = SiteTemplateModel::get($post_data['id']);
        $data->allowField($allow_field)->save($post_data);
        if($data){
            $this->success('保存成功','/admin/site_template/index');
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
        $data = SiteTemplateModel::get($id);
        if ($data){
            $data->delete();
            $this->success('删除成功');
        }else{
            $this->error('删除失败');
        }
    }
}