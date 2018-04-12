<?php

namespace app\admin\controller;


class Index extends AdminBase
{
    /**
     * 后台默认首页
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index()
    {
        return $this->fetch($this->template_path);
    }

}