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

        $typeCoRe = Db::query("select t.type,count(*) as count from todolist l,todoType t 
            where l.type=t.id and l.done=0 and l.deleted=0 group by l.type");
        $typeCount=array("food"=>0,"hotel"=>0,"shop"=>0,"travel"=>0,"life"=>0,"others"=>0);
        $countAll = 0;
        foreach ($typeCoRe as $key) {
            $typeCount[$key["type"]]=$key["count"];
            $countAll+=$key["count"];
        }
        $typeCount["all"]=$countAll;

        $typeCoReDone = Db::query("select t.type,count(*) as count from todolist l,todoType t 
            where l.type=t.id and l.done=1 and l.deleted=0 group by l.type");
        $typeCountDone=array("food"=>0,"hotel"=>0,"shop"=>0,"travel"=>0,"life"=>0,"others"=>0);
        $countAllDone = 0;
        foreach ($typeCoReDone as $key) {
            $typeCountDone[$key["type"]]=$key["count"];
            $countAllDone+=$key["count"];
        }
        $typeCountDone["all"]=$countAllDone;

        $todoItems = Db::query("select l.id,l.title,l.content,l.deadline,t.type,l.type as typeId from todolist l,todoType t
            where l.done=0 and l.deleted=0 and l.type=t.id order by l.deadline");
        $todoItemsDone = Db::query("select l.id,l.title,l.content,l.deadline,t.type,l.type as typeId from todolist l,todoType t
            where l.done=1 and l.deleted=0 and l.type=t.id order by l.deadline");
        $this->assign("typeCount",$typeCount);
        $this->assign("todoItems",$todoItems);
        $this->assign("typeCountDone",$typeCountDone);
        $this->assign("todoItemsDone",$todoItemsDone);
        $this->assign("username",session('username'));
        return $this->fetch();
    }

    public function addTodoItem()
    {
        if(!($this->checkLogin()))
        {
            return "Error. Not login.";
        }
        $dataInfo = input('post.data');
        //$dataInfo = str_replace("\n","<br>",$dataInfo);//这边一定要先处理换行符，否则无法解析json。另；textarea中的换行是\n，使用PHP_EOL会出错
        $data = json_decode($dataInfo,true);
        $insertData["title"] = $data['title'];
        $insertData["deadline"] = $data['deadline'];
        $insertData["type"] = $data['type'];
        //$data['content'] = str_replace(PHP_EOL,"<br>",$data['content']);//already replace
        $insertData["content"] = $data['content'];
        $resultID = Db::name('todolist')->insertGetId($insertData);
        if ($resultID) {
            return $resultID;
        }else{
            return "ERROR";
        }
    }

    public function updateTodoItem()
    {
        if(!($this->checkLogin()))
        {
            return "Error. Not login.";
        }
        $dataInfo = input('post.data');
        //$dataInfo = str_replace("\n","<br>",$dataInfo);//这边一定要先处理换行符，否则无法解析json。另；textarea中的换行是\n，使用PHP_EOL会出错
        $data = json_decode($dataInfo,true);
        $id = $data['id'];
        $updateData["title"] = $data['title'];
        $updateData["deadline"] = $data['deadline'];
        $updateData["type"] = $data['type'];
        //$data['content'] = str_replace(PHP_EOL,"<br>",$data['content']);
        $updateData["content"] = $data['content'];
        if ($id>=1) {
            $result = Db::name('todolist')
            ->where('id', $id)
            ->update($updateData);
            if ($result) {
                return "Success";
            }else{
                return "ERROR";
            }
        }
        return "ERROR";
    }

    public function todoListMarkAsCompleted()
    {
        if(!($this->checkLogin()))
        {
            return "Error. Not login.";
        }
        $id = input('post.id/d');
        $result = Db::name('todolist')->where('id',$id)->setField('done', 1);
        if ($result) {
            return "Success";
        }else{
            return "Error";
        }
    }

    public function todoListMarkAsUnCompleted()
    {
        if(!($this->checkLogin()))
        {
            return "Error. Not login.";
        }
        $id = input('post.id/d');
        $result = Db::name('todolist')->where('id',$id)->setField('done', 0);
        if ($result) {
            return "Success";
        }else{
            return "Error";
        }
    }

    public function deleteTodoItem()
    {
        if(!($this->checkLogin()))
        {
            return "Error. Not login.";
        }
        $id = input('post.id/d');
        $result = Db::name('todolist')->where('id',$id)->setField('deleted', 1);
        if ($result) {
            return "Success";
        }else{
            return "Error";
        }
    }

    public function futureMarriage()
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