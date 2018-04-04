<?php
/**
 * Created by PhpStorm.
 * User: tanzhenxing
 * Date: 2017/8/16
 * Time: 10:05
 */
namespace app\admin\controller;

use think\Request;
use app\base\model\AreaUnit as AreaUnitModel;
use app\base\model\AreaState;

class AreaUnit extends AdminBase
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
        // 找出列表数据
        $post_title = $request->param('title');
        $data = new AreaUnitModel;
        if(!empty($post_title)){
            $data_list = $data->where(['status' => 1, 'title' => ['like','%'.$post_title.'%']])->select();
        }else{
            $data_list = $data->where(['status'=>1])->select();
        }
        $data_count = count($data_list);

        foreach ($data_list as $data){
            $state_id = $data['state_id'];
            $state_info = AreaState::get($state_id);
            $data['state_title'] = $state_info['short_title'];
        }

        $this->assign('data_count',$data_count);
        $this->assign('data_list',$data_list);

        return $this->fetch($this->template_path);
    }

    /**
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function create()
    {
        // 获取分类列表
        $category_data = new AreaState();
        $category = $category_data->where(['status'=>1])->select();
        $this->assign('category',$category);

        return $this->fetch($this->template_path);
    }

    /**
     * @param Request $request
     */
    public function save(Request $request)
    {
        $post_category_id = $request->param('category_id');
        $post_sort = $request->param('sort');
        $post_title = $request->param('title');
        $post_status = $request->param('status');
        if(empty($post_title)){
            $this->error('名称不能为空');
        }
        $data = new AreaUnitModel;
        $data['title'] = $post_title;
        $data['sort'] = $post_sort;
        $data['state_id'] = $post_category_id;
        $data['status'] = $post_status;
        if ($data->save()) {
            $this->success('保存成功','/admin/area_unit/index');
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
        // 获取信息
        $data_list = AreaUnitModel::get($id);
        $this->assign('data',$data_list);

        // 获取分类列表
        $category_data = new AreaState();
        $category = $category_data->where(['status'=>1])->select();
        $this->assign('category',$category);

        $my_category_data = AreaState::get($data_list['state_id']);
        $my_category_title = $my_category_data['short_title'];
        $this->assign('my_state_id',$data_list['state_id']);
        $this->assign('my_state_title',$my_category_title);

        return $this->fetch($this->template_path);
    }

    /**
     * @param Request $request
     * @throws \think\exception\DbException
     */
    public function update(Request $request)
    {
        $post_id = $request->post('id');
        $post_sort = $request->post('sort');
        $post_title = $request->post('title');
        $post_status= $request->post('status');
        if(empty($post_title)){
            $this->error('名称不能为空');
        }

        $user = AreaUnitModel::get($post_id);
        $user['sort'] = $post_sort;
        if(!empty($post_title)){
            $user['title'] = $post_title;
        }
        $user['status'] = $post_status;

        if ($user->save()) {
            $this->success('更新成功', '/admin/area_unit/index');
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
        $data = AreaUnitModel::get($id);
        if ($data) {
            $data->delete();
            $this->success('删除成功', '/admin/area_unit/index');
        } else {
            $this->error('删除失败');
        }
    }
}