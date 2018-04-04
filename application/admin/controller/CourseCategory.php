<?php
/**
 * Created by PhpStorm.
 * User: tanzhenxing
 * Date: 2017/6/26
 * Time: 15:23
 */
namespace app\admin\controller;

use think\Request;
use app\base\model\CourseCategory as CourseCategoryModel;
use app\base\controller\Upload;

class CourseCategory extends AdminBase
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
        // 找出列表数据
        $post_title = $request->param('title');
        $data = new CourseCategoryModel;
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
     * @throws \think\exception\DbException
     */
    public function save(Request $request)
    {
        // 获取 分类略缩图 thumb文件
        $file_thumb = $request->file('thumb');
        if(!empty($file_thumb)){
            $local_thumb = $file_thumb->getInfo('tmp_name');
            $thumb_filename = $file_thumb->getInfo('name');
            $thumb_file_info = new Upload();
            $post_thumb=$thumb_file_info->qcloud_file($local_thumb,$thumb_filename);
        }

        $post_site_id = $request->param('site_id');
        $post_title = $request->param('title');
        $post_unique_code = $request->param('unique_code');
        $post_short_title = $request->param('short_title');
        $post_keywords = $request->param('keywords');
        $post_desc = $request->param('desc');
        $post_category_template_id = $request->param('category_template_id');
        $post_course_template_id = $request->param('course_template_id');
        $post_body = $request->param('body');

        $unique_code_info = CourseCategoryModel::get(['unique_code'=>$post_unique_code]);
        if(!empty($unique_code_info)){
            $this->error('分类标识码已经存在，创建广告分类失败');
        }
        $post_status = $request->param('status');
        if($post_title==''){
            $this->error('分类名称不能为空');
        }
        $data = new CourseCategoryModel;
        $data['site_id'] = $post_site_id;
        $data['title'] = $post_title;
        $data['unique_code'] = $post_unique_code;
        if(!empty($post_thumb)){
            $data['thumb'] = $post_thumb;
        }
        $data['short_title'] = $post_short_title;
        $data['keywords'] = $post_keywords;
        $data['desc'] = $post_desc;
        $data['category_template_id'] = $post_category_template_id;
        $data['course_template_id'] = $post_course_template_id;
        $data['body'] = $post_body;
        $data['status'] = $post_status;
        if ($data->save()) {
            $this->success('保存成功','/admin/course_category/index');
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
        // 获取网站信息
        $data_list = CourseCategoryModel::get($id);
        $this->assign('data',$data_list);

        return $this->fetch($this->template_path);
    }

    /**
     * @param Request $request
     * @throws \think\exception\DbException
     */
    public function update(Request $request)
    {
        // 获取 分类略缩图 thumb文件
        $file_thumb = $request->file('thumb');
        if(!empty($file_thumb)){
            $local_thumb = $file_thumb->getInfo('tmp_name');
            $thumb_filename = $file_thumb->getInfo('name');
            $thumb_file_info = new Upload();
            $post_thumb=$thumb_file_info->qcloud_file($local_thumb,$thumb_filename);
        }

        $post_id = $request->param('id');
        $post_site_id = $request->param('site_id');
        $post_title = $request->param('title');
        $post_unique_code = $request->param('unique_code');
        $post_short_title = $request->param('short_title');
        $post_keywords = $request->param('keywords');
        $post_desc = $request->param('desc');
        $post_category_template_id = $request->param('category_template_id');
        $post_course_template_id = $request->param('course_template_id');
        $post_body = $request->param('body');

        $post_status = $request->param('status');
        if($post_title==''){
            $this->error('分类名称不能为空');
        }

        $data = CourseCategoryModel::get($post_id);
        $data['site_id'] = $post_site_id;
        $data['title'] = $post_title;
        $data['unique_code'] = $post_unique_code;
        if(!empty($post_thumb)){
            $data['thumb'] = $post_thumb;
        }
        $data['short_title'] = $post_short_title;
        $data['keywords'] = $post_keywords;
        $data['desc'] = $post_desc;
        $data['category_template_id'] = $post_category_template_id;
        $data['course_template_id'] = $post_course_template_id;
        $data['body'] = $post_body;
        $data['status'] = $post_status;
        if ($data->save()) {
            $this->success('保存成功', '/admin/course_category/index');
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
        $data = CourseCategoryModel::get($id);
        if ($data) {
            $data->delete();
            $this->success('删除分类成功', '/admin/course_category/index');
        } else {
            $this->error('您要删除的分类不存在');
        }
    }

}