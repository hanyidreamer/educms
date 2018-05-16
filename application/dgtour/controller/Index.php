<?php
/**
 * Created by PhpStorm.
 * User: tanzhenxing
 * Date: 2018/5/15
 * Time: 9:23
 */
namespace app\dgtour\controller;

use app\base\controller\Base;

class Index extends Base
{
    public function index()
    {
        return $this->fetch($this->template_path);
    }
}