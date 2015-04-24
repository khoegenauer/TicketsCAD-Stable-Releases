<?php
require_once('../incs/functions.inc.php');
require_once('../incs/functions_major.inc.php');
@session_start();
$user_id = $_SESSION['user_id'];
$ret_arr = array();

function get_user_regions() {
	global $user_id;
	if((get_num_groups()) && (COUNT(get_allocates(4, $user_id)) > 1))  {	//	6/10/11
		$output = "";
		$output .="<form name='region_form' METHOD='post'><DIV class='even'><SPAN class='heading' style='text-align: center; width: 100%; display: inline-block;'>Regions</SPAN><BR /><BR />";
		$output .="<SPAN id='regs_conf_span' CLASS = 'message' style='text-align: center; color: blue;'></SPAN><BR />";
		$output .= "<DIV class='even' style='height: 300px; width: 98%; overflow-y: scroll; overflow-x: hidden; border: 1px outset #707070;'>";
		$output .= get_regions_buttons($user_id);
		$output .= "</DIV>";
		$output .= "<BR />";
		$output .= "<DIV style='text-align: center;'><SPAN ID='clr_spn' class='plain' style='float: none; display: inline-block;' onMouseOver='do_hover(this.id);' onMouseOut='do_plain(this.id);' onClick = \"do_clear(document.region_form, 'chk_spn', 'clr_spn')\">Un-check all</SPAN>";
		$output .= "<SPAN ID='chk_spn' class='plain' style='float: none; display: none;' onMouseOver='do_hover(this.id);' onMouseOut='do_plain(this.id);' onClick = \"do_check(document.region_form, 'chk_spn', 'clr_spn')\">Check all</SPAN></DIV><BR />";
		$output .= "<CENTER><SPAN id='reg_sub_but' class='plain' style='float: none;' onMouseOver='do_hover(this.id);' onMouseOut='do_plain(this.id);' onClick='form_validate(document.region_form);'>Update</SPAN></CENTER></DIV></form>";
		} else {
		$output = "No User Regions in use";
		}
	return $output;
	}
	
$ret_arr[0] = get_user_regions();

print json_encode($ret_arr);