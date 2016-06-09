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
	document.getElementById("ourtime").innerHTML="<h1 style='font-size:50px'>我们已经相伴走过</h1><span style='font-size:50px;font-weight:bold;'>"+days
		+"</span>&nbsp;&nbsp;天<br/><span style='font-size:50px;font-weight:bold;'>"+hours+"</span>&nbsp;&nbsp;小时<br/><span style='font-size:50px;font-weight:bold;'>"
		+minutes+"</span>&nbsp;&nbsp;分钟<br/><span style='font-size:50px;font-weight:bold;'>"+seconds+"</span>&nbsp;&nbsp;秒啦!";
	t=setTimeout("showtime()",1000);
}

function setHeartToWord () {
	document.getElementById("myHeart").style="opacity:1;";

	document.getElementById("myHeart").innerHTML="WangLu, I ♥ you.";
}

function setWordToHeart () {
	document.getElementById("myHeart").innerHTML="♥";
	document.getElementById("myHeart").style="margin-left: 44%;";
}	