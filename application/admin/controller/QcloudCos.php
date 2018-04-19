<?php
/**
 * Created by PhpStorm.
 * User: tanzhenxing
 * Date: 2016/11/19
 * Time: 16:20
 */
namespace app\admin\controller;

use app\common\model\QcloudCos as QcloudCosModel;

class QcloudCos extends AdminBase
{
    /**
     * 腾讯云存储
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index()
    {
        $post_title = $this->request->post('title');
        $qcloud_cos_data = new QcloudCosModel();
        if(!empty($post_title)){
            $data_list = $qcloud_cos_data->where(['status'=>1,'site_id'=>$this->site_id])
                ->where('title','like','%'.$post_title.'%')
                -> select();
        }else{
            $data_list = $qcloud_cos_data->where(['status'=>1,'site_id'=>$this->site_id]) -> select();
        }

        $data_count = count($data_list);

        $this->assign('data_list',$data_list);
        $this->assign('data_count',$data_count);

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
        if(empty($post_data['site_id'])){
            $this->error('名称不能为空');
        }
        $data = new QcloudCosModel;
        $data_array = array('site_id','title','app_id','secret_id','secret_key','url','bucket_name','region','status');
        $data_save = $data->allowField($data_array)->save($post_data);
        if ($data_save) {
            $this->success('新增腾讯云存储信息成功', '/admin/qcloud_cos/index');
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
        $data_list = QcloudCosModel::get($id);
        $this->assign('data_list',$data_list);
        return $this->fetch($this->template_path);
    }

    /**
     * 更新
     * @throws \think\exception\DbException
     */
    public function update()
    {
        $post_data = $this->request->param();
        $post_data['site_id'] = $this->site_id;
        if(empty($post_data['title'])){
            $this->error('名称不能为空');
        }

        $data = QcloudCosModel::get($post_data['id']);
        $data_array = array('site_id','title','app_id','secret_id','secret_key','url','bucket_name','region','status');
        $data_save = $data->allowField($data_array)->save($post_data);
        if ($data_save) {
            $this->success('新增腾讯云存储信息成功', '/admin/qcloud_cos/index');
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
        $user = QcloudCosModel::get($id);
        if ($user) {
            $user->delete();
            $this->success('删除腾讯云存储信息成功', '/admin/qcloud_cos/index');
        } else {
            $this->error('您要删除的套餐不存在');
        }
    }

}