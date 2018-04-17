<?php

namespace app\admin\controller;


class Index extends AdminBase
{
    /**
     * 后台默认首页
     * @return mixed
     */
    public function index()
    {
        return $this->fetch($this->template_path);
    }

}