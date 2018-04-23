<?php
/**
 * Created by PhpStorm.
 * User: tanzhenxing
 * Date: 2017/8/26
 * Time: 16:41
 */
namespace app\admin\controller;

use think\Request;
use app\common\model\Student as StudentModel;
use app\common\model\StudentCategory;
use app\base\controller\Upload;

class Student extends AdminBase
{
    /**
     * @param Request $request
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index(Request $request)
    {
        $site_id = $this->site_id;
        // 找出广告列表数据
        $post_title = $request->param('title');
        $data = new StudentModel;
        if(!empty($post_title)){
            $data_list = $data->where(['status' => 1, 'title' => ['like','%'.$post_title.'%']])->select();
        }else{
            $data_list = $data->where(['site_id'=>$site_id,'status'=>1])->select();
        }
        $data_count = count($data_list);
        $this->assign('data_count',$data_count);

        foreach ($data_list as $data){
            $category_id = $data['category_id'];
            $category_data = StudentCategory::get($category_id);
            $category_title = $category_data['title'];
            $data['category_title'] = $category_title;
        }

        $this->assign('data_list',$data_list);

        return $this->fetch($this->template_path);
    }

    /**
     * @return mixed
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function create()
    {
        $site_id = $this->site_id;
        // 获取网站分类列表
        $category_data = new StudentCategory();
        $category = $category_data->where(['site_id'=>$site_id])->select();
        $this->assign('category',$category);

        return $this->fetch($this->template_path);
    }

    /**
     * @param Request $request
     */
    public function save(Request $request)
    {
        $file_icon = $request->file('icon');
        if(!empty($file_icon)){
            $local_icon = $file_icon->getInfo('tmp_name');
            $icon_filename = $file_icon->getInfo('name');
            $icon_file_info = new Upload();
            $post_icon = $icon_file_info->qcloud_file($local_icon,$icon_filename);
        }else{
            $post_icon = '';
        }

        $file_qrcode = $request->file('qrcode');
        if(!empty($file_qrcode)){
            $local_qrcode = $file_qrcode->getInfo('tmp_name');
            $qrcode_filename = $file_qrcode->getInfo('name');
            $qrcode_file_info = new Upload();
            $post_qrcode = $qrcode_file_info->qcloud_file($local_qrcode,$qrcode_filename);
        }else{
            $post_qrcode = '';
        }
        $post_site_id = $request->param('site_id');
        $post_category_id = $request->param('category_id');
        $post_sort = $request->param('sort');
        $post_real_name = $request->param('real_name');
        $post_nickname = $request->param('nickname');
        $post_url = $request->param('url');

        $post_tel = $request->param('tel');
        $post_weixinhao = $request->param('weixinhao');
        $post_qq = $request->param('qq');
        $post_unique_code = $request->param('unique_code');
        if($post_unique_code==""){
            $post_unique_code= 'a'.time().rand(1000,9999);
        }

        $post_status = $request->param('status');

        if(empty($post_real_name)){
            $this->error('姓名不能为空');
        }
        $data = new StudentModel;
        if(!empty($post_icon)){
            $data['icon'] = $post_icon;
        }
        if(!empty($post_qrcode)){
            $data['qrcode'] = $post_qrcode;
        }
        $data['site_id'] = $post_site_id;
        $data['category_id'] = $post_category_id;
        $data['sort'] = $post_sort;
        $data['real_name'] = $post_real_name;
        $data['nickname'] = $post_nickname;
        $data['url'] = $post_url;
        $data['weixinhao'] = $post_weixinhao;
        $data['tel'] = $post_tel;
        $data['qq'] = $post_qq;
        $data['unique_code'] = $post_unique_code;

        $data['status'] = $post_status;
        if ($data->save()) {
            $this->success('保存成功','/admin/student/index');
        } else {
            $this->error('操作失败');
        }
    }

    /**
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
        $categorg_id_info = StudentModel::get($id);
        $categorg_id = $categorg_id_info['category_id'];

        // 获取信息
        $data_list = StudentModel::get($id);
        $this->assign('data',$data_list);

        // 获取网站分类列表
        $category_data = new StudentCategory();
        $category = $category_data->where(['site_id'=>$site_id])->select();
        $this->assign('category',$category);

        $my_categorg_data = StudentCategory::get($categorg_id);
        $my_categorg_title = $my_categorg_data['title'];
        $this->assign('my_category_id',$categorg_id);
        $this->assign('my_categorg_title',$my_categorg_title);

        return $this->fetch($this->template_path);
    }

    /**
     * @param Request $request
     * @throws \think\exception\DbException
     */
    public function update(Request $request)
    {
        $file_icon = $request->file('icon');
        if(!empty($file_icon)){
            $local_icon = $file_icon->getInfo('tmp_name');
            $icon_filename = $file_icon->getInfo('name');
            $icon_file_info = new Upload();
            $post_icon = $icon_file_info->qcloud_file($local_icon,$icon_filename);
        }else{
            $post_icon = '';
        }

        $file_qrcode = $request->file('qrcode');
        if(!empty($file_qrcode)){
            $local_qrcode = $file_qrcode->getInfo('tmp_name');
            $qrcode_filename = $file_qrcode->getInfo('name');
            $qrcode_file_info = new Upload();
            $post_qrcode = $qrcode_file_info->qcloud_file($local_qrcode,$qrcode_filename);
        }else{
            $post_qrcode = '';
        }
        $post_id = $request->param('id');
        $post_site_id = $request->param('site_id');
        $post_category_id = $request->param('category_id');
        $post_sort = $request->param('sort');
        $post_real_name = $request->param('real_name');
        $post_nickname = $request->param('nickname');
        $post_url = $request->param('url');

        $post_tel = $request->param('tel');
        $post_weixinhao = $request->param('weixinhao');
        $post_qq = $request->param('qq');
        $post_unique_code = $request->param('unique_code');

        $post_status = $request->param('status');

        if(empty($post_real_name)){
            $this->error('姓名不能为空');
        }

        $data = StudentModel::get($post_id);

        if(!empty($post_icon)){
            $data['icon'] = $post_icon;
        }
        if(!empty($post_qrcode)){
            $data['qrcode'] = $post_qrcode;
        }
        $data['site_id'] = $post_site_id;
        $data['category_id'] = $post_category_id;
        $data['sort'] = $post_sort;
        $data['real_name'] = $post_real_name;
        $data['nickname'] = $post_nickname;
        $data['url'] = $post_url;
        $data['weixinhao'] = $post_weixinhao;
        $data['tel'] = $post_tel;
        $data['qq'] = $post_qq;
        $data['unique_code'] = $post_unique_code;
        $data['status'] = $post_status;

        if ($data->save()) {
            $this->success('更新成功', '/admin/student/index');
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
        $data = StudentModel::get($id);
        if ($data) {
            $data->delete();
            $this->success('删除广告成功', '/admin/student/index');
        } else {
            $this->error('您要删除的广告不存在');
        }
    }
}