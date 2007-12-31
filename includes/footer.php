<?php
	// Ultimate PHP Board
	// Author: Tim Hoeppner aka RR_Pilot, FixITguy
	// Website: http://www.myupb.com
	// Version: 2.0
	// Using textdb Version: 4.3.2
	//Ending of center Table
	if (!defined('DB_DIR')) die('This must be run under a wrapper script!');
	if (!isset($script_end_time)) {
		$mt = explode(' ', microtime());
		$script_end_time = $mt[0] + $mt[1];
	}
	echo "
	<div class='copy'>Powered by myUPB&nbsp;&nbsp;&middot;&nbsp;&nbsp;<a href='http://www.myupb.com/'>PHP Outburst</a>
	&nbsp;&nbsp;&copy;2002 - 2007</div>
</div>
</body>
</html>";
?>