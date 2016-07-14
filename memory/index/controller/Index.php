<?php
namespace app\index\controller;

use think\Controller;
use think\Db;
use think\Session;
use think\Input;

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

    public function showLogin()
    {
        return $this->fetch();
    }

    public function login()
    {
	    $username = input('post.username');
	    $password = input('post.password');
        $to = input('post.to');
	   	
	   	$list = Db::name('user')->where('username', $username)->find();
	   	
	   	$message = "登录成功";

	   	if ($list && $list["password"]==$password) 
	   	{
	   		session('uid',$list["id"]);
       		session('username',$username);
            $this->logAction("登入系统   成功");
	   		return $this->success($message,$to,'',1);
	   	}else
	   	{
            $this->logAction("登入系统   失败",null,$username);
	   		return $this->error("用户名或密码错误哦",null,'',2);
	   	}

    }

    public function logout()
    {
    	session('uid',null);
	    session('username',null);
	    $this->redirect('/');
    }

    public function yesterday()
    {   
        if(!($this->checkLogin()))
        {
            return $this->redirect('/showLogin?to=yesterday');
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

    public function addYesterday()
    {
        if(!($this->checkLogin()))
        {
            return $this->redirect('/showLogin?to=yesterday');
        }

        $this->assign("username",session('username'));
        return $this->fetch();
    }

    public function insertIntoYesterday()
    {
        if(!($this->checkLogin()))
        {
            return $this->redirect('/showLogin?to=yesterday');
        }

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
        $image = input('post.image');
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
        $insertData['image'] = $image;

        $resultID = Db::name('yesterday')->insertGetId($insertData);
        if ($resultID)
        {
            $this->logAction("添加幸福回忆",$resultID);
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
            return $this->redirect('/showLogin?to=yesterday');
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
            return $this->redirect('/showLogin?to=yesterday');
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
        $image = input('post.image');
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
        //$updateData['updateTime'] = date("Y-m-d H:i:s");//database update automatically
        $updateData['image'] = $image;

        Db::table('yesterday')
        ->where('id', $id)
        ->update($updateData);
        $this->logAction("更新幸福回忆",$id);
        return $this->success('更新成功','/yesterday','',1);

    }

    public function deleteYesterday()
    {
        if(!($this->checkLogin()))
        {
            return $this->redirect('/showLogin?to=yesterday');
        }

        $id = input('post.id/d');

        Db::table('yesterday')
        ->where('id', $id)
        ->setField('deleted', 1);
        $this->logAction("删除幸福回忆",$id);
        return "success";
    }

    public function getMoreContentYesterday()
    {
        $id = input('post.id/d');
        $data = Db::name('yesterday')->where('id', $id)->find();
        return "{\"content\":\"".$data['content']."\"}";
    }

    public function memorial()
    {
        if(!($this->checkLogin()))
        {
            return $this->redirect('/showLogin?to=memorial');
        }
        $memorialDayData = Db::name('memorial')->where('deleted',0)->order('time')->select();
        $this->assign("data",$memorialDayData);
        $this->assign("username",session('username'));
        return $this->fetch();
    }


    public function tomorrow()
    {
        if(!($this->checkLogin()))
        {
            return $this->redirect('/showLogin?to=tomorrow');
        }

        $this->assign("username",session('username'));
        return $this->fetch();
    }


    public function todoList()
    {
        if(!($this->checkLogin()))
        {
            return $this->redirect('/showLogin?to=tomorrow');
        }

        $this->assign("username",session('username'));
        return $this->fetch();
    }

    /*====For memorial page start====*/
    public function addMemorial()
    {
        if(!($this->checkLogin()))
        {
            return $this->redirect('/showLogin?to=memorial');
        }

        $this->assign("username",session('username'));
        return $this->fetch();
    }

    public function editMemorial()
    {
        if(!($this->checkLogin()))
        {
            return $this->redirect('/showLogin?to=memorial');
        }

        $id = input('get.id/d');
        $data = Db::name('memorial')->where('id', $id)->find();

        $data['content'] = str_replace('<br>', PHP_EOL,  $data['content']);

        $this->assign("data",$data);
        $this->assign("username",session('username'));
        return $this->fetch();
    }

    public function updateMemorial()
    {
        if(!($this->checkLogin()))
        {
            return $this->redirect('/showLogin?to=memorial');
        }

        $id = input('post.id');
        $date = input('post.date');
        $title = input('post.title');
        $content = input('post.content');
        $content = str_replace(PHP_EOL,'<br>',$content);
        $image = input('post.image');

        $updateData['time'] = $date;
        $updateData['title'] = $title;
        $updateData['content'] = $content;
        $updateData['image'] = $image;

        Db::table('memorial')
        ->where('id', $id)
        ->update($updateData);
        $this->logAction("更新纪念日",$id);
        return $this->success('更新成功','/memorial','',1);

    }

    public function insertIntoMemorial()
    {
        if(!($this->checkLogin()))
        {
            return $this->redirect('/showLogin?to=memorial');
        }

        $date = input('post.date');
        $title = input('post.title');
        $content = input('post.content');
        $content = str_replace(PHP_EOL,'<br>',$content);
        $image = input('post.image');
        
        $insertData['time'] = $date;
        $insertData['title'] = $title;
        $insertData['content'] = $content;
        $insertData['createTime'] = date("Y-m-d H:i:s");
        $insertData['image'] = $image;

        $resultID = Db::name('memorial')->insertGetId($insertData);
        if ($resultID)
        {
            $this->logAction("添加纪念日",$resultID);
            $this->redirect('/memorial');
        }else
        {
            return $this->error("系统故障，数据添加失败，请稍后再试或联系你的宝贝儿老公~",null,'',3);
        }
    }

    public function deleteMemorial()
    {
        if(!($this->checkLogin()))
        {
            return $this->redirect('/showLogin?to=memorial');
        }

        $id = input('post.id/d');

        Db::table('memorial')
        ->where('id', $id)
        ->setField('deleted', 1);
        $this->logAction("删除纪念日",$id);
        return "success";
    }
    /*====For memorial page end====*/


    /*====For admin page start====*/
    public function adminMW()
    {
        if (session('isAdmin') == null) 
        {
            $this->assign("isAdmin",session('isAdmin'));
            return $this->fetch();
        }else
        {
            $data = Db::name("history")->order('id desc')->select();
            $this->assign("isAdmin",session('isAdmin'));
            $this->assign("data",$data);
            return $this->fetch();
        }
    }

    public function adminLogin()
    {
        $adminInfo = input('post.adminInfo');
        $adminObj = json_decode($adminInfo,true);
        $adminName = $adminObj['adminName'];
        $adminPassword = $adminObj['adminPassword'];
        $r = Db::name('admin')->where('adminName',$adminName)->where('adminPassword',$adminPassword)->find();
        if ($r != null) {
            session('isAdmin','yes');
            return "Success";
        }else{
            return "Fail";
        }
    }

    public function adminLogout()
    {
        session('isAdmin',null);
        $this->redirect('/adminMW');
    }

    public function logAction($action,$cID=null,$userName=null)
    {
        $insertData["time"] = date("Y-m-d H:i:s");
        $insertData["action"] = $action;
        $insertData["contentID"] = $cID;
        $insertData["fromIP"] = $this->getIp();
        if ($userName == null) {
            $userName = session('username');
        }
        $insertData["userName"] = $userName;
        Db::name('history')->insert($insertData);
    }

    function getIP() {
        $ip = $_SERVER['REMOTE_ADDR'];
        if (isset($_SERVER['HTTP_CLIENT_IP']) && preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif(isset($_SERVER['HTTP_X_FORWARDED_FOR']) AND preg_match_all('#\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}#s', $_SERVER['HTTP_X_FORWARDED_FOR'], $matches)) {
            foreach ($matches[0] AS $xip) {
                if (!preg_match('#^(10|172\.16|192\.168)\.#', $xip)) {
                    $ip = $xip;
                    break;
                }
            }
        }
        return $ip;
    }
    /*====For admin page end====*/
}