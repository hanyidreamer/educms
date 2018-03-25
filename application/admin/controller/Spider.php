<?php
/**
 * Created by PhpStorm.
 * User: tanzhenxing
 * Date: 2017/4/11
 * Time: 16:18
 */
namespace app\admin\controller;


class Spider extends Base
{
    public function index()
    {
        return $this->fetch();
    }
}