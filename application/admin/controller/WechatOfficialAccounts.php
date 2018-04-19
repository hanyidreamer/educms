<?php
/**
 * Created by PhpStorm.
 * User: tanzhenxing
 * Date: 2017/6/26
 * Time: 15:35
 */
namespace app\admin\controller;

use think\Request;
use app\common\model\WechatOfficialAccounts as WechatOfficialAccountsModel;
use app\base\controller\Upload;

class WechatOfficialAccounts extends AdminBase
{
    /**
     * 微信公众号列表
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
        $data = new WechatOfficialAccountsModel;
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
     * 新增微信公众号
     * @return mixed
     */
    public function create()
    {
        return $this->fetch($this->template_path);
    }

    /**
     * 保存公众号信息
     * @param Request $request
     * @throws \think\exception\DbException
     */
    public function save(Request $request)
    {
        $upload = new Upload();
        $post_qrcode = $upload->qcloud_file('qrcode');
        $post_icon = $upload->qcloud_file('icon');

        $post_title = $request->post('title');
        $post_wechat_id = $request->post('wechat_id');
        $post_original_id = $request->post('original_id');
        $post_app_id = $request->post('app_id');
        $post_app_secret = $request->post('app_secret');
        $post_desc = $request->post('desc');
        $post_username = $request->post('username');
        $post_password = $request->post('password');
        $post_password = md5($post_password);
        $post_merchant_id = $request->post('merchant_id');
        $post_api_key = $request->post('api_key');
        $post_access_token = $request->post('access_token');
        $post_refresh_token = $request->post('refresh_token');
        $post_expires_time_token = $request->post('expires_time_token');
        $post_status = $request->post('status');

        if($post_title==''){
            $this->error('标题不能为空');
        }
        $data = new WechatOfficialAccountsModel;
        $data['site_id'] = $this->site_id;
        $data['title']    = $post_title;
        $data['wechat_id'] = $post_wechat_id;
        $data['original_id'] = $post_original_id;
        $data['app_id'] = $post_app_id;
        $data['app_secret'] = $post_app_secret;
        $data['desc'] = $post_desc;
        $data['qrcode'] = $post_qrcode;
        $data['icon'] = $post_icon;
        $data['username'] = $post_username;
        $data['password'] = $post_password;
        $data['merchant_id'] = $post_merchant_id;
        $data['api_key'] = $post_api_key;
        $data['access_token'] = $post_access_token;
        $data['refresh_token'] = $post_refresh_token;
        $data['expires_time_token'] = $post_expires_time_token;
        $data['status'] = $post_status;

        if ($data->save()) {
            $this->success('新增成功', '/admin/wechat_official_accounts/index');
        } else {
            $this->error('操作失败');
        }

    }

    /**
     * 编辑公众号信息
     * @param $id
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function edit($id)
    {
        // 获取信息
        $data_list = WechatOfficialAccountsModel::get($id);
        $this->assign('data',$data_list);

        return $this->fetch($this->template_path);
    }

    /**
     * 更新公众号信息
     * @param Request $request
     * @throws \think\exception\DbException
     */
    public function update(Request $request)
    {
        $upload = new Upload();
        $post_qrcode = $upload->qcloud_file('qrcode');
        $post_icon = $upload->qcloud_file('icon');

        $post_id = $request->post('id');
        $post_title = $request->post('title');
        $post_wechat_id = $request->post('wechat_id');
        $post_original_id = $request->post('original_id');
        $post_app_id = $request->post('app_id');
        $post_app_secret = $request->post('app_secret');
        $post_description = $request->post('description');
        $post_username = $request->post('username');
        $post_password = $request->post('password');
        $post_password = md5($post_password);
        $post_merchant_id = $request->post('merchant_id');
        $post_api_key = $request->post('api_key');
        $post_access_token = $request->post('access_token');
        $post_refresh_token = $request->post('refresh_token');
        $post_expires_time_token = $request->post('expires_time_token');
        $post_status = $request->post('status');

        if($post_title==''){
            $this->error('标题不能为空');
        }
        $data = WechatOfficialAccountsModel::get($post_id);
        $data['site_id'] = $this->site_id;
        $data['title']    = $post_title;
        $data['wechat_id'] = $post_wechat_id;
        $data['original_id'] = $post_original_id;
        $data['app_id'] = $post_app_id;
        $data['app_secret'] = $post_app_secret;
        $data['description'] = $post_description;
        if(!empty($post_icon)){
            $data['icon'] = $post_icon;
        }
        if(!empty($post_qrcode)){
            $data['qrcode'] = $post_qrcode;
        }
        $data['username'] = $post_username;
        if(!empty($post_password)){
            $data['password'] = $post_password;
        }
        $data['merchant_id'] = $post_merchant_id;
        $data['api_key'] = $post_api_key;
        $data['access_token'] = $post_access_token;
        $data['refresh_token'] = $post_refresh_token;
        $data['expires_time_token'] = $post_expires_time_token;
        $data['status'] = $post_status;

        if ($data->save()) {
            $this->success('保存成功', '/admin/wechat_official_accounts/index');
        } else {
            $this->error('操作失败');
        }
    }

    /**
     * 删除公众号信息
     * @param $id
     * @throws \think\exception\DbException
     */
    public function delete($id)
    {
        $data = WechatOfficialAccountsModel::get($id);
        if ($data) {
            $data->delete();
            $this->success('删除成功', '/admin/wechat_official_accounts/index');
        } else {
            $this->error('删除失败');
        }
    }

}