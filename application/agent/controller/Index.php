<?php
/**
 * Created by PhpStorm.
 * User: tzx
 * Date: 2016/10/14
 * Time: 17:07
 */
namespace app\agent\controller;


class Index extends AgentBase
{

    public function index()
    {
        // 顶部菜单
        $right_menu = array('status'=>false,'menu_title'=>'','menu_url'=>'');
        $this->assign('right_menu',$right_menu);

        return $this->fetch($this->template_path);
    }
}