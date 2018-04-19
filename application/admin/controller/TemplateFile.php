<?php
/**
 * Created by PhpStorm.
 * User: tanzhenxing
 * Date: 2018/4/19
 * Time: 12:09
 */
namespace app\admin\controller;

use app\common\model\TemplateFile as TemplateFileModel;
use app\common\model\Template;
use app\common\model\TemplateCategory;
use app\base\controller\Site;

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
        $site_data = new Site();
        $site_info = $site_data->info();
        $post_title = $this->request->param('title');
        $data = new TemplateFileModel;
        if(!empty($post_title)){
            $data_list = $data->where(['status' => 1,'template_id'=>$site_info['template_id']])
                ->where('title','like','%'.$post_title.'%')
                ->select();
        }else{
            $data_list = $data->where(['status'=>1,'template_id'=>$site_info['template_id']])->select();
        }
        $data_count = count($data_list);
        $this->assign('data_count',$data_count);

        foreach ($data_list as $value){
            $template_data = Template::get($value['template_id']);
            $value->template_name = $template_data['unique_code'];
            $module_name = TemplateCategory::get($value['category_id']);
            $value->module_name = $module_name['module'];
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
        $module = new TemplateCategory();
        $module_data = $module->where('status','=',1)->select();
        $this->assign('module',$module_data);

        $site_data = new Site();
        $site_info = $site_data->info();
        $this->assign('template_id',$site_info['template_id']);

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
        $data_array = array('title','template_id','category_id','controller_name','template_file_name','sort','status');
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

        $site_data = new Site();
        $site_info = $site_data->info();

        $template_category_data = new TemplateCategory();
        $template_category = $template_category_data->where(['status'=>1])->select();
        $this->assign('category',$template_category);
        // 当前模块信息
        $my_template_category = TemplateCategory::get($data_list['category_id']);
        $this->assign('my_template_category',$my_template_category);

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
        $data_array = array('title','template_id','category_id','controller_name','template_file_name','sort','status');
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