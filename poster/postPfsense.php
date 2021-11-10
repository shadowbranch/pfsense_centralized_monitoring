<?php
/*
 * PFsense Centralized Monitoring
 * Post incoming PFsense data to Database
 * Daniel Moree <Shadowbranch@gmail.com>
 */
require("../config.php");
global $dbConn;

// Checks if the key matches the key from the checkin.php file. If not must be bast POST data
if($_POST['key'] == "1234567890abcdefghijklmnopqrstuv"){
    $id = $_POST['id'];
    $wanip = $_POST['wanip'];
    $hostname = $_POST['host'];
    $version = $_POST['version'];
    $versiondate = $_POST['versiondate'];

    $result = mysql_query("select ip from pfsense_firewalls where id='$id'", $dbConn) or die();
    $row = mysql_fetch_assoc($result);

    if($row['ip'] == '')
        mysql_query("insert into pfsense_firewalls(id, ip, hostname, version, versiondate, last_checkin) values ('$id', '$wanip', '$hostname', '$version', '$versiondate', '".time()."')", $dbConn);
    else
        mysql_query("update pfsense_firewalls set ip='$wanip', hostname='$hostname', version='$version', versiondate='$versiondate', last_checkin='".time()."' where id='$id'", $dbConn);

    $checksum = md5_file("checkin.php-latest");
    echo($checksum);
}else{
    http_response_code(404); // If key didn't match bounce them a 404 so the bots go away
}
