<?php
namespace app\index\controller;

use think\Controller;
use think\Db;
use think\Session;

class Index extends Controller
{
    public function index()
    {
    	$this->assign("data",session('username'));
        return $this->fetch();
    }

    public function login()
    {
	    $username = input('post.username');
	    $password = input('post.password');
	   	
	   	$list = Db::name('user')->where('username', $username)->find();
	   	
	   	$message = "登录成功";

	   	if ($list && $list["password"]==$password) 
	   	{
	   		session('uid',$list["id"]);
       		session('username',$username);
	   		return $this->success($message,null,'',1);
	   	}else
	   	{
	   		return "false";
	   	}

    }

    public function logout()
    {
    	session('uid',null);
	    session('username',null);
	    $this->redirect('index');
    }

    public function memorial()
    {
    	$this->assign("data",session('username'));
    	return $this->fetch();
    }

    public function yesterday()
    {
    	$this->assign("data",session('username'));
    	return $this->fetch();
    }

    public function tomorrow()
    {
    	$this->assign("data",session('username'));
    	return $this->fetch();
    }
}