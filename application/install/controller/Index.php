<?php
/**
 * Created by PhpStorm.
 * User: tanzhenxing
 * Date: 2018/4/12
 * Time: 6:39
 */
namespace app\install\controller;

use think\Controller;

class Index extends Controller
{
    public function index()
    {
        return $this->fetch();
    }
}