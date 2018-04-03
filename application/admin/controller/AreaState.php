<?php
/**
 * Created by PhpStorm.
 * User: tanzhenxing
 * Date: 2017/8/16
 * Time: 10:05
 */
namespace app\admin\controller;

use think\Request;
use app\base\model\AreaState as AreaStateModel;
use app\base\model\Area;

class AreaState extends AdminBase
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
        $data = new AreaStateModel;
        if(!empty($post_title)){
            $data_list = $data->where(['status' => 1, 'title' => ['like','%'.$post_title.'%']])->select();
        }else{
            $data_list = $data->where(['status'=>1])->select();
        }
        foreach ($data_list as $data){
            $area_id = $data['area_id'];
            $area_info = Area::get($area_id);
            $data['area_title'] = $area_info['title'];
        }
        $data_count = count($data_list);
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
        $category_data = new Area();
        $category = $category_data->where(['status'=>1])->select();
        $this->assign('category',$category);

        return $this->fetch($this->template_path);
    }

    /**
     * @param Request $request
     */
    public function save(Request $request)
    {
        $post_sort = $request->param('sort');
        $post_category_id = $request->param('category_id');
        $post_title = $request->param('title');
        $post_short_title = $request->param('short_title');
        $post_english_title = $request->param('english_title');
        $post_status = $request->param('status');
        if(empty($post_title)){
            $this->error('名称不能为空');
        }
        $data = new AreaStateModel;
        $data['title'] = $post_title;
        $data['short_title'] = $post_short_title;
        $data['area_id'] = $post_category_id;
        $data['sort'] = $post_sort;
        $data['english_title'] = $post_english_title;
        $data['status'] = $post_status;
        if ($data->save()) {
            $this->success('保存成功','/admin/area_state/index');
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
        $data_list = AreaStateModel::get($id);
        $this->assign('data',$data_list);

        // 获取分类列表
        $category_data = new Area();
        $category = $category_data->where(['status'=>1])->select();
        $this->assign('category',$category);

        $my_category_data = Area::get($data_list['area_id']);
        $my_category_title = $my_category_data['title'];
        $this->assign('my_area_id',$data_list['area_id']);
        $this->assign('my_area_title',$my_category_title);

        return $this->fetch($this->template_path);
    }

    /**
     * @param Request $request
     * @throws \think\exception\DbException
     */
    public function update(Request $request)
    {
        $post_id = $request->post('id');
        $post_category_id = $request->post('category_id');
        $post_sort = $request->post('sort');
        $post_title = $request->post('title');
        $post_short_title = $request->post('short_title');
        $post_english_title = $request->post('english_title');
        $post_status= $request->post('status');
        if(empty($post_title)){
            $this->error('名称不能为空');
        }

        $user = AreaStateModel::get($post_id);
        $user['area_id'] = $post_category_id;
        $user['sort'] = $post_sort;
        if(!empty($post_title)){
            $user['title'] = $post_title;
        }
        $user['short_title'] = $post_short_title;
        $user['english_title'] = $post_english_title;
        $user['status'] = $post_status;

        if ($user->save()) {
            $this->success('更新成功', '/admin/area_state/index');
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
        $data = AreaStateModel::get($id);
        if ($data) {
            $data->delete();
            $this->success('删除成功', '/admin/area_state/index');
        } else {
            $this->error('删除失败');
        }
    }
}