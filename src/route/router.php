<?php

// +----------------------------------------------------------------------
// | Center Plugin for ThinkAdmin
// +----------------------------------------------------------------------
// | 版权所有 2014~2023 ThinkAdmin [ thinkadmin.top ]
// +----------------------------------------------------------------------
// | 官方网站: https://thinkadmin.top
// +----------------------------------------------------------------------
// | 免责声明 ( https://thinkadmin.top/disclaimer )
// | 开源协议 ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | gitee 代码仓库：https://gitee.com/zoujingli/think-plugs-center-simple
// | github 代码仓库：https://github.com/zoujingli/think-plugs-center-simple
// +----------------------------------------------------------------------

use plugin\center\simple\controller\Index;
use think\admin\Library;

Library::$sapp->route->any('layout-simple/<code>', Index::class . '@layout')->pattern(['code' => '[\w-]+']);
