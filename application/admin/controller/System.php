<?php
/**
 * Created by PhpStorm.
 * User: tanzhenxing
 * Date: 2018/4/13
 * Time: 9:51
 */
namespace app\admin\controller;

use app\base\controller\Upload;
use app\base\model\System as SystemModel;
use app\base\model\Admin;

class System extends AdminBase
{
    /**
     * 后台系统配置
     * @throws \think\exception\DbException
     */
    public function index()
    {
        // 判断当前用户是否为最高管理员
        $admin_data = new Admin();
        $my_admin = $admin_data->get(['username'=>session('admin_username')]);
        $my_admin_id = $my_admin['id'];
        $system = new SystemModel();
        if($my_admin_id==1){
            $my_system = $system->where(['status'=>1])->select();
        }else{
            $my_system = $system->where(['site_id'=>$this->site_id])->select();
        }

        $this->assign('data_list',$my_system);
        $data_count = count($my_system);
        $this->assign('data_count',$data_count);

        return $this->fetch($this->template_path);
    }

    public function create()
    {
        return $this->fetch($this->template_path);
    }

    /**
     * @throws \think\exception\DbException
     */
    public function save()
    {
        $upload = new Upload();
        $post_logo = $upload->qcloud_file('logo');
        $post_thumb = $upload->qcloud_file('thumb');
        $post_sort = $this->request->param('sort');
        $post_title = $this->request->param('title');
        $post_description = $this->request->param('description');
        $post_tel = $this->request->param('tel');
        $post_url = $this->request->param('url');
        $post_url1 = $this->request->param('url1');
        $post_copyright = $this->request->param('copyright');
        $post_status = $this->request->param('status');


    }

    /**
     * 编辑网站配置
     * @param $id
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function edit($id)
    {
        $data = SystemModel::get($id);
        $this->assign('data', $data);
        return $this->fetch($this->template_path);
    }

    /**
     * @throws \think\exception\DbException
     */
    public function update()
    {
        $upload = new Upload();
        $post_logo = $upload->qcloud_file('logo');
        $post_thumb = $upload->qcloud_file('thumb');
        $post_id = $this->request->param('id');
        $post_sort = $this->request->param('sort');
        $post_title = $this->request->param('title');
        $post_description = $this->request->param('description');
        $post_tel = $this->request->param('tel');
        $post_url = $this->request->param('url');
        $post_url1 = $this->request->param('url1');
        $post_copyright = $this->request->param('copyright');
        $post_status = $this->request->param('status');

        $data = SystemModel::get($post_id);
        if(!empty($post_logo)){
            $data['logo'] = $post_logo;
        }
        if(!empty($post_thumb)){
            $data['thumb'] = $post_thumb;
        }
        $data['site_id'] = $this->site_id;
        $data['sort'] = $post_sort;
        $data['title'] = $post_title;
        $data['description'] = $post_description;
        $data['tel'] = $post_tel;
        $data['url'] = $post_url;
        $data['url1'] = $post_url1;
        $data['copyright'] = $post_copyright;
        $data['status'] = $post_status;

        if($data->save()){
            $this->success('更新成功','/admin/system/index');
        }else{
            $this->error('更新失败');
        }

    }
    /**
     * 删除后台系统配置
     * @param $id
     * @throws \think\exception\DbException
     */
    public function delete($id)
    {
        $data = SystemModel::get($id);
        if($data){
            $data ->delete();
            $this->success('删除成功','/admin/system/index');
        }else{
            $this->error('删除失败');
        }
    }
}