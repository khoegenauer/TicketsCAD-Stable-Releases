<?php
require_once('../incs/functions.inc.php');
require_once('../incs/status_cats.inc.php');
@session_start();
/* if($_GET['q'] != $_SESSION['id']) {
	exit();
	} */
$iw_width= "300px";					// map infowindow with
$iw_width2= "250px";					// map infowindow with
$ret_arr = array();
$fac_order_values = array(1 => "`handle`,`fac_type_name` ASC", 2 => "`fac_type_name`,`handle` ASC",  3 => "`fac_status_val`,`fac_type_name` ASC");		// 3/15/11

$eols = array ("\r\n", "\n", "\r");		// all flavors of eol
$internet = ((isset($_SESSION['internet'])) && ($_SESSION['internet'] == true)) ? true: false;
$status_vals = array();											// build array of $status_vals
$status_vals[''] = $status_vals['0']="TBD";
$sortby = (!(array_key_exists('sort', $_GET))) ? "tick_id" : $_GET['sort'];
$sortdir = (!(array_key_exists('dir', $_GET))) ? "ASC" : $_GET['dir'];
$locale = get_variable('locale');	// 08/03/09
$query = "SELECT * FROM `$GLOBALS[mysql_prefix]fac_status` ORDER BY `id`";
$result_st = mysql_query($query) or do_error($query, 'mysql query failed', mysql_error(), basename( __FILE__), __LINE__);
while ($row_st = stripslashes_deep(mysql_fetch_array($result_st))) {
	$temp = $row_st['id'];
	$status_vals[$temp] = $row_st['status_val'];
	}

function subval_sort($a, $subkey, $dd) {
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
	
function isempty($arg) {
	return (bool) (strlen($arg) == 0) ;
	}
	
function fac_cat($id) {
	$query = "SELECT * FROM `$GLOBALS[mysql_prefix]fac_types` WHERE `id` = " . $id;	// all dispatches this unit
	$result = mysql_query($query) or do_error($query, 'mysql query failed', mysql_error(), basename( __FILE__), __LINE__);	
	$row = stripslashes_deep(mysql_fetch_array($result));
	return $row['name'];
	}
	
function get_day() {
	$timestamp = (time() - (intval(get_variable('delta_mins'))*60));
	if(strftime("%w",$timestamp)==0) {$timestamp = $timestamp + 86400;}
	return strftime("%A",$timestamp);
	}

if (array_key_exists ('forder' , $_POST))	{$_SESSION['fac_flag_2'] =  $_POST['forder'];}		// 3/15/11
elseif (empty ($_SESSION['fac_flag_2'])) 	{$_SESSION['fac_flag_2'] = 2;}		// 3/15/11

$fac_order_str = $fac_order_values[$_SESSION['fac_flag_2']];		// 3/15/11	

$f_types = array();
$query = "SELECT * FROM `$GLOBALS[mysql_prefix]fac_types` ORDER BY `id`";		// types in use
$result = mysql_query($query) or do_error($query, 'mysql query failed', mysql_error(), basename( __FILE__), __LINE__);
while ($row = stripslashes_deep(mysql_fetch_assoc($result))) {
	$f_types [$row['id']] = array ($row['name'], $row['icon']);
	}
unset($result);	

$query = "SELECT * FROM `$GLOBALS[mysql_prefix]allocates` WHERE `type`= 4 AND `resource_id` = '$_SESSION[user_id]';";	//	6/10/11
$result = mysql_query($query);	//	6/10/11
$al_groups = array();
while ($row = stripslashes_deep(mysql_fetch_assoc($result))) 	{	//	6/10/11
	$al_groups[] = $row['group'];
	}	

if(isset($_SESSION['viewed_groups'])) {
	$curr_viewed= explode(",",$_SESSION['viewed_groups']);
	}
	
if(count($al_groups) == 0) {	//	catch for errors - no entries in allocates for the user.	//	5/30/13		
	$where2 = "WHERE `$GLOBALS[mysql_prefix]allocates`.`type` = 3";
	} else {	
	if(!isset($curr_viewed)) {	
		$x=0;	//	6/10/11
		$where2 = "WHERE (";	//	6/10/11
		foreach($al_groups as $grp) {	//	6/10/11
			$where3 = (count($al_groups) > ($x+1)) ? " OR " : ")";	
			$where2 .= "`$GLOBALS[mysql_prefix]allocates`.`group` = '{$grp}'";
			$where2 .= $where3;
			$x++;
			}
		} else {
		$x=0;	//	6/10/11
		$where2 = "WHERE (";	//	6/10/11
		foreach($curr_viewed as $grp) {	//	6/10/11
			$where3 = (count($curr_viewed) > ($x+1)) ? " OR " : ")";	
			$where2 .= "`$GLOBALS[mysql_prefix]allocates`.`group` = '{$grp}'";
			$where2 .= $where3;
			$x++;
			}
		}
	$where2 .= "AND `$GLOBALS[mysql_prefix]allocates`.`type` = 3";	//	6/10/11
	}

$query_fac = "SELECT *,`$GLOBALS[mysql_prefix]facilities`.`updated` AS `updated`, 
	`$GLOBALS[mysql_prefix]facilities`.`id` 						AS `fac_id`, 
	`$GLOBALS[mysql_prefix]fac_types`.`id` 							AS `type_id`,
	`$GLOBALS[mysql_prefix]facilities`.`description` 				AS `facility_description`,
	`$GLOBALS[mysql_prefix]facilities`.`boundary` 					AS `boundary`,		
	`$GLOBALS[mysql_prefix]fac_types`.`name` 						AS `fac_type_name`, 
	`$GLOBALS[mysql_prefix]fac_types`.`icon` 						AS `icon`, 
	`$GLOBALS[mysql_prefix]facilities`.`name` 						AS `facility_name`, 
	`$GLOBALS[mysql_prefix]fac_status`.`status_val` 				AS `fac_status_val`, 
	`$GLOBALS[mysql_prefix]facilities`.`status_id` 					AS `fac_status_id`
	FROM `$GLOBALS[mysql_prefix]facilities`
	LEFT JOIN `$GLOBALS[mysql_prefix]allocates` 	ON ( `$GLOBALS[mysql_prefix]facilities`.`id` = 			`$GLOBALS[mysql_prefix]allocates`.`resource_id` )	
	LEFT JOIN `$GLOBALS[mysql_prefix]fac_types` 	ON (`$GLOBALS[mysql_prefix]facilities`.`type` = 		`$GLOBALS[mysql_prefix]fac_types`.`id` )
	LEFT JOIN `$GLOBALS[mysql_prefix]fac_status` 	ON (`$GLOBALS[mysql_prefix]facilities`.`status_id` = 	`$GLOBALS[mysql_prefix]fac_status`.`id` )
	{$where2} 
	GROUP BY fac_id ORDER BY {$fac_order_str} ";											// 3/15/11, 6/10/11

$result_fac = mysql_query($query_fac) or do_error($query_fac, 'mysql query failed', mysql_error(), basename(__FILE__), __LINE__);
$facs_ct = mysql_affected_rows();			// 1/4/10
if ($facs_ct==0){
	} else {
	$fs_checked = array ("", "", "", "");
	$fs_checked[$_SESSION['fac_flag_2']] = " CHECKED";
	}

// ===========================  begin major while() for FACILITIES ==========

$quick = (!(is_guest()) && (intval(get_variable('quick')==1)));				// 11/27/09		
$f_sb_indx = 0;							// for fac's only 8/5/10, 12/23/13
$i = 1;
while($row_fac = mysql_fetch_assoc($result_fac)){		// 7/7/10
	$fac_gps = get_allocates(3, $row_fac['fac_id']);	//	6/10/11
	$grp_names = "Groups Assigned: ";	//	6/10/11
	$y=0;	//	6/10/11
	foreach($fac_gps as $value) {	//	6/10/11
		$counter = (count($fac_gps) > ($y+1)) ? ", " : "";
		$grp_names .= get_groupname($value);
		$grp_names .= $counter;
		$y++;
		}
	$grp_names .= " / ";
	
	$name = htmlentities($row_fac['facility_name'],ENT_QUOTES);
	$handle = htmlentities($row_fac['handle'],ENT_QUOTES);

	$fac_id=$row_fac['fac_id'];
	$fac_type=$row_fac['icon'];
	$fac_type_name = $row_fac['fac_type_name'];
	$fac_region = get_first_group(3, $fac_id);		
	
	$fac_index = $row_fac['icon_str'];	

	$latitude = $row_fac['lat'];
	$longitude = $row_fac['lng'];

	$facility_display_name = $f_disp_name = $row_fac['handle'];	
	$the_bg_color = 	$GLOBALS['FACY_TYPES_BG'][$row_fac['icon']];		// 2/8/10
	$the_text_color = 	$GLOBALS['FACY_TYPES_TEXT'][$row_fac['icon']];		// 2/8/10			

// MAIL						
	if ((may_email()) && (is_email($row_fac['contact_email']))) {	
		$mail_link = $row_fac['contact_email'];
		} elseif((may_email()) && (is_email($row_fac['security_email']))){
		$mail_link = $row_fac['security_email'];
		} else {
		$mail_link = "";
		}
// BEDS
	$beds_info = "<TD ALIGN='right'>{$row_fac['beds_a']}/{$row_fac['beds_o']}</TD>";
// STATUS
	$status = get_status_sel($row_fac['fac_id'], $row_fac['fac_status_id'], "f");		// status, 3/15/11
	$status_id = $row_fac['fac_status_id'];
	$temp = $row_fac['status_id'] ;
	$the_status = (array_key_exists($temp, $status_vals))? $status_vals[$temp] : "??";				// 2/2/09
// AS-OF - 11/3/2012
	$updated = format_sb_date_2 ( $row_fac['updated'] );
	
		if(!(isempty(trim($row_fac['opening_hours']))))  	{
			$opening_arr_serial = base64_decode($row_fac['opening_hours']);
			$opening_arr = unserialize($opening_arr_serial);
			$outputstring = "";
			$the_day = "";
			$z = 0;
			foreach($opening_arr as $val) {
				switch($z) {
					case 0:
					$dayname = "Monday";
					break;
					case 1:
					$dayname = "Tuesday";
					break;
					case 2:
					$dayname = "Wednesday";
					break;
					case 3:
					$dayname = "Thursday";
					break;
					case 4:
					$dayname = "Friday";
					break;
					case 5:
					$dayname = "Saturday";
					break;
					case 6:
					$dayname = "Sunday";
					break;
					}
				$openstring = ($dayname == get_day()) ? "Open" : "Closed";
				if($dayname == get_day()) {
					$the_day .= $dayname;
					$outputstring .= " Opens: " . $val[1] . " Closes: " . $val[2];
					}
				$z++;
				}
			$openingTimes = "Opening Times Today (" . $the_day . ")  ---  " . $outputstring;
			}
	$ret_arr[$i][0] = shorten($name, 50);
	$ret_arr[$i][1] = shorten($handle,30);
	$ret_arr[$i][2] = $fac_index;
	$ret_arr[$i][3] = $latitude;
	$ret_arr[$i][4] = $longitude;
	$ret_arr[$i][5] = $the_bg_color;
	$ret_arr[$i][6] = $the_text_color;
	$ret_arr[$i][7] = $mail_link;
	$ret_arr[$i][8] = $status_id;
	$ret_arr[$i][9] = $updated;		
	$ret_arr[$i][10] = $row_fac['fac_id'];
	$ret_arr[$i][11] = $fac_type;
	$ret_arr[$i][15] = htmlentities($fac_type_name, ENT_QUOTES);	
	$ret_arr[$i][16] = $openingTimes;	
	$ret_arr[$i][17] = $the_status;
	$i++;		
	}	// end while()
	
$col_width= max(320, intval($_SESSION['scr_width']* 0.45));
$ctrls_width = $col_width * .75;
$fac_categories = array();													// 12/03/10
$fac_categories = get_fac_category_butts();											// 12/03/10

$cats_buttons = "<TABLE WIDTH='100%'><TR class='heading_2'><TH ALIGN='center'>Facilities</TH></TR><TR class='odd'><TD COLSPAN=99 CLASS='td_label' ><form action='#'>";			//	12/03/10, 3/15/11

function get_fac_icon($fac_cat){			// returns legend string
	$icons = $GLOBALS['fac_icons'];
	$sm_fac_icons = $GLOBALS['sm_fac_icons'];
	$query = "SELECT * FROM `$GLOBALS[mysql_prefix]fac_types` WHERE `name` = \"$fac_cat\"";		// types in use
	$result = mysql_query($query) or do_error($query, 'mysql query failed', mysql_error(), basename( __FILE__), __LINE__);
	$print = "";
	while ($row = stripslashes_deep(mysql_fetch_assoc($result))) {
		$fac_icon = $row['icon'];
		$print .= "<IMG SRC = './our_icons/" . $sm_fac_icons[$fac_icon] . "' STYLE = 'vertical-align: middle'>";
		}
	unset($result);
	return $print;
	}
	
if(!empty($fac_categories)) {
	foreach($fac_categories as $key => $value) {
		$curr_icon = get_fac_icon($value);
		$cats_buttons .= "<DIV class='cat_button' onClick='set_fac_buttons(\"category\"); set_fac_chkbox(\"" . $value . "\")'>" . get_fac_icon($value) . "&nbsp;&nbsp;" . $value . ": <input type=checkbox id='" . $value . "'  onClick='set_fac_buttons(\"category\"); set_fac_chkbox(\"" . $value . "\")'/>&nbsp;&nbsp;&nbsp;</DIV>";
		}
	$all="fac_ALL";		//	12/03/10
	$none="fac_NONE";				//	12/03/10
	$cats_buttons .= "<DIV ID = 'fac_ALL_BUTTON'  class='cat_button' onClick='set_fac_buttons(\"all\"); set_fac_chkbox(\"" . $all . "\")'><FONT COLOR = 'red'>ALL</FONT><input type=checkbox id='" . $all . "' onClick='set_fac_buttons(\"all\"); set_fac_chkbox(\"" . $all . "\")'/></FONT></DIV>";
	$cats_buttons .= "<DIV ID = 'fac_NONE_BUTTON'  class='cat_button' onClick='set_fac_buttons(\"none\"); set_fac_chkbox(\"" . $none . "\")'><FONT COLOR = 'red'>NONE</FONT><input type=checkbox id='" . $none . "' onClick='set_fac_buttons(\"none\"); set_fac_chkbox(\"" . $none . "\")'/></FONT></DIV>";
	$cats_buttons .= "<DIV ID = 'fac_go_can' style='float:right; padding:2px;'><SPAN ID = 'fac_go_button' onClick='do_go_facilities_button()' class='plain' style='width: 50px; float: none; display: none; font-size: .8em; color: green;' onmouseover='do_hover(this.id);' onmouseout='do_plain(this.id);''>Next</SPAN>";
	$cats_buttons .= "<SPAN ID = 'fac_can_button'  onClick='fac_cancel_buttons()' class='plain' style='width: 50px; float: none; display: none; font-size: .8em; color: red;' onmouseover='do_hover(this.id);' onmouseout='do_plain(this.id);''>Cancel</SPAN></DIV>";
	$cats_buttons .= "</DIV></form></TD></TR></TABLE>";
	} else {
	foreach($fac_categories as $key => $value) {
		$curr_icon = get_fac_icon($value);
		$cats_buttons .= "<DIV class='cat_button' STYLE='display: none;' onClick='set_fac_buttons(\"category\"); set_fac_chkbox(\"" . $value . "\")'>" . get_fac_icon($value) . "&nbsp;&nbsp;" . $value . ": <input type=checkbox id='" . $value . "' onClick='set_fac_chkbox(\"" . $value . "\")'/>&nbsp;&nbsp;&nbsp;</DIV>";
		}
	$all="fac_ALL";		//	12/03/10
	$none="fac_NONE";				//	12/03/10
	$cats_buttons .= "<TABLE><TR class='heading_2'><TH width = '" . $ctrls_width . "' ALIGN='center' COLSPAN='99'>Facilities</TH></TR><TR class='odd'><TD COLSPAN=99 CLASS='td_label'><form action='#'>";
	$cats_buttons .= "<DIV class='cat_button' style='color: red;'>None Defined ! </DIV>";
	$all="fac_ALL";		//	12/03/10
	$none="fac_NONE";				//	12/03/10
	$cats_buttons .= "<DIV ID = 'fac_ALL_BUTTON' class='cat_button' STYLE='display: none;'><input type=checkbox id='" . $all . "'/></DIV>";
	$cats_buttons .= "<DIV ID = 'fac_NONE_BUTTON' class='cat_button' STYLE='display: none;'><input type=checkbox id='" . $none . "'/></DIV>";
	$cats_buttons .= "</form></TD></TR></TABLE></DIV>";
	}


if($sortdir == "ASC") {
	$dd = 1;
	} else {
	$dd = 0;
	}

switch($sortby) {
	case 'id':
		$sortval = 2;
		break;
	case 'name':
		$sortval = 0;
		break;
	case 'mail':
		$sortval = 7;
		break;
	case 'status':
		$sortval = 17;
		break;
	case 'updated':
		$sortval = 9;
		break;
	default:
		$sortval = 10;
	}

if($facs_ct > 0) {
	if((isset($ret_arr[0])) && ($ret_arr[0][13] == 0)) {
		$the_output = $ret_arr;
		} else {
		$the_arr = subval_sort($ret_arr, $sortval, $dd);
		$the_output = array();
		$z=1;
		foreach($the_arr as $val) {
			$the_output[$z] = $val;
			$z++;
			}
		}
	} else {
	$the_output[0][0] = 0;

	}
$the_output[0][12] = $cats_buttons;
$the_output[0][13] = $facs_ct;
print json_encode($the_output);
exit();
?>