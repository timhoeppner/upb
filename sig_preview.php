<?php
require_once("./includes/class/func.class.php");
if ($_POST['status'] == "set")
{
  $sig = format_text(filterLanguage(UPBcoding($_POST["sig"]), $_CONFIG["censor"]));
  $sig_title = "<strong>Signature Preview:</strong><br>To save this signature press Submit below";
}
else
{
  $rec = $tdb->get("users", $_POST["id"]);
  $sig = format_text(filterLanguage(UPBcoding($rec[0]['sig']), $_CONFIG["censor"]));
  $sig_title = "<strong>Current Signature:</strong>";
}
echo $sig."<!--divider-->".$sig_title;
?>
