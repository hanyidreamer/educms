<?php
/**
 * Created by PhpStorm.
 * User: tanzhenxing
 * Date: 2017/6/26
 * Time: 15:22
 */
namespace app\admin\controller;

use think\Request;
use app\common\model\Course as CourseModel;
use app\common\model\CourseCategory;
use app\common\model\Admin;
use app\common\model\Teacher;
use app\common\model\ResourcesFile;
use app\common\model\ResourcesVideo;
use app\base\controller\Upload;


class Course extends AdminBase
{
    /**
     * @param Request $request
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function index(Request $request)
    {
        $site_id = $this->site_id;
        $post_title= $request->post('title');
        if($post_title==!''){
            $data_sql['title'] =  ['like','%'.$post_title.'%'];
        }

        // 分页数量
        $pages=10;
        $article_list = new CourseModel();
        $data_list =$article_list->where(['site_id'=>$site_id,'status'=>1])->order('id desc') -> paginate($pages);
        $data_count = count($data_list);

        foreach($data_list as $data)
        {
            $category_id=$data->category_id;
            $category_list = CourseCategory::get($category_id);
            $data->category=$category_list['title'];

            $teacher_id = $data->teacher_id;
            $teacher_list = Teacher::get($teacher_id);
            $data->teacher=$teacher_list['real_name'];
        }
        $this->assign('data_list',$data_list);
        $this->assign('data_count',$data_count);
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
        $site_id = $this->site_id;
        $article_category_info = new CourseCategory();
        $category_list = $article_category_info->where(['site_id'=>$site_id]) -> select();
        foreach($category_list as $data)
        {
            $data->id;
            $data->title;
        }
        $this->assign('category_list',$category_list);

        $teacher_info = new Teacher();
        $teacher_list = $teacher_info->where(['site_id'=>$site_id]) -> select();
        $this->assign('teacher_list',$teacher_list);

        return $this->fetch($this->template_path);
    }

    /**
     * @param Request $request
     * @throws \think\exception\DbException
     */
    public function save(Request $request)
    {
        $post_site_id = $request->post('site_id');
        $admin_username= session('username');
        $admin_list = Admin::get(['username'=>$admin_username]);
        $admin_id = $admin_list['id'];

        // 获取 网站略缩图 thumb文件
        $file_thumb = $request->file('thumb');
        if(!empty($file_thumb)){
            $local_thumb = $file_thumb->getInfo('tmp_name');
            $thumb_filename = $file_thumb->getInfo('name');
            $thumb_file_info = new Upload();
            $post_thumb=$thumb_file_info->qcloud_file($local_thumb,$thumb_filename);
        }

        $file_course_file = $request->file('course_file');
        if(!empty($file_course_file)){
            $local_course_file = $file_course_file->getInfo('tmp_name');
            $course_file_filename = $file_course_file->getInfo('name');
            $thumb_course_file_info = new Upload();
            $post_course_file = $thumb_course_file_info->qcloud_file($local_course_file,$course_file_filename);
        }
        if(!empty($post_course_file)){
            $file_data = new ResourcesFile;
            $file_data['url'] = $post_course_file;
            $file_data['status'] = 1;
            $file_data['site_id'] = $post_site_id;
            $file_data->save();
            $file_id = $file_data['id'];
        }

        $file_video = $request->file('video');
        if(!empty($file_video)){
            $local_video = $file_video->getInfo('tmp_name');
            $video_filename = $file_video->getInfo('name');
            $video_file_info = new Upload();
            $post_video = $video_file_info->qcloud_file($local_video,$video_filename);
        }
        if(!empty($post_video)){
            $video_data = new ResourcesVideo;
            $video_data['url'] = $post_video;
            $video_data['status'] = 1;
            $video_data['site_id'] = $post_site_id;
            $video_data->save();
            $video_id = $video_data['id'];
        }



        $post_title= $request->post('title');
        $post_short_title= $request->post('short_title');
        $post_teacher= $request->post('teacher');

        $post_course_category= $request->post('course_category');
        $post_template_id= $request->post('template_id');

        $post_unique_code= $request->post('unique_code');
        if($post_unique_code==""){
            $post_unique_code= 'c'.time().rand(1000,9999);
        }
        $post_keywords = $request->post('keywords');
        $post_desc = $request->post('desc');

        $post_click = $request->post('click');
        $post_sort = $request->post('sort');
        $post_buy_number = $request->post('buy_number');
        $post_collection= $request->post('collection');
        $post_like = $request->post('like');

        $article_body = $request->post('myVent');
        $post_body = preg_replace('/mmbiz.qpic.cn\//',$_SERVER['HTTP_HOST'].'/qpic/',$article_body);

        $post_status= $request->post('status');
        if($post_title=='' or $post_unique_code==''){
            $this->error('标题和唯一标识码不能为空');
        }


        $user = new CourseModel;
        if(!empty($post_thumb)){
            $user['thumb'] = $post_thumb;
        }
        if(!empty($file_id)){
            $user['file_id'] = $file_id;
        }
        if(!empty($video_id)){
            $user['video_id'] = $video_id;
        }
        $user['site_id'] = $post_site_id;
        $user['title'] = $post_title;
        $user['short_title'] = $post_short_title;
        $user['teacher_id'] = $post_teacher;
        $user['admin_id'] = $admin_id;
        $user['category_id'] = $post_course_category;
        $user['template_id'] = $post_template_id;

        $user['unique_code'] = $post_unique_code;
        $user['keywords'] = $post_keywords;
        $user['desc'] = $post_desc;
        $user['click'] = $post_click;
        $user['sort'] = $post_sort;
        $user['buy_number'] = $post_buy_number;

        $user['collection'] = $post_collection;
        $user['like'] = $post_like;

        $user['body'] = $post_body;
        $user['status'] = $post_status;

        if ($user->save()) {
            $this->success('新增成功', '/admin/course/index');
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
        $site_id = $this->site_id;
        $article_info = new CourseModel();
        $data_list = $article_info -> get($id);
        $file_id = $data_list['file_id'];
        $video_id = $data_list['video_id'];
        $this->assign('data_list',$data_list);

        $file_info = ResourcesFile::get($file_id);
        $file_url = $file_info['url'];
        $this->assign('file_url',$file_url);

        $video_info = ResourcesVideo::get($video_id);
        $video_url = $video_info['url'];
        $this->assign('video_url',$video_url);


        $my_category_id = $data_list['category_id'];
        $this->assign('my_category_id',$my_category_id);

        $article_category_info = new CourseCategory();
        $category_list = $article_category_info->where(['status'=>1,'site_id'=>$site_id]) -> select();

        $this->assign('category_list',$category_list);

        $teacher_info = new Teacher();
        $teacher_list = $teacher_info->where(['site_id'=>$site_id]) -> select();
        $this->assign('teacher_list',$teacher_list);

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
        $admin_username= session('username');
        $admin_list = Admin::get(['username'=>$admin_username]);
        $admin_id = $admin_list['id'];

        // 获取 网站略缩图 thumb文件
        $file_thumb = $request->file('thumb');
        if(!empty($file_thumb)){
            $local_thumb = $file_thumb->getInfo('tmp_name');
            $thumb_filename = $file_thumb->getInfo('name');
            $thumb_file_info = new Upload();
            $post_thumb=$thumb_file_info->qcloud_file($local_thumb,$thumb_filename);
        }

        $file_course_file = $request->file('course_file');
        if(!empty($file_course_file)){
            $local_course_file = $file_course_file->getInfo('tmp_name');
            $course_file_filename = $file_course_file->getInfo('name');
            $thumb_course_file_info = new Upload();
            $post_course_file = $thumb_course_file_info->qcloud_file($local_course_file,$course_file_filename);
        }
        if(!empty($post_course_file)){
            $file_data = new ResourcesFile;
            $file_data['url'] = $post_course_file;
            $file_data['status'] = 1;
            $file_data['site_id'] = $post_site_id;
            $file_data->save();
            $file_id = $file_data['id'];
        }

        $file_video = $request->file('video');
        if(!empty($file_video)){
            $local_video = $file_video->getInfo('tmp_name');
            $video_filename = $file_video->getInfo('name');
            $video_file_info = new Upload();
            $post_video = $video_file_info->qcloud_file($local_video,$video_filename);
        }
        if(!empty($post_video)){
            $video_data = new ResourcesVideo;
            $video_data['url'] = $post_video;
            $video_data['status'] = 1;
            $video_data['site_id'] = $post_site_id;
            $video_data->save();
            $video_id = $video_data['id'];
        }

        $post_title= $request->post('title');
        $post_short_title= $request->post('short_title');
        $post_teacher= $request->post('teacher');

        $post_course_category= $request->post('course_category');
        $post_template_id= $request->post('template_id');

        $post_unique_code= $request->post('unique_code');
        if($post_unique_code==""){
            $post_unique_code= 'c'.time().rand(1000,9999);
        }
        $post_keywords = $request->post('keywords');
        $post_desc = $request->post('desc');

        $post_click = $request->post('click');
        $post_sort = $request->post('sort');
        $post_buy_number = $request->post('buy_number');
        $post_collection= $request->post('collection');
        $post_like = $request->post('like');

        $article_body = $request->post('myVent');
        $post_body = preg_replace('/mmbiz.qpic.cn\//',$_SERVER['HTTP_HOST'].'/qpic/',$article_body);

        $post_status= $request->post('status');
        if($post_title=='' or $post_unique_code==''){
            $this->error('文章标题和唯一标识码不能为空');
        }

        $user = CourseModel::get($post_id);
        if(!empty($post_thumb)){
            $user['thumb'] = $post_thumb;
        }
        if(!empty($file_id)){
            $user['file_id'] = $file_id;
        }
        if(!empty($video_id)){
            $user['video_id'] = $video_id;
        }
        $user['site_id'] = $post_site_id;
        $user['title'] = $post_title;
        $user['short_title'] = $post_short_title;
        $user['teacher_id'] = $post_teacher;
        $user['admin_id'] = $admin_id;
        $user['category_id'] = $post_course_category;
        $user['template_id'] = $post_template_id;

        $user['unique_code'] = $post_unique_code;
        $user['keywords'] = $post_keywords;
        $user['desc'] = $post_desc;
        $user['click'] = $post_click;
        $user['sort'] = $post_sort;
        $user['buy_number'] = $post_buy_number;

        $user['collection'] = $post_collection;
        $user['like'] = $post_like;

        $user['body'] = $post_body;
        $user['status'] = $post_status;

        if ($user->save()) {
            $this->success('保存内容成功', '/admin/course/index');
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
        $user = CourseModel::get($id);
        $user['status'] = 0;
        if ($user->save()) {
            $this->success('文章已删除', '/admin/course/recycle');
        } else {
            $this->error('操作失败');
        }
    }

    /**
     * @param Request $request
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function recycle(Request $request)
    {
        $post_title= $request->post('title');
        if($post_title==!''){
            $data_sql['title'] =  ['like','%'.$post_title.'%'];
        }
        // 分页数量
        $pages=15;
        $article_info = new CourseModel();
        $data_list = $article_info->where(['status'=>0])->order('id desc')  -> paginate($pages);
        $data_count = count($data_list);
        foreach($data_list as $data)
        {
            $category_id=$data->category_id;
            $category_list = CourseCategory::get(['id'=>$category_id]);
            $data->category=$category_list['title'];
        }
        $this->assign('data_list',$data_list);
        $this->assign('data_count',$data_count);
        return $this -> fetch($this->template_path);
    }

    /**
     * 恢复网站
     * @param $id
     * @throws \think\exception\DbException
     */
    public function recovery($id){
        $user = CourseModel::get($id);
        $user['status'] = 1;
        if ($user->save()) {
            $this->success('文章已恢复', '/admin/course/recycle');
        } else {
            $this->error('操作失败');
        }
    }

    /**
     * 永久删除
     * @param $id
     * @throws \think\exception\DbException
     */
    public function del($id)
    {
        $user = CourseModel::get($id);
        if ($user) {
            $user->delete();
            $this->success('文章已经永久删除', '/admin/course/recycle');
        } else {
            $this->error('您要删除的文章不存在');
        }
    }

}