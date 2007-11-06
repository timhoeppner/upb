// JavaScript Document
function change_order(index,to,type) {
if (type == "category")
  var list = document.form.admin_catagory_sorting;
else
  var list = document.form.fsort;
var total = list.options.length-1;
if (index == -1) return false;
if (to == +1 && index == total) return false;
if (to == -1 && index == 0) return false;
var items = new Array;
var values = new Array;
for (i = total; i >= 0; i--) {
items[i] = list.options[i].text;
values[i] = list.options[i].value;
}
for (i = total; i >= 0; i--) {
if (index == i) {
list.options[i + to] = new Option(items[i],values[i + to], 0, 1);
list.options[i] = new Option(items[i + to], values[i]);
i--;
}
else {
list.options[i] = new Option(items[i], values[i]);
   }
}
list.focus();
}

function submitorderform(type) {
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
document.form.submit();
}
