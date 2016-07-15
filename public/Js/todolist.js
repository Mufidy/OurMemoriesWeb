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
	$(".addTodoItemIcon").hide("normal");
	$(".addTodoItemBody").show();
}

function hideAddTodoItemBody() {
	$(".addTodoItemBody").hide("normal");
	$(".addTodoItemIcon").show();
}

function addTodoItem(){
	var title = document.forms["f"]["title"].value;
	var deadline = document.forms["f"]["deadline"].value;
	var type = $("#type option:selected").val();
	var content = document.forms["f"]["content"].value;
	if (title=="") {
		$("#submitHint").html("请输入标题哦~");
	}else if (deadline=="") {
		$("#submitHint").html("请输入截止日期哦~");
	}else if (type=="") {
		$("#submitHint").html("请选择事项类别哦~");
	}else{
		var data = "{\"title\":\""+title+"\",\"deadline\":\""+deadline+"\",\"type\":\""+type+"\",\"content\":\""+content+"\"}";
		alert(data);
	}	
}