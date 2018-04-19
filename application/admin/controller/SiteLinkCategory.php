<?php
/**
 * Created by PhpStorm.
 * User: tanzhenxing
 * Date: 2017/6/26
 * Time: 15:30
 */
namespace app\admin\controller;

use think\Request;
use app\common\model\SiteLinkCategory as SiteLinkCategoryModel;

class SiteLinkCategory extends AdminBase
{
    /**
     * 列表
     * @param Request $request
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index(Request $request)
    {
        // 找出列表数据
        $post_title = $request->param('title');
        $data = new SiteLinkCategoryModel;
        if(!empty($post_title)){
            $data_list = $data->where(['status' => 1,'site_id'=>$this->site_id])
                ->where('title','like','%'.$post_title.'%')
                ->select();
        }else{
            $data_list = $data->where(['site_id'=>$this->site_id,'status'=>1])->select();
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
        $post_data['site_id'] = $this->site_id;
        if(empty($post_data['title'])){
            $this->error('分类名称不能为空');
        }

        $data = new SiteLinkCategoryModel;
        $data_sql = array('site_id','title','description','level','unique_code','status');
        $data_save = $data->allowField($data_sql)->save($post_data);
        if ($data_save) {
            $this->success('保存成功','/admin/site_link_category/index');
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
        // 获取网站信息
        $data_list = SiteLinkCategoryModel::get($id);
        $this->assign('data',$data_list);

        return $this->fetch($this->template_path);
    }

    /**
     * 更新
     * @param Request $request
     * @throws \think\exception\DbException
     */
    public function update()
    {
        $post_data = $this->request->param();
        $post_data['site_id'] = $this->site_id;
        if(empty($post_data['title'])){
            $this->error('分类名称不能为空');
        }

        $data = SiteLinkCategoryModel::get($post_data['id']);
        $data_sql = array('site_id','title','description','level','unique_code','status');
        $data_save = $data->allowField($data_sql)->save($post_data);

        if ($data_save) {
            $this->success('保存成功', '/admin/site_link_category/index');
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
        $data = SiteLinkCategoryModel::get($id);
        if ($data) {
            $data->delete();
            $this->success('删除成功', '/admin/site_link_category/index');
        } else {
            $this->error('删除失败');
        }
    }

}