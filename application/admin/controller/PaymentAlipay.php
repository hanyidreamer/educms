<?php
/**
 * Created by PhpStorm.
 * User: tzx
 * Date: 2016/10/25
 * Time: 22:38
 */
namespace app\admin\controller;

use think\Request;
use app\common\model\PaymentAlipay as PaymentAlipayModel;

class PaymentAlipay extends AdminBase
{
    /**
     * 支付宝支付
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index()
    {
        $post_title = $this->request->param('title');
        $data = new PaymentAlipayModel;
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
        $post_sign = $request->post('sign');
        $post_key = $request->post('key');
        $post_status= $request->post('status');

        if($post_title==''){
            $this->error('幻灯片标题不能为空');
        }
        $user = new PaymentAlipayModel;
        $user['site_id'] = $this->site_id;
        $user['title']    = $post_title;
        $user['sign'] = $post_sign;
        $user['key']    = $post_key;
        $user['status'] = $post_status;

        if ($user->save()) {
            $this->success('新增成功', '/admin/payment_alipay/index');
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
        // 获取信息
        $data_list = PaymentAlipayModel::get($id);
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
        $post_sign = $request->post('sign');
        $post_key = $request->post('key');
        $post_status= $request->post('status');
        if($post_title=='' or $post_id==''){
            $this->error('幻灯片名称不能为空');
        }

        $user = PaymentAlipayModel::get($post_id);
        $user['site_id'] = $this->site_id;
        $user['title']    = $post_title;
        $user['sign'] = $post_sign;
        $user['key']    = $post_key;
        $user['status'] = $post_status;

        if ($user->save()) {
            $this->success('保存成功', '/admin/payment_alipay/edit/id/'.$post_id);
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
        $user = PaymentAlipayModel::get($id);
        if ($user) {
            $user->delete();
            $this->success('删除成功', '/admin/payment_alipay/index');
        } else {
            $this->error('删除失败');
        }
    }

}