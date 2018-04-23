<?php
/**
 * Created by PhpStorm.
 * User: tanzhenxing
 * Date: 2017/6/26
 * Time: 15:35
 */
namespace app\admin\controller;

use app\common\model\WechatOfficialAccounts as WechatOfficialAccountsModel;
use app\base\controller\Upload;

class WechatOfficialAccounts extends AdminBase
{
    /**
     * 微信公众号列表
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index()
    {
        // 找出列表数据
        $post_title = $this->request->param('title');
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
     * @throws \think\exception\DbException
     */
    public function save()
    {
        $post_data = $this->request->param();
        $upload = new Upload();
        $post_data['qrcode'] = $upload->qcloud_file('qrcode');
        $post_data['icon'] = $upload->qcloud_file('icon');
        if(empty($post_data['title'])){
            $this->error('标题不能为空');
        }

        $data = new WechatOfficialAccountsModel;
        $data_array = array('site_id','wechat_id','original_id','app_id','app_secret','title','description','icon','qrcode','username','password','merchant_id','api_key','access_token','refresh_token','expires_time_token','sort','status');
        $data_save = $data->allowField($data_array)->save($post_data);
        if ($data_save) {
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
     * @throws \think\exception\DbException
     */
    public function update()
    {
        $post_data = $this->request->param();
        $upload = new Upload();
        $post_data['qrcode'] = $upload->qcloud_file('qrcode');
        $post_data['icon'] = $upload->qcloud_file('icon');
        if(empty($post_data['title'])){
            $this->error('标题不能为空');
        }

        $data = WechatOfficialAccountsModel::get($post_data['id']);
        $data_array = array('site_id','wechat_id','original_id','app_id','app_secret','title','description','icon','qrcode','username','password','merchant_id','api_key','access_token','refresh_token','expires_time_token','sort','status');
        $data_save = $data->allowField($data_array)->save($post_data);
        if ($data_save) {
            $this->success('新增成功', '/admin/wechat_official_accounts/index');
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