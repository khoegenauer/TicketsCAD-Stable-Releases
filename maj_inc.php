<?php
error_reporting(E_ALL);

$units_side_bar_height = .5;		// max height of units sidebar as decimal fraction of screen height - default is 0.6 (60%)
$zoom_tight = FALSE;				// replace with a decimal number to over-ride the standard default zoom setting
$iw_width= "300px";					// map infowindow with
$groupname = isset($_SESSION['group_name']) ? $_SESSION['group_name'] : "";	//	4/11/11

$the_resp_id = (isset($_GET['id']))? $_GET['id']: 0;	//	11/18/13
/*
02/04/14 New file.
*/

@session_start();	

require_once($_SESSION['fip']);		//7/28/10
do_login(basename(__FILE__));

extract($_GET);
extract($_POST);

if(($_SESSION['level'] == $GLOBALS['LEVEL_UNIT']) && (intval(get_variable('restrict_units')) == 1)) {
	print "Not Authorized";
	exit();
	}
	
$comm_arr = array();
$query = "SELECT * FROM `$GLOBALS[mysql_prefix]user` ORDER BY `id` ASC";
$result = mysql_query($query);
while ($row = stripslashes_deep(mysql_fetch_assoc($result))) {
	$comm_arr[$row['id']][0] = $row['id'];
	$comm_arr[$row['id']][1] = $row['user'];	
	$comm_arr[$row['id']][2] = $row['name_f'];	
	$comm_arr[$row['id']][3] = $row['name_l'];	
	$comm_arr[$row['id']][4] = $row['email'];	
	$comm_arr[$row['id']][5] = $row['email_s'];
	$comm_arr[$row['id']][6] = $row['phone_p'];	
	$comm_arr[$row['id']][7] = $row['phone_s'];
	}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<HEAD><TITLE>Tickets - Major Incidents Module</TITLE>
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
		div.scrollingArea2 { max-height: 400px; overflow: auto; overflow-x: hidden; }
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
	<SCRIPT>
	var sortby = '`date`';	//	11/18/13
	var sort = "DESC";	//	11/18/13
	var columns = "<?php print get_msg_variable('columns');?>";	//	11/18/13
	var the_columns = new Array(<?php print get_msg_variable('columns');?>);	//	11/18/13
	var thescreen = 'units';	//	11/18/13
	var map, label;		// note global
	var layercontrol;
	var mi_interval = null;
	var micell1 = 0;
	var micell2 = 0;
	var micell3 = 0
	var micell4 = 0;
	var micell5 = 0;
	var micell6 = 0;
	var micell7 = 0;
	var comm_arr = <?php echo json_encode($comm_arr); ?>;
	var colors = new Array ('odd', 'even');
	var icons=[];
	icons[<?php echo $GLOBALS['SEVERITY_NORMAL'];?>] = 1;	// blue
	icons[<?php echo $GLOBALS['SEVERITY_MEDIUM'];?>] = 2;	// yellow
	icons[<?php echo $GLOBALS['SEVERITY_HIGH']; ?>] =  3;	// red
	icons[4] =  4;	// white
	
	try {
		parent.frames["upper"].$("whom").innerHTML  = "<?php print $_SESSION['user'];?>";
		parent.frames["upper"].$("level").innerHTML = "<?php print get_level_text($_SESSION['level']);?>";
		parent.frames["upper"].$("script").innerHTML  = "<?php print LessExtension(basename( __FILE__));?>";
		}
	catch(e) {
		}

	parent.upper.show_butts();												// 11/2/08

	var lat_lng_frmt = <?php print get_variable('lat_lng'); ?>;				// 9/9/08

	function JSfnTrim(argvalue) {					// drops leading and trailing spaces and cr's
		var tmpstr = ltrim(argvalue);
		return rtrim(tmpstr);
			function ltrim(argvalue) {
				while (1) {
					if ((argvalue.substring(0, 1) != " ") && (argvalue.substring(0, 1) != "\n"))
						break;
					argvalue = argvalue.substring(1, argvalue.length);
					}
				return argvalue;
				}								// end function ltrim()
			function rtrim(argvalue) {
				while (1) {
					if ((argvalue.substring(argvalue.length - 1, argvalue.length) != " ") && (argvalue.substring(argvalue.length - 1, argvalue.length) != "\n"))
						break;
					argvalue = argvalue.substring(0, argvalue.length - 1);
					}
				return argvalue;
			}									// end rtrim()
		}										// end JSfnTrim()	
	
	function mymiclick(id) {					// Responds to sidebar click, then triggers listener above -  note [i]
		document.mi_form.id.value=id;
		document.mi_form.view.value='true';
		document.mi_form.action='maj_inc.php';
		document.mi_form.submit();
		}
	
	function set_command_info(id, theDiv) {
		var email1 = comm_arr[id][4];
		var email2 = comm_arr[id][5];
		var phone1 = comm_arr[id][6];
		var phone2 = comm_arr[id][7];
		var theHTML = "<TABLE>";
		theHTML += "<TR>";
		theHTML += "<TD class='td_label'>Email 1</TD>";
		theHTML += "<TD class='td_data'>" + email1 + "</TD>";
		theHTML += "</TR><TR>";
		theHTML += "<TD class='td_label'>Email 2</TD>";
		theHTML += "<TD class='td_data'>" + email2 + "</TD>";
		theHTML += "</TR><TR>";
		theHTML += "<TD class='td_label'>Phone 1</TD>";
		theHTML += "<TD class='td_data'>" + phone1 + "</TD>";
		theHTML += "</TR><TR>";
		theHTML += "<TD class='td_label'>Phone 1</TD>";
		theHTML += "<TD class='td_data'>" + phone2 + "</TD>";
		theHTML += "</TR><TR></TABLE>";		
		$(theDiv).innerHTML = theHTML;
		}

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

	function createTicMarker(lat, lon, info, color, theid, sym, tip) {
		if((isFloat(lat)) && (isFloat(lon))) {
			var iconStr = sym;
			var iconurl = "./our_icons/gen_icon.php?blank=" + escape(window.icons[color]) + "&text=" + iconStr;	
			icon = new baseIcon({iconUrl: iconurl});	
			var marker = L.marker([lat, lon], {icon: icon, title: tip, riseOnHover: true, riseOffset: 30000}).bindPopup(info).openPopup();
			marker.id = color;
			tmarkers[theid] = marker;
			tmarkers[theid][lat] = lat;
			tmarkers[theid][lon] = lon;
			var point = new L.LatLng(lat, lon);
			bounds.extend(point);
			map.fitBounds(bounds);
			return marker;
			} else {
			return false;
			}
		}
		
	function createRespMarker(lat, lon, theid, sym, tip) {
		if((isFloat(lat)) && (isFloat(lon))) {
			var iconStr = sym;
			var iconurl = "./our_icons/gen_icon.php?blank=4&text=" + iconStr;	
			icon = new baseIcon({iconUrl: iconurl});	
			var marker = L.marker([lat, lon], {icon: icon, title: tip, riseOnHover: true, riseOffset: 30000});
			rmarkers[theid] = marker;
			rmarkers[theid][lat] = lat;
			rmarkers[theid][lon] = lon;
			var point = new L.LatLng(lat, lon);
			bounds.extend(point);
			map.fitBounds(bounds);
			return marker;
			} else {
			return false;
			}
		}

	var mi1_text = "<?php print get_text('ID');?>"; 
	var mi2_text = "<?php print get_text('Name');?>"; 
	var mi3_text = "<?php print get_text('Gold');?>"; 
	var mi4_text = "<?php print get_text('Silver');?>"; 
	var mi5_text = "<?php print get_text('Bronze');?>"; 
	var mi6_text = "<?php print get_text('As of');?>"; 
	var changed_mi_sort = false;
	var mi_direct = "ASC";
	var mi_field = "id";
	var mi_id = "mi1";
	var mi_header = "<?php print get_text('ID');?>";

	function set_mi_headers(id, header_text, the_bull) {
		alert(id + ", " + header_text + ", " + the_bull);
		if(id == "mi1") {
			window.mi1_text = header_text + the_bull;
			window.mi2_text = "<?php print get_text('Name');?>";
			window.mi3_text = "<?php print get_text('Gold');?>";
			window.mi4_text = "<?php print get_text('Silver');?>";
			window.mi5_text = "<?php print get_text('Bronze');?>";
			window.mi6_text = "<?php print get_text('As of');?>";
			} else if(id == "mi2") {
			window.mi2_text = header_text + the_bull;
			window.mi1_text = "<?php print get_text('ID');?>";
			window.mi3_text = "<?php print get_text('Gold');?>";
			window.mi4_text = "<?php print get_text('Silver');?>";
			window.mi5_text = "<?php print get_text('Bronze');?>";
			window.mi6_text = "<?php print get_text('As of');?>";
			} else if(id == "mi3") {
			window.mi3_text = header_text + the_bull;
			window.mi1_text = "<?php print get_text('ID');?>";
			window.mi2_text = "<?php print get_text('Name');?>";
			window.mi4_text = "<?php print get_text('Silver');?>";
			window.mi5_text = "<?php print get_text('Bronze');?>";
			window.mi6_text = "<?php print get_text('As of');?>";
			} else if(id == "mi4") {
			window.mi4_text = header_text + the_bull;
			window.mi1_text = "<?php print get_text('ID');?>";
			window.mi2_text = "<?php print get_text('Name');?>";
			window.mi3_text = "<?php print get_text('Gold');?>";
			window.mi5_text = "<?php print get_text('Bronze');?>";
			window.mi6_text = "<?php print get_text('As of');?>";
			} else if(id == "mi5") {
			window.mi5_text = header_text + the_bull;
			window.mi1_text = "<?php print get_text('ID');?>";
			window.mi2_text = "<?php print get_text('Name');?>";
			window.mi3_text = "<?php print get_text('Gold');?>";
			window.mi4_text = "<?php print get_text('Silver');?>";
			window.mi6_text = "<?php print get_text('As of');?>";
			} else if(id == "mi6") {
			window.mi6_text = header_text + the_bull;
			window.mi1_text = "<?php print get_text('ID');?>";
			window.mi2_text = "<?php print get_text('Name');?>";
			window.mi3_text = "<?php print get_text('Gold');?>";
			window.mi4_text = "<?php print get_text('Silver');?>";
			window.mi5_text = "<?php print get_text('Bronze');?>";
			}
		}
		
	function do_mi_sort(id, field, header_text) {
		window.changed_mi_sort = true;
		window.mi_last_display = 0;
		if(window.mi_field == field) {
			if(window.mi_direct == "ASC") {
				window.mi_direct = "DESC"; 
				var the_bull = "&#9660"; 
				window.mi_header = header_text;
				window.mi_field = field;
				set_mi_headers(id, header_text, the_bull);
				} else if(window.mi_direct == "DESC") { 
				window.mi_direct = "ASC"; 
				var the_bull = "&#9650"; 
				window.mi_header = header_text; 
				window.mi_field = field;
				set_mi_headers(id, header_text, the_bull);
				}
			} else {
			$(mi_id).innerHTML = mi_header;
			window.mi_field = field;
			window.mi_direct = "ASC";
			window.mi_id = id;
			window.mi_header = header_text;
			var the_bull = "&#9650";
			set_mi_headers(id, header_text, the_bull);
			}
		load_mi_list(field, mi_direct);
		return true;
		}

	function load_mi_list(sort, dir) {
		window.miFin = false;
		if(sort != window.mi_field) {
			window.mi_field = sort;
			}
		if(dir != window.mi_direct) {
			window.mi_direct = dir;
			}
		if($('the_milist').innerHTML == "") {
			$('the_milist').innerHTML = "<CENTER><IMG src='./images/owmloading.gif'></CENTER>";
			}
		var randomnumber=Math.floor(Math.random()*99999999);
		var sessID = "<?php print $_SESSION['id'];?>";
		var url = './ajax/mt_list.php?sort='+window.mi_field+'&dir='+ window.mi_direct+'&version='+randomnumber+'&q='+sessID;
		sendRequest (url,milist_cb, "");		
		function milist_cb(req) {
			var i = 1;
			var mi_number = 0;	
			var mi_arr = JSON.decode(req.responseText);
			if((mi_arr[0]) && (mi_arr[0][0] == 0)) {
				var outputtext = "<marquee direction='left' style='font-size: 2em; font-weight: bold;'>......No Major Incidents to view.........</marquee>";
				$('the_milist').innerHTML = outputtext;
				window.latest_mi = 0;
				} else {
				var outputtext = "<TABLE id='majorincidentstable' class='cruises scrollable' style='width: " + window.listwidth + "px;'>";
				outputtext += "<thead>";
				outputtext += "<TR style='width: " + window.listwidth + "px;'>";
				outputtext += "<TH id='mi1' class='plain_listheader' onMouseOver=\"do_hover_listheader(this.id); Tip('<?php print get_tip('ID');?>');\" onMouseOut=\"do_plain_listheader(this.id); UnTip();\" onClick=\"do_mi_sort(this.id, 'id', '<?php print get_text('ID');?>')\">" + window.mi1_text + "</TH>";
				outputtext += "<TH id='mi2' class='plain_listheader' onMouseOver=\"do_hover_listheader(this.id); Tip('<?php print get_tip('Major Incident Name');?>');\" onMouseOut=\"do_plain_listheader(this.id); UnTip();\" onClick=\"do_mi_sort(this.id, 'name', '<?php print get_text('Name');?>')\">" + window.mi2_text + "</TH>";
				outputtext += "<TH id='mi3' class='plain_listheader' onMouseOver=\"do_hover_listheader(this.id); Tip('<?php print get_tip('Gold Command');?>');\" onMouseOut=\"do_plain_listheader(this.id); UnTip();\" onClick=\"do_mi_sort(this.id, 'gold', '<?php print get_text('Gold');?>')\">" + window.mi3_text + "</TH>";
				outputtext += "<TH id='mi4' class='plain_listheader' onMouseOver=\"do_hover_listheader(this.id); Tip('<?php print get_tip('Silver Command');?>');\" onMouseOut=\"do_plain_listheader(this.id); UnTip();\" onClick=\"do_mi_sort(this.id, 'silver', '<?php print get_text('Silver');?>')\">" + window.mi4_text + "</TH>";
				outputtext += "<TH id='mi5' class='plain_listheader' onMouseOver=\"do_hover_listheader(this.id); Tip('<?php print get_tip('Bronze Command');?>');\" onMouseOut=\"do_plain_listheader(this.id); UnTip();\" onClick=\"do_mi_sort(this.id, 'bronze', '<?php print get_text('Bronze');?>')\">" + window.mi5_text + "</TH>";
				outputtext += "<TH id='mi6' class='plain_listheader' onMouseOver=\"do_hover_listheader(this.id); Tip('<?php print get_tip('Updated');?>');\" onMouseOut=\"do_plain_listheader(this.id); UnTip();\" onClick=\"do_mi_sort(this.id, 'updated', '<?php print get_text('As of');?>')\">" + window.mi6_text + "</TH>";
				outputtext += "<TH id='mi7'>" + pad(5, " ", "\u00a0") + "</TH>";
				outputtext += "</TR>";
				outputtext += "</thead>";
				outputtext += "<tbody>";
				for(var key in mi_arr) {
					if(key != 0) {
						if(mi_arr[key][2]) {
							var mi_id = mi_arr[key][10];
							outputtext += "<TR id='" + mi_arr[key][10] + mi_id +"' CLASS='" + colors[i%2] +"' style='width: " + window.listwidth + "px;'>";
							outputtext += "<TD onClick='mymiclick(" + mi_id + ");'>" + pad(6, mi_id, "\u00a0") + "</TD>";
							outputtext += "<TD onClick='mymiclick(" + mi_id + ");' style='color: " + mi_arr[key][13] + "; background-color: " + mi_arr[key][12] + ";'>" + mi_arr[key][0] + "</TD>";
							outputtext += "<TD onClick='mymiclick(" + mi_id + ");'>" + pad(15, mi_arr[key][6], "\u00a0") + "</TD>";
							outputtext += "<TD onClick='mymiclick(" + mi_id + ");'>" + pad(15, mi_arr[key][7], "\u00a0") + "</TD>";
							outputtext += "<TD onClick='mymiclick(" + mi_id + ");'>" + pad(15, mi_arr[key][8], "\u00a0") + "</TD>";
							outputtext += "<TD onClick='mymiclick(" + mi_id + ");'>" + mi_arr[key][3] + "</TD>";
							outputtext += "<TD>" + pad(5, " ", "\u00a0") + "</TD>";
							outputtext += "</TR>";
							if(window.mis_updated[mi_arr[key][10]]) {
								if(window.mis_updated[mi_arr[key][10]] != mi_arr[key][3]) {
									window.do_mi_update = true;
									} else {
									window.do_mi_update = false;
									}
								} else {
								window.mis_updated[mi_arr[key][10]] = mi_arr[key][3];
								window.do_mi_update = true;
								}
							mi_number = mi_id;
							var markup_arr = mi_arr[key][9];
							if(markup_arr) {
								if($('map_canvas')) {
								var theID = markup_arr['id'];
								var theLinename = markup_arr['name'];
								var theIdent = markup_arr['ident'];
								var theCategory = markup_arr['cat'];
								var theData = markup_arr['data'];
								var theColor = "#" + markup_arr['color'];
								var theOpacity = markup_arr['opacity'];
								var theWidth = markup_arr['width'];
								var theFilled = markup_arr['filled'];
								var theFillcolor = "#" + markup_arr['fill_color'];
								var theFillopacity = markup_arr['fill_opacity'];
								var theType = markup_arr['type'];
								if(theType == "p") {
									var polygon = draw_poly(theLinename, theCategory, theColor, theOpacity, theWidth, theFilled, theFillcolor, theFillopacity, theData, "basemarkup", theID);
									} else if(theType == "c") {
									var circle = drawCircle(theLinename, theData, theColor, theWidth, theOpacity, theFilled, theFillcolor, theFillopacity, "basemarkup", theID);
									} else if(theType == "t") {
									var banner = drawBanner(theLinename, theData, theWidth, theColor, "basemarkup", theID);
									}
								}
								}
							if(mi_arr[key][11]) {
								var tic_arr = mi_arr[key][11];
								if(tic_arr[key]) {
									if($('map_canvas')) {
									if(i == 1) {
										var thePoint = L.latLng(tic_arr[key][2],tic_arr[key][3]);
										window.bounds = L.latLngBounds(thePoint);
										}
									}
									}
								for (n = 0; n < tic_arr.length; n++) {
									var theTickid = tic_arr[n][0];
									var theScope = tic_arr[n][1];
									var theLat = tic_arr[n][2];
									var theLng = tic_arr[n][3];
									var theType = tic_arr[n][4];
									var theSeverity = tic_arr[n][5];
									if($('map_canvas')) {
										if((isFloat(theLat)) && (isFloat(theLng))) {
									var marker = createTicMarker(theLat, theLng, "Ticket: " + theScope + "<BR />Major Incident: " + mi_arr[key][0], theSeverity, theTickid, mi_id, theScope);
									marker.addTo(map);
											}
										}
									var theResp_arr = tic_arr[n][6];
									if(theResp_arr) {
										for(z = 0; z < theResp_arr.length; z++) {
											var resp_id = theResp_arr[z][0];
											var resp_handle = theResp_arr[z][1];
											var resp_lat = theResp_arr[z][2];
											var resp_lng = theResp_arr[z][3];
											if($('map_canvas')) {
												if((isFloat(resp_lat)) && (isFloat(resp_lng))) {
											var rmarker = createRespMarker(resp_lat, resp_lng, resp_id, mi_id, resp_handle)
											rmarker.addTo(map);
											}
										}
									}
								}
							}
								}
							}
						i++;
						}
					}
				outputtext += "</tbody>";
				outputtext += "</TABLE>";
				setTimeout(function() {
					if(window.mi_last_display == 0) {
						$('the_milist').innerHTML = outputtext;
						window.latest_mi = mi_number;
						} else {
						if((mi_number != window.latest_mi) || (window.do_mip_update == true) || (window.changed_mi_sort == true)) {
							$('the_milist').innerHTML = "";
							$('the_milist').innerHTML = outputtext;
							window.latest_mi = mi_number;
							}
						}
					var mitbl = document.getElementById('majorincidentstable');
					if(mitbl) {
						var headerRow = mitbl.rows[0];
						var tableRow = mitbl.rows[1];
						if(tableRow) {
							if(tableRow.cells[0] && headerRow.cells[0]) {headerRow.cells[0].style.width = tableRow.cells[0].clientWidth - 4 + "px";}
							if(tableRow.cells[1] && headerRow.cells[1]) {headerRow.cells[1].style.width = tableRow.cells[1].clientWidth - 4 + "px";}
							if(tableRow.cells[2] && headerRow.cells[2]) {headerRow.cells[2].style.width = tableRow.cells[2].clientWidth - 4 + "px";}
							if(tableRow.cells[3] && headerRow.cells[3]) {headerRow.cells[3].style.width = tableRow.cells[3].clientWidth - 4 + "px";}
							if(tableRow.cells[4] && headerRow.cells[4]) {headerRow.cells[4].style.width = tableRow.cells[4].clientWidth - 4 + "px";}
							if(tableRow.cells[5] && headerRow.cells[5]) {headerRow.cells[5].style.width = tableRow.cells[5].clientWidth - 4 + "px";}
							if(tableRow.cells[6] && headerRow.cells[6]) {headerRow.cells[6].style.width = tableRow.cells[6].clientWidth - 4 + "px";}
							} else {
							var cellwidthBase = window.listwidth / 28;
							micell1 = cellwidthBase * 3;
							micell2 = cellwidthBase * 5;
							micell3 = cellwidthBase * 4;
							micell4 = cellwidthBase * 4;
							micell5 = cellwidthBase * 4;
							micell6 = cellwidthBase * 5;
							micell7 = cellwidthBase * 3;
							headerRow.cells[0].style.width = micell1 + "px";
							headerRow.cells[1].style.width = micell2 + "px";
							headerRow.cells[2].style.width = micell3 + "px";
							headerRow.cells[3].style.width = micell4 + "px";						
							headerRow.cells[4].style.width = micell5 + "px";							
							headerRow.cells[5].style.width = micell6 + "px";						
							headerRow.cells[6].style.width = micell7 + "px";		
							}
						}
					window.mi_last_display = mi_number;
					window.miFin = true;
					mi_list_get();
					},500);
				}
			}				// end function responderlist_cb()
		}				// end function load_responderlist()

	function isViewable(element){
		return (element.clientHeight > 0);
		}
		
	function mi_list_setwidths() {
		var viewableRow = 1;
		var mitbl = document.getElementById('majorincidentstable');
		var headerRow = mitbl.rows[0];
		for (i = 1; i < mitbl.rows.length; i++) {
			if(!isViewable(mitbl.rows[i])) {
				} else {
				viewableRow = i;
				break;
				}
			}
		if(i != mitbl.rows.length) {
			var tableRow = mitbl.rows[viewableRow];
			tableRow.cells[0].style.width = window.micell1 + "px";
			tableRow.cells[1].style.width = window.micell2 + "px";
			tableRow.cells[2].style.width = window.micell3 + "px";
			tableRow.cells[3].style.width = window.micell4 + "px";
			tableRow.cells[4].style.width = window.micell5 + "px";
			tableRow.cells[5].style.width = window.micell6 + "px";
			tableRow.cells[6].style.width = window.micell7 + "px";
			headerRow.cells[0].style.width = tableRow.cells[0].clientWidth - 4 + "px";
			headerRow.cells[1].style.width = tableRow.cells[1].clientWidth - 4 + "px";
			headerRow.cells[2].style.width = tableRow.cells[2].clientWidth - 4 + "px";
			headerRow.cells[3].style.width = tableRow.cells[3].clientWidth - 4 + "px";
			headerRow.cells[4].style.width = tableRow.cells[4].clientWidth - 4 + "px";
			headerRow.cells[5].style.width = tableRow.cells[5].clientWidth - 4 + "px";
			headerRow.cells[6].style.width = tableRow.cells[6].clientWidth - 4 + "px";
			} else {
			var cellwidthBase = window.listwidth / 28;
			micell1 = cellwidthBase * 3;
			micell2 = cellwidthBase * 5;
			micell3 = cellwidthBase * 4;
			micell4 = cellwidthBase * 4;
			micell5 = cellwidthBase * 4;
			micell6 = cellwidthBase * 5;
			micell7 = cellwidthBase * 3;
			headerRow.cells[0].style.width = micell1 + "px";
			headerRow.cells[1].style.width = micell2 + "px";
			headerRow.cells[2].style.width = micell3 + "px";
			headerRow.cells[3].style.width = micell4 + "px";
			headerRow.cells[4].style.width = micell5 + "px";
			headerRow.cells[5].style.width = micell6 + "px";
			headerRow.cells[6].style.width = micell7 + "px";
			}
		}
		
	function mi_list_get() {
		if (mi_interval!=null) {return;}
		mi_interval = window.setInterval('mi_list_loop()', 60000); 
		}			// end function mu get()

	function mi_list_loop() {
		load_mi_list(mi_field, mi_direct);
		}			// end function do_loop()
		

	</SCRIPT>


<?php
	$_postmap_clear = 	(array_key_exists ('frm_clr_pos',$_POST ))? 	$_POST['frm_clr_pos']: "";	// 11/19/09
	$_postfrm_remove = 	(array_key_exists ('frm_remove',$_POST ))? 		$_POST['frm_remove']: "";
	$_getgoedit = 		(array_key_exists ('goedit',$_GET )) ? 			$_GET['goedit']: "";
	$_getgoadd = 		(array_key_exists ('goadd',$_GET ))? 			$_GET['goadd']: "";
	$_getedit = 		(array_key_exists ('edit',$_GET))? 				$_GET['edit']:  "";
	$_getadd = 			(array_key_exists ('add',$_GET))? 				$_GET['add']:  "";
	$_getview = 		(array_key_exists ('view',$_GET ))? 			$_GET['view']: "";

	$now = mysql_format_date(time() - (get_variable('delta_mins')*60));
	$caption = "";
	if ($_postfrm_remove == 'yes') {					//delete Responder - checkbox - 8/12/09
		$query = "DELETE FROM $GLOBALS[mysql_prefix]major_incidents WHERE `id`=" . $_POST['frm_id'];
		$result = mysql_query($query) or do_error($query, 'mysql_query() failed', mysql_error(), __FILE__, __LINE__);
		$caption = "<B>Unit <I>" . stripslashes_deep($_POST['frm_name']) . "</I> has been deleted from database.</B><BR /><BR />";
		} else {
		if ($_getgoedit == 'true') {
			$frm_mistart = "$_POST[frm_year_inc_startime]-$_POST[frm_month_inc_startime]-$_POST[frm_day_inc_startime] $_POST[frm_hour_inc_startime]:$_POST[frm_minute_inc_startime]:00";
			$frm_miend  = (isset($_POST['frm_year_inc_endtime'])) ? quote_smart("$_POST[frm_year_inc_endtime]-$_POST[frm_month_inc_endtime]-$_POST[frm_day_inc_endtime] $_POST[frm_hour_inc_endtime]:$_POST[frm_minute_inc_endtime]:00") : "NULL";

			$now = mysql_format_date(time() - (get_variable('delta_mins')*60));		
			$mi_id = $_POST['frm_id'];
			$by = $_SESSION['user_id'];
			$from = $_SERVER['REMOTE_ADDR'];
			$incs_arr = (isset($_POST['frm_inc'])) ? $_POST['frm_inc'] : array();

			
			$query = "UPDATE `$GLOBALS[mysql_prefix]major_incidents` SET
				`name`= " . 			quote_smart(trim($_POST['frm_name'])) . ",
				`description`= " . 		quote_smart(trim($_POST['frm_descr'])) . ",
				`type`= " . 			quote_smart(trim($_POST['frm_type'])) . ",
				`gold`= " . 			quote_smart(trim($_POST['frm_gold'])) . ",
				`silver`= " . 			quote_smart(trim($_POST['frm_silver'])) . ",
				`bronze`= " . 			quote_smart(trim($_POST['frm_bronze'])) . ",
				`boundary`= " . 		quote_smart(trim($_POST['frm_boundary'])) . ",
				`inc_startime`=".		quote_smart(trim($frm_mistart)) . ",
				`inc_endtime`=".		quote_smart(trim($frm_miend)) . ",
				`incident_notes`= " . 	quote_smart(trim($_POST['frm_notes'])) . ",
				`_by`= " . 		$by . ",
				`_on`= '" . 	$now . "',
				`_from`= '" . $from . "'
				WHERE `id`= " . 	quote_smart(trim($_POST['frm_id'])) . ";";
			$result = mysql_query($query) or do_error($query, 'mysql_query() failed', mysql_error(),basename( __FILE__), __LINE__);
			
			$existing_incs = array();
			$query_x = "SELECT * FROM `$GLOBALS[mysql_prefix]mi_x` WHERE `mi_id` = " . $mi_id . " ORDER BY `id`;";
			$result_x = mysql_query($query_x) or do_error($query_x, 'mysql query failed', mysql_error(),basename( __FILE__), __LINE__);
			while ($row_x = stripslashes_deep(mysql_fetch_assoc($result_x))) {
				$existing_incs[] = $row_x['ticket_id'];
				}
				
			if(isset($_POST['frm_inc'])) {
				foreach($_POST['frm_inc'] AS $val) {
					if(!in_array($val, $existing_incs, TRUE)) {
						$query  = "INSERT INTO `$GLOBALS[mysql_prefix]mi_x` (`mi_id`, `ticket_id`) VALUES ($mi_id, $val)";
						$result = mysql_query($query) or do_error($query, 'mysql query failed', mysql_error(),basename( __FILE__), __LINE__);	
						}
					}
				}
			foreach($existing_incs AS $val) {
				if(!in_array($val, $incs_arr, TRUE)) {
					$query  = "DELETE FROM `$GLOBALS[mysql_prefix]mi_x` WHERE `mi_id` = $mi_id AND `ticket_id` = $val";
					$result = mysql_query($query) or do_error($query, 'mysql query failed', mysql_error(),basename( __FILE__), __LINE__);	
					}
				}

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
						`title` ,
						`filename` ,
						`orig_filename`,
						`ticket_id`,
						`responder_id`,
						`facility_id`,
						`mi_id`,
						`type`,
						`filetype`,
						`_by`,
						`_on`,
						`_from`
					) VALUES (
						'" . $_POST['frm_file_title'] . "',
						'" . $filename . "',
						'" . $realfilename . "',
						0,
						0,
						0,
						" . $mi_id . ",
						0,
						'" . $_FILES['frm_file']['type'] . "',
						$by,
						'" . $now . "',
						'" . $from . "')";
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
			
			$caption = "<B>Major Incident<i> " . stripslashes_deep($_POST['frm_name']) . "</i>' data has been updated </B><BR /><BR />";
			}
		}				// end else {}

	if ($_getgoadd == 'true') {
		$frm_mistart = "$_POST[frm_year_inc_startime]-$_POST[frm_month_inc_startime]-$_POST[frm_day_inc_startime] $_POST[frm_hour_inc_startime]:$_POST[frm_minute_inc_startime]:00";
		$frm_miend  = (isset($_POST['frm_year_inc_endtime'])) ? quote_smart("$_POST[frm_year_inc_endtime]-$_POST[frm_month_inc_endtime]-$_POST[frm_day_inc_endtime] $_POST[frm_hour_inc_endtime]:$_POST[frm_minute_inc_endtime]:00") : "NULL";
		$by = $_SESSION['user_id'];
		$now = mysql_format_date(time() - (get_variable('delta_mins')*60));
		$from = $_SERVER['REMOTE_ADDR'];	
		$incs_arr = (isset($_POST['frm_inc'])) ? $_POST['frm_inc'] : array();
		
		$query = "INSERT INTO `$GLOBALS[mysql_prefix]major_incidents` (`name`, `description`, `type`, `gold`, `silver`, `bronze`, `boundary`, `inc_startime`, `inc_endtime`, `incident_notes`, `_by`, `_on`, `_from` )
			VALUES (" .
				quote_smart(trim($_POST['frm_name'])) . "," .
				quote_smart(trim($_POST['frm_descr'])) . "," .
				quote_smart(trim($_POST['frm_type'])) . "," .
				quote_smart(trim($_POST['frm_gold'])) . "," .
				quote_smart(trim($_POST['frm_silver'])) . "," .
				quote_smart(trim($_POST['frm_bronze'])) . "," .
				quote_smart(trim($_POST['frm_boundary'])) . "," .
				quote_smart(trim($frm_mistart)) . "," .
				quote_smart(trim($frm_miend)) . "," .
				quote_smart(trim($_POST['frm_notes'])) . "," .
				quote_smart(trim($_SESSION['user_id'])) . "," .
				quote_smart(trim($now)) . "," .
				quote_smart(trim($from)) . ");";

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
						`title` ,
						`filename` ,
						`orig_filename`,
						`ticket_id`,
						`responder_id`,
						`facility_id`,
						`mi_id`,
						`type`,
						`filetype`,
						`_by`,
						`_on`,
						`_from`
					) VALUES (
						'" . $_POST['frm_file_title'] . "',
						'" . $filename . "',
						'" . $realfilename . "',
						0,
						0,
						0,
						" . $new_id . ",
						0,
						'" . $_FILES['frm_file']['type'] . "',
						$by,
						'" . $now . "',
						'" . $from . "')";
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
		
		foreach($incs_arr AS $val) {
			$query  = "INSERT INTO `$GLOBALS[mysql_prefix]mi_x` (`mi_id`, `ticket_id`) VALUES ($new_id, $val)";
			$result = mysql_query($query) or do_error($query, 'mysql query failed', mysql_error(),basename( __FILE__), __LINE__);	
			}

		$caption = "<B>Major Incident<i> " . stripslashes_deep($_POST['frm_name']) . "</i>' has been created </B><BR /><BR />";
		}							// end if ($_getgoadd == 'true')

// add ===========================================================================================================================
// add ===========================================================================================================================
// add ===========================================================================================================================

	if ($_getadd == 'true') {
		require_once('./incs/links.inc.php');
		if (!($_SESSION['internet'])) {
			require_once('./forms/mi_add_screen_NM.php');			
			} else {
		require_once('./forms/mi_add_screen.php');
			}
		exit();
		}		// end if ($_GET['add'])

// edit =================================================================================================================
// edit =================================================================================================================
// edit =================================================================================================================

	if ($_getedit == 'true') {
		require_once('./incs/links.inc.php');
		if (!($_SESSION['internet'])) {
			require_once('./forms/mi_edit_screen_NM.php');			
			} else {
		require_once('./forms/mi_edit_screen.php');
			}
		exit();
		}		// end if ($_GET['edit'])
// =================================================================================================================
// view =================================================================================================================

	if ($_getview == 'true') {
		require_once('./incs/links.inc.php');
		if (!($_SESSION['internet'])) {
			require_once('./forms/mi_view_screen_NM.php');			
			} else {
		require_once('./forms/mi_view_screen.php');
			}
		exit();
		}
// ============================================= initial display =======================
	if (!isset($mapmode)) {$mapmode="a";}
	require_once('./incs/links.inc.php');
	if (!($_SESSION['internet'])) {
		require_once('./forms/mi_screen_NM.php');			
		} else {
	require_once('./forms/mi_screen.php');
		}
	exit();
    break;
?>
