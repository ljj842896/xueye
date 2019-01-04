<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------



// [ 应用入口文件 ]
namespace think;

ini_set('default_charset','UTF-8');


define('APP_PATH', __DIR__ . '/../application/');
define('APP_NAME','application');
define('CACHE_DATA_TIME',60);
define('CACHE_STATUS_TIME',3);
define('UPLOADS',"/data/www/od/");
define('XYAPPID','wx602a56ee4a4ac69b');
define('APPSECRET','105e74a9d1436e5fc5f05bf07c11d0ac');

define('ZTAPPID','wx1189697f6d6a1212');
define('ZTAPPSECRET','114a17fbd2249a2d096f2b31c5316813');

// 加载基础文件
require __DIR__ . '/../thinkphp/base.php';


// 支持事先使用静态方法设置Request对象和Config对象

// 执行应用并响应
Container::get('app')->run()->send();
