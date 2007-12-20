<?php
// Ultimate PHP Board
// Author: Tim Hoeppner aka RR_Pilot, FixITguy
// Website: http://www.myupb.com
// Version: 2.0
// Using textdb Version: 4.3.2

//Ending of center Table

if(!defined('DB_DIR')) die('This must be run under a wrapper script!');
if(!isset($script_end_time)) {
    $mt = explode(' ', microtime()); 
    $script_end_time = $mt[0] + $mt[1]; 
}

?></font></center>
          </td>
        </tr>
        <tr> 
          <td width='50%'><img height=9 src='<?php echo $_CONFIG["skin_dir"]; ?>/images/bottomleft_c.gif' width=11 border=0></td>
          <td width='50%'><p align=right><img height=9 src='<?php echo $_CONFIG["skin_dir"]; ?>/images/bottomright_c.gif' width=11 border=0></p>
          </td>
        </tr>
        </tbody>
      </table>
    </td>
  </tr>
  </tbody>
</table>


<table style='BORDER-COLLAPSE: collapse' bordercolor=#111111 cellspacing=0 cellpadding=0 width='100%' border=0>
    <tr bgcolor='#eef0f5' bordercolor='#333333'> 
<td valign='top' width='513'> 
        <div align='center'><font face=verdana size='1'> </font></div>
                  </td>
                  <td width='237'> 
                    <div align='right'><font face=verdana color=#000000 size='1'><img src='<?php echo $_CONFIG["skin_dir"]; ?>/images/footer_bg.PNG' width='235' height='98'></font></div>
                  </td>
                </tr>
                <tbody> 
                <tr>
                  <td width='513' background='<?php echo $_CONFIG["skin_dir"]; ?>/images/blueline1.gif'><img height=5 src='<?php echo $_CONFIG["skin_dir"]; ?>/images/blueline1.gif' width=1 border=0></td>
                  <td width='237' background='<?php echo $_CONFIG["skin_dir"]; ?>/images/blueline1.gif'><img height=5 src='<?php echo $_CONFIG["skin_dir"]; ?>/images/blueline1.gif' width=1 border=0></td>
                </tr>
                </tbody> 
              </table>
              <table style='BORDER-COLLAPSE: collapse' bordercolor=#111111 cellspacing=0 cellpadding=4 width='100%' background='<?php echo $_CONFIG["skin_dir"]; ?>/images/title_bg.gif' border=0>
                <tbody> 
                <tr> 
                  <td width='33%'><b><font face=Verdana color=#a9b3ba size='1'>&nbsp;</font><font face='Verdana' color='#CCCCCC' size='1'>Powered by UPB Version : <?php echo UPB_VERSION; ?></font></b></td>
                  <td align=center  width='34%'><b><font face='Verdana' color='#CCCCCC' size='1'>Rendered in <i><?php echo round($script_end_time - $script_start_time, 5); ?> seconds</i></font></b></td>
                  <td align=right width='33%'><b><font face=verdana color=#000000 size='1'><a href='http://www.myupb.com/'><font color='#CCCCCC'>PHP Outburst</font></a></font><font face='verdana,arial,helvetica' color='#CCCCCC' size='1'> &copy;2002 - <?php echo date('Y'); ?></font></b></td>
                </tr>
                </tbody> 
              </table>
              <table style='BORDER-COLLAPSE: collapse' bordercolor=#111111 cellspacing=0 cellpadding=0 width='100%' background=<?php echo $_CONFIG["skin_dir"]; ?>/images/blueline2.gif border=0>
                <tbody> 
                <tr> 
                  <td width='100%'><img height=6 src='<?php echo $_CONFIG["skin_dir"]; ?>/images/blueline2.gif' width=1 border=0></td>
                </tr>
                </tbody> 
              </table>
              <table style='BORDER-COLLAPSE: collapse' bordercolor=#111111 cellspacing=0 cellpadding=4 width='100%' background=<?php echo $_CONFIG["skin_dir"]; ?>/images/head_bottom_left_bg.JPG border=0>
                <tbody> 
                <tr valign='top'> 
                  <td width='100%' height='16'> 
                    <p align=center><font face='Verdana' size='1'><b>
     &nbsp;<a href='<?php echo $_CONFIG["homepage"]; ?>'>Home</a>&nbsp; 
    |&nbsp; <a href='register.php'>Register</a>&nbsp; 
    |&nbsp; <a href='profile.php'>User Cp</a>&nbsp; 
    |&nbsp; <a href='showmembers.php'>Members</a>&nbsp; 
    |&nbsp; <a href='search.php'>Search</a>&nbsp;
    |&nbsp; <a href='pmsystem.php' target='_blank'>Private MSG</a>&nbsp; 
    |&nbsp; <a href='board_faq.php'>Faq</a>&nbsp; 
    |&nbsp; <a href='getpass.php'>Forgotten Password?</a>&nbsp; 
    |&nbsp; <a href='<?php echo $loginlink; ?>'>Login/Logout</a></b></font></p>
            </td>
                </tr>
                </tbody> 
              </table>

      </td>
    </tr>
  </table>
</body>
</html>
