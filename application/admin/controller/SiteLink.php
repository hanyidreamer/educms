<?php
/**
 * Created by PhpStorm.
 * User: tanzhenxing
 * Date: 2017/4/25
 * Time: 16:38
 */
namespace app\admin\controller;

use think\Request;
use app\common\model\SiteLink as SiteLinkModel;
use app\common\model\SiteLinkCategory;

class SiteLink extends AdminBase
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

        // 友情链接分类id
        $category_info = SiteLinkCategory::get(['unique_code'=>'footer_links']);
        $category_id = $category_info['id'];

        // 找出广告列表数据
        $post_title = $request->param('title');
        $data = new SiteLinkModel;
        if(!empty($post_title)){
            $data_list = $data->where(['site_id'=>$site_id,'status' => 1, 'title' => ['like','%'.$post_title.'%']])->select();
        }else{
            $data_list = $data->where(['site_id'=>$site_id,'category_id'=>$category_id,'status'=>1])->select();
        }
        $data_count = count($data_list);
        $this->assign('data_count',$data_count);

        foreach ($data_list as $data){
            $category_id = $data['category_id'];
            $category_data = SiteLinkCategory::get($category_id);
            $category_title = $category_data['title'];
            $data['category_title'] = $category_title;
        }

        $this->assign('data_list',$data_list);


        return $this->fetch($this->template_path);
    }

    /**
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function add()
    {
        $site_id = $this->site_id;
        $this->assign('site_id',$site_id);
        // 分类id
        $site_link_category_info = SiteLinkCategory::get(['site_id'=>$site_id]);
        $category_id = $site_link_category_info['id'];
        $this->assign('category_id',$category_id);

        $this->assign('data_list',$site_link_category_info);

        return $this->fetch($this->template_path);

    }

    /**
     * @param Request $request
     */
    public function insert(Request $request)
    {
        $post_category_id= $request->post('category_id');
        $post_site_id= $request->post('site_id');
        $post_title= $request->post('title');
        $post_url= $request->post('url');
        $post_sort= $request->post('sort');
        $post_status= $request->post('status');
        if($post_title==''){
            $this->error('名称不能为空');
        }
        $user = new SiteLinkModel;
        $user['category_id'] = $post_category_id;
        $user['site_id'] = $post_site_id;
        $user['title'] = $post_title;
        $user['url'] = $post_url;
        $user['sort'] = $post_sort;
        $user['status'] = $post_status;
        if ($user->save()) {
            $this->success('新增链接成功', '/admin/site_link/index');
        } else {
            $user->getError();
        }
    }

    /**
     * @param $id
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function edit($id)
    {
        $top_link_info = SiteLinkModel::get($id);

        $this->assign('data_list',$top_link_info);

        return $this->fetch($this->template_path);
    }

    /**
     * @param Request $request
     * @throws \think\exception\DbException
     */
    public function save(Request $request)
    {
        $post_id= $request->post('id');
        $post_url= $request->post('url');
        $post_title= $request->post('title');
        $post_sort= $request->post('sort');
        if($post_id==''){
            $this->error('名称不能为空');
        }

        $user = SiteLinkModel::get($post_id);
        $user['url'] = $post_url;
        $user['title'] = $post_title;
        $user['sort'] = $post_sort;
        if ($user->save()) {
            $this->success('保存链接信息成功', '/admin/site_link/index');
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
        $user = SiteLinkModel::get($id);
        if ($user) {
            $user->delete();
            $this->success('删除链接成功', '/admin/site_link/index');
        } else {
            $this->error('您要删除的链接不存在');
        }
    }

}