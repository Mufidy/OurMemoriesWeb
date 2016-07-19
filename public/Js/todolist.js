function showCountSpan(element) {
	$(element).children(".todoProjCount").show();
}

function hideCountSpan(element) {
	$(element).children(".todoProjCount").hide();
}

function toggleContent(element) {
	var displayState = false;
	if ($(element).parent().children(".oneTodoItemContent").css("display")==='block') {
		displayState = true;
	}
	$(".oneTodoItemContent").hide("normal");
	if (!displayState) {
		$(element).parent().children(".oneTodoItemContent").show("normal");
	}
}

function showAddTodoItemBody() {
	mScroll("addTodoItem");
	$(".addTodoItemIcon").hide();
	$(".addTodoItemBody").show("normal");
}

function hideAddTodoItemBody() {
	$(".addTodoItemBody").hide("normal");
	$(".addTodoItemIcon").show();
}

function addTodoItem(){
	var title = document.forms["f"]["title"].value;
	var deadline = document.forms["f"]["deadline"].value;
	var type = $("#type option:selected").val();
	var typeStr = new Array("","food","hotel","shop","travel","life","others");
	var content = document.forms["f"]["content"].value;
	if (title=="") {
		$("#submitHint").html("请输入标题哦~");
	}else if (deadline=="") {
		$("#submitHint").html("请输入截止日期哦~");
	}else if (type=="") {
		$("#submitHint").html("请选择事项类别哦~");
	}else{
		//var data = "{\"title\":\""+title+"\",\"deadline\":\""+deadline+"\",\"type\":\""+type+"\",\"content\":\""+content+"\"}";
		//alert(data)
		content = symbolToEndl(content);
		var thing = {title:title,deadline:deadline,type:type,content:content};
		var data = $.toJSON(thing);//该插件可以转义大部分特殊字符
		//alert(data)//!!!!!!!!!!!!!!!!!debug
		$.ajax({
	        type: "post",
	        url: "addTodoItem",
	        data: {data,data},
	        dataType: "text",
	        success: function(data){
	        	//alert(data)//!!!!!!!!!!!!!!!!!!!!!!!!!!debug
	            if (data=="\"ERROR\"") {
	            	alert("哎呀，出错了，添加失败了，请稍后再试或联系你的宝贝儿老公哦~");
	            }else{
	            	data = data.substring(1,(data.length-1))
	            	var itemId = parseInt(data);
	            	document.forms["f"]["title"].value="";
	            	document.forms["f"]["deadline"].value="";
	            	document.forms["f"]["content"].value="";
	            	$("#type").val("");
	            	hideAddTodoItemBody();
	            	content = endlToDisplay(content);
	            	var toAppendHtml = '<div class="oneTodoItem" id="oneTodoItem'+itemId+'">\
			            <i class="icon-circle-blank '+typeStr[type]+'Type" onclick="toggleContent(this);"></i>\
			            <span class="oneTodoItemTitle" onclick="toggleContent(this);">'+title+'</span>\
			            <div class="oneTodoItemDdl">截止日期：'+deadline+'</div>\
			            <div class="oneTodoItemContent"><span class="oneTodoItemContentText">'+content+'</span><br/>\
			              <button class="button button-pill button-tiny '+typeStr[type]+'Type" onclick="markAsCompleted(this,'+itemId+');">标记为完成</button>\
			              <button class="button button-pill button-tiny '+typeStr[type]+'Type" onclick="editTodoItem(this,'+itemId+','+type+');">修改</button>\
			              <button class="button button-pill button-tiny" style="color:red" onclick="deleteTodoItem(this,'+itemId+');"><i class="icon-trash"></i></button>\
			            </div>\
			          </div>'
	            	$("#todoLists").append(toAppendHtml);
	            	showAllItems();
	            }
	        }
      	});
	}	
}

function showAllItems() {
	$("#typeTitle").html("全部");
	$("#todoListsDone").hide();
	$("#todoLists").show("normal");
}

function showAllDoneItems() {
	$("#typeTitle").html("全部（已完成）");
	$("#todoLists").hide();
	$("#todoListsDone").show("normal");
}

function showTypeList(typeId) {
	var typeArr = new Array("","food","hotel","shop","travel","life","others");
	var typeStr = new Array("全部","美食","酒店","购物","旅行","生活","杂项");
	$("#typeTitle").html(typeStr[typeId]);
	if (typeId==0) {
		for (var i = typeArr.length - 1; i >= 0; i--) {
			$("div#todoLists ."+typeArr[i]+"Type").parent().show("normal");
			$("div#todoLists ."+typeArr[i]+"Type").parent().children(".oneTodoItemContent").hide();
		}
	}else{
		for (var i = typeArr.length - 1; i >= 0; i--) {
			if (typeId==i) {
				$("div#todoLists ."+typeArr[i]+"Type").parent().show("normal");
				$("div#todoLists ."+typeArr[i]+"Type").parent().children(".oneTodoItemContent").hide();
			}else{
				$("div#todoLists ."+typeArr[i]+"Type").parent().hide();
			}
		}
	}
	
}

function showDoneTypeList(typeId) {
	var typeArr = new Array("","food","hotel","shop","travel","life","others");
	var typeStr = new Array("全部（已完成）","美食（已完成）","酒店（已完成）","购物（已完成）","旅行（已完成）","生活（已完成）","杂项（已完成）");
	$("#typeTitle").html(typeStr[typeId]);
	if (typeId==0) {
		for (var i = typeArr.length - 1; i >= 0; i--) {
			$("div#todoListsDone ."+typeArr[i]+"Type").parent().show("normal");
			$("div#todoListsDone ."+typeArr[i]+"Type").parent().children(".oneTodoItemContent").hide();
		}
	}else{
		for (var i = typeArr.length - 1; i >= 0; i--) {
			if (typeId==i) {
				$("div#todoListsDone ."+typeArr[i]+"Type").parent().show("normal");
				$("div#todoListsDone ."+typeArr[i]+"Type").parent().children(".oneTodoItemContent").hide();
			}else{
				$("div#todoListsDone ."+typeArr[i]+"Type").parent().hide();
			}
		}
	}
	
}

function markAsCompleted(element,id) {
	//var ret = window.confirm("确定要删除这条待办事项嘛宝贝儿~\r\n删除了你的宝贝儿老公可就不完成了哦~~");
  	//当点击确定时 返回 true 
  	var ret = true;
  	if(ret){
      	$.ajax({
         	type: "post",
         	url: "todoListMarkAsCompleted",
         	data: {id,id},
         	dataType: "text",
         	success: function(data){
         		var hintSpan = "<span style='color:#00FF33'>&nbsp;&nbsp;成功标记为已完成</span>";
         		$(element).parent().parent().children(".oneTodoItemTitle").append(hintSpan);
         		setTimeout("javascript:window.location.reload()",1000);
          	}
      	});
  	}
}

function markAsUnCompleted(element,id) {
	//var ret = window.confirm("确定要删除这条待办事项嘛宝贝儿~\r\n删除了你的宝贝儿老公可就不完成了哦~~");
  	//当点击确定时 返回 true 
  	var ret = true;
  	if(ret){
      	$.ajax({
         	type: "post",
         	url: "todoListMarkAsUnCompleted",
         	data: {id,id},
         	dataType: "text",
         	success: function(data){
         		var hintSpan = "<span style='color:#00FF33'>&nbsp;&nbsp;成功标记为未完成</span>";
         		$(element).parent().parent().children(".oneTodoItemTitle").append(hintSpan);
         		setTimeout("javascript:window.location.reload()",1000);
          	}
      	});
  	}
}

function deleteTodoItem(element,id) {
	var ret = window.confirm("确定要删除这条待办事项嘛宝贝儿~\r\n删除了你的宝贝儿老公可就不完成了哦~~");
  	//当点击确定时 返回 true 
  	if(ret){
      	$.ajax({
         	type: "post",
         	url: "deleteTodoItem",
         	data: {id,id},
         	dataType: "text",
         	success: function(data){
         		var hintSpan = "<span style='color:red'>&nbsp;&nbsp;删除成功</span>";
         		$(element).parent().parent().children(".oneTodoItemTitle").append(hintSpan);
         		setTimeout(function(){$(element).parent().parent().remove();}, 1500);
          	}
      	});
  	}
}

function editTodoItem(element,id,typeId) {
	$(element).parent().parent().children("i").click();
	$(".addTodoItemBody").hide();
	$(".addTodoItemIcon").hide();
	$(".editTodoItem").show();
	document.forms["editF"]["id"].value=id;
	document.forms["editF"]["title"].value=$(element).parent().parent().children(".oneTodoItemTitle").text();
	document.forms["editF"]["deadline"].value=$(element).parent().parent().children(".oneTodoItemDdl").text().substring(5);
	var contentValue = $(element).parent().parent().children(".oneTodoItemContent").children(".oneTodoItemContentText").html();
	contentValue = contentValue.replace(/\<br\>/g,"\n");
	document.forms["editF"]["content"].value=contentValue;
	$("#editType").val(typeId);
	mScroll("editTodoItem");
}

function updateTodoItem(){
	var id = document.forms["editF"]["id"].value;
	var title = document.forms["editF"]["title"].value;
	var deadline = document.forms["editF"]["deadline"].value;
	var type = $("#editType option:selected").val();
	var typeStr = new Array("","food","hotel","shop","travel","life","others");
	var content = document.forms["editF"]["content"].value;
	if (title=="") {
		$("#submitHintEdit").html("请输入标题哦~");
	}else if (deadline=="") {
		$("#submitHintEdit").html("请输入截止日期哦~");
	}else if (type=="") {
		$("#submitHintEdit").html("请选择事项类别哦~");
	}else{
		//var data = "{\"id\":\""+id+"\",\"title\":\""+title+"\",\"deadline\":\""+deadline+"\",\"type\":\""+type+"\",\"content\":\""+content+"\"}";
		content = symbolToEndl(content);
		var thing = {id:id,title:title,deadline:deadline,type:type,content:content};
		var data = $.toJSON(thing);//该插件可以转义大部分特殊字符
		//alert(data)
		$.ajax({
	        type: "post",
	        url: "updateTodoItem",
	        data: {data,data},
	        dataType: "text",
	        success: function(data){
	        	//alert(data);//!!!!!!!!!!!!debug
	            if (data=="\"ERROR\"") {
	            	alert("哎呀，出错了，更新失败了，请稍后再试或联系你的宝贝儿老公哦~");
	            }else{
	            	content = endlToDisplay(content);
	            	$("#oneTodoItem"+id).children(".oneTodoItemTitle").text(title);
	            	$("#oneTodoItem"+id).children(".oneTodoItemDdl").text("截止日期："+deadline);
	            	$("#oneTodoItem"+id).children(".oneTodoItemContent").children(".oneTodoItemContentText").html(content);
	            	$("#oneTodoItem"+id).children("i").attr("class","icon-circle-blank "+typeStr[type]+"Type");
	            	$("#oneTodoItem"+id).find("button").attr("class","button button-pill button-tiny "+typeStr[type]+"Type");
	            	$("#submitHintEdit").html("更新成功");
	            	setTimeout(function()
	            		{
	            			clearEditFormAndHide();
	            			mScrollById("oneTodoItem"+id);
	            		},1000)            	
	            }
	        },
	        error: function(){
	            alert("哎呀，出错啦，更新失败了，请稍后再试或联系你的宝贝儿老公哦~");
	        }
      	});
	}	
}

function clearEditFormAndHide(){
	$(".addTodoItemIcon").show();
	document.forms["editF"]["id"].value="";
	document.forms["editF"]["title"].value="";
	document.forms["editF"]["deadline"].value="";
	document.forms["editF"]["content"].value="";
	$("#submitHintEdit").html("");
	$("#editType").val("");
	$(".editTodoItem").hide();
}

function mScroll(className){
	$("html,body").stop(true);
	$("html,body").animate({scrollTop:$("."+className).offset().top}, 1000);
}

function mScrollById(id){
	$("html,body").stop(true);
	$("html,body").animate({scrollTop:$("#"+id).offset().top-50}, 1000);
}

function symbolToEndl(str){
	var result = str.replace(/\n/g,"&endl;");
	return result;
}

function endlToDisplay(str){
	var result = str.replace(/&endl;/g,"<br/>");
	return result;
}