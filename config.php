<?php
/*
 * PFsense Centralized Monitoring
 * Config file and DB connector
 * Daniel Moree <Shadowbranch@gmail.com>
 */
date_default_timezone_set('UTC');

$dbHost = "";
$dbUser = "";
$dbPass = "";
$dbName = "";

$dbConn = mysql_connect($dbHost, $dbUser, $dbPass) or die("Unable to connect to database!");
mysql_select_db($dbName, $dbConn) or die("Uable to select database!");