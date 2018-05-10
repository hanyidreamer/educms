<?php
/**
 * Created by PhpStorm.
 * User: tanzhenxing
 * Date: 2016/11/2
 * Time: 14:28
 */
namespace app\admin\controller;

use think\Request;
use app\common\model\Product as ProductModel;

class Product extends AdminBase
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
        $post_username= $request->post('name');
        if($post_username==!''){
            $data_sql['name'] =  ['like','%'.$post_username.'%'];
        }
        $data_sql['status'] = 1;
        $product_data = new ProductModel();
        $data_list = $product_data->where($data_sql) -> select();
        $data_count = count($data_list);

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
        $user = new ProductModel;
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
        $data_list = ProductModel::get($id);

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
        $user = ProductModel::get($post_id);
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
        $user = ProductModel::get($id);
        if ($user) {
            $user->delete();
            $this->success('删除套餐成功', '/admin/product/index');
        } else {
            $this->error('您要删除的套餐不存在');
        }
    }

}