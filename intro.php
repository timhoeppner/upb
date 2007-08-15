<html>
<head>
<meta http-equiv="expires" content="31 Dec 1990">
<title>Chat Room</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<center>
<?php 
/*	develooping flash chat	    	                    */
/*	by Juan Carlos PosŽ                                 */
/*	juancarlos@develooping.com	                        */
/*	version 1.2	                                        */
require_once ('chat/required/config.php');
?>
<script language="JavaScript">
<!--
function check_the_form() { 
  var the_error='';
  var the_error_name='';
  var the_error_password='';
  var the_person=document.the_form.person.value;
  var the_password=document.the_form.password.value;
  var validperson=" abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
<?php 
if ($password_system=="ip"){
?>
  var validpassword=" 0123456789.";
<?php 
}else{
?>
var validpassword="abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
<?php
}
?>
if (the_person.length < 4){
the_error = "<?php echo $alert_message_1;?>\n";
}
<?php 
if ($password_system!="ip"){
?>
if (the_password.length < 4){
the_error = the_error + "<?php echo $alert_message_2;?>\n";
}
<?php 
}
?>   
for (var i=0; i<the_person.length; i++) {
   if (validperson.indexOf(the_person.charAt(i)) < 0) {
         the_error_name = "<?php echo $alert_message_3;?>\n";
        }
    }  
for (var i=0; i<the_password.length; i++) {
   if (validpassword.indexOf(the_password.charAt(i)) < 0) {
    <?php 
if ($password_system=="ip"){
?>
the_error_password = "<?php echo $alert_message_4;?>\n";
<?php 
}else{
?>
the_error_password = "<?php echo $alert_message_5;?>\n";
<?php 
}
?>
        }
    }  
the_error = the_error + the_error_name + the_error_password ;  
if (the_error!=''){alert('<?php echo $intro_alert;?>\t\t\t\t\t\n\n'+the_error)}
  document.return_the_value = (the_error=='');
}
//-->
</script>
</head>
<body bgcolor="#FFFFFF">
<font face="Verdana, Arial, Helvetica, sans-serif" size="1"><b> 
<?php  
        $lines = file("chat/required/users.txt"); 
        $a = count($lines);
        $counter=0;
        for($i = $a; $i >= 0 ;$i=$i-2){
        $each_user = strval($lines[$i]);//each connected user
        $each_user = str_replace ("\n","", $each_user);
        $each_password = strval($lines[$i+1]);
        $each_password = str_replace ("\n","", $each_password);
        $each_password = trim ($each_password);
        $userisgood=1;
        if (($each_password=="kicked")or($each_password=="banned")){$userisgood=0;}
        if (($each_user!="") and ($userisgood==1)){
        $counter++;
        }
        }
        echo $counter;?>
<?php 
echo htmlentities($person_word);
if($counter != 1){echo htmlentities($plural_particle);}?>
</b><?php echo htmlentities($now_in_the_chat);?></font><br>

      <form name="the_form" action="chat/index.php" method="POST" onSubmit="check_the_form();return document.return_the_value">
  <font face="Verdana, Arial, Helvetica, sans-serif" size="1">
  <?php echo htmlentities($enter_sentence_1);
if ($password_system!="ip"){
echo htmlentities($enter_sentence_2);
}
echo htmlentities($enter_sentence_3);
echo htmlentities($enter_sentence_4);
?>
</font> 
  <table width="475" border="0" cellspacing="0" cellpadding="0">
    <tr align="left" valign="top"> 
      <td colspan="3"><font face="Verdana, Arial, Helvetica, sans-serif" size="1"><br>
        </font></td>
    </tr>
    <tr> 
      <td align="right" bgcolor="#CECECE"><font face="Verdana, Arial, Helvetica, sans-serif" size="1"><?php echo htmlentities($name_word);?> </font></td>
      <td align="left" bgcolor="#CECECE"> 
        <input type="hidden" name="person" maxlength="12" size="8" value="<?php echo $user_env;?>">&nbsp;&nbsp;<?php echo "<small>$user_env</small>"; ?>
        <?php 
if ($password_system=="ip"){
?>
        <input type="hidden" name="password" value="<?php echo $REMOTE_ADDR;?>">
<?php 
}else{
?>
          </td><td align="right" bgcolor="#CECECE"><font face="Verdana, Arial, Helvetica, sans-serif" size="1"><?php echo htmlentities($password_word);?> </font></td>
      <td align="left" bgcolor="#CECECE"> 
        <input type="text" name="password" maxlength="12" size="8">
<?php 
}
?>
      </td>
      <td align="right" bgcolor="#CECECE"> 
        <input type="submit" name="Submit" value="<?php echo htmlentities($enter_button);?>">
      </td>
    </tr>
    <tr align="left" valign="top"> 
<?php 
if ($password_system=="ip"){
?>
<td colspan="3">
<?php 
}else{
?>
<td colspan="5">
<?php 
}
?>       
<hr noshade size="1">
        <p><font face="Verdana, Arial, Helvetica, sans-serif" size="1"><?php echo htmlentities($require_sentence);?></font></p>
      </td>
    </tr>
  </table>
        </form></center>
</body>
</html>