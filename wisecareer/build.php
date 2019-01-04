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

return [
    // 生成应用公共文件
    '__file__' => ['common.php', 'template.php', 'database.php'],


    // lopo就是你要建立的项目模块的名称
    'test'     => [
        '__file__'   => ['common.php'],
        '__dir__'    => ['behavior', 'controller', 'model', 'view'],//创建lopo下的文件夹名称
        'controller' => ['Index', 'Test', 'UserType'],//控制器的名称
        'model'      => ['User.php', 'UserType'],
        'view'       => ['index/index'],/
    ],
    // 其他更多的模块定义
];