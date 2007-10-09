// JavaScript Document

// new javascript code for inserting smilies and bbcode

<!--
var clientInfo = navigator.userAgent.toLowerCase();
var isIE = ( clientInfo.indexOf("msie") != -1 );
var isWin = ( (clientInfo.indexOf("win")!=-1) || (clientInfo.indexOf("16bit") != -1) );

// function bb_dropdown creates the bbcode for the value selected from the dropdown
// field is the document object containing the selected value, selectname is the name of the dropdown box
function bb_dropdown(field,selectname)
{
val = field.options[field.selectedIndex].value;

if (selectname == 'colors')
{
  document.newentry.colors.selectedIndex = 0;
  createBBtag('[color='+val+']','[/color]','message');
}
if (selectname == 'typeface')
{
  document.newentry.typeface.selectedIndex = 0;
  createBBtag('[font='+val+']','[/font]','message');
}
if (selectname == 'size')
{
document.newentry.size.selectedIndex = 0;
createBBtag('[size='+val+']','[/size]','message');
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
		return;
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
		return;
	}
}

//function setsmilies chooses the correct function for the browser to enter the smilie tag
//Tag is the smilie code, areaID is the name of the textarea
//This function is only used for smilies that appear under the textbox

function setsmilies(Tag,areaId)
{
	if(isIE && isWin) {
		setsmilies_IE( Tag , areaId );
	}
	else {
		setsmilies_nav( Tag ,areaId );
	}
	return;
}

//function setsmilies_nav enters the smilie tag for IE browsers
//Tag is the smilie code, areaID is the name of the textarea
//This function is only used for smilies that appear under the textbox

function setsmilies_IE( Tag ,areaId ) {
	var txtArea = document.getElementById(areaId);
	var aSelection = document.selection.createRange().text;
	var range = txtArea.createTextRange();

	if(aSelection) {
		document.selection.createRange().text = Tag + aSelection;
		txtArea.focus();
		range.move('textedit');
		range.select();
	}
	else {
		var oldStringLength = range.text.length + Tag.length;
		txtArea.value += Tag;
		txtArea.focus();
		range.move('character',oldStringLength);
		range.collapse(false);
		range.select();
	}
	return;
}

//function setsmilies_nav enters the smilie tag for non-IE browsers
//Tag is the smilie code, areaID is the name of the textarea
//This function is only used for smilies that appear under the textbox

function setsmilies_nav( Tag , areaId ) {
	var txtArea = document.getElementById(areaId);
	if (txtArea.selectionEnd && (txtArea.selectionEnd - txtArea.selectionStart > 0) ) {
		var preString = (txtArea.value).substring(0,txtArea.selectionStart);
		var newString = Tag + (txtArea.value).substring(txtArea.selectionStart,txtArea.selectionEnd);
		var postString = (txtArea.value).substring(txtArea.selectionEnd);
		txtArea.value = preString + newString + postString;
		txtArea.focus();
	}
	else {
		var offset = txtArea.selectionStart;
		var preString = (txtArea.value).substring(0,offset);
		var newString = Tag;
		var postString = (txtArea.value).substring(offset);
		txtArea.value = preString + newString + postString;
		txtArea.selectionStart = offset + Tag.length;
		txtArea.selectionEnd = offset + Tag.length;
		txtArea.focus();
	}
	return;
}

//function addsmilies enters the smilie tag for non-IE browsers
//Tag is the smilie code
//This function is only used for smilies that appear on the 'more smilies' page

function addsmilies(Tag)
{
  var offset = opener.document.newentry.message.selectionStart;
	var preString = (opener.document.newentry.message.value).substring(0,offset);
	var newString = '[img]' + Tag+ '[/img]';
	var postString = (opener.document.newentry.message.value).substring(offset);
	opener.document.newentry.message.value = preString + newString + postString;
	opener.document.newentry.message.selectionStart = offset + Tag.length;
	opener.document.newentry.message.selectionEnd = offset + Tag.length;
	opener.document.newentry.message.focus();
}

//function add_url_email adds urls or emails to the textbox
//parameters: tag (bbcode generated by bb_url_email)
//function will eventually place the email or url where the cursor is.
function add_url_email(type,areaId)
{
	if(isIE && isWin) {
		add_url_email_IE(type,areaId );
	}
	else {
		add_url_email_nav(type,areaId );
	}
	return;
}


//second half of function needs rewriting
function add_url_email_IE(type,areaId) {
	//alert(areaId)
  var link = select = url = text = '';
  var txtArea = document.getElementById( areaId );
	var aSelection = document.selection.createRange().text;
	
  var range = txtArea.createTextRange();

	if(aSelection) {
	  var openerTag = '['+type+']';
		var closerTag = '[/'+type+']';
		select = aSelection;
    if (type == 'url')
		{
      found = select.indexOf("http://")
      if (found == -1)
      {
        url = 'http://' + select;
      }
    }
    else
      url = select;
    document.selection.createRange().text = openerTag + url + closerTag;
    txtArea.focus();
		//range.move('textedit');
		range.select();
		return;
	}
	else 
  {
		if (type == 'email')
		{
      url = prompt('Enter the email address:','');
      var closerTag = "[/email]";
    }
    else
    { 
      url = prompt('Enter the url:','http://');
      var closerTag = "[/url]";
    }
    
    if (url.length > 0)
    {
      if (type == 'url')
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
      
    link = prompt('Enter the link text (optional):','');
    
    if (link.length > 0)
    {
      if (type == 'email')
        var openerTag = '[email='+url+']';
      else
        var openerTag = '[url='+url+']';
      
      var Tag = openerTag + link + closerTag;
    }
    else
    {
      if (type == 'email')
        var openerTag = '[email]';
      else
        var openerTag = '[url]';
      
      var Tag = openerTag + url + closerTag;
    }

    txtArea.value += Tag;
		txtArea.focus();
		range.collapse(false);
		range.select();
    return;
  }
}

function add_url_email_nav(type,areaId)
{
	var link = url = text = '';
  var txtArea = document.getElementById( areaId );
	if (txtArea.selectionEnd && (txtArea.selectionEnd - txtArea.selectionStart > 0) ) {
		var openerTag = '['+type+']';
		var closerTag = '[/'+type+']';
    var preString = (txtArea.value).substring(0,txtArea.selectionStart);
		url = (txtArea.value).substring(txtArea.selectionStart,txtArea.selectionEnd)
    if (type == 'url')
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
	else {
		var openerTag = '['+type+']';
		var closerTag = '[/'+type+']';
		if (type == 'email')
		  link = prompt('Enter the email address:','');
    else
      link = prompt('Enter the url:','http://');
    
    if (link.length > 0)
    {
      if (type == 'url')
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
    
    linktext = prompt('Enter the link text (optional):','');
    //alert(text);
    var open = '['+type;
    if (text.length > 0)
        open += '='+link+"]"+linktext;
      else
        open += ']'+link;
      open += '[/'+type+']';
    }
    var offset = txtArea.selectionStart;
		var preString = (txtArea.value).substring(0,offset);
		var newString = openerTag + closerTag;
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
  
  //alert(itemsize);
	
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
		
	//alert(item);
	
	itemString = items.join('');
  itemsize = items.length;
  
  minus += itemsize;
  
  //alert(itemsize);
  Tag = openerTag + itemString + closerTag;
  
	var oldStringLength = range.text.length + Tag.length - minus;
	txtArea.value += Tag;
	txtArea.focus();
	range.move('character',oldStringLength);
	range.collapse(false);
	range.select();
	
	return;
}
//-->
