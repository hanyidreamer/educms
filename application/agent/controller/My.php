<?php
/**
 * Created by PhpStorm.
 * User: tanzhenxing
 * Date: 2018/5/9
 * Time: 16:26
 */
namespace app\agent\controller;

use app\common\model\Agent;

class My extends AgentBase
{
    public function index()
    {
        // 顶部菜单
        $right_menu = array('status'=>false,'menu_title'=>'','menu_url'=>'');
        $this->assign('right_menu',$right_menu);
        return $this->fetch($this->template_path);
    }

    /**
     * @throws \think\exception\DbException
     */
    public function edit()
    {
        // 顶部菜单
        $right_menu = array('status'=>false,'menu_title'=>'','menu_url'=>'');
        $this->assign('right_menu',$right_menu);

        $agent_username = session('agent_username');
        $agent = Agent::get(['username'=>$agent_username]);
        $this->assign('agent',$agent);

       return $this->fetch($this->template_path);
    }

    /**
     * @throws \think\exception\DbException
     */
    public function update()
    {
        $post_data = $this->request->param();

        $data_array = array('nickname','tel','weixinhao');

        $data = Agent::get($post_data['id']);

        $data_save = $data->allowField($data_array)->save($post_data);
        if($data_save){
            $this->success('更新成功','/agent/my/');
        }else{
            $this->error('更新失败');
        }
    }

    /**
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function set_password()
    {
        // 顶部菜单
        $right_menu = array('status'=>false,'menu_title'=>'','menu_url'=>'');
        $this->assign('right_menu',$right_menu);

        $agent_username = session('agent_username');
        $agent = Agent::get(['username'=>$agent_username]);
        $this->assign('agent',$agent);

        return $this->fetch($this->template_path);
    }

    /**
     * @throws \think\exception\DbException
     */
    public function update_password()
    {
        $post_data = $this->request->param();
        if(empty($post_data['password'])){
            $this->error('您没有输入新密码');
        }else{
            $post_data['password'] = md5($post_data['password']);
        }
        $data_array = array('password');

        $data = Agent::get($post_data['id']);

        $data_save = $data->allowField($data_array)->save($post_data);
        if($data_save){
            $this->success('密码修改成功','/agent/my/');
        }else{
            $this->error('密码修改失败');
        }
    }

    public function set_tel()
    {

    }

    public function update_tel()
    {

    }

    public function set_wechat()
    {

    }

    public function update_wechat()
    {

    }
}