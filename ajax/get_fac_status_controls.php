<?php
require_once('../incs/functions.inc.php');
set_time_limit(0);
@session_start();
/* if($_GET['q'] != $_SESSION['id']) {
	exit();
	} */
$ret_arr = array();
$status_vals = array();											// build array of $status_vals
$status_vals[''] = $status_vals['0']="TBD";

$query = "SELECT * FROM `$GLOBALS[mysql_prefix]fac_status` ORDER BY `id`";
$result_st = mysql_query($query) or do_error($query, 'mysql query failed', mysql_error(), basename( __FILE__), __LINE__);
while ($row_st = stripslashes_deep(mysql_fetch_array($result_st))) {
	$temp = $row_st['id'];
	$status_vals[$temp] = $row_st['status_val'];
	}

unset($result_st);

$query = "SELECT * FROM `$GLOBALS[mysql_prefix]facilities` ORDER BY `id` ASC";											// 2/1/10, 3/15/10, 6/10/11
$result = mysql_query($query) or do_error($query, 'mysql query failed', mysql_error(), basename( __FILE__), __LINE__);
while ($row = stripslashes_deep(mysql_fetch_assoc($result))) {			// 7/7/10
	$status = get_status_sel($row['id'], $row['status_id'], "f");		// status
	$ret_arr[$row['id']] = $status;
	}				// end  ==========  while() for RESPONDER ==========

//dump($ret_arr);
print json_encode($ret_arr);
exit();
?>