<?php
/**
 * Created by PhpStorm.
 * User: tzx
 * Date: 2016/10/25
 * Time: 9:08
 */
namespace app\admin\controller;

use think\Request;
use app\base\model\Agent as AgentModel;
use app\base\model\AgentCategory;
use app\base\controller\Upload;

class Agent extends AdminBase
{
    /**
     * 代理列表
     * @return mixed
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index()
    {
        // 找出列表数据
        $post_username = $this->request->param('username');
        $data = new AgentModel;
        if(!empty($post_username)){
            $data_list = $data->where(['status' => 1, 'username' => ['like','%'.$post_username.'%']])->select();
        }else{
            $data_list = $data->where(['status'=>1])->select();
        }
        $data_count = count($data_list);
        $this->assign('data_count',$data_count);

        foreach ($data_list as $data){
            $category_id = $data['category_id'];
            $category_data = AgentCategory::get($category_id);
            $category_title = $category_data['title'];
            $data['category_title'] = $category_title;
        }

        $this->assign('data_list',$data_list);

        return $this->fetch($this->template_path);
    }

    /**
     * 新增代理商
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function create()
    {
        // 获取网站分类列表
        $category_data = new AgentCategory();
        $category = $category_data->where(['status'=>1])->select();
        $this->assign('category',$category);

        return $this->fetch($this->template_path);
    }

    /**
     * 保存代理商数据
     * @param Request $request
     */
    public function save(Request $request)
    {
        // 获取 略缩图 thumb文件
        $file_icon = $request->file('icon');
        if(!empty($file_icon)){
            $local_icon = $file_icon->getInfo('tmp_name');
            $icon_filename = $file_icon->getInfo('name');
            $icon_file_info = new Upload();
            $post_icon = $icon_file_info->qcloud_file($local_icon,$icon_filename);
        }

        $file_logo = $request->file('logo');
        if(!empty($file_logo)){
            $local_logo = $file_logo->getInfo('tmp_name');
            $logo_filename = $file_logo->getInfo('name');
            $logo_file_info = new Upload();
            $post_logo = $logo_file_info->qcloud_file($local_logo,$logo_filename);
        }

        $file_qrcode = $request->file('qrcode');
        if(!empty($file_qrcode)){
            $local_qrcode = $file_qrcode->getInfo('tmp_name');
            $qrcode_filename = $file_qrcode->getInfo('name');
            $qrcode_file_info = new Upload();
            $post_qrcode = $qrcode_file_info->qcloud_file($local_qrcode,$qrcode_filename);
        }

        $post_category_id = $request->param('category_id');
        $post_username = $request->param('username');
        $post_password = $request->param('password');
        $post_password = md5($post_password);
        $post_nickname = $request->param('nickname');
        $post_contact_person = $request->param('contact_person');
        $post_tel = $request->param('tel');
        $post_phone = $request->param('phone');
        $post_weixinhao = $request->param('weixinhao');
        $post_qq = $request->param('qq');
        $post_company_name = $request->param('company_name');
        $post_city = $request->param('city');
        $post_address = $request->param('address');
        $post_site_name = $request->param('site_name');
        $post_url = $request->param('url');

        $post_status = $request->param('status');

        if(empty($post_username)){
            $this->error('用户名不能为空');
        }

        $data = new AgentModel;
        if(!empty($post_icon)){
            $data['icon'] = $post_icon;
        }
        if(!empty($post_logo)){
            $data['logo'] = $post_logo;
        }
        if(!empty($post_qrcode)){
            $data['qrcode'] = $post_qrcode;
        }
        $data['category_id'] = $post_category_id;
        $data['username'] = $post_username;
        $data['password'] = $post_password;
        $data['nickname'] = $post_nickname;
        $data['contact_person'] = $post_contact_person;

        $data['tel'] = $post_tel;
        $data['phone'] = $post_phone;
        $data['weixinhao'] = $post_weixinhao;
        $data['qq'] = $post_qq;
        $data['city'] = $post_city;
        $data['company_name'] = $post_company_name;
        $data['address'] = $post_address;
        $data['site_name'] = $post_site_name;
        $data['url'] = $post_url;
        $data['status'] = $post_status;
        if ($data->save()) {
            $this->success('保存成功','/admin/agent/index');
        } else {
            $this->error('操作失败');
        }
    }

    /**
     * 编辑代理商信息
     * @param $id
     * @return mixed
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function edit($id)
    {
        // 获取当前分类id
        $category_id_info = AgentModel::get($id);
        $category_id = $category_id_info['category_id'];

        // 获取信息
        $data_list = AgentModel::get($id);
        $this->assign('data',$data_list);

        // 获取网站分类列表
        $category_data = new AgentCategory();
        $category = $category_data->where(['status'=>1])->select();
        $this->assign('category',$category);

        $my_category_data = AgentCategory::get($category_id);
        $my_category_title = $my_category_data['title'];
        $this->assign('my_category_id',$category_id);
        $this->assign('my_category_title',$my_category_title);

        return $this->fetch($this->template_path);
    }

    /**
     * 更新代理商信息
     * @param Request $request
     * @throws \think\exception\DbException
     */
    public function update(Request $request)
    {
        // 获取 略缩图 thumb文件
        $file_icon = $request->file('icon');
        if(!empty($file_icon)){
            $local_icon = $file_icon->getInfo('tmp_name');
            $icon_filename = $file_icon->getInfo('name');
            $icon_file_info = new Upload();
            $post_icon = $icon_file_info->qcloud_file($local_icon,$icon_filename);
        }

        $file_logo = $request->file('logo');
        if(!empty($file_logo)){
            $local_logo = $file_logo->getInfo('tmp_name');
            $logo_filename = $file_logo->getInfo('name');
            $logo_file_info = new Upload();
            $post_logo = $logo_file_info->qcloud_file($local_logo,$logo_filename);
        }

        $file_qrcode = $request->file('qrcode');
        if(!empty($file_qrcode)){
            $local_qrcode = $file_qrcode->getInfo('tmp_name');
            $qrcode_filename = $file_qrcode->getInfo('name');
            $qrcode_file_info = new Upload();
            $post_qrcode = $qrcode_file_info->qcloud_file($local_qrcode,$qrcode_filename);
        }

        $post_id = $request->post('id');
        $post_category_id = $request->param('category_id');
        $post_password = $request->param('password');
        $post_password = md5($post_password);
        $post_nickname = $request->param('nickname');
        $post_contact_person = $request->param('contact_person');
        $post_tel = $request->param('tel');
        $post_phone = $request->param('phone');
        $post_weixinhao = $request->param('weixinhao');
        $post_qq = $request->param('qq');
        $post_company_name = $request->param('company_name');
        $post_city = $request->param('city');
        $post_address = $request->param('address');
        $post_site_name = $request->param('site_name');
        $post_url = $request->param('url');
        $post_status = $request->param('status');

        $data = AgentModel::get($post_id);
        if(!empty($post_icon)){
            $data['icon'] = $post_icon;
        }
        if(!empty($post_logo)){
            $data['logo'] = $post_logo;
        }
        if(!empty($post_qrcode)){
            $data['qrcode'] = $post_qrcode;
        }
        $data['category_id'] = $post_category_id;
        $data['password'] = $post_password;
        $data['nickname'] = $post_nickname;
        $data['contact_person'] = $post_contact_person;
        $data['tel'] = $post_tel;
        $data['phone'] = $post_phone;
        $data['weixinhao'] = $post_weixinhao;
        $data['qq'] = $post_qq;
        $data['city'] = $post_city;
        $data['company_name'] = $post_company_name;
        $data['address'] = $post_address;
        $data['site_name'] = $post_site_name;
        $data['url'] = $post_url;
        $data['status'] = $post_status;

        if ($data->save()) {
            $this->success('操作成功', '/admin/agent/index');
        } else {
            $this->error('操作失败');
        }
    }

    /**
     * 删除代理商
     * @param $id
     * @throws \think\exception\DbException
     */
    public function delete($id)
    {
        $user = AgentModel::get($id);
        if ($user) {
            $user->delete();
            $this->success('删除成功', '/admin/agent/index');
        } else {
            $this->error('删除失败');
        }
    }
}