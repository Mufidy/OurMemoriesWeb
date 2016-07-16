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


\think\Route::rule('yesterday','index/index/yesterday');
\think\Route::rule('addYesterday','index/index/addYesterday');
\think\Route::rule('editYesterday','index/index/editYesterday');
\think\Route::rule('deleteYesterday','index/index/deleteYesterday');
\think\Route::rule('getMoreContentYesterday','index/index/getMoreContentYesterday');
\think\Route::rule('memorial','index/index/memorial');
\think\Route::rule('addMemorial','index/index/addMemorial');
\think\Route::rule('editMemorial','index/index/editMemorial');
\think\Route::rule('deleteMemorial','index/index/deleteMemorial');
\think\Route::rule('tomorrow','index/index/tomorrow');
\think\Route::rule('todoList','index/index/todoList');
\think\Route::rule('addTodoItem','index/index/addTodoItem');
\think\Route::rule('updateTodoItem','index/index/updateTodoItem');
\think\Route::rule('todoListMarkAsCompleted','index/index/todoListMarkAsCompleted');
\think\Route::rule('todoListMarkAsUnCompleted','index/index/todoListMarkAsUnCompleted');
\think\Route::rule('deleteTodoItem','index/index/deleteTodoItem');
\think\Route::rule('showLogin','index/index/showLogin');
\think\Route::rule('login','index/index/login');
\think\Route::rule('adminMW','index/index/adminMW');
\think\Route::rule('adminLogin','index/index/adminLogin');
\think\Route::rule('adminLogout','index/index/adminLogout');