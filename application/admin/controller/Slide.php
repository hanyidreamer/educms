<?php
/**
 * Created by PhpStorm.
 * User: tzx
 * Date: 2016/10/25
 * Time: 17:40
 */
namespace app\admin\controller;

use think\Request;
use app\common\model\Slide as SlideModel;
use app\common\model\SlideCategory;
use app\base\controller\Upload;

class Slide extends AdminBase
{
    /**
     * 当前网站幻灯片列表
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
        $data = new SlideModel;
        if(!empty($post_title)){
            $data_list = $data->where(['status' => 1,'site_id'=>$this->site_id])
                ->where('title','like','%'.$post_title.'%')
                ->select();
        }else{
            $data_list = $data->where(['status'=>1,'site_id'=>$this->site_id])->select();
        }
        $data_count = count($data_list);
        $this->assign('data_count',$data_count);

        foreach ($data_list as $data){
            $category_id = $data['category_id'];
            $category_data = SlideCategory::get($category_id);
            $category_title = $category_data['title'];
            $data['category_title'] = $category_title;
        }

        $this->assign('data_list',$data_list);

        return $this->fetch($this->template_path);
    }

    /**
     * 新增幻灯片
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function create()
    {
        // 获取分类列表
        $category_data = new SlideCategory();
        $category = $category_data->where(['status'=>1])->select();
        $this->assign('category',$category);

        return $this->fetch($this->template_path);
    }

    /**
     * 保存幻灯片信息
     * @param Request $request
     * @throws \think\exception\DbException
     */
    public function save(Request $request)
    {
        $upload = new Upload();
        $post_thumb = $upload->qcloud_file('thumb');
        $post_icon = $upload->qcloud_file('icon');
        $post_category_id = $request->post('category_id');
        $post_title = $request->post('title');
        $post_desc = $request->post('desc');
        $post_sort= $request->post('sort');
        $post_sort=(int)$post_sort;
        $post_button_text= $request->post('button');
        $post_url= $request->post('url');
        $post_status= $request->post('status');

        if($post_title==''){
            $this->error('幻灯片标题不能为空');
        }
        $user = new SlideModel;
        $user['site_id'] = $this->site_id;
        $user['category_id'] = $post_category_id;
        $user['title']    = $post_title;
        $user['desc'] = $post_desc;
        if(!empty($post_thumb)){
            $user['thumb'] = $post_thumb;
        }
        if(!empty($post_icon)){
            $user['icon'] = $post_icon;
        }
        $user['sort']    = $post_sort;
        $user['button'] = $post_button_text;
        $user['url'] = $post_url;
        $user['status'] = $post_status;

        if ($user->save()) {
            $this->success('新增幻灯片成功', '/admin/slide/index');
        } else {
            $this->error('操作失败');
        }

    }

    /**
     * 编辑幻灯片信息
     * @param $id
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function edit($id)
    {
        // 获取当前分类id
        $category_id_info = SlideModel::get($id);
        $category_id = $category_id_info['category_id'];

        // 获取信息
        $data_list = SlideModel::get($id);
        $this->assign('data',$data_list);

        // 获取网站分类列表
        $category_data = new SlideCategory();
        $category = $category_data->where(['status'=>1])->select();
        $this->assign('category',$category);

        $my_category_data = SlideCategory::get($category_id);
        $my_category_title = $my_category_data['title'];
        $this->assign('my_category_id',$category_id);
        $this->assign('my_category_title',$my_category_title);

        return $this->fetch($this->template_path);
    }

    /**
     * 更新幻灯片信息
     * @param Request $request
     * @throws \think\exception\DbException
     */
    public function update(Request $request)
    {
        $upload = new Upload();
        $post_thumb = $upload->qcloud_file('thumb');
        $post_icon = $upload->qcloud_file('icon');

        $post_id = $request->post('id');
        $post_category_id = $request->post('category_id');
        $post_title = $request->post('title');
        $post_desc = $request->post('desc');
        $post_sort= $request->post('sort');
        $post_sort=(int)$post_sort;
        $post_button_text= $request->post('button');
        $post_url= $request->post('url');
        $post_status= $request->post('status');
        if($post_title=='' or $post_id==''){
            $this->error('幻灯片名称不能为空');
        }

        $user = SlideModel::get($post_id);
        $user['site_id'] = $this->site_id;
        $user['category_id'] = $post_category_id;
        $user['title']    = $post_title;
        $user['desc'] = $post_desc;
        if(!empty($post_thumb)){
            $user['thumb'] = $post_thumb;
        }
        if(!empty($post_icon)){
            $user['icon'] = $post_icon;
        }
        $user['sort']    = $post_sort;
        $user['button'] = $post_button_text;
        $user['url'] = $post_url;
        $user['status'] = $post_status;

        if ($user->save()) {
            $this->success('保存幻灯片信息成功', '/admin/slide/index');
        } else {
            $this->error('操作失败');
        }
    }

    /**
     * 删除幻灯片
     * @param $id
     * @throws \think\exception\DbException
     */
    public function delete($id)
    {
        $data = SlideModel::get($id);
        if ($data) {
            $data->delete();
            $this->success('删除幻灯片成功', '/admin/slide/index');
        } else {
            $this->error('您要删除的幻灯片不存在');
        }
    }

}