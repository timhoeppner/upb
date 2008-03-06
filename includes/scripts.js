// Ultimate PHP Board Javascripts
// Author: Chris Kent aka Clark and others for Ultimate PHP Board by Tim Hoeppner aka RR_Pilot, FixITguy
// Website: http://www.myupb.com
// Version: 2.2.1

//START OF BBCODE SCRIPTS

var clientInfo = navigator.userAgent.toLowerCase();
var isIE = ( clientInfo.indexOf("msie") != -1 );
var isWin = ( (clientInfo.indexOf("win")!=-1) || (clientInfo.indexOf("16bit") != -1) );

// function bb_dropdown creates the bbcode for the value selected from the dropdown
// field is the document object containing the selected value, selectname is the name of the dropdown box
function bb_dropdown(field,selectname,txtArea)
{
val = field.options[field.selectedIndex].value;

if (selectname == 'colors')
{
  document.newentry.colors.selectedIndex = 0;
  createBBtag('[color='+val+']','[/color]',txtArea);
}
if (selectname == 'typeface')
{
  document.newentry.typeface.selectedIndex = 0;
  createBBtag('[font='+val+']','[/font]',txtArea);
}
if (selectname == 'size')
{
document.newentry.size.selectedIndex = 0;
createBBtag('[size='+val+']','[/size]',txtArea);
}
}

//function createBBtag chooses the correct function for the browser to enter the BBcode tags
//openerTag is the opening tag, closerTag is the closing tag, areaId is the name of the textarea
function createBBtag( openerTag , closerTag , areaId ) {
	if(isIE && isWin) {
		createBBtag_IE( openerTag , closerTag , areaId );
	}
	else {
		createBBtag_nav( openerTag , closerTag , areaId );
	}
	return;
}

//functions createBB_tag_IE creates the BBcode for IE browsers
//parameters are the same as for createBBTag

function createBBtag_IE( openerTag , closerTag , areaId ) {
	var txtArea = document.getElementById( areaId );
	var aSelection = document.selection.createRange().text;
	var range = txtArea.createTextRange();

	if(aSelection) {
		document.selection.createRange().text = openerTag + aSelection + closerTag;
		txtArea.focus();
		range.move('textedit');
		range.select();
	}
	else {
		var oldStringLength = range.text.length + openerTag.length;
		txtArea.value += openerTag + closerTag;
		txtArea.focus();
		range.move('character',oldStringLength);
		range.collapse(false);
		range.select();
	}
	return;
}

//functions createBB_tag_nav creates the BBcode for non-IE browsers
//parameters are the same as for createBBTag

function createBBtag_nav( openerTag , closerTag , areaId ) {
	var txtArea = document.getElementById( areaId );
	var counter = 1;
  if (txtArea.selectionEnd && (txtArea.selectionEnd - txtArea.selectionStart > 0) ) {

    var preString = (txtArea.value).substring(0,txtArea.selectionStart);
		var newString = openerTag + (txtArea.value).substring(txtArea.selectionStart,txtArea.selectionEnd) + closerTag;
		var postString = (txtArea.value).substring(txtArea.selectionEnd);

    txtArea.value = preString + newString + postString;
		txtArea.focus();
	}
	else {
		var offset = txtArea.selectionStart;
		var preString = (txtArea.value).substring(0,offset);
		var newString = openerTag + closerTag;
		var postString = (txtArea.value).substring(offset);
    txtArea.value = preString + newString + postString;
		txtArea.selectionStart = offset + openerTag.length;
		txtArea.selectionEnd = offset + openerTag.length;
		txtArea.focus();
	}	
  return;
}

//This function is only used for smilies that appear under the textbox

function setsmilies(Tag,areaId) 
{
  var pos = document.getElementById(areaId).selectionStart;
  var scrollPos = document.getElementById(areaId).scrollTop
  if(document.selection) 
  {
    if( !document.getElementById(areaId).focus() )
      document.getElementById(areaId).focus();
    document.selection.createRange().text=Tag;
  }
  else 
  {
    document.getElementById(areaId).value =
    document.getElementById(areaId).value.substr(0, pos) + Tag + document.getElementById(areaId).value.substr(pos);
    document.getElementById(areaId).selectionStart = pos + Tag.length;
    document.getElementById(areaId).selectionEnd = pos + Tag.length;
  }
  document.getElementById(areaId).scrollTop = scrollPos;
}

//This function is used for smilies that appear on the 'more smilies' page

function moresmilies(Tag)
{
  var pos = opener.document.newentry.message.selectionStart;
  var scrollPos = opener.document.newentry.message.scrollTop
  if(document.selection) 
  {
    if( !opener.document.newentry.message.focus() )
      opener.document.newentry.message.focus();
    opener.document.selection.createRange().text=Tag;
  }
  else 
  {
    opener.document.newentry.message.value = opener.document.newentry.message.value.substr(0, pos) + Tag +      opener.document.newentry.message.value.substr(pos);
    opener.document.newentry.message.selectionStart = pos + Tag.length;
    opener.document.newentry.message.selectionEnd = pos + Tag.length;
  }
  opener.document.newentry.message.scrollTop = scrollPos;
}

//function add_link adds urls, images or emails to the textbox
//parameters: type,areaId (type is type of link, areaId is name of textbox)
//function will eventually place the email, image link or url where the cursor is.
function add_link(type,areaId)
{
	if(isIE && isWin) {
		add_link_IE(type,areaId );
	}
	else {
		add_link_nav(type,areaId );
	}
	return;
}


function add_link_IE(type,areaId) {
	//alert(areaId)
  var link = select = url = text = '';
  var txtArea = document.getElementById( areaId );
	var aSelection = document.selection.createRange().text;
	
  var range = txtArea.createTextRange();

	if(aSelection) {
	  var openerTag = '['+type+']';
		var closerTag = '[/'+type+']';
		select = aSelection;
  
    if (type == 'url' || type == 'img')
		{
      found = select.indexOf("http://")
      if (found == -1)
      {
        url = 'http://' + select;
      }
      else
        url = select;
    }
    else
      url = select;

    document.selection.createRange().text = openerTag + url + closerTag;
    txtArea.focus();
		range.move('textedit');

		return;
	}
	else 
  {
		if (type == 'email')
		{
      url = prompt('Enter the email address:','');
    }
    else if (type == 'url')
    { 
      url = prompt('Enter the url:','http://');
    }
    else
    { 
      url = prompt('Enter the url of the image:','http://');
    }
    
    var closerTag = "[/"+type+"]";
    if (url.length > 0)
    {
      if (type == 'url' || type == 'img')
      {
        found = url.indexOf("http://")

        if (found == -1)
        {
          url = 'http://'+url;
        }
      }
    }
    else
      return; 
      
    if (type == "url" || type == "email")
    {
      link = prompt('Enter the link text (optional):','');
    
      if (link.length > 0)
      {
        if (type == 'email')
          var openerTag = '[email='+url+']';
        else
          var openerTag = '[url='+url+']';
      }
      else
      {
        var openerTag = "["+type+"]";
      }
    }
    else
    {
      var openerTag = "["+type+"]";
    }
    var Tag = openerTag + url + closerTag;
    txtArea.value += Tag;
		txtArea.focus();
		range.collapse(false);
		range.select();
    return;
  }
}

function add_link_nav(type,areaId)
{
	var link = url = text = '';
  var txtArea = document.getElementById( areaId );
	if (txtArea.selectionEnd && (txtArea.selectionEnd - txtArea.selectionStart > 0) ) {
		var openerTag = '['+type+']';
		var closerTag = '[/'+type+']';
    var preString = (txtArea.value).substring(0,txtArea.selectionStart);
		url = (txtArea.value).substring(txtArea.selectionStart,txtArea.selectionEnd)
    if (type == 'url' || type == 'img')
		{
      found = url.indexOf("http://")
      if (found == -1)
      {
        link = 'http://' + url;
      }
      else
        link = url;
    }
    else
    link = url;
    var newString = openerTag + link + closerTag;
		var postString = (txtArea.value).substring(txtArea.selectionEnd);
		txtArea.value = preString + newString + postString;
		txtArea.focus();
		return;
	}
	else 
  {
		var openerTag = '['+type+']';
		var closerTag = '[/'+type+']';
		if (type == 'email')
		  link = prompt('Enter the email address:','');
    else if (type == 'url')
      link = prompt('Enter the url:','http://');
    else
      link = prompt('Enter the url of the image:','http://');
    
    if (link.length > 0)
    {
      if (type == 'url' || type == 'img')
      {
        found = link.indexOf("http://")

        if (found == -1)
        {
          url = 'http://'+link;
        }
        else
          url = link;
        
        link = url;
      }
    } 
    else
      return;
    
    var open = '['+type;
    if (type == 'url' || type == 'email')
    {
      linktext = prompt('Enter the link text (optional):','');
      
      if (text.length > 0)
        open += '='+link+"]"+linktext;
      else
        open += ']'+link;
    }
    else
    {
      open += ']'+link;
      
    } 
    open += '[/'+type+']';
  }
    
    
    var offset = txtArea.selectionStart;
		var preString = (txtArea.value).substring(0,offset);
		var postString = (txtArea.value).substring(offset);
		txtArea.value = preString + open + postString;
		txtArea.selectionStart = offset + openerTag.length;
		txtArea.selectionEnd = offset + openerTag.length;
		txtArea.focus();
	  return;
}

function add_list(type,areaId)
{
	if(isIE && isWin) {
		add_list_IE(type,areaId );
	}
	else {
		add_list_nav(type,areaId );
	}
	return;
}

function add_list_nav(type,areaId)
{
  var txtArea = document.getElementById(areaId);
  var offset = txtArea.selectionStart;
  var minus = 0;
  var closerTag = "[/"+type+"]";

  var openerTag = "["+type+"]\r\n";

  minus +=1
  
  var items = new Array();
  var itemString = "";
  var x;
  
	while (item = prompt('Enter an item\r\nLeave the box empty or click cancel to complete the list',''))
	 items.push("[*]"+item+"\r\n");
	
	itemString = items.join('');
  itemsize = items.length;
  
  minus += itemsize;
	
  var preString = (txtArea.value).substring(0,offset);
	var newString = openerTag + itemString + closerTag;
	var postString = (txtArea.value).substring(offset);
	txtArea.value = preString + newString + postString;
	txtArea.selectionStart = offset + newString.length - minus;
	txtArea.selectionEnd = offset + newString.length - minus;
	txtArea.focus();
  return;
}

function add_list_IE(type,areaId)
{
  var txtArea = document.getElementById(areaId);
	var aSelection = document.selection.createRange().text;
	var range = txtArea.createTextRange();

	var minus = 0;
  var closerTag = "[/"+type+"]";

  var openerTag = "["+type+"]\r\n";

  minus +=1
  
  var items = new Array();
  var itemString = "";
  var item;
  
  while (item = prompt('Enter an item\r\nLeave the box empty or click cancel to complete the list',''))
	 items.push("[*]"+item+"\r\n");
	
	itemString = items.join('');
  itemsize = items.length;
  
  minus += itemsize;
  
  Tag = openerTag + itemString + closerTag;
  
	var oldStringLength = range.text.length + Tag.length - minus;
	txtArea.value += Tag;
	txtArea.focus();
	range.move('character',oldStringLength);
	range.collapse(false);
	range.select();
	
	return;
}

//END OF BBCODE SCRIPTS

//START OF FORM SCRIPTS

var ns6=document.getElementById&&!document.all

function restrictinput(maxlength,e,placeholder){
if (window.event&&event.srcElement.value.length>=maxlength)
return false
else if (e.target&&e.target==eval(placeholder)&&e.target.value.length>=maxlength){
var pressedkey=/[a-zA-Z0-9\.\,\/]/ //detect alphanumeric keys
if (pressedkey.test(String.fromCharCode(e.which)))
e.stopPropagation()
}
}

function countlimit(maxlength,e,placeholder){
var theform=eval(placeholder)
var lengthleft=maxlength-theform.value.length
var placeholderobj=document.all? document.all[placeholder] : document.getElementById(placeholder)
if (window.event||e.target&&e.target==eval(placeholder)){
if (lengthleft<0)
theform.value=theform.value.substring(0,maxlength)
placeholderobj.innerHTML=lengthleft
}
}


function displaylimit(theform,thelimit){
var limit_text='<b><span id=\"'+theform.toString()+'\">'+thelimit+'</span></b> characters remaining on your input limit'
if (document.all||ns6)
document.write(limit_text)
if (document.all){
eval(theform).onkeypress=function(){ return restrictinput(thelimit,event,theform)}
eval(theform).onkeyup=function(){ countlimit(thelimit,event,theform)}
}
else if (ns6){
document.body.addEventListener('keypress', function(event) { restrictinput(thelimit,event,theform) }, true);
document.body.addEventListener('keyup', function(event) { countlimit(thelimit,event,theform) }, true);
}
}

var counter=0;
function check_submit()
{
counter++;
if (counter>1)
{
alert('You cannot submit the form again! Please Wait.');
return false;
}
}

//END OF FORM SCRIPTS

//START OF AJAX SCRIPTS

var div="";
var what="";
var Utf8 = {

	// public method for url encoding
	encode : function (string) {
		string = string.replace(/\r\n/g,"\n");
		var utftext = "";

		for (var n = 0; n < string.length; n++) {

			var c = string.charCodeAt(n);

			if (c < 128) {
				utftext += String.fromCharCode(c);
			}
			else if((c > 127) && (c < 2048)) {
				utftext += String.fromCharCode((c >> 6) | 192);
				utftext += String.fromCharCode((c & 63) | 128);
			}
			else {
				utftext += String.fromCharCode((c >> 12) | 224);
				utftext += String.fromCharCode(((c >> 6) & 63) | 128);
				utftext += String.fromCharCode((c & 63) | 128);
			}

		}

		return utftext;
	},

	// public method for url decoding
	decode : function (utftext) {
		var string = "";
		var i = 0;
		var c = c1 = c2 = 0;

		while ( i < utftext.length ) {

			c = utftext.charCodeAt(i);

			if (c < 128) {
				string += String.fromCharCode(c);
				i++;
			}
			else if((c > 191) && (c < 224)) {
				c2 = utftext.charCodeAt(i+1);
				string += String.fromCharCode(((c & 31) << 6) | (c2 & 63));
				i += 2;
			}
			else {
				c2 = utftext.charCodeAt(i+1);
				c3 = utftext.charCodeAt(i+2);
				string += String.fromCharCode(((c & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
				i += 3;
			}

		}

		return string;
	}

}

var http_request = false;
   
   function makePOSTRequest(url, parameters,type){
      http_request = false;

      //select type of request according to browser
      
      if (window.XMLHttpRequest) { // Mozilla, Safari,...
         http_request = new XMLHttpRequest();
         if (http_request.overrideMimeType) {
            http_request.overrideMimeType('text/html');
         }
      } else if (window.ActiveXObject) { // IE
         try {
            http_request = new ActiveXObject("Msxml2.XMLHTTP");
         } catch (e) {
            try {
               http_request = new ActiveXObject("Microsoft.XMLHTTP");
            } catch (e) {}
         }
      }
      if (!http_request) {
         alert('Cannot create XMLHTTP instance');
         return false;
      }
      
      if (type == 'edit')
        http_request.onreadystatechange = EditContents;
      else if (type == 'getpost')
        http_request.onreadystatechange = GetPost;
      else if (type == 'reply')
        http_request.onreadystatechange = ReplyContents;
      else if (type == 'sig')
        http_request.onreadystatechange = Sig;
      else
        http_request.onreadystatechange = SortForums;
      http_request.open('POST', url, true);
      http_request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
      http_request.setRequestHeader("Content-length", parameters.length);
      http_request.setRequestHeader("Connection", "close");
      http_request.send(parameters);
   }

   function Sig()
   {
      if (http_request.readyState == 3) {
      document.getElementById('sig_preview').innerHTML = "<img src='images/spinner.gif' alt='' title='' style='vertical-align: middle;'><br>Getting Preview of Signature";
      }
      if (http_request.readyState == 4) {
         if (http_request.status == 200) {
            result = http_request.responseText;
            result_array = result.split("<!--divider-->");
            document.getElementById('sig_preview').innerHTML = result_array[0];
            document.getElementById('sig_title').innerHTML = result_array[1];       
         } else {
            alert(http_request.status)
            alert('There was a problem with the request.');
         }
      }
   }
   
   function SortForums() {
    if (http_request.readyState == 3) {
      if (what == 'forum')
        waitwhat = 'Forums';
      else
        waitwhat = 'Categories';
      html = "<div class='main_cat_wrapper'><div class='cat_area_1'>Quick Reply</div><table class='main_table' cellspacing='1'><tbody><td class='area_2' style='text-align:center'><img src='images/spinner.gif' alt='' title='' style='vertical-align: middle;'>&nbsp;<strong>Sorting "+waitwhat+"</strong></td></tr></tbody></table><div class='footer'></div></div>";
      document.getElementById(div).innerHTML = html;
      }
      if (http_request.readyState == 4) {
         if (http_request.status == 200) {
            result = http_request.responseText;
            
            document.getElementById(div).innerHTML = result;       
         } else {
            alert(http_request.status)
            alert('There was a problem with the request.');
         }
      }
   }
   
   function GetPost() {
      if (http_request.readyState == 3)
        document.getElementById(div).innerHTML = "Getting Post from Database....Please Wait";
      if (http_request.readyState == 4) {
         if (http_request.status == 200) {
            result = http_request.responseText;
            
            document.getElementById(div).innerHTML = result;       
         } else {
            alert(http_request.status)
            alert('There was a problem with the request.');
         }
      }
   }
   
   function EditContents() {
      if (http_request.readyState == 3)
        document.getElementById(div).innerHTML = "Editing Post....Please Wait";
      if (http_request.readyState == 4) {
         if (http_request.status == 200) {
            result = http_request.responseText;
            
            result_array = result.split("<!--divider-->");
            var editdiv = "edit"+div;
            document.getElementById(div).innerHTML = result_array[0]; 
            
            document.getElementById(editdiv).innerHTML = result_array[1];       
         } else {
            alert(http_request.status)
            alert('There was a problem with the request.');
         }
      }
   }
   
   function ReplyContents() {
      
      if (http_request.readyState == 3)
      {
        html = "<div class='main_cat_wrapper'><div class='cat_area_1'>Quick Reply</div><table class='main_table' cellspacing='1'><tbody><td class='area_2' style='text-align:center'><img src='images/spinner.gif' alt='' title='' style='vertical-align: middle;'>&nbsp;<strong>Adding Quick Reply....Please Wait</strong></td></tr></tbody></table><div class='footer'></div></div>";
        document.getElementById('quickreplyform').innerHTML = html;
      }
      if (http_request.readyState == 4) {
         if (http_request.status == 200) {
            result = http_request.responseText;
            
            result_array = result.split("<!--divider-->");
            document.getElementById('current_posts').innerHTML = result_array[0];
            
            document.getElementById('pagelink1').innerHTML = result_array[1];
            document.getElementById('pagelink2').innerHTML = result_array[2];
            document.getElementById('quickreplyform').innerHTML = result_array[3];
         } else {
            alert(http_request.status)
            alert('There was a problem with the request.');
         }
      }
   }
   
   function getEdit(obj,divname) {
      div = divname;
      var poststr = "newedit=" + escape(Utf8.encode( document.getElementById("newedit").value ));
      poststr += "&forumid="+escape(Utf8.encode(document.getElementById("forumid").value));
      poststr += "&userid="+escape(Utf8.encode( document.getElementById("userid").value ));
      poststr += "&threadid="+escape(Utf8.encode( document.getElementById("threadid").value ));
      poststr += "&postid="+escape(Utf8.encode( document.getElementById("postid").value ));
      poststr += "&type=edit";
      
      makePOSTRequest('./ajax.php', poststr,'edit');     
   }
   
   function getReply(obj) {
      var poststr = "id="+escape(Utf8.encode( document.getElementById("id").value));
      poststr += "&t_id="+escape(Utf8.encode( document.getElementById("t_id").value));
      poststr += "&page="+escape(Utf8.encode( document.getElementById("page").value));
      poststr += "&user_id="+escape(Utf8.encode( document.getElementById("user_id").value));
      poststr += "&icon="+escape(Utf8.encode( document.getElementById("icon").value));
      poststr += "&newentry=" + escape(Utf8.encode( document.getElementById("newentry").value));
      poststr += "&username="+escape(Utf8.encode( document.getElementById("username").value));
      poststr += "&type=reply";
      
      makePOSTRequest('./ajax.php', poststr,'reply');
   }
    
   function getPost(userid,divname,method)
   {  
      div = divname;
      splitstring = divname.split("-");
      var poststr = "forumid="+escape(Utf8.encode(splitstring[0]));
      poststr += "&postid="+escape(Utf8.encode(splitstring[2]));
      poststr += "&userid="+escape(Utf8.encode(userid));
      poststr += "&threadid="+escape(Utf8.encode(splitstring[1]));
      poststr += "&divname="+escape(Utf8.encode(divname));
      poststr += "&method="+escape(Utf8.encode(method));
      poststr += "&type=getpost";

      makePOSTRequest('./ajax.php', poststr,'getpost');  
   }
   
   function forumSort(type,where,id)
   {
      div = 'sorting';
      if (type == "forum")
        what = 'forum';
      else
        what = 'cat';
      var poststr = "what="+escape(Utf8.encode(type));
      poststr += "&where="+escape(Utf8.encode(where));
      poststr += "&id="+escape(Utf8.encode(id));
      poststr += "&divname=sorting";
      poststr += "&type=sort";
      
      makePOSTRequest('./ajax.php', poststr,'sort');

   }
   
    function sigPreview(obj,id,status)
    {
    var poststr = "sig="+escape(Utf8.encode(document.getElementById("u_sig").value));
    poststr += "&id="+escape(Utf8.encode(id));
    poststr += "&status="+escape(Utf8.encode(status));
    poststr += "&type=sig";
    
    makePOSTRequest('./ajax.php', poststr,'sig'); 
    }

//END OF AJAX SCRIPTS

//START OF MISCELLANEOUS SCRIPTS

function swap(source) {
    if (document.images) {
        document.images['myImage'].src = source;
    }
}

function PopUp(where) {
window.open("where", "This PM has been Recieved Within the Last 5 Minutes", "toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=0,width=500,height=350");
}

//adds quote information to the quick reply box
function addQuote(details,message)
{
split = details.split("-");
output = "\r\n[quote="+split[0]+";"+split[1]+";"+split[2]+"]"+message+"[/quote]\r\n";
document.quickreply.newentry.value += output;
}

//removes all bbcode from post boxes

//function below is not complete, relevant bbcode button has been commented out until completion.
function removeBBcode(areaId)
{

//alert(message);
var txtArea = document.getElementById( areaId );
//alert(document.getElementById( areaId ).value)
pattern = new RegExp ("/\[[^\[]*?\]/g");
//document.getElementById( areaId ).value = document.getElementById( areaId ).value.replace(pattern,"");
if (document.getElementById( areaId ).value.match(pattern))
alert("Success");
else
alert("Something went wrong with the regex");
}

//END OF MISCELLANEOUS SCRIPTS
