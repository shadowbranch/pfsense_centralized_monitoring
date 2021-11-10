<?php
/*
 * PFsense Centralized Monitoring
 * Primary interface page
 * Daniel Moree <Shadowbranch@gmail.com>
 */
require("config.php");
global $dbConn;

?>
<html>
<head><title>PFsense Portal</title>
<style>
body{
background-color: black;
	color: white;
}
a{
	color: eee;
	visited: eee;
}
tr#altrow{
	background-color: 333333;
}
</style>
</head>
<body><center><h2>PFsense firewalls</h2></center>

<table>
<tr><th>IP</th><th>Name</th><th>Version</th><th>Last Checkin</th></tr>
<?php
$i = 0;
$result = mysql_query("select * from pfsense_firewalls order by version", $dbConn) or die("Unable to query pfsense database!");
while($row = mysql_fetch_assoc($result)){
	if(preg_match("/(^0)|(^127\.)|(^10\.)|(^172\.16\.)|(^192\.168\.)/", $row['ip']) == 1) // Check WAN IP against private subnets
		$link = "DOUBLE NAT? IP: ".$row['ip'];
	else
		$link = "<a href='https://".$row['ip']."' target='_blank'>".$row['ip']."</a>"; // Assuming https, because why you opening http to WAN!

	$rowstyle="";
	if($i%2)
		$rowstyle = "id='altrow'";

	$downloadConfig="";
	if(strlen($row['config'])>0)
		$downloadConfig="<a href='process.php?cmd=downloadconfig&id=".$row['id']."'>Download Config</a>";

	echo "<tr $rowstyle><td>".$link."</td><td>".$row['hostname']."</td><td>".$row['version']."</td><td>".date('H:i:s Y-m-d', $row['last_checkin'])."</td><td>$downloadConfig</td><td><a href='process.php?cmd=deletedevice&id=".$row['id']."'>Delete Device</a></td></tr>";
	$i++;
}
?>
</table>
</body></html>
