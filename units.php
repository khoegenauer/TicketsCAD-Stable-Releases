<?php
error_reporting(E_ALL);

$units_side_bar_height = .5;		// max height of units sidebar as decimal fraction of screen height - default is 0.6 (60%)
$zoom_tight = FALSE;				// replace with a decimal number to over-ride the standard default zoom setting
$iw_width= "300px";					// map infowindow with
$groupname = isset($_SESSION['group_name']) ? $_SESSION['group_name'] : "";	//	4/11/11

$the_resp_id = (isset($_GET['id']))? $_GET['id']: 0;	//	11/18/13
/*
5/23/08	added check for associated assign records before allowing deletions line 843 area
5/29/08	addded do_kml callsu
6/1/08	revised 'type' display
6/1/08	added latest APRS data to 'view' display
6/2/08  do_log revisions for explicit parameter values
6/7/08  added show incidents for dispatch -
6/10/08 added js trim function numerous JS checks
6/11/08 revised mobile/fixed JS datatypes to radio buttons
6/15/08	revised terminology to 'Station', 'Mobile unit'
6/17/08	added tracks handling for date/time, per tracks.php
6/25/08	added APRS window handling
6/27/08	added condionality for Dispatch button
6/27/08	added conditional to detect active dispatches
8/02/08	added link to dispatch function in infowindow
8/3/08	added dispatch target to unit infowin
8/23/08	added usng position, with 'other' as repository
8/23/08	corrected theForm.frm_m_or_f disable
8/24/08 added TITLE to TD's
8/26/08 added default lat/lng to new mobile units (needs usng conversions)
9/3/08  NULL to mobile units center
9/9/08	added selectable lat/lng formats
9/13/08	LLtoUSNG implemented, replacing 8/23/08 rev's above
10/4/08 bypass position check if mode = edit
10/4/08 set auto-refresh if APRS active
10/6/08	renamed validate_res
10/6/08	bypass map check if in edit.
10/8/08	operator-level may now create/edit Units
10/14/08 added grid function
10/15/08 added check for empty arguments
10/15/08 $frm_ngs removed fm update/insert
10/16/08 changed ticket_id to frm_ticket_id
10/25/08 unchanged 10/16/08 entry
11/3/08 single call for graticule.js, relocated gmaps call
12/20/08 $ function added
12/24/08 edit position data for fixed stations only
12/25/08 changed to settings-driven unit types, set tightest zoom
12/27/08 revised unit types logic
1/1/09   converted to variable unit-types
1/5/09   aprs added to u_types array
1/5/09   letters for icons and sidebar
1/21/09 top.calls.start added, aprs moved from unit type to responder schema
1/23/09 revised aprs unit handling, auto refresh
1/27/09 corrections re mobile, aprs
2/2/09 fixes for bad unit_status values
2/13/09 added to_str()  for # units > 25
2/24/09 allow NULL coords, accommodate blank status vals
2/25/09 disable directions
3/3/09 added UL for no-mapped units
3/13/09 instamapper handling added
3/16/09 assignments added, get curent re aprs, instam
3/18/09 'aprs_poll' to 'auto_poll'
3/19/09 'directions' added
3/22/09 added 2nd tab for instamapper data
4/2/09 added fixed default map bounds
4/9/09 restored directions
4/10/09 street view added
4/29/09 my_is_float replaces is_float
4/27/09 multi added to schema, addslashes repl htmlentities
6/13/09 added mail function
6/18/09 addslashes added in function list_responders()
7/9/09  rearrange form fields
7/10/09 tracking to <select>
7/21/09 changed to onClick from onSubmit, other corrections
7/24/09 Changed function do_tracking to explicitly select all of the states for all choices (turn one on and others off or all off if none selected).
7/24/09 Changed Infowindow second tab lable to show name and last three digits of tracking ID to improve screen display.
7/24/09 Added code to get gtrack URL from settings table and check for valid entry - if no valid entry don't show Gtrack tracking option.
7/29/09 Added Handle field to form, changed display of tracking type to text from disabled Select menu.
7/29/09 Modified code to get tracking data, updated time and speed to fix errors. variable for updated and speed is now set before query result is unset.
8/1/09	corrections to unit delete, dispatch any unit
8/2/09 Added code to get maptype variable and switch to change default maptype based on variable setting
8/3/09 Added code to get locale variable and change USNG/OSGB/UTM dependant on variable in tabs and sidebar.
8/8/09 'handle' made optional
8/10/09	locale = 2 dropped, default added
8/11/09	validate() rewritten
8/12/09	corrected delete 
8/17/09	added mail link to window, other corrections
9/11/09	corr's for unit type edit, refresh limited to view operation - by AS
10/6/09 Added route to facility, added links button
10/8/09 Index in list and on marker changed to part of name after /
10/8/09 Added Display name to remove part of name after / in name field of sidebar and in infotabs
10/29/09 Removed Period after index in sidebar
11/11/09 Fixed sidebar when not using map location, 'top' anchor added.
11/15/09 added map position 'clear' option, corrections to 'select incidents for dispatch'
11/17/09 limited access to edit functions to super, admin
11/20/09 display sort order
1/2/10 corrected to disallow guest dispatch, added street view to edit page
1/7/10 fixed div for sidebar
1/23/10 refresh meta removed 
3/11/10 added function get_un_div_height () 
3/15/10 front table re-organized for legibility, color-coding unit backgrounds and status values
3/24/10 function validate - location requirement added for non-mobile units
4/11/10 fix to 'as of' source
4/14/10 added in-line status update, misc_function.js
5/11/10 disallow user/operator add unit
5/12/10 added quote to force ident as string - subtle!
7/5/10 Added Location fields and phone number fields as for Incident. Geocoding of address and reverse geocoding of map click implemented.
7/7/10 `clear` is NULL OR ... added to queries 
7/15/10 'NULL'  handling fixes applied
7/18/10 look for open assigns only
7/22/10 NULL handling revised, miscjs, google reverse geocode parse added
7/28/10 Added inclusion of startup.inc.php for checking of network status and setting of file name variables to support no-maps versions of scripts.
7/28/10 Added default icon for tickets entered in no maps operation
8/10/10 up arrow re-located
8/13/10 map.setUIToDefault();	
8/15/10 increased track_u window size 
8/25/10 light top-frame button
8/29/10 dispatch status added
8/31/10 routesfile correction
10/28/10 Added include and function calls for addon modules. 
3/15/11 added reference to stylesheet.php for revisable day night colors.
3/19/11 index expanded to 6 chars
4/22/11 addslashes added
4/25/11 icon handling added
4/27/11 name field size increased
4/5/11 get_new_colors added
5/8/11 cleaned up tracked systems captions
6/10/11 Added groups and boundaries
8/1/11 state length increased to 4 chars
11/8/11 Added inclusion of lat_lng.js file for OSGB Javascript function.
3/14/12 added unit to quarters logic - i. e., home facility
3/24/12 accommodate OGTS in validate()
6/18/12 'points' boolean to 'got_points'
6/20/12 applied get_text() to Units
7/20/12 changed 'updated' unixtime to mysql timestamp, to_home disabled
9/4/12 add reset corrected
5/30/13 Implement catch for when there are no allocated regions for current user. 
5/31/2013 track speed display correction applied - for consistency with sit-screen
6/13/13 revised to remove id conflict on despatch from unit.
6/21/13 Added "Status_updated" field. Used for Auto status functionality
7/2/2013 revised to use updated as server timestamp vs packet timestamp
7/2/13 Revised SQL in query that builds Ticket list to dispatch
9/6/13 Added Native "Mobile Tracker" to tracking options, also status about field and Roster User
9/10/13 Added Unit Log functions
11/18/13 Fix to include previously removed (in error) messaging code
11/18/13 Fixed extra spurious assigned count at top of units list.
1/30/14 Added tracking for APRS via XASTIR.
*/

@session_start();	

if (!($_SESSION['internet'])) {				// 8/22/10
	header("Location: units_nm.php");
	}

$tester = (((isset($_REQUEST['edit'])) && $_REQUEST['edit'] == TRUE) || ((isset($_REQUEST['add'])) && ($_REQUEST['add'] == TRUE)) || ((isset($_REQUEST['view'])) && ($_REQUEST['view'] == TRUE))) ? 0 : 1;

require_once($_SESSION['fip']);		//7/28/10
$column_arr = explode(',', get_msg_variable('columns'));	//	11/18/13
if(file_exists("./incs/modules.inc.php")) {	//	10/28/10
	require_once('./incs/modules.inc.php');
	}
do_login(basename(__FILE__));

require_once('./incs/messaging.inc.php');	//	11/18/13
$key_field_size = 30;						// 7/23/09
$st_size = (get_variable("locale") ==0)?  2: 4;		
$gt_handle = get_text("Handle");			// 7/20/12
$gt_unit = get_text("Unit");
$gt_as_of = get_text("As of");
$gt_dispatched = get_text("Dispatched");
$gt_status = get_text("Status");

//$tolerance = 5 * 60;		// nr. seconds report time may differ from UTC

$RespID = (isset($_GET['id'])) ? $_GET['id'] : 0;	

extract($_GET);
extract($_POST);
/*
if((($istest)) && (!empty($_GET))) {dump ($_GET);}
if((($istest)) && (!empty($_POST))) {dump ($_POST);}
*/
$remotes = get_current();		// returns array - 3/16/09

if(($_SESSION['level'] == $GLOBALS['LEVEL_UNIT']) && (intval(get_variable('restrict_units')) == 1)) {
	print "Not Authorized";
	exit();
	}

$u_types = array();												// 1/1/09
$query = "SELECT * FROM `$GLOBALS[mysql_prefix]unit_types` ORDER BY `id`";		// types in use
$result = mysql_query($query) or do_error($query, 'mysql query failed', mysql_error(), basename( __FILE__), __LINE__);
while ($row = stripslashes_deep(mysql_fetch_assoc($result))) {
	$u_types [$row['id']] = array ($row['name'], $row['icon']);		// name, index, aprs - 1/5/09, 1/21/09
	}

unset($result);

$icons = $GLOBALS['icons'];				// 1/1/09
$sm_icons = $GLOBALS['sm_icons'];

function get_icon_legend (){			// returns legend string - 1/1/09
	global $u_types, $sm_icons;
	$query = "SELECT DISTINCT `type` FROM `$GLOBALS[mysql_prefix]responder` ORDER BY `name`";
	$result = mysql_query($query) or do_error($query, 'mysql query failed', mysql_error(), basename( __FILE__), __LINE__);
	$print = "";											// output string
	while ($row = stripslashes_deep(mysql_fetch_assoc($result))) {
		$temp = $u_types[$row['type']];
		$print .= "\t\t<SPAN class='legend' style='height: 3em; text-align: center; vertical-align: middle; float: none;'> ". $temp[0] . " &raquo; <IMG SRC = './our_icons/" . $sm_icons[$temp[1]] . "' STYLE = 'vertical-align: middle' BORDER=0 PADDING='10'>&nbsp;&nbsp;&nbsp;</SPAN>";
		}
	return $print;
	}			// end function get_icon_legend ()
	
function get_roster($current=null) {	//	9/6/13
	$query = "SELECT * FROM `$GLOBALS[mysql_prefix]personnel` ORDER BY `person_identifier`";
	$result = mysql_query($query) or do_error($query, 'mysql query failed', mysql_error(), basename( __FILE__), __LINE__);
	$the_ret = "<SELECT NAME='frm_roster_id' onChange = 'get_roster_details(this.form, this.options[this.selectedIndex].value);' >";
	$the_ret .= "<OPTION VALUE='0' SELECTED>Select a Person</OPTION>";	
	while ($row = stripslashes_deep(mysql_fetch_assoc($result))) {
		$sel = (($current) && ($current == $row['id'])) ? "SELECTED " : "";
		$the_ret .= "<OPTION VALUE=" .  $row['id'] . " " . $sel . ">" . $row['person_identifier'] . "</OPTION>";
		}
	$the_ret .= "</SELECT>";
	return $the_ret;
	}

function get_user_details($rosterID) {	//	9/6/13
	$query = "SELECT * FROM `$GLOBALS[mysql_prefix]personnel` WHERE `id` = '" . $rosterID . "' LIMIT 1"; 
	$result = mysql_query($query) or do_error('', 'mysql query failed', mysql_error(), basename( __FILE__), __LINE__);
	if(mysql_affected_rows() != 0) {
		$row = stripslashes_deep(mysql_fetch_assoc($result));
		$the_ret =  "Name: " . $row['forenames'] . " " . $row['surname'] . "<BR />";
		$the_ret .= "Street: " . $row['address'] . "<BR />";
		$the_ret .= "State: " . $row['state'] . "<BR />";
		$the_ret .= "Email: " . $row['email'] . "<BR />";
		$the_ret .= "Home phone: " . $row['homephone'] . "<BR />";
		$the_ret .= "Work Phone: " . $row['workphone'] . "<BR />";
		$the_ret .= "Cellphone: " . $row['cellphone'] . "<BR />";
		} else {
		$the_ret = "";
		}
	return $the_ret;
	}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<HEAD><TITLE>Tickets - Units Module</TITLE>
	<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=UTF-8">
	<META HTTP-EQUIV="Expires" CONTENT="0">
	<META HTTP-EQUIV="Cache-Control" CONTENT="NO-CACHE">
	<META HTTP-EQUIV="Pragma" CONTENT="NO-CACHE">
	<META HTTP-EQUIV="Content-Script-Type"	CONTENT="text/javascript">
	<META HTTP-EQUIV="Script-date" CONTENT="<?php print date("n/j/y G:i", filemtime(basename(__FILE__)));?>">
	<LINK REL=StyleSheet HREF="stylesheet.php?version=<?php print time();?>" TYPE="text/css">	<!-- 3/15/11 -->
	<link rel="stylesheet" href="./js/leaflet/leaflet.css" />
	<!--[if lte IE 8]>
		 <link rel="stylesheet" href="./js/leaflet/leaflet.ie.css" />
	<![endif]-->
	<link rel="stylesheet" href="./js/leaflet-openweathermap.css" />
	<STYLE>
		.disp_stat	{ FONT-WEIGHT: bold; FONT-SIZE: 9px; COLOR: #FFFFFF; BACKGROUND-COLOR: #000000; FONT-FAMILY: Verdana, Arial, Helvetica, sans-serif;}
		table.cruises { font-family: verdana, arial, helvetica, sans-serif; font-size: 11px; cellspacing: 0; border-collapse: collapse; }
		table.cruises td {overflow: hidden; }
		div.scrollableContainer { position: relative; padding-top: 1.8em; border: 1px solid #999; }
		div.scrollableContainer2 { position: relative; padding-top: 1.3em; }
		div.scrollingArea { max-height: 240px; overflow: auto; overflow-x: hidden; }
		div.scrollingArea2 { max-height: 480px; overflow: auto; overflow-x: hidden; }
		table.scrollable thead tr { left: -1px; top: 0; position: absolute; }
		table.cruises th { text-align: left; border-left: 1px solid #999; background: #CECECE; color: black; font-weight: bold; overflow: hidden; }
		.olPopupCloseBox{background-image:url(img/close.gif) no-repeat;cursor:pointer;}	
		div.tabBox {}
		div.tabArea { font-size: 80%; font-weight: bold; padding: 0px 0px 3px 0px; }
		span.tab { background-color: #CECECE; color: #8060b0; border: 2px solid #000000; border-bottom-width: 0px; -moz-border-radius: .75em .75em 0em 0em;	border-radius-topleft: .75em; border-radius-topright: .75em;
				padding: 2px 1em 2px 1em; position: relative; text-decoration: none; top: 3px; z-index: 100; }
		span.tabinuse {	background-color: #FFFFFF; color: #000000; border: 2px solid #000000; border-bottom-width: 0px;	border-color: #f0d0ff #b090e0 #b090e0 #f0d0ff; border-radius: .75em .75em 0em 0em;
				border-radius-topleft: .75em; border-radius-topright: .75em; padding: 2px 1em 2px 1em; position: relative; text-decoration: none; top: 3px;	z-index: 100;}
		span.tab:hover { background-color: #FEFEFE; border-color: #c0a0f0 #8060b0 #8060b0 #c0a0f0; color: #ffe0ff;}
		div.content { font-size: 80%; background-color: #F0F0F0; border: 2px outset #707070; border-radius: 0em .5em .5em 0em;	border-radius-topright: .5em; border-radius-bottomright: .5em; padding: .5em;
				position: relative;	z-index: 101; cursor: auto; height: 250px;}
		div.contentwrapper { width: 260px; background-color: #F0F0F0; cursor: auto;}
	</STYLE>
	<SCRIPT TYPE="text/javascript" SRC="./js/misc_function.js"></SCRIPT>	<!-- 5/3/11 -->	
	<SCRIPT TYPE="text/javascript" SRC="./js/domready.js"></script>
	<SCRIPT SRC="./js/messaging.js" TYPE="text/javascript"></SCRIPT><!-- 10/23/12-->
<?php 
if ($_SESSION['internet']) {				// 8/22/10
?>
	<script type="text/javascript" src="./js/geotools2.js"></script>
	<script src="./js/usng.js"></script>
	<script type="text/javascript" src="./js/osgb.js"></script>
	<script src="./js/proj4js.js"></script>
	<script src="./js/proj4-compressed.js"></script>
	<script src="./js/leaflet/leaflet.js"></script>
	<script src="./js/proj4leaflet.js"></script>
	<script src="./js/leaflet/KML.js"></script>  
	<script src="./js/leaflet-openweathermap.js"></script>
	<script src="./js/esri-leaflet.js"></script>
	<script src="./js/OSOpenspace.js"></script>
	<script src="./js/Control.Geocoder.js"></script>
	<script type="text/javascript" src="./js/osm_map_functions.js.php"></script>
	<script type="text/javascript" src="./js/L.Graticule.js"></script>
	<script type="text/javascript" src="./js/leaflet-providers.js"></script>
<?php
	}
?>
	<SCRIPT>
	var sortby = '`date`';	//	11/18/13
	var sort = "DESC";	//	11/18/13
	var columns = "<?php print get_msg_variable('columns');?>";	//	11/18/13
	var the_columns = new Array(<?php print get_msg_variable('columns');?>);	//	11/18/13
	var thescreen = 'units';	//	11/18/13
	var map, label;		// note global
	var layercontrol;

	try {
		parent.frames["upper"].$("whom").innerHTML  = "<?php print $_SESSION['user'];?>";
		parent.frames["upper"].$("level").innerHTML = "<?php print get_level_text($_SESSION['level']);?>";
		parent.frames["upper"].$("script").innerHTML  = "<?php print LessExtension(basename( __FILE__));?>";
		}
	catch(e) {
		}

	parent.upper.show_butts();												// 11/2/08
	parent.upper.light_butt('resp');										// light the button - 8/25/10

	var lat_lng_frmt = <?php print get_variable('lat_lng'); ?>;				// 9/9/08
	var check_initialized = false;
	var check_interval = null;	

	function get_new_colors() {
		window.location.href = '<?php print basename(__FILE__);?>';
		}

	function isNull(val) {								// checks var stuff = null;
		return val === null;
		}

	var type;					// Global variable - identifies browser family
	BrowserSniffer();

	function BrowserSniffer() {													//detects the capabilities of the browser
		if (navigator.userAgent.indexOf("Opera")!=-1 && $) type="OP";	//Opera
		else if (document.all) type="IE";										//Internet Explorer e.g. IE4 upwards
		else if (document.layers) type="NN";									//Netscape Communicator 4
		else if (!document.all && $) type="MO";			//Mozila e.g. Netscape 6 upwards
		else type = "IE";														//????????????
		}

	var starting = false;

	function blink_text_rf(id, bgcol, bgcol2, maincol, seccol) {	//	6/10/11
		if($('fence_flag')) {
			$('fence_flag').style.fontSize = '14px';
			$('fence_flag').style.color = 'red';
			$('fence_flag').style.backgroundColor = 'yellow';	
			$('fence_flag').style.fontWeight = 'bold';				
			$('fence_flag').innerHTML = "This unit is outside a ring fence";
			}		
		function BlinkIt () {
			if(document.getElementById (id)) {
				var blink = document.getElementById (id);
				color = (color == maincol) ? seccol : maincol;
				back = (back == bgcol) ? bgcol2 : bgcol;
				blink.style.background = back;
				blink.style.color = color;
				if($('flag')) {	
					$('flag').innerHTML = "RF";
					}
				}
			}
		window.setInterval (BlinkIt, 1000);
		var color = maincol;
		var back = bgcol;				
		}
			
	function unblink_text_rf(id) {	//	6/10/11
		if(!document.getElementById(id)) {
		} else {	
		if(document.getElementById (id)) {
			var unblink = document.getElementById (id);
			unblink.style.background = "";
			unblink.style.color = "";			
				}
			}
		}	

	function blink_text2_rf(id, bgcol, bgcol2, maincol, seccol) {	//	6/10/11
		if($('fence_flag')) {
			$('fence_flag').style.fontSize = '14px';
			$('fence_flag').style.color = 'red';
			$('fence_flag').style.backgroundColor = 'yellow';	
			$('fence_flag').style.fontWeight = 'bold';			
			$('fence_flag').innerHTML = "This unit is inside an exclusion zone";
			}		
		function BlinkIt () {
			if(document.getElementById (id)) {
				var blink = document.getElementById (id);
				var flag = id + "_flag";
				color = (color == maincol) ? seccol : maincol;
				back = (back == bgcol) ? bgcol2 : bgcol;
				blink.style.background = back;
				blink.style.color = color;
				if($('flag')) {	
					$('flag').innerHTML = "EZ";
					}
				}
			}
		window.setInterval (BlinkIt, 1000);
		var color = maincol;
		var back = bgcol;				
		}
		
	function unblink_text2_rf(id) {	//	6/10/11
		if(!document.getElementById(id)) {
		} else {	
		if(document.getElementById (id)) {
			var unblink = document.getElementById (id);
			unblink.style.background = "";
			unblink.style.color = "";			
				}
			}
		}					

	</SCRIPT>
					   
					   
<?php
	function do_calls($id = 0) {				// generates js callsigns array
		$print = "\n<SCRIPT >\n";
		$print .="\t\tvar calls = new Array();\n";
		$query	= "SELECT `id`, `callsign` FROM `$GLOBALS[mysql_prefix]responder` where `id` != $id";
		$result	= mysql_query($query) or do_error($query, 'mysql_query() failed', mysql_error(), __FILE__, __LINE__);
		while($row = stripslashes_deep(mysql_fetch_array($result))) {
			if (!empty($row['callsign'])) {
				$print .="\t\tcalls.push('" .$row['callsign'] . "');\n";
				}
			}				// end while();
		$print .= "</SCRIPT>\n";
		return $print;
		}		// end function do calls()

	$_postmap_clear = 	(array_key_exists ('frm_clr_pos',$_POST ))? 	$_POST['frm_clr_pos']: "";	// 11/19/09
	$_postfrm_remove = 	(array_key_exists ('frm_remove',$_POST ))? 		$_POST['frm_remove']: "";
	$_getgoedit = 		(array_key_exists ('goedit',$_GET )) ? 			$_GET['goedit']: "";
	$_getgoadd = 		(array_key_exists ('goadd',$_GET ))? 			$_GET['goadd']: "";
	$_getedit = 		(array_key_exists ('edit',$_GET))? 				$_GET['edit']:  "";
	$_getadd = 			(array_key_exists ('add',$_GET))? 				$_GET['add']:  "";
	$_getview = 		(array_key_exists ('view',$_GET ))? 			$_GET['view']: "";
	$_dodisp = 			(array_key_exists ('disp',$_GET ))? 			$_GET['disp']: "";
	$_dodispfac = 		(array_key_exists ('dispfac',$_GET ))? 			$_GET['dispfac']: "";	//10/6/09
									   
	$now = mysql_format_date(time() - (get_variable('delta_mins')*60));
	$caption = "";
	if ($_postfrm_remove == 'yes') {					//delete Responder - checkbox - 8/12/09
		$query = "DELETE FROM $GLOBALS[mysql_prefix]responder WHERE `id`=" . $_POST['frm_id'];
		$result = mysql_query($query) or do_error($query, 'mysql_query() failed', mysql_error(), __FILE__, __LINE__);
		$caption = "<B>Unit <I>" . stripslashes_deep($_POST['frm_name']) . "</I> has been deleted from database.</B><BR /><BR />";
			  }
	else {
		if ($_getgoedit == 'true') {
			$now = mysql_format_date(time() - (get_variable('delta_mins')*60));		
			$station = TRUE;			//
			$the_lat = empty($_POST['frm_lat'])? "NULL" : quote_smart(trim($_POST['frm_lat'])) ; // 2/24/09
			$the_lng = empty($_POST['frm_lng'])? "NULL" : quote_smart(trim($_POST['frm_lng'])) ;
			$status_updated = (($_POST['frm_status_update'] == 1) || ($_POST['frm_status_updated'] == "")) ? $now : $_POST['frm_status_updated'];	//	6/21/13
			$curr_groups = $_POST['frm_exist_groups']; 	//	4/14/11
			$groups = isset($_POST['frm_group']) ? ", " . implode(',', $_POST['frm_group']) . "," : $_POST['frm_exist_groups'];	//	3/28/12 - fixes error when accessed from view ticket screen..	
			$resp_id = $_POST['frm_id'];
			$resp_stat = $_POST['frm_un_status_id'];
			$by = $_SESSION['user_id'];
							   
//			if (($_POST['frm_clr_pos'])=='on') {$the_lat = $the_lng = "NULL";}			// 11/15/09
							   
			if ($_postmap_clear=='on') {$the_lat = $the_lng = "NULL";}					// 11/19/09
			else {
				if ((isset($_POST['frm_facility_sel'])) && (intval($_POST['frm_facility_sel'])> 0 )) {							// obtain facility location - 6/20/12
					$query_fac = "SELECT `lat`, `lng`, `id` FROM `$GLOBALS[mysql_prefix]facilities` WHERE `id` = {$_POST['frm_facility_sel']} LIMIT 1";
//					dump($query_fac);
					$result_fac = mysql_query($query_fac) or do_error($query, 'mysql_query() failed', mysql_error(),basename( __FILE__), __LINE__);
					if (mysql_num_rows($result_fac) ==1) {
						$row_fac = stripslashes_deep(mysql_fetch_assoc($result_fac));
						$the_lat = doubleval($row_fac['lat']);							// apply to unit location
						$the_lng = doubleval($row_fac['lng']);
		  }
			}	
				}				// end else {}
		
			$query = "UPDATE `$GLOBALS[mysql_prefix]responder` SET
				`roster_user`= " . 	quote_smart(trim($_POST['frm_roster_id'])) . ",
				`name`= " . 		quote_smart(trim($_POST['frm_name'])) . ",
				`street`= " . 		quote_smart(trim($_POST['frm_street'])) . ",
				`city`= " . 		quote_smart(trim($_POST['frm_city'])) . ",
				`state`= " . 		quote_smart(trim($_POST['frm_state'])) . ",
				`phone`= " . 		quote_smart(trim($_POST['frm_phone'])) . ",
				`handle`= " . 		quote_smart(trim($_POST['frm_handle'])) . ",
				`icon_str`= " . 	quote_smart(trim($_POST['frm_icon_str'])) . ",
				`description`= " . 	quote_smart(trim($_POST['frm_descr'])) . ",
				`capab`= " . 		quote_smart(trim($_POST['frm_capab'])) . ",
				`un_status_id`= " . quote_smart(trim($_POST['frm_un_status_id'])) . ",
				`status_about`= " . quote_smart(trim($_POST['frm_status_about'])) . ",
				`callsign`= " . 	quote_smart(trim($_POST['frm_callsign'])) . ",
				`mobile`= " . 		quote_smart(trim($_POST['frm_mobile'])) . ",
				`multi`= " . 		quote_smart(trim($_POST['frm_multi'])) . ",
				`aprs`= " . 		quote_smart(trim($_POST['frm_aprs'])) . ",
				`instam`= " . 		quote_smart(trim($_POST['frm_instam'])) . ",
				`locatea`= " . 		quote_smart(trim($_POST['frm_locatea'])) . ",
				`gtrack`= " . 		quote_smart(trim($_POST['frm_gtrack'])) . ",
				`glat`= " . 		quote_smart(trim($_POST['frm_glat'])) . ",
				`t_tracker`= " . 	quote_smart(trim($_POST['frm_t_tracker'])) . ",	
				`ogts`= " . 		quote_smart(trim($_POST['frm_ogts'])) . ",
				`mob_tracker`= " . 	quote_smart(trim($_POST['frm_mob_tracker'])) . ",
				`xastir_tracker`= " . 	quote_smart(trim($_POST['frm_xastir_tracker'])) . ",
				`ring_fence`= " . 	quote_smart(trim($_POST['frm_ringfence'])) . ",		
				`excl_zone`= " . 	quote_smart(trim($_POST['frm_excl_zone'])) . ",						
				`direcs`= " . 		quote_smart(trim($_POST['frm_direcs'])) . ",
				`lat`= " . 			$the_lat . ",
				`lng`= " . 			$the_lng . ",
				`contact_name`= " . quote_smart(trim($_POST['frm_contact_name'])) . ",
				`contact_via`= " . 	quote_smart(trim($_POST['frm_contact_via'])) . ",
				`smsg_id`= " . 		quote_smart(trim($_POST['frm_smsg_id'])) . ",				
				`type`= " . 		quote_smart(trim($_POST['frm_type'])) . ",
				`user_id`= " . 		quote_smart(trim($_SESSION['user_id'])) . ",
				`updated`= " . 		quote_smart(trim($now)) . ",
				`status_updated`= '" . $status_updated . "'
				WHERE `id`= " . 	quote_smart(trim($_POST['frm_id'])) . ";";	//	5/11/11 added internal Tickets tracker, 6/21/13 added field status_updated for auto status function. 9/6/13, 11/18/13, 1/30/14 added xastir tracker
			$result = mysql_query($query) or do_error($query, 'mysql_query() failed', mysql_error(),basename( __FILE__), __LINE__);
			if (!empty($_POST['frm_log_it'])) { do_log($GLOBALS['LOG_UNIT_STATUS'], 0, $_POST['frm_id'], $_POST['frm_un_status_id']);}	// 6/2/08
					   
			$list = $_POST['frm_exist_groups']; 	//	4/14/11
			$ex_grps = explode(',', $list); 	//	4/14/11 
					   
			if($curr_groups != $groups) { 	//	4/14/11
				foreach($_POST['frm_group'] as $posted_grp) { 	//	4/14/11
					if(!in_array($posted_grp, $ex_grps)) {
						$query  = "INSERT INTO `$GLOBALS[mysql_prefix]allocates` (`group` , `type`, `al_as_of` , `al_status` , `resource_id` , `sys_comments` , `user_id`) VALUES 
								($posted_grp, 2, '$now', $resp_stat, $resp_id, 'Allocated to Group' , $by)";
						$result = mysql_query($query) or do_error($query, 'mysql query failed', mysql_error(),basename( __FILE__), __LINE__);	
									}
				}
				foreach($ex_grps as $existing_grps) { 	//	4/14/11
					if(!in_array($existing_grps, $_POST['frm_group'])) {
						if($existing_grps != "") {	//	6/19/14
							$query  = "DELETE FROM `$GLOBALS[mysql_prefix]allocates` WHERE `type` = 2 AND `group` = $existing_grps AND `resource_id` = {$resp_id}";
							$result = mysql_query($query) or do_error($query, 'mysql query failed', mysql_error(),basename( __FILE__), __LINE__);
		  }
			}	
				}
			}

			$query = "SELECT * FROM `$GLOBALS[mysql_prefix]allocates` WHERE `type` = 2 AND `resource_id` = {$resp_id}";	//	unallocated resource_id catcher, 6/19/14
			$result = mysql_query($query) or do_error($query, 'mysql query failed', mysql_error(),basename( __FILE__), __LINE__);
			if(!mysql_num_rows($result) > 0) {
				$query  = "INSERT INTO `$GLOBALS[mysql_prefix]allocates` (`group` , `type`, `al_as_of` , `al_status` , `resource_id` , `sys_comments` , `user_id`) VALUES 
						(1, 2, '$now', $resp_stat, $resp_id, 'Allocated to Group' , $by)";
				$result = mysql_query($query) or do_error($query, 'mysql query failed', mysql_error(),basename( __FILE__), __LINE__);	
		}

			$mobstr = (($frm_mobile) && ($frm_aprs)||($frm_instam))? "Mobile": "Unit ";
			$caption = "<B>Unit<i> " . stripslashes_deep($_POST['frm_handle']) . "</i>' data has been updated </B><BR /><BR />";
		}
		}				// end else {}

	if ($_getgoadd == 'true') {
		$by = $_SESSION['user_id'];			// 5/27/10
		$frm_lat = (empty($_POST['frm_lat']))? 'NULL': quote_smart(trim($_POST['frm_lat']));					// 9/3/08 7/22/10
		$frm_lng = (empty($_POST['frm_lng']))? 'NULL': quote_smart(trim($_POST['frm_lng']));

		$aprs = 	(empty($_POST['frm_aprs']))? 		0: quote_smart(trim($_POST['frm_aprs']));				// 8/13/10
		$instam = 	(empty($_POST['frm_instam']))? 		0: quote_smart(trim($_POST['frm_instam']));
		$locatea = 	(empty($_POST['frm_locatea']))? 	0: quote_smart(trim($_POST['frm_locatea']));
		$gtrack = 	(empty($_POST['frm_gtrack']))? 		0: quote_smart(trim($_POST['frm_gtrack']));
		$glat = 	(empty($_POST['frm_glat']))? 		0: quote_smart(trim($_POST['frm_glat'])) ;
		$t_tracker = (empty($_POST['frm_t_tracker']))? 		0: quote_smart(trim($_POST['frm_t_tracker'])) ;	//	5/11/11
		$ogts = 	(empty($_POST['frm_ogts']))? 		0: quote_smart(trim($_POST['frm_ogts'])) ;
		$mob_tracker = 	(empty($_POST['frm_mob_tracker']))? 		0: quote_smart(trim($_POST['frm_mob_tracker'])) ;	//	9/6/13
		$xastir_tracker = 	(empty($_POST['frm_xastir_tracker']))? 		0: quote_smart(trim($_POST['frm_xastir_tracker'])) ;	//	1/30/14
		$now = mysql_format_date(time() - (get_variable('delta_mins')*60));							// 1/27/09

		$query = "INSERT INTO `$GLOBALS[mysql_prefix]responder` (
			`roster_user`, `name`, `street`, `city`, `state`, `phone`, `handle`, `icon_str`, `description`, `capab`, `un_status_id`, `status_about`, `callsign`, `mobile`, `multi`, `aprs`, 
			`instam`, `locatea`, `gtrack`, `glat`, `t_tracker`, `ogts`, `mob_tracker`, `xastir_tracker`, `ring_fence`, `excl_zone`, `direcs`, `contact_name`, `contact_via`, `smsg_id`, `lat`, `lng`, `type`, `user_id`, `updated`, `status_updated` )
			VALUES (" .
				quote_smart(trim($_POST['frm_roster_id'])) . "," .
				quote_smart(trim($_POST['frm_name'])) . "," .
				quote_smart(trim($_POST['frm_street'])) . "," .
				quote_smart(trim($_POST['frm_city'])) . "," .
				quote_smart(trim($_POST['frm_state'])) . "," .
				quote_smart(trim($_POST['frm_phone'])) . "," .
				quote_smart(trim($_POST['frm_handle'])) . "," .
				quote_smart(trim($_POST['frm_icon_str'])) . "," .
				quote_smart(trim($_POST['frm_descr'])) . "," .
				quote_smart(trim($_POST['frm_capab'])) . "," .
				quote_smart(trim($_POST['frm_un_status_id'])) . "," .
				quote_smart(trim($_POST['frm_status_about'])) . "," .
				quote_smart(trim($_POST['frm_callsign'])) . "," .
				quote_smart(trim($_POST['frm_mobile'])) . "," .
				quote_smart(trim($_POST['frm_multi'])) . "," .
				quote_smart(trim($_POST['frm_aprs'])) . "," .
				quote_smart(trim($_POST['frm_instam'])) . "," .
				quote_smart(trim($_POST['frm_locatea'])) . "," .
				quote_smart(trim($_POST['frm_gtrack'])) . "," .
				quote_smart(trim($_POST['frm_glat'])) . "," .
				quote_smart(trim($_POST['frm_t_tracker'])) . "," .	
				quote_smart(trim($_POST['frm_ogts'])) . "," .
				quote_smart(trim($_POST['frm_mob_tracker'])) . "," .
				quote_smart(trim($_POST['frm_xastir_tracker'])) . "," .
				quote_smart(trim($_POST['frm_ringfence'])) . "," .	
				quote_smart(trim($_POST['frm_excl_zone'])) . "," .					
				quote_smart(trim($_POST['frm_direcs'])) . "," .
				quote_smart(trim($_POST['frm_contact_name'])) . "," .
				quote_smart(trim($_POST['frm_contact_via'])) . "," .
				quote_smart(trim($_POST['frm_smsg_id'])) . "," .				
				$frm_lat . "," .
				$frm_lng . "," .
				quote_smart(trim($_POST['frm_type'])) . "," .
				quote_smart(trim($_SESSION['user_id'])) . "," .
				quote_smart(trim($now)) . "," .
				quote_smart(trim($now)) . ");";								// 8/23/08, 5/11/11, 6/21/13, 9/6/13, 11/18/13

		$result = mysql_query($query) or do_error($query, 'mysql_query() failed', mysql_error(), __FILE__, __LINE__);
		$new_id=mysql_insert_id();

//	9/10/13 File Upload support
		$print = "";
		if ((isset($_FILES['frm_file'])) && ($_FILES['frm_file']['name'] != "")){
			$nogoodFile = false;	
			$blacklist = array(".php", ".phtml", ".php3", ".php4", ".js", ".shtml", ".pl" ,".py"); 
			foreach ($blacklist as $file) { 
				if(preg_match("/$file\$/i", $_FILES['frm_file']['name'])) { 
					$nogoodFile = true;
					}
					}
			if(!$nogoodFile) {
				$exists = false;
				$existing_file = "";
				$upload_directory = "./files/";
				if (!(file_exists($upload_directory))) {				
					mkdir ($upload_directory, 0770);
		}
				chmod($upload_directory, 0770);	
				$filename = rand(1,999999);
				$realfilename = $_FILES["frm_file"]["name"];
				$file = $upload_directory . $filename;

//	Does the file already exist in the files table		

				$query = "SELECT * FROM `$GLOBALS[mysql_prefix]files` WHERE `orig_filename` = '" . $realfilename . "'";
				$result = mysql_query($query) or do_error($query, $query, mysql_error(), basename( __FILE__), __LINE__);	
				if(mysql_affected_rows() == 0) {	//	file doesn't exist already
					if (move_uploaded_file($_FILES['frm_file']['tmp_name'], $file)) {	// If file uploaded OK
						if (strlen(filesize($file)) < 20000000) {
							$print .= "";
							} else {
							$print .= "Attached file is too large!";
			}
			} else {
						$print .= "Error uploading file";
			}
			} else {
					$row = stripslashes_deep(mysql_fetch_assoc($result));			
					$exists = true;
					$existing_file = $row['filename'];	//	get existing file name
			}

				$from = $_SERVER['REMOTE_ADDR'];	
				$filename = ($existing_file == "") ? $filename : $existing_file;	//	if existing file, use this file and write new db entry with it.
				$query_insert  = "INSERT INTO `$GLOBALS[mysql_prefix]files` (
						`title` , `filename` , `orig_filename`, `ticket_id` , `responder_id` , `facility_id`, `type`, `filetype`, `_by`, `_on`, `_from`
					) VALUES (
						'" . $_POST['frm_file_title'] . "', '" . $filename . "', '" . $realfilename . "', 0, " . $id . ",
						0, 0, '" . $_FILES['frm_file']['type'] . "', $by, '" . $now . "', '" . $from . "'
					)";
				$result_insert	= mysql_query($query_insert) or do_error($query_insert,'mysql_query() failed', mysql_error(), basename( __FILE__), __LINE__);
				if($result_insert) {	//	is the database insert successful
					$dbUpdated = true;
					} else {	//	problem with the database insert
					$dbUpdated = false;				
					}
				}
			} else {	// Problem with the file upload
			$fileUploaded = false;
			}

// End of file upload

		$status_id = $_POST['frm_un_status_id'];
		if(!empty($_POST['frm_group'])) {
			foreach ($_POST['frm_group'] as $grp_val) {	// 6/10/11
				if(test_allocates($new_id, $grp_val, 2))	{		
					$query_a  = "INSERT INTO `$GLOBALS[mysql_prefix]allocates` (`group` , `type`, `al_as_of` , `al_status` , `resource_id` , `sys_comments` , `user_id`) VALUES 
							($grp_val, 2, '$now', $status_id, $new_id, 'Allocated to Group' , $by)";
					$result_a = mysql_query($query_a) or do_error($query_a, 'mysql query failed', mysql_error(),basename( __FILE__), __LINE__);
					}
			}
			} else {
				$query_a  = "INSERT INTO `$GLOBALS[mysql_prefix]allocates` (`group` , `type`, `al_as_of` , `al_status` , `resource_id` , `sys_comments` , `user_id`) VALUES 
						(1, 2, '$now', $status_id, $new_id, 'Allocated to Group' , $by)";
				$result_a = mysql_query($query_a) or do_error($query_a, 'mysql query failed', mysql_error(),basename( __FILE__), __LINE__);	
			}	//	end if(!empty($_POST['frm_group']

		do_log($GLOBALS['LOG_UNIT_STATUS'], 0, mysql_insert_id(), $_POST['frm_un_status_id']);	// 6/2/08

		$caption = "<B>Unit  <i>" . stripslashes_deep($_POST['frm_name']) . "</i> data has been applied </B><BR /><BR />";
		}							// end if ($_getgoadd == 'true')

// add ===========================================================================================================================
// add ===========================================================================================================================
// add ===========================================================================================================================

	if ($_getadd == 'true') {
		require_once('./incs/links.inc.php');
		require_once('./forms/units_add_screen.php');
		if((is_super()) || (is_administrator())) {	//	10/28/10 Added for add on modules
			if(file_exists("./incs/modules.inc.php")) {
				get_modules('res_add_Form');
			}
			}
		exit();
		}		// end if ($_GET['add'])
		
// edit =================================================================================================================
// edit =================================================================================================================
// edit =================================================================================================================

	if ($_getedit == 'true') {
		require_once('./incs/links.inc.php');
		require_once('./forms/units_edit_screen.php');
		if((is_super()) || (is_administrator())) {	//	10/28/10 Added for add on modules
			if(file_exists("./incs/modules.inc.php")) {
				get_modules('res_edit_Form');
				}
				}
		}		// end if ($_GET['edit'])
// =================================================================================================================
// view =================================================================================================================

	if ($_getview == 'true') {
		require_once('./incs/links.inc.php');
		require_once('./forms/units_view_screen.php');
		if((is_super()) || (is_administrator())) {	//	10/28/10 Added for add on modules
				if(file_exists("./incs/modules.inc.php")) {
				get_modules('res_view_Form');
				}
					}
				}	
// ============================================= initial display =======================
			if (!isset($mapmode)) {$mapmode="a";}
		require_once('./incs/links.inc.php');
	require_once('./forms/units_screen.php');
		if((is_super()) || (is_administrator())) {	//	10/28/10 Added for add on modules
			if(file_exists("./incs/modules.inc.php")) {
				get_modules('list_form');
				}
			}					
		exit();
    break;
?>
