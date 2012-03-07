<?php
/*
3/25/10 initial release
12/1/10 get_text patient added
4/28/11 add'l get_texts, error added
5/25/11 intrusion detection added
6/19/11 add LOG_CALL_EDIT
*/

$patient = get_text("Patient");
$facility = get_text("Facility");
$unit = get_text("Unit");
$incident = get_text("Incident");

$types = array();

	$types[$GLOBALS['LOG_SIGN_IN']]							="Sign in";
	$types[$GLOBALS['LOG_SIGN_OUT']]						="Sign out";
	$types[$GLOBALS['LOG_COMMENT']]							="Comment";
	$types[$GLOBALS['LOG_INCIDENT_OPEN']]					="{$incident} opened";
	$types[$GLOBALS['LOG_INCIDENT_CLOSE']]					="{$incident} closed";
	$types[$GLOBALS['LOG_INCIDENT_CHANGE']]					="{$incident} updated";
	$types[$GLOBALS['LOG_ACTION_ADD']]						="Action added";
	$types[$GLOBALS['LOG_PATIENT_ADD']]						="{$patient} data added";
	$types[$GLOBALS['LOG_INCIDENT_DELETE']]					="{$incident} deleted";
	$types[$GLOBALS['LOG_ACTION_DELETE']]					="Action deleted";
	$types[$GLOBALS['LOG_PATIENT_DELETE']]					="{$patient} data deleted";
	$types[$GLOBALS['LOG_UNIT_STATUS']]						="{$unit} status change";
	$types[$GLOBALS['LOG_UNIT_COMPLETE']]					="{$unit} completed";
	$types[$GLOBALS['LOG_UNIT_CHANGE']]						="{$unit} updated";
	
	$types[$GLOBALS['LOG_CALL_EDIT']]						="Call edit";				// 6/19/11
	$types[$GLOBALS['LOG_CALL_DISP']]						="{$unit} dispatched";
	$types[$GLOBALS['LOG_CALL_RESP']]						="{$unit} responding";
	$types[$GLOBALS['LOG_CALL_ONSCN']]						="{$unit} on scene";
	$types[$GLOBALS['LOG_CALL_CLR']]						="{$unit} clear";
	$types[$GLOBALS['LOG_CALL_RESET']]						="Call reset";
	
	$types[$GLOBALS['LOG_CALL_REC_FAC_SET']]				="Call rcv {$facility} set";
	$types[$GLOBALS['LOG_CALL_REC_FAC_CHANGE']]				="Call rcv {$facility} changed";
	$types[$GLOBALS['LOG_CALL_REC_FAC_UNSET']]				="Call rcv {$facility} unset";
	$types[$GLOBALS['LOG_CALL_REC_FAC_CLEAR']]				="Call rcv {$facility} cleared";
	
	$types[$GLOBALS['LOG_FACILITY_ADD']]					="{$facility} added";
	$types[$GLOBALS['LOG_FACILITY_CHANGE']]					="{$facility} changed";
	
	$types[$GLOBALS['LOG_FACILITY_INCIDENT_OPEN']]			="{$facility} {$incident} opened";
	$types[$GLOBALS['LOG_FACILITY_INCIDENT_CLOSE']]			="{$facility} {$incident} closed";
	$types[$GLOBALS['LOG_FACILITY_INCIDENT_CHANGE']]		="{$facility} {$incident} changed";
	
	$types[$GLOBALS['LOG_CALL_U2FENR']]						="Call {$unit} to {$facility} enroute";
	$types[$GLOBALS['LOG_CALL_U2FARR']]						="Call {$unit} to {$facility} arrived";
	
	$types[$GLOBALS['LOG_FACILITY_DISP']]					="{$facility} dispatched";
	$types[$GLOBALS['LOG_FACILITY_RESP']]					="{$facility} responding";
	$types[$GLOBALS['LOG_FACILITY_ONSCN']]					="{$facility} on scene";
	$types[$GLOBALS['LOG_FACILITY_CLR']]					="{$facility} cleared";
	$types[$GLOBALS['LOG_FACILITY_RESET']]					="{$facility} reset";
	$types[$GLOBALS['LOG_ERROR']]							="Error";					
	$types[$GLOBALS['LOG_INTRUSION']]						="Security alert: intrusion detected";					

?>