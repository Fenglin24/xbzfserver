<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// use think\Route;

// //新闻列表首页
// Route::any('news', '@admin/News/index');
// //新闻分类首页
// Route::any('news_category', '@admin/NewsCategory/index');
return [
	'__pattern__' => [
		'name' => '\w+',
	],
	'/admin$' => ['admin/index/index', ['method' => 'get']],
	'/login$' => ['admin/index/login', ['method' => 'get']],
	'/logout$' => ['admin/index/logout', ['method' => 'get']],
	'/view/:id' => ['index/index/view', ['method' => 'get'], ['id' => '\d+']],
];
