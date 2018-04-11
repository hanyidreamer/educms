<?php
/**
 * Created by PhpStorm.
 * User: tanzhenxing
 * Date: 2017/6/23
 * Time: 5:58
 */
namespace app\teacher\controller;

use think\Controller;

class Teacher extends Controller
{
    public function index()
    {
        return $this->fetch();
    }
}