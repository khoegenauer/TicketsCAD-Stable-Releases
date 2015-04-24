<?php
/*
1/3/14 - new file, lists road condition alerts for plotting on situation screen map
3/31/2015 - corrected field-name in initial sql 'on' clause
*/
@session_start();
require_once('../incs/functions.inc.php');
@session_start();
$iw_width= "300px";					// map infowindow with
$ret_arr = array();
$ret_arr[0][0] = 0;

$query = "SELECT *,
		`r`.`id` AS `cond_id`,
		`c`.`id` AS `type_id`,
		`r`.`description` AS `r_description`,
		`c`.`description` AS `type_description`,
		`r`.`title` AS `r_title`,
		`c`.`title` AS `type_title`,
		`c`.`icon`AS `icon_url`,
		`r`.`_on` AS `updated`
		FROM `$GLOBALS[mysql_prefix]roadinfo` `r` 
		LEFT JOIN `$GLOBALS[mysql_prefix]conditions` `c` ON `r`.`description`=`c`.`id`
		WHERE `r`.`_on` >= (NOW() - INTERVAL 2 DAY) ORDER BY `cond_id`";
$result = mysql_query($query) or do_error('', 'mysql query failed', mysql_error(), basename( __FILE__), __LINE__);
$z=0;
while ($row = stripslashes_deep(mysql_fetch_assoc($result))){

// tab 1
		if (my_is_float($row['lat'])) {										// position data? 4/29/09
			$theTabs = "<div class='infowin'><BR />";
			$theTabs .= '<div class="tabBox" style="float: left; width: 100%;">';
			$theTabs .= '<div class="tabArea">';
			$theTabs .= '<span id="tab1" class="tabinuse" style="cursor: pointer;" onClick="do_tab(\'tab1\', 1, null, null);">Summary</span>';
			$theTabs .= '</div>';
			$theTabs .= '<div class="contentwrapper">';
			
			$tab_1 = "<TABLE width='{$iw_width}' style='height: 280px;'><TR><TD><TABLE>";			
			$tab_1 .= "<TR CLASS='even'><TD COLSPAN=2 ALIGN='center'><B>" . $row['r_title'] . "</B></TD></TR>";
			$tab_1 .= "<TR CLASS='odd'><TD>Description:</TD><TD>" . $row['type_title'] . "</TD></TR>";
			$tab_1 .= "<TR CLASS='even'><TD>Status:</TD><TD>" . stripslashes_deep($row['address']) . " </TD></TR>";
			$tab_1 .= "<TR CLASS='odd'><TD>Contact:</TD><TD>" . stripslashes_deep($row['r_description']) . "</TD></TR>";
			$tab_1 .= "<TR CLASS='even'><TD>As of:</TD><TD>" . format_date_2(strtotime($row['updated'])) . "</TD></TR>";		// 4/11/10
			$tab_1 .= "</TABLE></TD></TR></TABLE>";
			
		$theTabs .= "<div class='content' id='content1' style = 'display: block;'>" . $tab_1 . "</div>";
		$theTabs .= "</div>";
		$theTabs .= "</div>";
		$theTabs .= "</div>";
		}

	$ret_arr[$z][0] = $row['cond_id'];
	$ret_arr[$z][1] = $row['r_title'];	
	$ret_arr[$z][2] = $row['type_title'];	
	$ret_arr[$z][3] = stripslashes_deep($row['address']);
	$ret_arr[$z][4] = stripslashes_deep($row['r_description']);
	$ret_arr[$z][5] = stripslashes_deep($row['icon_url']);
	$ret_arr[$z][6] = format_date_2(strtotime($row['updated']));
	$ret_arr[$z][7] = $row['lat'];
	$ret_arr[$z][8] = $row['lng'];		
	$ret_arr[$z][9] = $theTabs;
	$z++;
	} // end while
//dump($ret_arr);
print json_encode($ret_arr);
exit();
?>
