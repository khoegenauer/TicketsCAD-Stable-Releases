<?php
/*
9/10/13 - new file, lists tickets that are assigned to the mobile user
*/

/*
$GLOBALS['LOG_SIGN_IN']				= 1;
$GLOBALS['LOG_SIGN_OUT']			= 2;
$GLOBALS['LOG_COMMENT']				= 3;		// misc comment
$GLOBALS['LOG_INCIDENT_OPEN']		=10;
$GLOBALS['LOG_INCIDENT_CLOSE']		=11;
$GLOBALS['LOG_INCIDENT_CHANGE']		=12;
$GLOBALS['LOG_ACTION_ADD']			=13;
$GLOBALS['LOG_PATIENT_ADD']			=14;
$GLOBALS['LOG_INCIDENT_DELETE']		=15;		// added 6/4/08 
$GLOBALS['LOG_ACTION_DELETE']		=16;		// 8/7/08
$GLOBALS['LOG_PATIENT_DELETE']		=17;
$GLOBALS['LOG_UNIT_STATUS']			=20;
$GLOBALS['LOG_UNIT_COMPLETE']		=21;		// 	run complete
$GLOBALS['LOG_UNIT_CHANGE']			=22;
$GLOBALS['LOG_UNIT_TO_QUARTERS']	=23;		// 3/11/12

$GLOBALS['LOG_CALL_EDIT']			=29;		// 6/17/11
$GLOBALS['LOG_CALL_DISP']			=30;		// 1/20/09
$GLOBALS['LOG_CALL_RESP']			=31;
$GLOBALS['LOG_CALL_ONSCN']			=32;
$GLOBALS['LOG_CALL_CLR']			=33;
$GLOBALS['LOG_CALL_RESET']			=34;		// 7/7/09

$GLOBALS['LOG_CALL_REC_FAC_SET']	=35;		// 9/29/09
$GLOBALS['LOG_CALL_REC_FAC_CHANGE']	=36;		// 9/29/09
$GLOBALS['LOG_CALL_REC_FAC_UNSET']	=37;		// 9/29/09
$GLOBALS['LOG_CALL_REC_FAC_CLEAR']	=38;		// 9/29/09

$GLOBALS['LOG_FACILITY_ADD']		=40;		// 9/22/09
$GLOBALS['LOG_FACILITY_CHANGE']		=41;		// 9/22/09

$GLOBALS['LOG_FACILITY_INCIDENT_OPEN']	=42;		// 9/29/09
$GLOBALS['LOG_FACILITY_INCIDENT_CLOSE']	=43;		// 9/29/09
$GLOBALS['LOG_FACILITY_INCIDENT_CHANGE']=44;		// 9/29/09

$GLOBALS['LOG_CALL_U2FENR']			=45;		// 9/29/09
$GLOBALS['LOG_CALL_U2FARR']			=46;		// 9/29/09

$GLOBALS['LOG_FACILITY_DISP']		=47;		// 9/22/09
$GLOBALS['LOG_FACILITY_RESP']		=48;		// 9/22/09
$GLOBALS['LOG_FACILITY_ONSCN']		=49;		// 9/22/09
$GLOBALS['LOG_FACILITY_CLR']		=50;		// 9/22/09
$GLOBALS['LOG_FACILITY_RESET']		=51;		// 9/22/09

$GLOBALS['LOG_ICS_MESSAGE_SEND']	=60;		// 4/7/2014

$GLOBALS['LOG_ERROR']				=90;		// 1/10/11
$GLOBALS['LOG_INTRUSION']			=91;		// 5/25/11
$GLOBALS['LOG_ERRONEOUS']			=0;			// 1/10/11

$GLOBALS['LOG_SMSGATEWAY_CONNECT']	=1000;		// 10/23/12
$GLOBALS['LOG_SMSGATEWAY_SEND']		=1001;		// 10/23/12
$GLOBALS['LOG_SMSGATEWAY_RECEIVE']	=1002;		// 10/23/12

$GLOBALS['LOG_EMAIL_CONNECT']		=1010;		// 10/23/12
$GLOBALS['LOG_EMAIL_SEND']			=1011;		// 10/23/12
$GLOBALS['LOG_EMAIL_RECEIVE']		=1012;		// 10/23/12

$GLOBALS['LOG_NEW_REQUEST']			=2010;		// 26/7/13
$GLOBALS['LOG_EDIT_REQUEST']		=2011;		// 26/7/13
$GLOBALS['LOG_CANCEL_REQUEST']		=3012;		// 26/7/13
$GLOBALS['LOG_ACCEPT_REQUEST']		=3013;		// 26/7/13
$GLOBALS['LOG_TENTATIVE_REQUEST']	=3014;		// 26/7/13
$GLOBALS['LOG_DECLINE_REQUEST']		=3015;		// 26/7/13

$GLOBALS['LOG_WARNLOCATION_ADD']	=4010;		// 8/9/13
$GLOBALS['LOG_WARNLOCATION_CHANGE']	=4013;		// 8/9/13
$GLOBALS['LOG_WARNLOCATION_DELETE']	=4014;		// 8/9/13

$GLOBALS['LOG_SPURIOUS']			=127;		// 10/24/13 Added to catch failed logs
*/
@session_start();
if($_GET['q'] != $_SESSION['id']) {
	exit();
	}
require_once('../incs/functions.inc.php');
require_once('../incs/log_codes.inc.php');
function br2nl($input) {
	return preg_replace('/<br(\s+)?\/?>/i', "\n", $input);
	}

$sort = (isset($_GET['sort'])) ? $_GET['sort'] : 'id';
$dir = (isset($_GET['dir'])) ? $_GET['dir'] : 'DESC';

function distance($lat1, $lon1, $lat2, $lon2, $unit) { 
	$theta = $lon1 - $lon2; 
	$dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta)); 
	$dist = acos($dist); 
	$dist = rad2deg($dist); 
	$miles = $dist * 60 * 1.1515;
	$unit = strtoupper($unit);

	if ($unit == "K") {
		return ($miles * 1.609344); 
		} else if ($unit == "N") {
		return ($miles * 0.8684);
		} else {
		return $miles;
		}
	}

function subval_sort($a,$subkey, $dd) {
	foreach($a as $k=>$v) {
		$b[$k] = strtolower($v[$subkey]);
		}
	if($dd == 1) {	
		asort($b);
		} else {
		arsort($b);
		}
	foreach($b as $key=>$val) {
		$c[] = $a[$key];
		}
	return $c;
	}

$query = "SELECT * FROM `$GLOBALS[mysql_prefix]log` WHERE `who` != " . $_SESSION['user_id'] . " AND `code` != 90 AND `code` != 127 ORDER BY `id` DESC LIMIT 1000";

$result = mysql_query($query) or do_error($query, $query, mysql_error(), basename( __FILE__), __LINE__);
$num_rows = mysql_num_rows($result);
$i = 0;
if (($result) && (mysql_num_rows($result) >=1)) {
	while ($row = stripslashes_deep(mysql_fetch_assoc($result))) 	{
		if($row['ticket_id'] != 0) {
			$the_onClick = "edit.php?id=" . $row['ticket_id'];
			} else {
			$the_onClick = "";
			}
		$ret_arr[$i][0] = $row['id'];
		$ret_arr[$i][1] = get_owner($row['who']);
		$ret_arr[$i][2] = $row['from'];
		$ret_arr[$i][3] = format_date_2(strtotime($row['when']));
		$ret_arr[$i][4] = $types[$row['code']];
		$ret_arr[$i][5] = $row['ticket_id'];
		$ret_arr[$i][6] = get_respondername($row['responder_id']);
		$ret_arr[$i][7] = get_facilityname($row['facility']);
		$ret_arr[$i][8] = get_facilityname($row['rec_facility']);
		$ret_arr[$i][9] = $row['mileage'];
		$ret_arr[$i][10] = replace_quotes(shorten($row['info'], 20));
		$ret_arr[$i][11] = $the_onClick;
		$ret_arr[$i][12] = $row['info'];		
		$i++;
		}
	} else {
	$ret_arr[0][99] = 0;
	}	//	end else
//dump($ret_arr);	
$output_arr = $ret_arr;
if($dir == "ASC") {
	$dd = 1;
	} else {
	$dd = 0;
	}

switch($sort) {
	case 'id':
		$sortval = 0;
		break;
	case 'code':
		$sortval = 4;
		break;
	case 'when':
		$sortval = 3;
		break;
	case 'responder_id':
		$sortval = 6;
		break;
	case 'ticket_id':
		$sortval = 5;
		break;
	case 'info':
		$sortval = 12;
		break;
	default:
		$sortval = 0;
	}

if($num_rows > 0) {
	$the_arr = subval_sort($output_arr,$sortval, $dd);
	} else {
	$the_arr[0][0] = 0;
	}

print json_encode($the_arr);
exit();
?>