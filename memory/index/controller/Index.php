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

    public function insertIntoYesterday()
    {
        $date = input('post.date');
        $dateArr = explode('-',$date);
        $year = intval($dateArr[0]);
        $month = intval($dateArr[1]);
        $day = intval($dateArr[2]);
        $title = input('post.title');
        $highlightStr = input('post.highlight');
        $highlight = $highlightStr=="highlightYes"?1:0;
        $content = input('post.content');
        $content = str_replace(PHP_EOL,'<br>',$content);
        $togetherDay = input('post.togetherDay');
        if ($togetherDay == "yes") 
        {
            $startdate=strtotime("2014-11-7");
            $enddate=strtotime($date);
            $togetherDay=round(($enddate-$startdate)/86400);
        }
        
        $insertData['year'] = $year;
        $insertData['month'] = $month;
        $insertData['day'] = $day;
        $insertData['title'] = $title;
        $insertData['highlight'] = $highlight;
        $insertData['content'] = $content;
        $insertData['togetherDay'] = $togetherDay;

        $result = Db::name('yesterday')->insert($insertData);
        if ($result == 1)
        {
            $this->redirect('/yesterday');
        }else
        {
            return $this->error("系统故障，数据添加失败，请稍后再试~",null,'',3);
        }
    }
}