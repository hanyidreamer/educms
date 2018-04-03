<?php
/**
 * Created by PhpStorm.
 * User: tanzhenxing
 * Date: 2017/8/5
 * Time: 16:41
 */

namespace app\admin\controller;

use think\Request;
use app\base\model\Ad as AdModel;
use app\base\model\AdCategory;
use app\base\controller\Upload;

class AliyunOss extends AdminBase
{
    /**
     * 广告列表
     * @param Request $request
     * @return mixed
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index(Request $request)
    {
        $site_id = $this->site_id;
        // 找出广告列表数据
        $post_title = $request->param('title');
        $data = new AdModel;
        if(!empty($post_title)){
            $data_list = $data->where(['status' => 1, 'title' => ['like','%'.$post_title.'%']])->select();
        }else{
            $data_list = $data->where(['site_id'=>$site_id,'status'=>1])->select();
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
     * @return mixed
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function create()
    {
        $site_id = $this->site_id;
        // 获取网站分类列表
        $category_data = new AdCategory();
        $category = $category_data->where(['site_id'=>$site_id])->select();
        $this->assign('category',$category);

        return $this->fetch($this->template_path);
    }

    /**
     * @param Request $request
     */
    public function save(Request $request)
    {
        // 获取 略缩图 thumb文件
        $post_thumb = '';
        $file_thumb = $request->file('thumb');
        if(!empty($file_thumb)){
            $local_thumb = $file_thumb->getInfo('tmp_name');
            $thumb_filename = $file_thumb->getInfo('name');
            $thumb_file_info = new Upload();
            $post_thumb = $thumb_file_info->qcloud_file($local_thumb,$thumb_filename);
        }

        $post_site_id = $request->param('site_id');
        $post_category_id = $request->param('category_id');
        $post_sort = $request->param('sort');
        $post_title = $request->param('title');
        $post_url = $request->param('url');
        $post_desc = $request->param('desc');
        $post_background = $request->param('background');
        $post_status = $request->param('status');

        if(empty($post_title)){
            $this->error('广告标题不能为空');
        }

        $data = new AdModel;
        $data['site_id'] = $post_site_id;
        $data['title'] = $post_title;
        $data['sort'] = $post_sort;
        $data['category_id'] = $post_category_id;
        $data['url'] = $post_url;
        $data['desc'] = $post_desc;
        $data['background'] = $post_background;
        $data['thumb'] = $post_thumb;
        $data['status'] = $post_status;
        if ($data->save()) {
            $this->success('保存成功','/admin/ad/index');
        } else {
            $this->error('操作失败');
        }
    }

    /**
     * @param $id
     * @return mixed
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function edit($id)
    {
        $site_id = $this->site_id;
        // 获取当前分类id
        $categorg_id_info = AdModel::get($id);
        $categorg_id = $categorg_id_info['category_id'];

        // 获取广告信息
        $data_list = AdModel::get($id);
        $this->assign('data',$data_list);

        // 获取网站分类列表
        $category_data = new AdCategory();
        $category = $category_data->where(['site_id'=>$site_id])->select();
        $this->assign('category',$category);

        $my_categorg_data = AdCategory::get($categorg_id);
        $my_categorg_title = $my_categorg_data['title'];
        $this->assign('my_category_id',$categorg_id);
        $this->assign('my_categorg_title',$my_categorg_title);

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
        $post_id = $request->post('id');
        $post_site_id = $request->post('site_id');
        $post_category_id = $request->post('category_id');
        $post_sort = $request->post('sort');
        $post_title = $request->post('title');
        $post_desc = $request->post('desc');
        $post_url = $request->post('url');
        $post_background = $request->post('background');
        $post_status= $request->post('status');
        if(empty($post_title)){
            $this->error('广告标题不能为空');
        }

        $user = AdModel::get($post_id);

        if(!empty($post_thumb)){
            $user['thumb'] = $post_thumb;
        }
        $user['site_id'] = $post_site_id;
        $user['category_id'] = $post_category_id;
        $user['sort'] = $post_sort;
        if(!empty($post_title)){
            $user['title'] = $post_title;
        }
        $user['desc'] = $post_desc;
        $user['url'] = $post_url;
        $user['background'] = $post_background;
        $user['status'] = $post_status;

        if ($user->save()) {
            $this->success('更新成功', '/admin/ad/index');
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
        $data = AdModel::get($id);
        if ($data) {
            $data->delete();
            $this->success('删除广告成功', '/admin/ad/index');
        } else {
            $this->error('您要删除的广告不存在');
        }
    }
}