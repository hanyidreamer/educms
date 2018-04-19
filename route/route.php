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

Route::get('xht/:id', 'admin/login/index');

Route::get('article_category/:id', 'article/Category/view');
Route::get('article/:id', 'article/Article/view');
Route::get('wx_img/:name', 'base/ImgUrl/wei_xin'); // 微信图片
Route::get('qq_img/:name', 'base/ImgUrl/qq_news'); // QQ新闻图片

return [

];
