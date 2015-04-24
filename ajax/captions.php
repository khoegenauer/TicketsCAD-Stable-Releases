<?php
require_once('../incs/functions.inc.php');
@session_start();
$theTerm = $_GET['q'];
$theAnswer = get_text($theTerm);
print json_encode($theAnswer);
exit();
?>