<?php
/**
 * Created by PhpStorm.
 * User: tanzhenxing
 * Date: 2017/6/26
 * Time: 15:31
 */
namespace app\admin\controller;

use think\Request;
use app\common\model\SlideCategory as SlideCategoryModel;
use app\common\model\AdCategory;

class SlideCategory extends AdminBase
{
    /**
     * 幻灯片分类
     * @param Request $request
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index(Request $request)
    {
        // 找出广告列表数据
        $post_title = $request->param('title');
        $data = new SlideCategoryModel;
        if(!empty($post_title)){
            $data_list = $data->where(['status' => 1])
                ->where('title','like','%'.$post_title.'%')
                ->select();
        }else{
            $data_list = $data->where(['status'=>1])->select();
        }
        $data_count = count($data_list);
        $this->assign('data_count',$data_count);

        $this->assign('data_list',$data_list);

        return $this->fetch($this->template_path);
    }

    /**
     * 新增幻灯片分类
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function create()
    {
        // 获取网站分类列表
        $category_data = new AdCategory();
        $category = $category_data->where(['status'=>1])->select();
        $this->assign('category',$category);

        return $this->fetch($this->template_path);
    }

    /**
     * 保存幻灯片分类
     * @param Request $request
     * @throws \think\exception\DbException
     */
    public function save(Request $request)
    {
        $post_title = $request->param('title');
        $post_description = $request->param('description');
        $post_unique_code = $request->param('unique_code');
        $post_status = $request->param('status');

        $slide_category_data = SlideCategoryModel::get(['unique_code'=>$post_unique_code]);
        if(!empty($slide_category_data)){
            $this->error('你添加的唯一识别码重复，请更改！');
        }

        if(empty($post_title)){
            $this->error('幻灯片类型不能为空');
        }

        $data = new SlideCategoryModel;
        $data['title'] = $post_title;
        $data['description'] = $post_description;
        $data['unique_code'] = $post_unique_code;
        $data['status'] = $post_status;
        if ($data->save()) {
            $this->success('保存成功','/admin/slide_category/index');
        } else {
            $this->error('操作失败');
        }
    }

    /**
     * 编辑幻灯片分类
     * @param $id
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function edit($id)
    {
        $data_list = SlideCategoryModel::get($id);
        $this->assign('data',$data_list);
        return $this->fetch($this->template_path);
    }

    /**
     * 更新幻灯片分类
     * @param Request $request
     * @throws \think\exception\DbException
     */
    public function update(Request $request)
    {
        $post_id = $request->param('id');
        $post_title = $request->param('title');
        $post_description = $request->param('description');
        $post_status= $request->param('status');

        if(empty($post_title)){
            $this->error('名称不能为空');
        }

        $data = SlideCategoryModel::get($post_id);
        $data['title'] = $post_title;
        $data['description'] = $post_description;
        $data['status'] = $post_status;

        if ($data->save()) {
            $this->success('更新成功', '/admin/slide_category/index');
        } else {
            $this->error('操作失败');
        }

    }

    /**
     * 删除幻灯片分类
     * @param $id
     * @throws \think\exception\DbException
     */
    public function delete($id)
    {
        $data = SlideCategoryModel::get($id);
        if ($data) {
            $data->delete();
            $this->success('删除成功', '/admin/slide_category/index');
        } else {
            $this->error('删除失败');
        }
    }
}