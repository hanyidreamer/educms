<?php
/**
 * Created by PhpStorm.
 * User: tanzhenxing
 * Date: 2017/6/26
 * Time: 15:17
 */
namespace app\admin\controller;

use think\Request;
use app\common\model\AgentApply as AgentApplyModel;
use app\common\model\AgentCategory;
use app\common\model\Admin;

class AgentApply extends AdminBase
{
    /**
     * 代理申请
     * @param Request $request
     * @return mixed
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index(Request $request)
    {
        // 找出广告列表数据
        $post_title = $request->param('title');
        $data = new AgentApplyModel;
        if(!empty($post_title)){
            $data_list = $data->where(['status' => 1, 'title' => ['like','%'.$post_title.'%']])->select();
        }else{
            $data_list = $data->where(['status'=>1])->select();
        }
        $data_count = count($data_list);
        $this->assign('data_count',$data_count);

        foreach ($data_list as $data){
            $category_id = $data['category_id'];
            $category_data = AgentCategory::get($category_id);
            $category_title = $category_data['title'];
            $data['category_title'] = $category_title;

            $admin_id = $data['admin_id'];
            $admin_data = Admin::get($admin_id);
            $admin_username = $admin_data['username'];
            $data['admin_username'] = $admin_username;
        }

        $this->assign('data_list',$data_list);

        return $this->fetch($this->template_path);
    }

    /**
     * 编辑代理商申请信息
     * @param $id
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function edit($id)
    {
        // 获取当前分类id
        $category_id_info = AgentApplyModel::get($id);
        $category_id = $category_id_info['category_id'];

        // 获取信息
        $data_list = AgentApplyModel::get($id);
        $this->assign('data',$data_list);

        // 获取网站分类列表
        $category_data = new AgentCategory();
        $category = $category_data->where(['status'=>1])->select();
        $this->assign('category',$category);

        $my_category_data = AgentCategory::get($category_id);
        $my_category_title = $my_category_data['title'];
        $this->assign('my_category_id',$category_id);
        $this->assign('my_category_title',$my_category_title);

        return $this->fetch($this->template_path);
    }

    /**
     * 更新代理商申请资料
     * @param Request $request
     * @throws \think\exception\DbException
     */
    public function update(Request $request)
    {
        $post_id = $request->post('id');
        $post_category_id = $request->post('category_id');
        $post_contact_person = $request->post('contact_person');
        $post_tel = $request->post('tel');
        $post_weixinhao = $request->post('weixinhao');
        $post_qq = $request->post('qq');
        $post_city = $request->post('city');
        $post_company_name = $request->post('company_name');
        $post_comment = $request->post('comment');
        $post_admin_opinion = $request->post('admin_opinion');
        $post_status= $request->post('status');

        $user = AgentApplyModel::get($post_id);
        $user['contact_person'] = $post_contact_person;
        $user['category_id'] = $post_category_id;
        $user['tel'] = $post_tel;
        $user['weixinhao'] = $post_weixinhao;
        $user['qq'] = $post_qq;
        $user['city'] = $post_city;
        $user['company_name'] = $post_company_name;
        $user['comment'] = $post_comment;
        $user['admin_opinion'] = $post_admin_opinion;
        $user['status'] = $post_status;

        if ($user->save()) {
            $this->success('更新成功', '/admin/agent_apply/index');
        } else {
            $this->error('操作失败');
        }

    }

    /**
     * 删除代理商申请信息
     * @param $id
     * @throws \think\exception\DbException
     */
    public function delete($id)
    {
        $data = AgentApplyModel::get($id);
        if ($data) {
            $data->delete();
            $this->success('删除成功', '/admin/agent_apply/index');
        } else {
            $this->error('删除失败');
        }
    }
}