<?php
/**
 * Created by PhpStorm.
 * User: tanzhenxing
 * Date: 2016/11/2
 * Time: 14:35
 */
namespace app\admin\controller;

use think\Request;
use app\common\model\Member;
use app\common\model\Product;
use app\common\model\ProductOrder as ProductOrderModel;

class ProductOrder extends AdminBase
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
        $post_username= $request->post('order_number');
        if($post_username==!''){
            $data_sql['order_number'] =  ['like','%'.$post_username.'%'];
        }
        $data_sql['status'] = 1;
        $product_order_data = new ProductOrderModel();
        $data_list = $product_order_data->where($data_sql) -> select();
        $data_count = count($data_list);
        foreach($data_list as $data)
        {
            $data->id;
            $mid=$data->mid;

            $member_list = Member::get($mid);
            $username=$member_list['username'];
            $data->username=$username;

            $pid=$data->pid;
            $product_list = Product::get($pid);
            $product=$product_list['name'];
            $data->product=$product;
        }
        $this->assign('data_list',$data_list);
        $this->assign('data_count',$data_count);

        return $this->fetch();
    }

    /**
     * @return mixed
     */
    public function add()
    {
        return $this->fetch();
    }

    /**
     * @param Request $request
     */
    public function insert(Request $request)
    {
        $post_name= $request->post('name');
        $post_en_name= $request->post('en_name');
        $post_market_price= $request->post('market_price');
        $post_price= $request->post('price');
        $post_status= $request->post('status');
        if($post_name==''){
            $this->error('套餐名称不能为空');
        }
        $user = new ProductOrderModel;
        $user['name'] = $post_name;
        $user['en_name'] = $post_en_name;
        $user['market_price'] = $post_market_price;
        $user['price'] = $post_price;
        $user['status'] = $post_status;

        if ($user->save()) {
            $this->success('新增套餐成功', '/admin/product/index');
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
        $data_list = ProductOrderModel::get(['id'=>$id]);
        $this->assign('data_list',$data_list);

        return $this->fetch();
    }

    /**
     * @param Request $request
     * @throws \think\exception\DbException
     */
    public function save(Request $request)
    {
        $post_id= $request->post('id');
        $post_status= $request->post('status');
        $post_name= $request->post('name');
        $post_en_name= $request->post('en_name');
        $post_market_price= $request->post('market_price');
        $post_price= $request->post('price');
        if($post_id==''){
            $this->error('产品id不能为空');
        }
        $user = ProductOrderModel::get($post_id);
        $user['name'] = $post_name;
        $user['en_name'] = $post_en_name;
        $user['market_price'] = $post_market_price;
        $user['price'] = $post_price;
        $user['status'] = $post_status;
        if ($user->save()) {
            $this->success('保存产品信息成功', '/admin/product/index');
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
        $user = ProductOrderModel::get($id);
        if ($user) {
            $user->delete();
            $this->success('删除套餐成功', '/admin/product/index');
        } else {
            $this->error('您要删除的套餐不存在');
        }
    }

}