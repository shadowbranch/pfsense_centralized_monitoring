<?php
/*
 * PFsense Centralized Monitoring
 * Simple Command Processor
 * Daniel Moree <Shadowbranch@gmail.com>
 */
require("config.php");
global $dbConn;

function downloadConfig($id){
    global $dbConn;

    $result = mysql_query("SELECT config FROM pfsense_firewalls WHERE id='$id'", $dbConn) or die("Error pulling config!");
    $config = base64_decode(mysql_fetch_row($result));

    header('Content-Description: File Transfer');
    header('Content-Type: application/txt');
    header('Content-Disposition: attachment; filename="'.$id.'.xml"');
    echo $config;
    exit();
}

function deleteDevice($id){
    global $dbConn;

    mysql_query("DELETE FROM pfsense_firewalls WHERE id='$id'", $dbConn) or die("Unable to deleted from database!");
    header('Location: index.php');
}

$cmd = $_GET['cmd'];
$id = $_GET['id'];
if($cmd == 'downloadconfig')
    downloadConfig($id);
elseif($cmd == 'deletedevice')
    deleteDevice($id);
