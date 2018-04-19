<?php
/**
 * Created by PhpStorm.
 * User: tanzhenxing
 * Date: 2018/4/19
 * Time: 12:09
 */
namespace app\admin\controller;

use app\common\model\TemplateFile as TemplateFileModel;

class TemplateFile extends AdminBase
{
    /**
     * 模板管理
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index()
    {
        $post_title = $this->request->param('title');
        $data = new TemplateFileModel;
        if(!empty($post_title)){
            $data_list = $data->where(['status' => 1])
                ->where('title','like','%'.$post_title.'%')
                ->select();
        }else{
            $data_list = $data->where(['status'=>1])->select();
        }
        $data_count = count($data_list);
        $this->assign('data_count',$data_count);

        $this->assign('data_list',$data_list);

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
     */
    public function save()
    {
        $post_data = $this->request->param();
        if(empty($post_data['title'])){
            $this->error('名称不能为空');
        }

        $data = new TemplateFileModel();
        $data_array = array('title','thumb','pc','mobile','wechat','unique_code','sort','status');
        $data_save = $data->allowField($data_array)->save($post_data);
        if ($data_save) {
            $this->success('保存成功','/admin/template_file/index');
        } else {
            $this->error('操作失败');
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
        $data_list = TemplateFileModel::get($id);
        $this->assign('data',$data_list);
        return $this->fetch($this->template_path);
    }

    /**
     * 更新
     * @throws \think\exception\DbException
     */
    public function update()
    {
        $post_data = $this->request->param();
        if(empty($post_data['title'])){
            $this->error('名称不能为空');
        }

        $data = TemplateFileModel::get($post_data['id']);
        $data_array = array('title','thumb','pc','mobile','wechat','unique_code','sort','status');
        $data_save = $data->allowField($data_array)->save($post_data);
        if ($data_save) {
            $this->success('保存成功','/admin/template_file/index');
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
        $data = TemplateFileModel::get($id);
        if ($data) {
            $data->delete();
            $this->success('删除成功', '/admin/template_file/index');
        } else {
            $this->error('删除失败');
        }
    }
}