<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
Route::get('single/:id', 'article/SinglePage/index');
Route::get('single_page/:id', 'article/SinglePage/view');
Route::get('article_category/:id', 'article/Category/view');
Route::get('article/:id', 'article/Article/view');
Route::get('search/:name', 'article/Search/index');
Route::get('wx_img/:name', 'base/ImgUrl/wei_xin'); // 微信图片
Route::get('qq_img/:name', 'base/ImgUrl/qq_news'); // QQ新闻图片
Route::get('course_category/all', 'course/Course/index');
Route::get('course_category/:id', 'course/Course/category');
Route::get('course/:id', 'course/Course/view');
Route::get('teacher/:id', 'teacher/Teacher/view');
return [

];
