<?php
error_reporting(E_ALL);				// 9/13/08
$units_side_bar_height = .6;		// max height of units sidebar as decimal fraction of screen height - default is 0.6 (60%)
require_once('./incs/functions.inc.php');

$the_inc = ((array_key_exists('internet', ($_SESSION))) && ($_SESSION['internet']))? './incs/functions_major.inc.php' : './incs/functions_major_nm.inc.php';
$the_level = (isset($_SESSION['level'])) ? $_SESSION['level'] : 0 ;
require_once($the_inc);

function get_markup($id) {
	$ret_arr = array();
	$query = "SELECT * FROM `{$GLOBALS['mysql_prefix']}mmarkup` WHERE `id` = " . $id;
	$result = mysql_query($query)or do_error($query,$query, mysql_error(), basename(__FILE__), __LINE__);
	if(mysql_num_rows($result) > 0) {
		$row = stripslashes_deep(mysql_fetch_assoc($result));
		$ret_arr['id'] = $row['id'];
		$ret_arr['name'] = $row['line_name'];
		$ret_arr['type'] = $row['line_type'];
		$ret_arr['status'] = $row['line_status'];
		$ret_arr['ident'] = $row['line_ident'];
		$ret_arr['cat'] = get_categoryName($row['line_cat_id']);
		$ret_arr['data'] = $row['line_data'];
		$ret_arr['color'] = $row['line_color'];
		$ret_arr['opacity'] = $row['line_opacity'];
		$ret_arr['width'] = $row['line_width'];
		$ret_arr['fill_color'] = $row['fill_color'];
		$ret_arr['fill_opacity'] = $row['fill_opacity'];
		$ret_arr['filled'] = $row['filled'];
		$ret_arr['updated'] = format_date_2($row['_on']);
		} else {
		$ret_arr['id'] = 0;
		}
	return $ret_arr;
	}
	
function get_categoryName($id) {
	$query = "SELECT * FROM `$GLOBALS[mysql_prefix]mmarkup_cats` WHERE `id`= " . $id . " LIMIT 1";
	$result = mysql_query($query);
	$row = stripslashes_deep(mysql_fetch_assoc($result));
	return $row['category'];
	}
?>
<SCRIPT>
window.onresize=function(){set_size();}

window.onload = function(){set_size();}

var mapWidth;
var mapHeight;
var listHeight;
var colwidth;
var listwidth;
var inner_listwidth;
var celwidth;
var res_celwidth;
var fac_celwidth;
var viewportwidth;
var viewportheight;
var colheight;
var outerwidth;
var outerheight;
var r_interval = null;
var latest_responder = 0;
var do_resp_update = true;
var responders_updated = new Array();

var baseIcon = L.Icon.extend({options: {shadowUrl: './our_icons/shadow.png',
	iconSize: [20, 32],	shadowSize: [37, 34], iconAnchor: [10, 31],	shadowAnchor: [10, 32], popupAnchor: [0, -20]
	}
	});
var baseFacIcon = L.Icon.extend({options: {iconSize: [28, 28], iconAnchor: [14, 29], popupAnchor: [0, -20]
	}
	});
var baseSqIcon = L.Icon.extend({options: {iconSize: [20, 20], iconAnchor: [10, 21], popupAnchor: [0, -20]
	}
	});
var basecrossIcon = L.Icon.extend({options: {iconSize: [40, 40], iconAnchor: [20, 41], popupAnchor: [0, -41]
	}
	});
			
var colors = new Array ('odd', 'even');

function set_size() {
	if (typeof window.innerWidth != 'undefined') {
		viewportwidth = window.innerWidth,
		viewportheight = window.innerHeight
		} else if (typeof document.documentElement != 'undefined'	&& typeof document.documentElement.clientWidth != 'undefined' && document.documentElement.clientWidth != 0) {
		viewportwidth = document.documentElement.clientWidth,
		viewportheight = document.documentElement.clientHeight
		} else {
		viewportwidth = document.getElementsByTagName('body')[0].clientWidth,
		viewportheight = document.getElementsByTagName('body')[0].clientHeight
		}
	mapWidth = viewportwidth * .40;
	mapHeight = viewportheight * .55;
	outerwidth = viewportwidth * .99;
	outerheight = viewportheight * .95;
	colwidth = outerwidth * .42;
	colheight = outerheight * .95;
	listHeight = viewportheight * .7;
	listwidth = colwidth * .95;
	inner_listwidth = listwidth *.9;
	celwidth = listwidth * .20;
	res_celwidth = listwidth * .15;
	fac_celwidth = listwidth * .15;
	$('outer').style.width = outerwidth + "px";
	$('outer').style.height = outerheight + "px";
	$('leftcol').style.width = colwidth + "px";
	$('leftcol').style.height = colheight + "px";	
	$('rightcol').style.width = colwidth + "px";
	$('rightcol').style.height = colheight + "px";	
	$('map_canvas').style.width = mapWidth + "px";
	$('map_canvas').style.height = mapHeight + "px";
	$('viewform').style.width = colwidth + "px";
	$('incs_table').style.width = mapWidth + "px";
	$('incs_heading').style.width = mapWidth + "px";
	map.invalidateSize();
	}
	
function contains(array, item) {
	for (var i = 0, I = array.length; i < I; ++i) {
		if (array[i] == item) return true;
		}
	return false;
	}

function validate(theForm) {						// Responder form contents validation	8/11/09
	if (theForm.frm_remove) {
		if (theForm.frm_remove.checked) {
			var str = "Please confirm removing '" + theForm.frm_name.value + "'";
			if(confirm(str)) 	{
				theForm.submit();					// 8/11/09
				return true;}
			else 				{return false;}
			}
		}
	var errmsg="";
							// 2/24/09, 3/24/10
	if (theForm.frm_name.value.trim()=="")													{errmsg+="Major Incident NAME is required.\n";}
	if (theForm.frm_type.options[theForm.frm_type.selectedIndex].value==0)					{errmsg+="Major Incident TYPE selection is required.\n";}			// 1/1/09

	if (theForm.frm_descr.value.trim()=="")													{errmsg+="Major Incident DESCRIPTION is required with Tracking.\n";}

	if (errmsg!="") {
		alert ("Please correct the following and re-submit:\n\n" + errmsg);
		return false;
		}
	else {																	// good to go!
		theForm.submit();													// 7/21/09
		}
	}				// end function validate(theForm)

</SCRIPT>
</HEAD>
<?php

$id = mysql_real_escape_string($_GET['id']);
$query	= "SELECT * FROM `$GLOBALS[mysql_prefix]major_incidents` WHERE `id`= " . $id;
$result	= mysql_query($query) or do_error($query, 'mysql_query() failed', mysql_error(), __FILE__, __LINE__);
$row = stripslashes_deep(mysql_fetch_assoc($result));
$lat = get_variable('def_lat');
$lng = get_variable('def_lng');
$existing_incs = array();
$query_x = "SELECT * FROM `$GLOBALS[mysql_prefix]mi_x` WHERE `mi_id` = " . $id . " ORDER BY `id`;";
$result_x = mysql_query($query_x) or do_error($query_x, 'mysql query failed', mysql_error(),basename( __FILE__), __LINE__);
while ($row_x = stripslashes_deep(mysql_fetch_assoc($result_x))) {
	$existing_incs[] = $row_x['ticket_id'];
	}

?>
<BODY>
<SCRIPT TYPE="text/javascript" src="./js/wz_tooltip.js"></SCRIPT>
<A NAME='top'></A>
	<DIV ID='to_bottom' style='position:fixed; top:2px; left:50px; height: 12px; width: 10px;' onclick = 'to_bottom()'><IMG SRC='markers/down.png'  BORDER=0 /></DIV>
	<DIV id='outer' style='position: absolute; left: 0px; top: 20px;'>
		<DIV id='leftcol' style='position: absolute; left: 10px;'>
			<A NAME='top'>		<!-- 11/11/09 -->
			<TABLE ID='viewform'>
				<TR>
					<TD ALIGN='center' COLSPAN='2'><FONT CLASS='header'><FONT SIZE=-1><FONT COLOR='green'>&nbsp;View Major Incident '<?php print $row['name'];?>' data</FONT>&nbsp;&nbsp;(#<?php print $id; ?>)</FONT></FONT><BR /><BR />
						<FONT SIZE=-1>(mouseover caption for help information)</FONT></FONT>
					</TD>
				</TR>
				<TR class='spacer'>
					<TD class='spacer' COLSPAN=99>&nbsp;</TD>
				</TR>	
				<TR CLASS = "odd">
					<TD CLASS="td_label">
						<A CLASS="td_label" HREF="#" TITLE="Major Incident Name">Major Incident Name</A>:
					</TD>			
					<TD CLASS='td_data'><?php print $row['name'] ;?></TD>
				</TR>
				<TR class='spacer'>
					<TD class='spacer' COLSPAN=99>&nbsp;</TD>
				</TR>
				<TR CLASS = "odd">
					<TD CLASS="td_label"><A CLASS="td_label" HREF="#" TITLE="Major Incident Start Time / Date">Start Date/Time</A>:&nbsp;</TD>
					<TD CLASS="td_data"><?php print format_date_2(strtotime($row['inc_startime']));?></TD>
				</TR>
				<TR CLASS = "even">
					<TD CLASS="td_label"><A CLASS="td_label" HREF="#" TITLE="Major Incident End Time / Date">End Date/Time</A>:&nbsp;</TD>
					<TD CLASS="td_data">
<?php 
						if(is_date($row['inc_endtime'])) {
							print format_date_2(strtotime($row['inc_endtime']));
							} else {
							print "Not Closed";
							}
?>
					</TD>
				</TR>
				<TR CLASS='odd' VALIGN="top">	<!--  6/10/11 -->
					<TD CLASS="td_label"><A CLASS="td_label" HREF="#" TITLE="Boundary for this Major Incident"><?php print get_text("Boundary");?></A>:</TD>
					<TD CLASS="td_data">
<?php
						if($row['boundary'] > 0) {
							$boundary = get_markup($row['boundary']);
							print $boundary['name'];
							} else {
							print "";
							}
?>
					</TD>
				</TR>
				<TR class='spacer'>
					<TD class='spacer' COLSPAN=99>&nbsp;</TD>
				</TR>	
				<TR CLASS='even' VALIGN="top">	<!--  6/10/11 -->
					<TD CLASS="td_label"><A CLASS="td_label" HREF="#"  TITLE="<?php print get_text("Gold Command");?>"><?php print get_text("Gold Command");?></A>:</TD>
					<TD CLASS="td_data"><SPAN class='heading' style='width: 100%; display: block;'><?php print get_owner($row['gold']);?></SPAN>
						<TABLE>
							<TR>
								<TD class='td_label'>Email 1</TD>
								<TD class='td_data'>
<?php 
									if($comm_arr[$row['gold']]) {
										print $comm_arr[$row['gold']][4];
										}
?>
								</TD>
							</TR>
							<TR>
								<TD class='td_label'>Email 2</TD>
								<TD class='td_data'>
<?php 
									if($comm_arr[$row['gold']]) {
										print $comm_arr[$row['gold']][5];
										}
?>
								</TD>
							</TR>
							<TR>
								<TD class='td_label'>Phone 1</TD>
								<TD class='td_data'>
<?php 
									if($comm_arr[$row['gold']]) {
										print $comm_arr[$row['gold']][6];
										}
?>
								</TD>
							</TR>
							<TR>
								<TD class='td_label'>Phone 2</TD>
								<TD class='td_data'>
<?php 
									if($comm_arr[$row['gold']]) {
										print $comm_arr[$row['gold']][7];
										}
?>
								</TD>
							</TR>
						</TABLE>
					</TD>
				</TR>
				<TR CLASS='odd' VALIGN="top">	<!--  6/10/11 -->
					<TD CLASS="td_label"><A CLASS="td_label" HREF="#"  TITLE="<?php print get_text("Silver Command");?>"><?php print get_text("Silver Command");?></A>:</TD>
					<TD CLASS="td_data"><SPAN class='heading' style='width: 100%; display: block;'><?php print get_owner($row['silver']);?></SPAN>
						<TABLE>
							<TR>
								<TD class='td_label'>Email 1</TD>
								<TD class='td_data'>
<?php 
									if($comm_arr[$row['silver']]) {
										print $comm_arr[$row['silver']][4];
										}
?>
								</TD>
							</TR>
							<TR>
								<TD class='td_label'>Email 2</TD>
								<TD class='td_data'>
<?php 
									if($comm_arr[$row['silver']]) {
										print $comm_arr[$row['silver']][5];
										}
?>
								</TD>
							</TR>
							<TR>
								<TD class='td_label'>Phone 1</TD>
								<TD class='td_data'>
<?php 
									if($comm_arr[$row['silver']]) {
										print $comm_arr[$row['silver']][6];
										}
?>
								</TD>
							</TR>
							<TR>
								<TD class='td_label'>Phone 2</TD>
								<TD class='td_data'>
<?php 
									if($comm_arr[$row['silver']]) {
										print $comm_arr[$row['silver']][7];
										}
?>
								</TD>
							</TR>
						</TABLE>
					</TD>
				</TR>
				<TR CLASS='even' VALIGN="top">	<!--  6/10/11 -->
					<TD CLASS="td_label"><A CLASS="td_label" HREF="#"  TITLE="<?php print get_text("Bronze Command");?>"><?php print get_text("Bronze Command");?></A>:</TD>
					<TD CLASS="td_data"><SPAN class='heading' style='width: 100%; display: block;'><?php print get_owner($row['bronze']);?></SPAN>
						<TABLE>
							<TR>
								<TD class='td_label'>Email 1</TD>
								<TD class='td_data'>
<?php 
									if(isset($comm_arr[$row['bronze']])) {
										print $comm_arr[$row['bronze']][4];
										}
?>
								</TD>
							</TR>
							<TR>
								<TD class='td_label'>Email 2</TD>
								<TD class='td_data'>
<?php 
									if(isset($comm_arr[$row['bronze']])) {
										print $comm_arr[$row['bronze']][5];
										}
?>
								</TD>
							</TR>
							<TR>
								<TD class='td_label'>Phone 1</TD>
								<TD class='td_data'>
<?php 
									if(isset($comm_arr[$row['bronze']])) {
										print $comm_arr[$row['bronze']][6];
										}
?>
								</TD>
							</TR>
							<TR>
								<TD class='td_label'>Phone 2</TD>
								<TD class='td_data'>
<?php 
									if(isset($comm_arr[$row['bronze']])) {
										print $comm_arr[$row['bronze']][7];
										}
?>
								</TD>
							</TR>
						</TABLE>				
					</TD>
				</TR>					
				<TR class='spacer'>
					<TD class='spacer' COLSPAN=99>&nbsp;</TD>
				</TR>		
				<TR CLASS = "even">
					<TD CLASS="td_label">
						<A CLASS="td_label" HREF="#" TITLE="Unit Description - additional details about unit">Description</A>:&nbsp;<font color='red' size='-1'>*</font>
					</TD>	
					<TD CLASS="td_data_wrap"COLSPAN=3><?php print $row['description'];?></TD>
				</TR>
				<TR class='spacer'>
					<TD class='spacer' COLSPAN=99>&nbsp;</TD>
				</TR>	
				<TR CLASS="odd" style='height: 30px; vertical-align: middle;'>
					<TD COLSPAN="2" ALIGN="center" style='vertical-align: middle;'>
						<SPAN id='can_but' CLASS='plain' style='float: none; width: 100px; display: inline-block;' onMouseover='do_hover(this.id);' onMouseout='do_plain(this.id);' onClick='document.can_Form.submit();'>Cancel</SPAN>
						<SPAN id='ed_but' CLASS='plain' style='float: none; width: 100px; display: inline-block;' onMouseover='do_hover(this.id);' onMouseout='do_plain(this.id);' onClick='document.to_edit_Form.submit();'>Edit</SPAN>
					</TD>
				</TR>
			</TABLE>
		</DIV>
		<DIV id='rightcol' style='position: absolute; right: 2%; z-index: 1;'>
			<DIV id='map_canvas' style='border: 1px outset #707070;'></DIV>
			<BR /><BR />
			<DIV id='incs_heading' class='heading' style='text-align: center;'>Incidents to be managed as part of the Major Incident (click to view)</DIV>
			<DIV id= 'incs_table' style = 'max-height: 400px; border: 1px outset #707070; overflow-y: scroll;'>
				<TABLE style='width: 100%;'>
					<TR class='plain_listheader' style='width: 100%;'>
						<TH class='plain_listheader' style='text-align: left;'>Scope</TH>
						<TH class='plain_listheader' style='text-align: left;'>Opened</TH>
						<TH class='plain_listheader' style='text-align: left;'>Units Assigned</TH>
						<TH class='plain_listheader' style='text-align: left;'>Elapsed</TH>
					</TR>
<?php
						if(count($existing_incs) != 0) {
							$class = "even";
							foreach($existing_incs AS $val) {
								$query_inc = "SELECT *, 
									(SELECT  COUNT(*) as numfound FROM `$GLOBALS[mysql_prefix]assigns` 
									WHERE `$GLOBALS[mysql_prefix]assigns`.`ticket_id` = `$GLOBALS[mysql_prefix]ticket`.`id`) AS `units_assigned` 	
									FROM `$GLOBALS[mysql_prefix]ticket` WHERE `$GLOBALS[mysql_prefix]ticket`.`id` = " . $val;
								$result_inc = mysql_query($query_inc) or do_error($query_inc, 'mysql query failed', mysql_error(),basename( __FILE__), __LINE__);
								$class = "even";
								$row_inc = stripslashes_deep(mysql_fetch_assoc($result_inc));
								$the_id = $row_inc['id'];
								$elapsed = get_elapsed_time($row_inc);
								print "<TR class='" . $class . "'  style='width: 100%;' onClick='do_popup(" . $the_id . ");'>";
								print "<TD class='plain_list'>" . $row_inc['scope'] . "</TD>";
								print "<TD class='plain_list'>" . format_date_2($row_inc['problemstart']) . "</TD>";
								print "<TD class='plain_list' style='text-align: left;'>" . $row_inc['units_assigned'] . "</TD>";
								print "<TD class='plain_list'>" . $elapsed . "</TD>";										
								print "</TR>";
								$class = ($class == "even") ? "odd" : "even";
								}
							} else {
							print "<TR class='plain_list' style='width: 100%;'><TD COLSPAN = 99 style='text-align: center;'>No Incidents set</TD></TR>";
							}										
?>
				</TABLE>
			</DIV>
		</DIV>
	</DIV>
<?php
$allow_filedelete = ($the_level == $GLOBALS['LEVEL_SUPER']) ? TRUE : FALSE;
print add_sidebar(FALSE, TRUE, TRUE, FALSE, $allow_filedelete, 0, 0, 0, $row['id']);
?>
<FORM NAME='can_Form' METHOD="post" ACTION = "maj_inc.php"></FORM>
<FORM NAME="to_edit_Form" METHOD="post" ACTION = "maj_inc.php?edit=true&id=<?php print $id; ?>"></FORM>
<A NAME="bottom" />
<DIV ID='to_top' style="position:fixed; bottom:50px; left:50px; height: 12px; width: 10px;" onclick = "location.href = '#top';"><IMG SRC="markers/up.png"  BORDER=0></div>
<SCRIPT>
var latLng;
var tmarkers = [];	//	Incident markers array
var rmarkers = [];	//	Responder markers array
var boundary = [];			//	exclusion zones array
var bound_names = [];
var mapWidth = <?php print get_variable('map_width');?>+20;
var mapHeight = <?php print get_variable('map_height');?>+20;
$('map_canvas').style.width = mapWidth + "px";
$('map_canvas').style.height = mapHeight + "px";
var boundary = [];			//	exclusion zones array
var bound_names = [];
var theLocale = <?php print get_variable('locale');?>;
var useOSMAP = <?php print get_variable('use_osmap');?>;
init_map(1, <?php print $lat;?>, <?php print $lng;?>, "", 13, theLocale, useOSMAP, "tr");
map.setView([<?php print $lat;?>, <?php print $lng;?>], 13);
var bounds = map.getBounds();	
var zoom = map.getZoom();

<?php
do_kml();

foreach($existing_incs AS $val) {
	$query_tick = "SELECT *	FROM `$GLOBALS[mysql_prefix]ticket` WHERE `id` = " . $val . " ORDER BY `id` ASC";
	$result_tick = mysql_query($query_tick) or do_error($query_tick, 'mysql query failed', mysql_error(), basename( __FILE__), __LINE__);
	$mi_num_tick = mysql_num_rows($result_tick);
	while ($row_tick = stripslashes_deep(mysql_fetch_assoc($result_tick))) {
?>
		var marker = createTicMarker(<?php print $row_tick['lat'];?>, <?php print $row_tick['lng'];?>, "Ticket: <?php print $row_tick['scope'];?><BR />Major Incident: <?php print $row['name'];?>", <?php print $row_tick['severity'];?>, <?php print $row_tick['id'];?>, <?php print $row['id'];?>, "<?php print $row_tick['scope'];?>");
		marker.addTo(map);
<?php
		$query_resp = "SELECT *, 
			`r`.`id` AS `resp_id`,
			`r`.`lat` AS `resp_lat`,
			`r`.`lng` AS `resp_lng`,
			`r`.`handle` AS `resp_handle`
			FROM `$GLOBALS[mysql_prefix]assigns` `a` 
			LEFT JOIN `$GLOBALS[mysql_prefix]responder` `r` ON ( `a`.`responder_id` = `r`.`id` )
			WHERE `a`.`ticket_id` = " . $val . " ORDER BY `resp_id` ASC";
		$result_resp = mysql_query($query_resp) or do_error($query_resp, 'mysql query failed', mysql_error(), basename( __FILE__), __LINE__);
		$mi_num_resp = mysql_num_rows($result_resp);
		while ($row_resp = stripslashes_deep(mysql_fetch_assoc($result_resp))) {
?>
			var rmarker = createRespMarker(<?php print $row_resp['resp_lat'];?>, <?php print $row_resp['resp_lng'];?>, <?php print $row_resp['resp_id'];?>, <?php print $row['id'];?>, "<?php print $row_resp['resp_handle'];?>")
			rmarker.addTo(map);
<?php
			}
		}
	}
if($row['boundary'] > 0) {
	$theBound = get_markup($row['boundary']);
?>
	var theID = <?php print $theBound['id'];?>;
	var theLinename = "<?php print $theBound['name'];?>";
	var theIdent = "<?php print $theBound['ident'];?>";
	var theCategory = "<?php print $theBound['cat'];?>";
	var theData = "<?php print $theBound['data'];?>";
	var theColor = "<?php print '#' . $theBound['color'];?>";
	var theOpacity = <?php print $theBound['opacity'];?>;
	var theWidth = <?php print $theBound['width'];?>;
	var theFilled = <?php print $theBound['filled'];?>;
	var theFillcolor = "<?php print '#' . $theBound['fill_color'];?>";
	var theFillopacity = <?php print $theBound['fill_opacity'];?>;
	var theType = "<?php print $theBound['type'];?>";
	if(theType == "p") {
		var polygon = draw_poly(theLinename, theCategory, theColor, theOpacity, theWidth, theFilled, theFillcolor, theFillopacity, theData, "basemarkup", theID);
		} else if(theType == "c") {
		var circle = drawCircle(theLinename, theData, theColor, theWidth, theOpacity, theFilled, theFillcolor, theFillopacity, "basemarkup", theID);
		} else if(theType == "t") {
		var banner = drawBanner(theLinename, theData, theWidth, theColor, "basemarkup", theID);
		}
<?php
	}
?>
</SCRIPT>
</BODY>
</HTML>

