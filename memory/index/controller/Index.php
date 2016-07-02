<?php
namespace app\index\controller;

use think\Controller;
use think\Db;
use think\Session;

class Index extends Controller
{
    public function index()
    {
    	$this->assign("username",session('username'));
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
	   		return $this->error("用户名或密码错误哦",null,'',2);
	   	}

    }

    public function logout()
    {
    	session('uid',null);
	    session('username',null);
	    $this->redirect('/');
    }

    public function memorial()
    {
    	$this->assign("username",session('username'));
    	return $this->fetch();
    }

    public function yesterday()
    {   
        $years = Db::query("select year from yesterday group by year order by year desc");
        foreach ($years as $year) {
            $yearData = Db::name('yesterday')->where('year', $year['year'])->order('month desc,day desc')->select();
            $data[$year['year']] = $yearData;
            
        }
        //echo $data;
        $this->assign("data",$data);
    	$this->assign("username",session('username'));
    	return $this->fetch();
    }

    public function tomorrow()
    {
    	$this->assign("username",session('username'));
    	return $this->fetch();
    }

    public function addYesterday()
    {
        $this->assign("username",session('username'));
        return $this->fetch();
    }
}