<?php
namespace app\index\controller;

use think\Controller;
use think\Db;
use think\Session;

class Index extends Controller
{
    protected function checkLogin()
    {
        if(session('username') != null)
        {
            return true;
        }else
        {
            return false;
        }
    }

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
        if(!($this->checkLogin()))
        {
            return $this->error("访问隐私数据需要登录哦！","/",'',2);
        }
    	$this->assign("username",session('username'));
    	return $this->fetch();
    }

    public function yesterday()
    {   
        if(!($this->checkLogin()))
        {
            return $this->error("访问隐私数据需要登录哦！","/",'',2);
        }

        $years = Db::query("select year from yesterday group by year order by year desc");
        foreach ($years as $year) {
            $yearData = Db::name('yesterday')->where('year', $year['year'])->where('deleted',0)->order('month desc,day desc')->select();

            //以下为截取内容的函数，缩短内容。。后续可通过ajax加载全部内容。
            
            for ($i=0; $i < count($yearData); $i++) { 
                $brPos=strpos($yearData[$i]['content'],'<br>');
                $conLen=strlen($yearData[$i]['content']);
                if ($conLen<=80 && !$brPos) {
                    $yearData[$i]['more']=0;
                }else{
                    $yearData[$i]['more']=1;
                    if ($conLen>80 && !$brPos) {
                        $yearData[$i]['content']=substr($yearData[$i]['content'], 0, 79);
                    }elseif ($brPos<80) {
                        $yearData[$i]['content']=substr($yearData[$i]['content'], 0, $brPos);
                    }else{
                        $yearData[$i]['content']=substr($yearData[$i]['content'], 0, 79);
                    }                
                }
            }

            $data[$year['year']] = $yearData;
        }
        //echo $data;
        $this->assign("data",$data);
    	$this->assign("username",session('username'));
    	return $this->fetch();
    }

    public function tomorrow()
    {
        if(!($this->checkLogin()))
        {
            return $this->error("访问隐私数据需要登录哦！","/",'',2);
        }

    	$this->assign("username",session('username'));
    	return $this->fetch();
    }

    public function addYesterday()
    {
        if(!($this->checkLogin()))
        {
            return $this->error("访问隐私数据需要登录哦！","/",'',2);
        }

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
        $insertData['addTime'] = date("Y-m-d H:i:s");

        $result = Db::name('yesterday')->insert($insertData);
        if ($result == 1)
        {
            $this->redirect('/yesterday');
        }else
        {
            return $this->error("系统故障，数据添加失败，请稍后再试或联系你的宝贝儿老公~",null,'',3);
        }
    }

    public function editYesterday()
    {
        if(!($this->checkLogin()))
        {
            return $this->error("访问隐私数据需要登录哦！","/",'',2);
        }

        $id = input('get.id/d');
        $data = Db::name('yesterday')->where('id', $id)->find();

        $data['content'] = str_replace('<br>', PHP_EOL,  $data['content']);

        $this->assign("data",$data);
        $this->assign("username",session('username'));
        return $this->fetch();
    }

    public function updateYesterday()
    {
        if(!($this->checkLogin()))
        {
            return $this->error("更新隐私数据也需要登录哦！","/",'',2);
        }

        $id = input('post.id');
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

        $updateData['year'] = $year;
        $updateData['month'] = $month;
        $updateData['day'] = $day;
        $updateData['title'] = $title;
        $updateData['highlight'] = $highlight;
        $updateData['content'] = $content;
        $updateData['togetherDay'] = $togetherDay;
        $updateData['updateTime'] = date("Y-m-d H:i:s");

        Db::table('yesterday')
        ->where('id', $id)
        ->update($updateData);

        return $this->success('更新成功','/yesterday','',1);

    }

    public function deleteYesterday()
    {
        if(!($this->checkLogin()))
        {
            return $this->error("删除隐私数据也需要登录哦！","/",'',2);
        }

        $id = input('post.id/d');

        Db::table('yesterday')
        ->where('id', $id)
        ->setField('deleted', 1);

        return "success";
    }

    public function getMoreContentYesterday()
    {
        $id = input('post.id/d');
        $data = Db::name('yesterday')->where('id', $id)->find();
        return "{\"content\":\"".$data['content']."\"}";
    }
}