// JavaScript Document
// moveOptionsUp
//
// move the selected options up one location in the select list
//
function moveOptionsUp(selectId) {
 var selectList = document.getElementById(selectId);
 var selectOptions = selectList.getElementsByTagName('option');
 for (var i = 1; i < selectOptions.length; i++) {
  var opt = selectOptions[i];
  if (opt.selected) {
   selectList.removeChild(opt);
   selectList.insertBefore(opt, selectOptions[i - 1]);
     }
    }
}

// moveOptionsDown
//
// move the selected options down one location in the select list
//
function moveOptionsDown(selectId) {
 var selectList = document.getElementById(selectId);
 var selectOptions = selectList.getElementsByTagName('option');
 for (var i = selectOptions.length - 2; i >= 0; i--) {
  var opt = selectOptions[i];
  if (opt.selected) {
   var nextOpt = selectOptions[i + 1];
   opt = selectList.removeChild(opt);
   nextOpt = selectList.replaceChild(opt, nextOpt);
   selectList.insertBefore(nextOpt, opt);
     }
    }
}

function submitorderform(type,status) 
{
//detect whether there is a list to be sorted
  if (status == 'full')
  {
    //select the right name for the list
    if (type == "category")
      var list = document.form.admin_catagory_sorting;
    else
      var list = document.form.fsort;

    var theList = "";

    //get the order of the values and populate the hidden field with the new order
    for (i = 0; i <= list.options.length-1; i++) 
    { 
      theList += list.options[i].value;
      
      if (i != list.options.length-1) 
        theList += ",";
    }
    document.form.neworder.value = theList;
  }
document.form.submit();
}

/*function submitorderform(type,status) {
if (status == 'full')
{
if (type == "category")
  var list = document.form.admin_catagory_sorting;
else
  var list = document.form.fsort;
var theList = "&";
// start with a "?" to make it look like a real query-string
for (i = 0; i <= list.options.length-1; i++) { 
theList += "list" + list.options[i].value + "=" + list.options[i].text;
// a "&" only BETWEEN the items, so not at the end
if (i != list.options.length-1) theList += "&";
}
document.form.neworder.value = theList;
}
document.form.submit();
}*/

function swap(source) {
    if (document.images) {
        document.images['myImage'].src = source;
    }
}

var counter = 0;

function addFields(divname)
{
  document.getElementById(divname).innerHTML = '';
  //document.getElementById(divname).innerHTML = "<input type='file' name='icon_file[]'><br>Hello";
  counter = document.icon_upload.count.options[document.icon_upload.count.selectedIndex].value;
  for (i = 1; i <= counter; i++)
  {
    document.getElementById(divname).innerHTML += "<input type='file' name='icon_file[]'><br>";
  }
  return true;
}

function isImage(upload) {
	if(!/(\.gif)$/i.test(upload.value)) {
		alert("Invalid image file type.");
		//upload.form.reset();
		upload.focus();
		return false;
	}
	return true;
}
