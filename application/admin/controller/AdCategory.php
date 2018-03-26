<?php
/**
 * Created by PhpStorm.
 * User: tanzhenxing
 * Date: 2017/6/26
 * Time: 15:16
 */
namespace app\admin\controller;

use think\Request;
use app\base\model\AdCategory as AdCategoryModel;
use app\base\controller\TemplatePath;
use app\base\controller\Base;
use app\base\controller\SiteId;

class AdCategory extends Base
{
    /**
     * 广告分类
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index()
    {
        $title = '广告分类列表';
        $this->assign('title',$title);

        // 当前方法不同终端的模板路径
        $controller_name = $this->request->controller();
        $action_name = $this->request->action();
        $template_path_info = new TemplatePath();
        $template_path = $template_path_info->admin_path($controller_name,$action_name);
        $template_public = $template_path_info->admin_public_path();
        $template_public_header = $template_public.'/header';
        $template_public_footer = $template_public.'/footer';
        $this->assign('public_header',$template_public_header);
        $this->assign('public_footer',$template_public_footer);

        // 获取网站id
        $get_domain = $this->request->server('HTTP_HOST');
        $this->assign('domain',$get_domain);
        $site_id_data = new SiteId();
        $site_id = $site_id_data->info($get_domain);

        // 找出广告列表数据
        $post_title = $this->request->param('title');
        $data = new AdCategoryModel;
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

        return $this->fetch($template_path);
    }

    /**
     * 新增分类
     * @return mixed
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */
    public function create()
    {
        // 新增
        $title = '新增分类';
        $this->assign('title',$title);

        // 当前方法不同终端的模板路径
        $controller_name = $this->request->controller();
        $action_name = $this->request->action();
        $template_path_info = new TemplatePath();
        $template_path = $template_path_info->admin_path($controller_name,$action_name);
        $template_public = $template_path_info->admin_public_path();
        $template_public_header = $template_public.'/header';
        $template_public_footer = $template_public.'/footer';
        $this->assign('public_header',$template_public_header);
        $this->assign('public_footer',$template_public_footer);

        // 获取网站id
        $get_domain = $this->request->server('HTTP_HOST');
        $this->assign('domain',$get_domain);
        $site_id_data = new SiteId();
        $site_id = $site_id_data->info($get_domain);
        $this->assign('site_id',$site_id);

        return $this->fetch($template_path);
    }

    /**
     * 保存分类
     * @throws \think\exception\DbException
     */
    public function save()
    {
        $post_site_id = $this->request->param('site_id');
        $post_title = $this->request->param('title');
        $post_unique_code = $this->request->param('unique_code');
        $unique_code_info = AdCategoryModel::get(['unique_code'=>$post_unique_code]);
        if(!empty($unique_code_info)){
            $this->error('分类标识码已经存在，创建广告分类失败');
        }
        $post_status = $this->request->param('status');
        if($post_title==''){
            $this->error('分类名称不能为空');
        }
        $data = new AdCategoryModel;
        $data['site_id'] = $post_site_id;
        $data['title'] = $post_title;
        $data['unique_code'] = $post_unique_code;
        $data['status'] = $post_status;
        if ($data->save()) {
            $this->success('保存成功','/admin/ad_category/index');
        } else {
            $this->error('操作失败');
        }

    }

    /**
     *  编辑分类
     * @param $id
     * @return mixed
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */
    public function edit($id)
    {
        $title = '编辑广告分类';
        $this->assign('title',$title);

        // 当前方法不同终端的模板路径
        $controller_name = $this->request->controller();
        $action_name = $this->request->action();
        $template_path_info = new TemplatePath();
        $template_path = $template_path_info->admin_path($controller_name,$action_name);
        $template_public = $template_path_info->admin_public_path();
        $template_public_header = $template_public.'/header';
        $template_public_footer = $template_public.'/footer';
        $this->assign('public_header',$template_public_header);
        $this->assign('public_footer',$template_public_footer);

        // 获取网站id
        $get_domain = $this->request->server('HTTP_HOST');
        $this->assign('domain',$get_domain);
        $site_id_data = new SiteId();
        $site_id = $site_id_data->info($get_domain);
        $this->assign('site_id',$site_id);

        // 获取网站信息
        $data_list = AdCategoryModel::get($id);
        $this->assign('data',$data_list);

        return $this->fetch($template_path);
    }

    /**
     * 更新分类
     * @param Request $request
     * @throws \think\exception\DbException
     */
    public function update(Request $request)
    {
        $post_id = $request->post('id');
        $post_site_id = $request->post('site_id');
        $post_title = $request->post('title');
        $post_status= $request->post('status');
        if(empty($post_title)){
            $this->error('分类名称不能为空');
        }

        $user = AdCategoryModel::get($post_id);
        $user['site_id'] = $post_site_id;
        $user['title'] = $post_title;
        $user['status'] = $post_status;
        if ($user->save()) {
            $this->success('保存成功', '/admin/ad_category/index');
        } else {
            $this->error('操作失败');
        }
    }

    /**
     * 删除广告分类
     * @param $id
     * @throws \think\exception\DbException
     */
    public function delete($id)
    {
        $data = AdCategoryModel::get($id);
        if ($data) {
            $data->delete();
            $this->success('删除广告分类成功', '/admin/ad_category/index');
        } else {
            $this->error('您要删除的广告分类不存在');
        }
    }
}