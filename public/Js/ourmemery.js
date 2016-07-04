function showtime(){
	var date1=new Date(2014,10,7,8,6,0);
	var date2=new Date();    //结束时间

	var date3=date2.getTime()-date1.getTime();  //时间差的毫秒数

	var days=Math.floor(date3/(24*3600*1000));
	var leave1=date3%(24*3600*1000);   //计算天数后剩余的毫秒数
	var hours=Math.floor(leave1/(3600*1000));
	var leave2=leave1%(3600*1000); //计算小时数后剩余的毫秒数
	var minutes=Math.floor(leave2/(60*1000));
	var leave3=leave2%(60*1000);     //计算分钟数后剩余的毫秒数
	var seconds=Math.round(leave3/1000);
	if (seconds==60) {seconds=0};
	document.getElementById("ourtimeDay").innerHTML=days;
	document.getElementById("ourtimeHour").innerHTML=hours;
	document.getElementById("ourtimeMinute").innerHTML=minutes;
	document.getElementById("ourtimeSecond").innerHTML=seconds;
	t=setTimeout("showtime()",1000);
}

function setHeartToWord () {
	document.getElementById("myHeart").style="opacity:1;";
	document.getElementById("myHeart").innerHTML="WangLu, I ♥ you.";
	//document.getElementById("myHeart").style="margin-left: 24%;";
}

function setWordToHeart () {
	document.getElementById("myHeart").innerHTML="♥";
	//document.getElementById("myHeart").style="margin-left: 44%;";
}

function showCountDownTime(){
	var date1=new Date();
	var date2=new Date(2020,10,7,0,0,0);    //结束时间

	var date3=date2.getTime()-date1.getTime();  //时间差的毫秒数

	var days=Math.floor(date3/(24*3600*1000));
	var leave1=date3%(24*3600*1000);   //计算天数后剩余的毫秒数
	var hours=Math.floor(leave1/(3600*1000));
	var leave2=leave1%(3600*1000); //计算小时数后剩余的毫秒数
	var minutes=Math.floor(leave2/(60*1000));
	var leave3=leave2%(60*1000);     //计算分钟数后剩余的毫秒数
	var seconds=Math.round(leave3/1000);
	if (seconds==60) {seconds=0};

	document.getElementById("cdDay1").innerHTML=days%10;
	days = parseInt(days/10);
	document.getElementById("cdDay2").innerHTML=days%10;
	days = parseInt(days/10);
	document.getElementById("cdDay3").innerHTML=days;

	document.getElementById("cdHour1").innerHTML=hours%10;
	document.getElementById("cdHour2").innerHTML=parseInt(hours/10)%10;	

	document.getElementById("cdMin1").innerHTML=minutes%10;
	document.getElementById("cdMin2").innerHTML=parseInt(minutes/10)%10;

	document.getElementById("cdSec1").innerHTML=seconds%10;
	document.getElementById("cdSec2").innerHTML=parseInt(seconds/10)%10;

	t=setTimeout("showCountDownTime()",1000);
}