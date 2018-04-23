<?php
/**
 * Created by PhpStorm.
 * User: tanzhenxing
 * Date: 2017/6/23
 * Time: 11:27
 */
namespace app\member\controller;

use think\Controller;

class UserAgent extends Controller
{
    /**
     *
     */
    public function index()
    {
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        echo $user_agent;
    }
}