 function Wordbooker_Download_Code(t,bid) {
xmlhttp=null
// code for Mozilla, etc.
if (window.XMLHttpRequest)
  {
  xmlhttp=new XMLHttpRequest()
  }
// code for IE
else if (window.ActiveXObject)
  {
  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP")
  }
if (xmlhttp!=null)
  {
  xmlhttp.open("GET","/wp-content/plugins/wordbooker/wordbooker_get_friend.php?name="+t+"&userid="+userid,false)
  xmlhttp.send(false)
  xxx=xmlhttp.responseText.replace("\n","");
xxx=xxx.replace(" ","");
  }
else
  {
  alert("Your browser does not support XMLHTTP.")
  }
return(xxx);
}
function Wordbooker_getFBFriend(tag) {
code_id=Wordbooker_Download_Code(tag,userid);
junk=document.getElementById("FriendID");
//junk2=document.getElementById("tagtypeID");
junk3=document.getElementById("wordbooker_tag_list");
junk3.value=junk3.value+ '[' + code_id + ':1:' + junk.value + '] ';
junk.value="";
return;
}

