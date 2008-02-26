// Ultimate PHP Board AJAX System
// Author: Chris Kent aka Clark for Ultimate PHP Board by Tim Hoeppner aka RR_Pilot, FixITguy
// Website: http://www.myupb.com
// Version: 1.0
// Using textdb Version: 4.3.2
// UTF-8 data encode / decode by http://www.webtoolkit.info/


var div="";
var what="";
var isIE = ( clientInfo.indexOf("msie") != -1 );
var isWin = ( (clientInfo.indexOf("win")!=-1) || (clientInfo.indexOf("16bit") != -1) );
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
      //alert(div);
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
            //alert(result)
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
            //alert(result)
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
            //alert(result)
            result_array = result.split("<!--divider-->");
            var editdiv = "edit"+div;
            document.getElementById(div).innerHTML = result_array[0]; 
            //alert(result_array[1])
            document.getElementById(editdiv).innerHTML = result_array[1];       
         } else {
            alert(http_request.status)
            alert('There was a problem with the request.');
         }
      }
   }
   
   function ReplyContents() {
      //alert(div)
      if (http_request.readyState == 3)
      {
        html = "<div class='main_cat_wrapper'><div class='cat_area_1'>Quick Reply</div><table class='main_table' cellspacing='1'><tbody><td class='area_2' style='text-align:center'><img src='images/spinner.gif' alt='' title='' style='vertical-align: middle;'>&nbsp;<strong>Adding Quick Reply....Please Wait</strong></td></tr></tbody></table><div class='footer'></div></div>";
        document.getElementById('quickreplyform').innerHTML = html;
      }
      if (http_request.readyState == 4) {
         if (http_request.status == 200) {
            result = http_request.responseText;
            //alert(result);
            result_array = result.split("<!--divider-->");
            document.getElementById('current_posts').innerHTML = result_array[0];
            //alert(result_array[1]);
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
      //alert(div)
      var poststr = "newedit=" + escape(Utf8.encode( document.getElementById("newedit").value ));
      poststr += "&forumid="+escape(Utf8.encode(document.getElementById("forumid").value));
      poststr += "&userid="+escape(Utf8.encode( document.getElementById("userid").value ));
      poststr += "&threadid="+escape(Utf8.encode( document.getElementById("threadid").value ));
      poststr += "&postid="+escape(Utf8.encode( document.getElementById("postid").value ));
      //poststr += "&divid="+escape(Utf8.encode( document.getElementById("divid").value ));
      //alert(poststr);
           
      makePOSTRequest('quickedit.php', poststr,'edit');
   }
   
   function getReply(obj) {
      var poststr = "id="+escape(Utf8.encode( document.getElementById("id").value));
      poststr += "&t_id="+escape(Utf8.encode( document.getElementById("t_id").value));
      poststr += "&page="+escape(Utf8.encode( document.getElementById("page").value));
      poststr += "&user_id="+escape(Utf8.encode( document.getElementById("user_id").value));
      poststr += "&icon="+escape(Utf8.encode( document.getElementById("icon").value));
      poststr += "&newentry=" + escape(Utf8.encode( document.getElementById("newentry").value));
      poststr += "&username="+escape(Utf8.encode( document.getElementById("username").value));
      
      //alert(poststr)
      makePOSTRequest('quickreply.php', poststr,'reply');
   }
   
   function getPost(userid,divname)
   {  
      div = divname;
      splitstring = divname.split("-");
      //alert(div);
      var poststr = "forumid="+escape(Utf8.encode(splitstring[0]));
      poststr += "&postid="+escape(Utf8.encode(splitstring[2]));
      poststr += "&userid="+escape(Utf8.encode(userid));
      poststr += "&threadid="+escape(Utf8.encode(splitstring[1]));
      poststr += "&divname="+escape(Utf8.encode(divname));
      poststr += "&type=getpost";
      //alert(poststr)
      makePOSTRequest('quickedit.php', poststr,'getpost'); 
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
      poststr += "&type=forumSort";
      makePOSTRequest('sort_forums.php', poststr,'sort'); 
   }
   
    function sigPreview(obj,id,status)
    {
    var poststr = "sig="+escape(Utf8.encode(document.getElementById("u_sig").value));
    poststr += "&id="+escape(Utf8.encode(id));
    poststr += "&status="+escape(Utf8.encode(status));
    makePOSTRequest('sig_preview.php', poststr,'sig'); 
    }
