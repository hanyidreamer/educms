<?php
/**
 * Created by PhpStorm.
 * User: tanzhenxing
 * Date: 2017/6/26
 * Time: 15:34
 */
namespace app\admin\controller;

use think\Request;
use app\common\model\TeacherCategory as TeacherCategoryModel;

class TeacherCategory extends AdminBase
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
        $site_id = $this->site_id;
        // 找出广告列表数据
        $post_title = $request->param('title');
        $data = new TeacherCategoryModel;
        if(!empty($post_title)){
            $data_list = $data->where([
                'site_id'=>$site_id,
                'status' => 1,
                'title' => ['like','%'.$post_title.'%']
            ])
                ->select();
        }else{
            $data_list = $data->where(['site_id'=>$site_id,'status'=>1])->select();
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
        $post_site_id = $request->param('site_id');
        $post_title = $request->param('title');
        $post_desc = $request->param('desc');
        $post_keywords = $request->param('keywords');
        $post_status = $request->param('status');
        if($post_title==''){
            $this->error('分类名称不能为空');
        }
        $data = new TeacherCategoryModel;
        $data['site_id'] = $post_site_id;
        $data['title'] = $post_title;
        $data['desc'] = $post_desc;
        $data['keywords'] = $post_keywords;
        $data['status'] = $post_status;
        if ($data->save()) {
            $this->success('保存成功','/admin/teacher_category/index');
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
        // 获取网站信息
        $data_list = TeacherCategoryModel::get($id);
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
        $post_site_id = $request->post('site_id');
        $post_title = $request->post('title');
        $post_keywords = $request->post('keywords');
        $post_desc = $request->post('desc');
        $post_status= $request->post('status');
        if(empty($post_title)){
            $this->error('分类名称不能为空');
        }

        $user = TeacherCategoryModel::get($post_id);
        $user['site_id'] = $post_site_id;
        $user['title'] = $post_title;
        $user['keywords'] = $post_keywords;
        $user['desc'] = $post_desc;
        $user['status'] = $post_status;
        if ($user->save()) {
            $this->success('保存成功', '/admin/teacher_category/index');
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
        $data = TeacherCategoryModel::get($id);
        if ($data) {
            $data->delete();
            $this->success('删除分类成功', '/admin/teacher_category/index');
        } else {
            $this->error('您要删除的分类不存在');
        }
    }
}