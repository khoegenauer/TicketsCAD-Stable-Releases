<?php
/*
9/10/13 New File - provides ticket and responder markers and infowindows to the portal
*/

@session_start();
require_once('../../incs/functions.inc.php');
	
$ticket_ids = array();
$where = (isset($_GET['id'])) ? "WHERE `requester` = " . strip_tags($_GET['id']) : "";
$the_ret = array();	
$query = "SELECT * FROM `$GLOBALS[mysql_prefix]requests` " . $where;
$result = mysql_query($query) or do_error('', 'mysql query failed', mysql_error(), basename( __FILE__), __LINE__);
if(mysql_num_rows($result) == 0) {
	$the_ret[0] = -1;
	}
while ($row = stripslashes_deep(mysql_fetch_assoc($result))){
	if(($row['ticket_id'] != 0) && ($row['ticket_id'] != 0)) {
		$ticket_ids[] = $row['ticket_id'];
		}
	}

foreach($ticket_ids as $val) {
	$query1 = "SELECT * FROM `$GLOBALS[mysql_prefix]ticket` `t`			
			WHERE `t`.`id` = " . $val;
	$result1 = mysql_query($query1);	
	while($row1 = stripslashes_deep(mysql_fetch_assoc($result1))){
		$the_ret[$val]['lat'] = $row1['lat'];		
		$the_ret[$val]['lng'] = $row1['lng'];		
		$the_ret[$val]['scope'] = $row1['scope'];	
		$the_ret[$val]['description'] = nl2br($row1['description']);				
		$query2 = "SELECT * FROM `$GLOBALS[mysql_prefix]assigns` `a` WHERE `a`.`ticket_id` = " . $row1['id'];	
		$result2 = mysql_query($query2) or do_error('', 'mysql query failed', mysql_error(), basename( __FILE__), __LINE__);
		while($row2 = stripslashes_deep(mysql_fetch_assoc($result2))){
			$resp_id = $row2['responder_id'];
			$query3 = "SELECT * FROM `$GLOBALS[mysql_prefix]responder` `r` WHERE `r`.`id` = " . $resp_id;	
			$result3 = mysql_query($query3) or do_error('', 'mysql query failed', mysql_error(), basename( __FILE__), __LINE__);
			while($row3 = stripslashes_deep(mysql_fetch_assoc($result3))){	
				$the_id = $row3['id'];
				$the_ret[$val]['responders'][$the_id]['lat'] = $row3['lat'];	
				$the_ret[$val]['responders'][$the_id]['lng'] = $row3['lng'];			
				$the_ret[$val]['responders'][$the_id]['handle'] = $row3['icon_str'];		
				$the_ret[$val]['responders'][$the_id]['jobtitle'] = $row1['scope'];	
				}
			}
		}
	}
print json_encode($the_ret);	
exit();
?>