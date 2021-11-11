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

    $result = mysql_query("SELECT config FROM pfsense_firewalls WHERE id='$id'") or die("Error pulling config");
    $config = base64_decode(mysql_fetch_row($result)[0]);
    $tmpFile = tmpfile();
    $tmpFilePath = stream_get_meta_data($tmpFile)['uri'];
    fwrite($tmpFile, $config);

    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="'.$id.'-config.xml"');
    header('Content-Length: '.filesize($tmpFilePath));
    readfile($tmpFilePath);
    fclose($tmpFile);
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
