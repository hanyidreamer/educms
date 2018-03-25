<?php
/**
 * Created by PhpStorm.
 * User: tanzhenxing
 * Date: 2017/6/26
 * Time: 15:18
 */
namespace app\admin\controller;

use think\Request;
use app\base\model\AreaCounty as AreaCountyModel;
use app\base\model\AreaCity;
use app\base\controller\TemplatePath;
use app\base\controller\Base;

class AreaCounty extends Base
{
    public function index(Request $request)
    {
        // 给当页面标题赋值
        $title = '列表';
        $this->assign('title',$title);

        // 当前方法不同终端的模板路径
        $controller_name = Request::instance()->controller();
        $action_name = Request::instance()->action();
        $template_path_info = new TemplatePath();
        $template_path = $template_path_info->admin_path($controller_name,$action_name);
        $template_public = $template_path_info->admin_public_path();
        $template_public_header = $template_public.'/header';
        $template_public_footer = $template_public.'/footer';
        $this->assign('public_header',$template_public_header);
        $this->assign('public_footer',$template_public_footer);


        // 找出列表数据
        $pages=15;
        $post_title = $request->param('title');
        $data = new AreaCountyModel;
        if(!empty($post_title)){
            $data_list = $data->where(['status' => 1, 'title' => ['like','%'.$post_title.'%']])->order('id desc') -> paginate($pages);
        }else{
            $data_list = $data->where(['status'=>1])->order('id desc') -> paginate($pages);
        }
        $data_count = count($data_list);

        foreach ($data_list as $data){
            $city_id = $data['city_id'];
            $city_info = AreaCity::get($city_id);
            $data['city_title'] = $city_info['title'];
        }

        $this->assign('data_count',$data_count);
        $this->assign('data_list',$data_list);

        return $this->fetch($template_path);
    }

    public function create()
    {
        // 新增
        $title = '类目';
        $this->assign('title',$title);

        // 当前方法不同终端的模板路径
        $controller_name = Request::instance()->controller();
        $action_name = Request::instance()->action();
        $template_path_info = new TemplatePath();
        $template_path = $template_path_info->admin_path($controller_name,$action_name);
        $template_public = $template_path_info->admin_public_path();
        $template_public_header = $template_public.'/header';
        $template_public_footer = $template_public.'/footer';
        $this->assign('public_header',$template_public_header);
        $this->assign('public_footer',$template_public_footer);

        // 获取分类列表
        $category_data = new AreaCity();
        $category = $category_data->where(['status'=>1])->select();
        $this->assign('category',$category);

        return $this->fetch($template_path);
    }

    public function save(Request $request)
    {
        $post_city_id = $request->param('city_id');
        $post_sort = $request->param('sort');
        $post_title = $request->param('title');
        $post_zipcode = $request->param('zipcode');
        $post_status = $request->param('status');
        if(empty($post_title)){
            $this->error('名称不能为空');
        }
        $data = new AreaCountyModel;
        $data['title'] = $post_title;
        $data['zipcode'] = $post_zipcode;
        $data['sort'] = $post_sort;
        $data['city_id'] = $post_city_id;
        $data['status'] = $post_status;
        if ($data->save()) {
            $this->success('保存成功','/admin/area_county/index');
        } else {
            $this->error('操作失败');
        }
    }

    public function edit($id)
    {
        $title = '编辑类目';
        $this->assign('title',$title);

        // 当前方法不同终端的模板路径
        $controller_name = Request::instance()->controller();
        $action_name = Request::instance()->action();
        $template_path_info = new TemplatePath();
        $template_path = $template_path_info->admin_path($controller_name,$action_name);
        $template_public = $template_path_info->admin_public_path();
        $template_public_header = $template_public.'/header';
        $template_public_footer = $template_public.'/footer';
        $this->assign('public_header',$template_public_header);
        $this->assign('public_footer',$template_public_footer);

        // 获取信息
        $data_list = AreaCountyModel::get($id);
        $this->assign('data',$data_list);

        // 获取分类列表
        $category_data = new AreaCity();
        $category = $category_data->where(['status'=>1])->select();
        $this->assign('city_category',$category);

        $my_category_data = AreaCity::get($data_list['city_id']);
        $my_category_title = $my_category_data['title'];
        $this->assign('my_city_id',$data_list['city_id']);
        $this->assign('my_city_title',$my_category_title);

        return $this->fetch($template_path);
    }

    public function update(Request $request)
    {
        $post_id = $request->post('id');
        $post_city_id = $request->post('city_id');
        $post_sort = $request->post('sort');
        $post_title = $request->post('title');
        $post_zipcode = $request->post('zipcode');
        $post_status= $request->post('status');
        if(empty($post_title)){
            $this->error('名称不能为空');
        }

        $user = AreaCountyModel::get($post_id);
        $user['city_id'] = $post_city_id;
        $user['zipcode'] = $post_zipcode;
        $user['sort'] = $post_sort;
        if(!empty($post_title)){
            $user['title'] = $post_title;
        }
        $user['status'] = $post_status;

        if ($user->save()) {
            $this->success('更新成功', '/admin/area_county/index');
        } else {
            $this->error('操作失败');
        }

    }

    public function delete($id)
    {
        $data = AreaCountyModel::get($id);
        if ($data) {
            $data->delete();
            $this->success('删除成功', '/admin/area_county/index');
        } else {
            $this->error('删除失败');
        }
    }
}