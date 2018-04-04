<?php
/**
 * Created by PhpStorm.
 * User: tanzhenxing
 * Date: 2017/8/16
 * Time: 23:38
 */
namespace app\admin\controller;

use think\Request;
use app\base\model\AreaTown as AreaTownModel;
use app\base\model\AreaCounty;

class AreaTown extends AdminBase
{
    /**
     * @param Request $request
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function index(Request $request)
    {
        // 找出列表数据
        $pages=15;
        $post_title = $request->param('title');
        $data = new AreaTownModel;
        if(!empty($post_title)){
            $data_list = $data->where(['status' => 1, 'title' => ['like','%'.$post_title.'%']])->order('id desc') -> paginate($pages);
        }else{
            $data_list = $data->where(['status'=>1])->order('id desc') -> paginate($pages);
        }
        $data_count = count($data_list);

        foreach ($data_list as $data){
            $county_id = $data['county_id'];
            $county_info = AreaCounty::get($county_id);
            $data['county_title'] = $county_info['title'];
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
        $category_data = new AreaCounty();
        $category = $category_data->where(['status'=>1])->select();
        $this->assign('county_category',$category);

        return $this->fetch($this->template_path);
    }

    public function save(Request $request)
    {
        $post_county_id = $request->param('county_id');
        $post_sort = $request->param('sort');
        $post_title = $request->param('title');
        $post_zipcode = $request->param('zipcode');
        $post_status = $request->param('status');
        if(empty($post_title)){
            $this->error('名称不能为空');
        }
        $data = new AreaTownModel;
        $data['title'] = $post_title;
        $data['zipcode'] = $post_zipcode;
        $data['sort'] = $post_sort;
        $data['county_id'] = $post_county_id;
        $data['status'] = $post_status;
        if ($data->save()) {
            $this->success('保存成功','/admin/area_town/index');
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
        $data_list = AreaTownModel::get($id);
        $this->assign('data',$data_list);

        // 获取分类列表
        $category_data = new AreaCounty();
        $category = $category_data->where(['status'=>1])->select();
        $this->assign('county_category',$category);

        $my_category_data = AreaCounty::get($data_list['county_id']);
        $my_category_title = $my_category_data['title'];
        $this->assign('my_county_id',$data_list['county_id']);
        $this->assign('my_county_title',$my_category_title);

        return $this->fetch($this->template_path);
    }

    /**
     * @param Request $request
     * @throws \think\exception\DbException
     */
    public function update(Request $request)
    {
        $post_id = $request->post('id');
        $post_county_id = $request->post('county_id');
        $post_sort = $request->post('sort');
        $post_title = $request->post('title');
        $post_zipcode = $request->post('zipcode');
        $post_status= $request->post('status');
        if(empty($post_title)){
            $this->error('名称不能为空');
        }

        $user = AreaTownModel::get($post_id);
        $user['county_id'] = $post_county_id;
        $user['zipcode'] = $post_zipcode;
        $user['sort'] = $post_sort;
        if(!empty($post_title)){
            $user['title'] = $post_title;
        }
        $user['status'] = $post_status;

        if ($user->save()) {
            $this->success('更新成功', '/admin/area_town/index');
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
        $data = AreaTownModel::get($id);
        if ($data) {
            $data->delete();
            $this->success('删除成功', '/admin/area_town/index');
        } else {
            $this->error('删除失败');
        }
    }
}