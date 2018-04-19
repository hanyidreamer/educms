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
     * 链接列表
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index()
    {
        // 友情链接分类id
        $category_info = SiteLinkCategory::get(['unique_code'=>'footer_links','site_id'=>$this->site_id]);
        $category_id = $category_info['id'];

        // 找出广告列表数据
        $post_title = $this->request->param('title');
        $data = new SiteLinkModel;
        if(!empty($post_title)){
            $data_list = $data->where(['site_id'=>$this->site_id,'status' => 1])
                ->where('title','like','%'.$post_title.'%')
                ->select();
        }else{
            $data_list = $data->where(['site_id'=>$this->site_id,'category_id'=>$category_id,'status'=>1])->select();
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
     * 新增
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function create()
    {
        // 分类id
        $site_link_category_info = SiteLinkCategory::get(['unique_code'=>'footer_links','site_id'=>$this->site_id]);
        $category_id = $site_link_category_info['id'];
        $this->assign('category_id',$category_id);

        $this->assign('data',$site_link_category_info);

        return $this->fetch($this->template_path);

    }

    /**
     * 保存
     */
    public function save()
    {
        $post_data = $this->request->param();
        $post_data['site_id'] = $this->site_id;
        if($post_data['title']==''){
            $this->error('名称不能为空');
        }
        $data = new SiteLinkModel;
        $data_array = array('site_id','category_id','title','description','keywords','url','icon','sort','status');
        $data_save = $data->allowField($data_array)->save($post_data);
        if ($data_save) {
            $this->success('新增链接成功', '/admin/site_link/index');
        } else {
            $this->error('保存失败');
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
        $this->assign('data',$top_link_info);
        return $this->fetch($this->template_path);
    }

    /**
     * 更新
     * @param Request $request
     * @throws \think\exception\DbException
     */
    public function update()
    {
        $post_data = $this->request->param();
        $post_data['site_id'] = $this->site_id;
        if($post_data['title']==''){
            $this->error('名称不能为空');
        }

        $data = SiteLinkModel::get($post_data['id']);
        $data_array = array('site_id','category_id','title','description','keywords','url','icon','sort','status');
        $data_save = $data->allowField($data_array)->save($post_data);
        if ($data_save) {
            $this->success('保存成功', '/admin/site_link/index');
        } else {
            $this->error('保存失败');
        }

    }

    /**
     * 删除
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