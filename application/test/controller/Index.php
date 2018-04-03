<?php
/**
 * Created by PhpStorm.
 * User: tanzhenxing
 * Date: 2018/4/3
 * Time: 10:23
 */
namespace app\test\controller;

use think\Controller;

class Index extends Controller
{
    public function machong()
    {
       return $this->fetch();
    }

}