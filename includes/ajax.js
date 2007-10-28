// JavaScript Document
/**
*
*  UTF-8 data encode / decode
*  http://www.webtoolkit.info/
*
**/
var div="";
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

function changediv(userid,threadid,postid,divname)
{
  var hiddendiv = divname+'h';
  var message = document.getElementById(hiddendiv).innerHTML;
  //alert(message)
  
  if (isIE && isWin)
    message = message.replace(/<BR>/g,'\n');
    
  var output = "<form action='editpost.php?id="+userid+"&t_id="+threadid+"&p_id="+postid+"' method='POST' id='quickedit' name='quickedit'>";
  output += '<input type=\'hidden\' id=\'userid\' name=\'userid\' value=\''+userid+'\'>';
  output += '<input type=\'hidden\' id=\'threadid\' name=\'threadid\' value=\''+threadid+'\'>';
  output += '<input type=\'hidden\' id=\'postid\' name=\'postid\' value=\''+postid+'\'>';
  output += '<input type=\'hidden\' id=\'divid\' name=\'divid\' value=\''+hiddendiv+'\'>';
  output += '<textarea name=\'newentry\' id=\'newentry\' cols=\"60\" rows=\"18\">'+message+'</textarea><br>';
  output += "\n<input type=\'button\' onclick=\'javascript:getEdit(document.getElementById(\"quickedit\"),\""+divname+"\");'\' name=\'qedit\' value=\'Save Edit\'>";
  output += "\n<input type=\'submit\' name=\'submit\' value=\'Go Advanced\'>";
  output += '</form>';
  document.getElementById(divname).innerHTML = output;
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
      else
        http_request.onreadystatechange = ReplyContents;
      http_request.open('POST', url, true);
      http_request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
      http_request.setRequestHeader("Content-length", parameters.length);
      http_request.setRequestHeader("Connection", "close");
      http_request.send(parameters);
   }

   function EditContents() {
      if (http_request.readyState == 3)
        document.getElementById('div').innerHTML = "Editing Post....Please Wait";
      if (http_request.readyState == 4) {
         if (http_request.status == 200) {
            result = http_request.responseText;
            alert(result)
            result_array = result.split("<!--divider-->");
            var hiddendiv = div+'h';
            var editdiv = "edit"+div;
            document.getElementById(div).innerHTML = result_array[0];
            document.getElementById(hiddendiv).innerHTML = result_array[1]; 
            //alert(result_array[2])
            document.getElementById(editdiv).innerHTML = result_array[2];       
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
        document.getElementById('posts').innerHTML = "Adding Quick Reply....Please Wait";
      }
      if (http_request.readyState == 4) {
         if (http_request.status == 200) {
            result = http_request.responseText;
            //alert(result);
            result_array = result.split("<!--divider-->");
            document.getElementById('posts').innerHTML = result_array[0];
            //alert(result_array[1]);
            document.getElementById('pagelink1').innerHTML = result_array[1];
            document.getElementById('pagelink2').innerHTML = result_array[1];
            txtArea = document.getElementById('newentry');
            txtArea.value = "";
         } else {
            alert(http_request.status)
            alert('There was a problem with the request.');
         }
      }
   }
   
   function getEdit(obj,divname) {
      div = divname
      //alert(div)
      var poststr = "message=" + escape(Utf8.encode( document.getElementById("newentry").value ));
      poststr += "&id="+escape(Utf8.encode( document.getElementById("userid").value ));
      poststr += "&t_id="+escape(Utf8.encode( document.getElementById("threadid").value ));
      poststr += "&p_id="+escape(Utf8.encode( document.getElementById("postid").value ));
      poststr += "&divid="+escape(Utf8.encode( document.getElementById("divid").value ));
      
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
      poststr += "&page="+escape(Utf8.encode( document.getElementById("page").value));
      
      //alert(poststr)
      makePOSTRequest('quickreply.php', poststr,'reply');
   }
   
