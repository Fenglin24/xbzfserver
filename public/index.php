<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// [ 应用入口文件 ]
define('BASE_URL', '/');
// 定义应用目录
define('APP_PATH', __DIR__ . '/../application/');
header("Content-type: text/html; charset=utf-8");
date_default_timezone_set('Asia/Shanghai');
// 加载框架引导文件
require __DIR__ . '/../thinkphp/start.php';
