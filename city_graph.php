<?php  
/*
3/21/10 settings value for pie diameter added
7/28/10 Added inclusion of startup.inc.php for checking of network status and setting of file name variables to support no-maps versions of scripts.
*/


@session_start();
require_once($_SESSION['fip']);		//7/28/10
extract($_GET);

$severities = array();
$temp = explode ("/", get_variable('pie_charts'));
$location_diam = (count($temp)> 0 )? $temp[2] : "300";		// 3/21/10

$where = " WHERE `when` > '" . $p1 . "' AND `when` < '" . $p2 . "' ";

$query = "SELECT *, UNIX_TIMESTAMP(`when`) AS `when`, t.id AS `tick_id`, t.city AS `tick_city` FROM `$GLOBALS[mysql_prefix]log`
	LEFT JOIN `$GLOBALS[mysql_prefix]ticket` t ON (log.ticket_id = t.id)
	". $where . " AND `code` = '" . $GLOBALS['LOG_INCIDENT_OPEN'] ."'
	ORDER BY `tick_city` ASC ";
//dump ($query);
$result = mysql_query($query) or do_error($query, 'mysql query failed', mysql_error(), __FILE__, __LINE__);
$cities = array();

while($row = stripslashes_deep(mysql_fetch_array($result), MYSQL_ASSOC)){			// build assoc arrays of types and counts
	if (array_key_exists($row['tick_city'], $cities)) {
		$cities[$row['tick_city']]++;
		}
	else {
		$cities[$row['tick_city']] = 1;
		}
	}
//dump ($cities);

include('baaChart.php');
$width = isset($img_width)? $img_width: $location_diam;		// 3/21/10
$mygraph = new baaChart($width);
$mygraph->setTitle("Incidents by Location", "");

foreach($cities as $key => $val) {
		$mygraph->addDataSeries('P',PIE_CHART_PCENT + PIE_LEGEND_VALUE,$val, $key);
    }		// end foreach()
$mygraph->setBgColor(0,0,0,1);  			// transparent background
$mygraph->setChartBgColor(0,0,0,1);  		// as background
$mygraph->setSeriesColor (1,222,227,231);	// 
$mygraph->setSeriesColor (2,102,102,204);	//
$mygraph->setSeriesColor (3,255,0,0);		//
$mygraph->drawGraph();

/*

include('baaChart.php');
	$mygraph = new baaChart(1000);
	$mygraph->setTitle('Regional Sales','Jan - Jun 2002');
	$mygraph->addDataSeries('P',PIE_CHART_PCENT + PIE_LEGEND_VALUE,"25,30,35,40,30,35","Hello");
	$mygraph->addDataSeries('P',PIE_CHART_PCENT + PIE_LEGEND_VALUE,"65,70,80,90,75,48","Goodbye");
	$mygraph->addDataSeries('P',PIE_CHART_PCENT + PIE_LEGEND_VALUE,"12,18,25,20,22,30","West");
	$mygraph->addDataSeries('P',PIE_CHART_PCENT + PIE_LEGEND_VALUE,"50,60,75,80,60,75","East");
	$mygraph->addDataSeries('P',PIE_CHART_PCENT + PIE_LEGEND_VALUE,"30,45,50,55,52,60","Europe");
	$mygraph->drawGraph();
*/	
?>	