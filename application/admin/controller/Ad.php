<?php
namespace app\admin\controller;

use app\common\model\Ad as AdModel;
use app\common\model\AdCategory;
use app\base\controller\Upload;

class Ad extends AdminBase
{
    /**
     * 广告列表
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index()
    {
        // 找出广告列表数据
        $post_title = $this->request->param('title');
        $data = new AdModel;
        if(!empty($post_title)){
            $data_list = $data->where(['status'=>1,'site_id'=>$this->site_id])
                ->where('title', 'like', '%'.$post_title.'%')
                ->select();
        }else{
            $data_list = $data->where(['status'=>1,'site_id'=>$this->site_id])->select();
        }
        $data_count = count($data_list);
        $this->assign('data_count',$data_count);

        foreach ($data_list as $data){
            $category_id = $data['category_id'];
            $category_data = AdCategory::get($category_id);
            $category_title = $category_data['title'];
            $data['category_title'] = $category_title;
        }

        $this->assign('data_list',$data_list);

        return $this->fetch($this->template_path);
    }

    /**
     * 添加广告
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
     * 保存广告数据
     * @throws \think\exception\DbException
     */
    public function save()
    {
        $post_data = $this->request->param();
        $post_data['site_id'] = $this->site_id;
        $upload = new Upload();
        $post_data['thumb'] = $upload->qcloud_file('thumb');
        if(empty($post_data['title'])){
            $this->error('广告标题不能为空');
        }

        $data = new AdModel;
        $data_array = array('site_id','category_id','title','description','url','thumb','sort','click','background','status');
        $data_save = $data->allowField($data_array)->save($post_data);
        if ($data_save) {
            $this->success('保存成功','/admin/ad/index');
        } else {
            $this->error('操作失败');
        }
    }

    /**
     * 编辑广告
     * @param $id
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function edit($id)
    {
        // 获取当前分类id
        $category_id_info = AdModel::get($id);
        $category_id = $category_id_info['category_id'];

        // 获取广告信息
        $data_list = AdModel::get($id);
        $this->assign('data',$data_list);

        // 获取网站分类列表
        $category_data = new AdCategory();
        $category = $category_data->where(['status'=>1])->select();
        $this->assign('category',$category);

        $my_category_data = AdCategory::get($category_id);
        $my_category_title = $my_category_data['title'];
        $this->assign('my_category_id',$category_id);
        $this->assign('my_category_title',$my_category_title);

        return $this->fetch($this->template_path);
    }

    /**
     * 更新广告
     * @throws \think\exception\DbException
     */
    public function update()
    {
        $post_data = $this->request->param();
        $post_data['site_id'] = $this->site_id;
        $upload = new Upload();
        $post_data['thumb'] = $upload->qcloud_file('thumb');
        if(empty($post_data['thumb'])){
            unset($post_data['thumb']);
        }
        if(empty($post_data['title'])){
            $this->error('广告标题不能为空');
        }

        $data = AdModel::get($post_data['id']);
        $data_array = array('site_id','category_id','title','description','url','thumb','sort','click','background','status');
        $data_save = $data->allowField($data_array)->save($post_data);
        if ($data_save) {
            $this->success('保存成功','/admin/ad/index');
        } else {
            $this->error('操作失败');
        }
    }

    /**
     * 删除广告
     * @param $id
     * @throws \think\exception\DbException
     */
    public function delete($id)
    {
        $data = AdModel::get($id);
        if ($data) {
            $data->delete();
            $this->success('删除广告成功', '/admin/ad/index');
        } else {
            $this->error('您要删除的广告不存在');
        }
    }
}
