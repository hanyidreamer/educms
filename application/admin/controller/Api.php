<?php
/**
 * Created by PhpStorm.
 * User: tanzhenxing
 * Date: 2016/11/2
 * Time: 15:05
 */
namespace app\admin\controller;

use think\Request;
use app\common\model\Api as ApiModel;
use app\common\model\ApiCategory;

class Api extends AdminBase
{
    /**
     * API 接口列表
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index()
    {
        // 找出列表数据
        $data = new ApiModel;
        $data_list = $data->where(['site_id'=>$this->site_id,'status'=>1])->select();
        foreach ($data_list as $data){
            $category_id = $data['category_id'];
            $admin_category = ApiCategory::get($category_id);
            $category_title = $admin_category['title'];
            $data['category_title'] = $category_title;
        }

        $data_count = count($data_list);
        $this->assign('data_count',$data_count);
        $this->assign('data_list',$data_list);

        return $this->fetch($this->template_path);
    }

    /**
     * 添加API 接口
     * @return mixed
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function create()
    {
        // 获取分类列表
        $category_data = new ApiCategory();
        $category = $category_data->where(['site_id'=>$this->site_id])->select();
        $this->assign('category',$category);

        return $this->fetch($this->template_path);
    }

    /**
     * 保存接口数据
     * @param Request $request
     */
    public function save(Request $request)
    {
        $post_category_id = $request->param('category_id');
        $post_title = $request->param('title');
        $post_url = $request->param('url');
        $post_desc = $request->param('desc');
        $post_status = $request->param('status');

        if(empty($post_title)){
            $this->error('接口名称不能为空');
        }


        $data = new ApiModel;
        $data['site_id'] = $this->site_id;
        $data['category_id'] = $post_category_id;
        $data['title'] = $post_title;
        $data['url'] = $post_url;
        $data['desc'] = $post_desc;

        $data['status'] = $post_status;
        if ($data->save()) {
            $this->success('保存成功','/admin/api/index');
        } else {
            $this->error('操作失败');
        }
    }

    /**
     * 编辑接口
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
        $category_id_info = ApiModel::get($id);
        $category_id = $category_id_info['category_id'];

        // 获取信息
        $data_list = ApiModel::get($id);
        $this->assign('data',$data_list);

        // 获取网站分类列表
        $category_data = new ApiCategory();
        $category = $category_data->where(['site_id'=>$this->site_id])->select();
        $this->assign('category',$category);

        $my_categorg_data = ApiCategory::get($category_id);
        $my_categorg_title = $my_categorg_data['title'];
        $this->assign('my_category_id',$category_id);
        $this->assign('my_category_title',$my_categorg_title);

        return $this->fetch($this->template_path);
    }

    /**
     * 更新接口
     * @param Request $request
     * @throws \think\exception\DbException
     */
    public function update(Request $request)
    {
        $post_id = $request->post('id');
        $post_category_id = $request->post('category_id');
        $post_title = $request->post('title');
        $post_url = $request->param('url');
        $post_desc = $request->param('desc');
        $post_status = $request->param('status');
        if(empty($post_title)){
            $this->error('名称不能为空');
        }

        $user = ApiModel::get($post_id);
        $user['site_id'] = $this->site_id;
        $user['category_id'] = $post_category_id;

        $user['title'] = $post_title;
        $user['url'] = $post_url;
        $user['desc'] = $post_desc;
        $user['status'] = $post_status;

        if ($user->save()) {
            $this->success('更新成功', '/admin/api/index');
        } else {
            $this->error('操作失败');
        }

    }

    /**
     * 删除接口
     * @param $id
     * @throws \think\exception\DbException
     */
    public function delete($id)
    {
        $data = ApiModel::get($id);
        if ($data) {
            $data->delete();
            $this->success('删除成功', '/admin/api/index');
        } else {
            $this->error('您要删除的数据不存在');
        }
    }

}