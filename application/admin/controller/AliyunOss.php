<?php
/**
 * Created by PhpStorm.
 * User: tanzhenxing
 * Date: 2017/8/5
 * Time: 16:41
 */

namespace app\admin\controller;

use think\Request;
use app\common\model\AliyunOss as AliyunOssModel;

class AliyunOss extends AdminBase
{
    /**
     * 阿里云接口列表
     * @param Request $request
     * @return mixed
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index(Request $request)
    {
        $post_title = $request->param('title');
        $data = new AliyunOssModel;
        if(!empty($post_title)){
            $data_list = $data->where(['site_id'=>$this->site_id,'status' => 1])
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
     * 新增阿里云OSS
     * @return mixed
     */
    public function create()
    {
        return $this->fetch($this->template_path);
    }

    /**
     * 保存阿里云OSS信息
     * @param Request $request
     */
    public function save(Request $request)
    {
        $post_sort = $request->param('sort');
        $post_title = $request->param('title');
        $post_key = $request->param('key');
        $post_sign = $request->param('sign');
        $post_status = $request->param('status');

        if(empty($post_title)){
            $this->error('标题不能为空');
        }

        $data = new AliyunOssModel;
        $data['site_id'] = $this->site_id;
        $data['title'] = $post_title;
        $data['sort'] = $post_sort;
        $data['key'] = $post_key;
        $data['sign'] = $post_sign;
        $data['status'] = $post_status;
        if ($data->save()) {
            $this->success('保存成功','/admin/aliyun_oss/index');
        } else {
            $this->error('操作失败');
        }
    }

    /**
     * 编辑阿里云OSS
     * @param $id
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function edit($id)
    {
        $data_list = AliyunOssModel::get($id);
        $this->assign('data',$data_list);

        return $this->fetch($this->template_path);
    }

    /**
     * 跟新阿里云OSS
     * @param Request $request
     * @throws \think\exception\DbException
     */
    public function update(Request $request)
    {
        $post_id = $request->param('id');
        $post_sort = $request->param('sort');
        $post_title = $request->param('title');
        $post_key = $request->param('key');
        $post_sign = $request->param('sign');
        $post_status = $request->param('status');
        if(empty($post_title)){
            $this->error('广告标题不能为空');
        }

        $data = AliyunOssModel::get($post_id);
        $data['site_id'] = $this->site_id;
        $data['key'] = $post_key;
        $data['sort'] = $post_sort;
        if(!empty($post_title)){
            $data['title'] = $post_title;
        }
        $data['sign'] = $post_sign;
        $data['status'] = $post_status;

        if ($data->save()) {
            $this->success('更新成功', '/admin/aliyun_oss/index');
        } else {
            $this->error('操作失败');
        }

    }

    /**
     * 删除阿里云oss
     * @param $id
     * @throws \think\exception\DbException
     */
    public function delete($id)
    {
        $data = AliyunOssModel::get($id);
        if ($data) {
            $data->delete();
            $this->success('删除成功', '/admin/aliyun_oss/index');
        } else {
            $this->error('删除失败');
        }
    }
}