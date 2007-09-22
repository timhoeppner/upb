// JavaScript Document

// new javascript code for inserting smilies and bbcode

<!--
var clientInfo = navigator.userAgent.toLowerCase();
var isIE = ( clientInfo.indexOf("msie") != -1 );
var isWin = ( (clientInfo.indexOf("win")!=-1) || (clientInfo.indexOf("16bit") != -1) );

function createBBtag( openerTag , closerTag , areaId ) {
	if(isIE && isWin) {
		createBBtag_IE( openerTag , closerTag , areaId );
	}
	else {
		createBBtag_nav( openerTag , closerTag , areaId );
	}
	return;
}

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

function createBBtag_nav( openerTag , closerTag , areaId ) {
	var txtArea = document.getElementById( areaId );
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

function setsmilies_IE( Tag ,areaId ) {
	var txtArea = document.getElementById( areaId );
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

function setsmilies_nav( Tag , areaId ) {
	var txtArea = document.getElementById( areaId );
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

function addsmilies(Tag,areaId)
{
//var txtArea = opener.document.getElementById( areaId );
var offset = opener.document.newentry.message.selectionStart;
		var preString = (opener.document.newentry.message.value).substring(0,offset);
		var newString = '[img]' + Tag+ '[/img]';
		var postString = (opener.document.newentry.message.value).substring(offset);
		opener.document.newentry.message.value = preString + newString + postString;
		opener.document.newentry.message.selectionStart = offset + Tag.length;
		opener.document.newentry.message.selectionEnd = offset + Tag.length;
		opener.document.newentry.message.focus();
}
//-->
