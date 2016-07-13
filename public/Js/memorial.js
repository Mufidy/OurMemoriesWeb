function deleteDataMem(id)
{
  var ret = window.confirm("真的要删除这条纪念日嘛宝贝儿~\r\n你的老公公好难过滴哦~~");
  //当点击确定时 返回 true 
  if(ret){
      //do something 点确定
      $.ajax({
         type: "post",
         url: "deleteMemorial",
         data: {id,id},
         dataType: "text",
         success: function(data){
            $("#memAllContent"+id).css("color","red");
            $("#memAllContent"+id).css("font-size","20px");
            $("#memAllContent"+id).css("text-align","center");
            $("#memAllContent"+id).html("删除成功<br>重新加载中...");
             
            setTimeout("location.reload();", 1000);
          }
      });
  }
}