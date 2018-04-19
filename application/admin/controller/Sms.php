<?php
/**
 * Created by PhpStorm.
 * User: tzx
 * Date: 2016/10/25
 * Time: 21:32
 */
namespace app\admin\controller;

use app\common\model\Sms as SmsModel;

class Sms extends AdminBase
{
    /**
     * 短信接口
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index()
    {
        $post_title = $this->request->param('title');
        $data = new SmsModel;
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
            $this->error('平台名称不能为空');
        }

        $data = new SmsModel;
        $data_array = array('site_id','title','platform','sign','app_id','app_key','secret_key','sign_name','url','status');
        $data_save = $data->allowField($data_array)->save($post_data);
        if ($data_save) {
            $this->success('保存成功', '/admin/sms/index');
        } else {
            $this->error('操作失败');
        }
    }

    /**
     * @param $id
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function edit($id)
    {
        $data_list = SmsModel::get($id);
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
            $this->error('平台名称不能为空');
        }

        $data = SmsModel::get($post_data['id']);
        $data_array = array('site_id','title','platform','sign','app_id','app_key','secret_key','sign_name','url','status');
        $data_save = $data->allowField($data_array)->save($post_data);
        if ($data_save) {
            $this->success('保存成功', '/admin/sms/index');
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
        $user = SmsModel::get($id);
        if ($user) {
            $user->delete();
            $this->success('删除成功', '/admin/sms/index');
        } else {
            $this->error('删除的信息不存在');
        }
    }


}