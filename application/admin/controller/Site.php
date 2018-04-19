<?php
/**
 * Created by PhpStorm.
 * User: tanzhenxing
 * Date: 2017/6/26
 * Time: 15:30
 */
namespace app\admin\controller;

use think\Request;
use app\common\model\Site as SiteModel;
use app\common\model\Admin;
use app\base\controller\Upload;

class Site extends AdminBase
{
    /**
     * 网站基本信息设置
     * @return mixed
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index()
    {
        $admin_data = Admin::get(['site_id'=>$this->site_id]);
        $site_data = new SiteModel();
        if($admin_data['id']==1){
            $site_info = $site_data->where('status',1)
                -> select();
        }else{
            $site_info = $site_data->where('id',$this->site_id)
                -> select();
        }

        $site_count = count($site_info);
        foreach ($site_info as $data)
        {
            $site_admin_data = Admin::get(['site_id'=>$data['id']]);
            $site_admin = $site_admin_data['nickname'];
            $data->site_admin = $site_admin;
        }

        $this->assign('site_info',$site_info);
        $this->assign('site_count',$site_count);

        return $this->fetch($this->template_path);
    }

    /**
     * 新增网站
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function create()
    {
        // 网站管理员信息
        $data = new Admin();
        $admin = $data->where('status','=',1)
            ->where('site_id',$this->site_id)
            ->select();
        $this->assign('admin',$admin);

        return $this->fetch($this->template_path);
    }

    /**
     * 保存新增网站数据
     * @throws \think\exception\DbException
     */
    public function save()
    {
        // 上传文件
        $upload = new Upload();
        // 获取icon文件
        $post_icon = $upload->qcloud_file('icon');
        // 获取logo文件
        $post_logo = $upload->qcloud_file('logo');

        // 获取 分类略缩图 thumb文件
        $post_thumb = $upload->qcloud_file('thumb');

        // 获取 二维码图片文件
        $post_qrcode = $upload->qcloud_file('qrcode');

        $post_domain = $this->request->post('domain');
        $post_title= $this->request->post('title');
        $post_description = $this->request->post('description');
        $post_keywords= $this->request->post('keywords');

        $post_home_title = $this->request->post('home_title');
        $post_template_id = $this->request->post('template_id');
        $post_stats = $this->request->post('stats');
        $post_icp = $this->request->post('icp');
        $post_copyright = $this->request->post('copyright');

        $post_tel = $this->request->post('tel');
        $post_phone = $this->request->post('phone');
        $post_qq = $this->request->post('qq');
        $post_email = $this->request->post('email');
        $post_address = $this->request->post('address');

        $post_status= $this->request->post('status');
        if($post_domain=='' or $post_title==''){
            $this->error('网站域名和名称不能为空');
        }
        $data = new SiteModel;
        $data['domain'] = $post_domain;
        $data['title'] = $post_title;
        $data['description'] = $post_description;
        $data['keywords'] = $post_keywords;
        if(!empty($post_icon)){
            $data['icon'] = $post_icon;
        }
        if(!empty($post_thumb)){
            $data['thumb'] = $post_thumb;
        }
        if(!empty($post_logo)){
            $data['logo'] = $post_logo;
        }
        if(!empty($post_qrcode)){
            $data['qrcode'] = $post_qrcode;
        }
        $data['home_title'] = $post_home_title;
        $data['template_id'] = $post_template_id;
        $data['stats'] = $post_stats;
        $data['icp'] = $post_icp;
        $data['copyright'] = $post_copyright;
        $data['tel'] = $post_tel;
        $data['phone'] = $post_phone;
        $data['qq'] = $post_qq;
        $data['email'] = $post_email;
        $data['address'] = $post_address;
        $data['status'] = $post_status;

        if ($data->save()) {
            $this->success('新增网站成功', '/admin/site/index');
        } else {
            $this->error('操作失败');
        }
    }

    /**
     * 读取网站信息
     * @param $id
     * @return mixed
     */
    public function read($id)
    {
       return $id;
    }

    /**
     * 编辑网站信息
     * @param $id
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function edit($id)
    {
        // 网站管理员信息
        $data = new Admin();
        $admin = $data->where('status','=',1)
            ->where('site_id',$this->site_id)
            ->select();
        $this->assign('admin',$admin);

        // 获取网站信息
        $site_info = SiteModel::get($id);
        $this->assign('site_info',$site_info);

        // 当前管理员信息
        $site_admin = Admin::get(['username'=>session('admin_username')]);
        $this->assign('site_admin',$site_admin);

        return $this->fetch($this->template_path);
    }

    /**
     * 更新网站信息
     * @param Request $request
     * @param $id
     * @throws \think\exception\DbException
     */
    public function update($id)
    {
        $upload = new Upload();
        // 获取icon文件
        $post_icon = $upload->qcloud_file('icon');
        // 获取logo文件
        $post_logo = $upload->qcloud_file('logo');
        // 获取 分类略缩图 thumb文件
        $post_thumb = $upload->qcloud_file('thumb');
        // 获取 二维码图片文件
        $post_qrcode = $upload->qcloud_file('qrcode');
        $post_domain = $this->request->post('domain');
        $post_title= $this->request->post('title');
        $post_desc = $this->request->post('description');
        $post_keywords= $this->request->post('keywords');
        $post_home_title = $this->request->post('home_title');
        $post_template_id = $this->request->post('template_id');
        $post_stats = $this->request->post('stats');
        $post_icp = $this->request->post('icp');
        $post_copyright = $this->request->post('copyright');
        $post_tel = $this->request->post('tel');
        $post_phone = $this->request->post('phone');
        $post_qq = $this->request->post('qq');
        $post_email = $this->request->post('email');
        $post_address = $this->request->post('address');
        $post_status= $this->request->post('status');
        if($post_domain=='' or $post_title==''){
            $this->error('网站域名和名称不能为空');
        }

        $data = SiteModel::get($id);
        $data['domain'] = $post_domain;
        $data['title'] = $post_title;
        $data['description'] = $post_desc;
        $data['keywords'] = $post_keywords;
        if(!empty($post_icon)){
            $data['icon'] = $post_icon;
        }
        if(!empty($post_thumb)){
            $data['thumb'] = $post_thumb;
        }
        if(!empty($post_logo)){
            $data['logo'] = $post_logo;
        }
        if(!empty($post_qrcode)){
            $data['qrcode'] = $post_qrcode;
        }
        $data['home_title'] = $post_home_title;
        $data['template_id'] = $post_template_id;
        $data['stats'] = $post_stats;
        $data['icp'] = $post_icp;
        $data['copyright'] = $post_copyright;
        $data['tel'] = $post_tel;
        $data['phone'] = $post_phone;
        $data['qq'] = $post_qq;
        $data['email'] = $post_email;
        $data['address'] = $post_address;
        $data['status'] = $post_status;
        if ($data->save()) {
            $this->success('更新网站成功', '/admin/site/index');
        } else {
            $this->error('操作失败');
        }
    }

    /**
     * 删除网站
     * @param $id
     * @throws \think\exception\DbException
     */
    public function delete($id)
    {
        $user = SiteModel::get($id);
        if ($user) {
            $user->delete();
            $this->success('删除网站成功', '/admin/site/index');
        } else {
            $this->error('您要删除的网站不存在');
        }
    }
}