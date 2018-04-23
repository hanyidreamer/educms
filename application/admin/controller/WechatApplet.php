<?php
/**
 * Created by PhpStorm.
 * User: tanzhenxing
 * Date: 2017/6/29
 * Time: 22:22
 */
namespace app\admin\controller;

use think\Request;
use app\common\model\WechatApplet as WechatAppletModel;

class WechatApplet extends AdminBase
{
    /**
     * 微信小程序列表
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
        $data = new WechatAppletModel;
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
     * @return mixed
     */
    public function create()
    {
        return $this->fetch($this->template_path);
    }

    /**
     * @param Request $request
     */
    public function save(Request $request)
    {
        $post_title = $request->post('title');
        $post_status= $request->post('status');

        if($post_title==''){
            $this->error('标题不能为空');
        }
        $data = new WechatAppletModel;
        $data['site_id'] = $this->site_id;
        $data['title']    = $post_title;
        $data['status'] = $post_status;

        if ($data->save()) {
            $this->success('新增成功', '/admin/wechat_applet/index');
        } else {
            $this->error('操作失败');
        }

    }

    /**
     * @param $id
     * @return mixed
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */
    public function edit($id)
    {
        // 获取信息
        $data_list = WechatAppletModel::get($id);
        $this->assign('data',$data_list);

        return $this->fetch($this->template_path);
    }

    /**
     * @param Request $request
     * @throws \think\exception\DbException
     */
    public function update(Request $request)
    {
        $post_id = $request->post('id');
        $post_title = $request->post('title');
        $post_status= $request->post('status');

        if($post_title=='' or $post_id==''){
            $this->error('幻灯片名称不能为空');
        }

        $data = WechatAppletModel::get($post_id);
        $data['site_id'] = $this->site_id;
        $data['title']    = $post_title;
        $data['status'] = $post_status;

        if ($data->save()) {
            $this->success('保存成功', '/admin/wechat_applet/index');
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
        $data = WechatAppletModel::get($id);
        if ($data) {
            $data->delete();
            $this->success('删除成功', '/admin/wechat_applet/index');
        } else {
            $this->error('删除失败');
        }
    }

}