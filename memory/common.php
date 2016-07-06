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
//路由注册文件


 \think\Route::rule('tomorrow','index/index/tomorrow');
 \think\Route::rule('yesterday','index/index/yesterday');
 \think\Route::rule('memorial','index/index/memorial');
 \think\Route::rule('addYesterday','index/index/addYesterday');
 \think\Route::rule('editYesterday','index/index/editYesterday');
 \think\Route::rule('deleteYesterday','index/index/deleteYesterday');
 \think\Route::rule('getMoreContentYesterday','index/index/getMoreContentYesterday');
 \think\Route::rule('adminMW','index/index/adminMW');
 \think\Route::rule('adminLogin','index/index/adminLogin');
 \think\Route::rule('adminLogout','index/index/adminLogout');