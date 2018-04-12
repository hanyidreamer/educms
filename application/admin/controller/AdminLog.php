<?php
/**
 * Created by PhpStorm.
 * User: tanzhenxing
 * Date: 2017/6/26
 * Time: 15:17
 */
namespace app\admin\controller;

use app\base\model\AdminLog as AdminLogModel;
use app\base\model\Admin;

class AdminLog extends AdminBase
{
    /**
     * 日志列表
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index()
    {
        $site_id = $this->site_id;

        // 找出广告列表数据
        $post_title = $this->request->param('title');
        $data = new AdminLogModel;
        if(!empty($post_title)){
            $data_list = $data->where(['status' => 1])
                ->where('username' ,'like','%'.$post_title.'%')
                ->select();
        }else{
            $data_list = $data->where(['site_id'=>$site_id,'status'=>1])->select();
        }
        $data_count = count($data_list);
        $this->assign('data_count',$data_count);

        foreach ($data_list as $data){
            $admin_id = $data['admin_id'];
            $admin_data = Admin::get($admin_id);
            $admin_username = $admin_data['username'];
            $data['username'] = $admin_username;
        }

        $this->assign('data_list',$data_list);

        return $this->fetch($this->template_path);
    }
}