# pfsense_centralized_monitoring
Centralized Monitoring portal for PFsense devices

Uses a checkin.php script that injects itself into the cron jobs of the config file to run every 5 minutes. It then posts the OS version, OS date, WAN IP, and hostname. Nightly it attempts to post a copy of the config for constant offsite backups.

Can be used to standardize your initial configuration. My company used it to add remote access to firewall, add allowed IP aliases, enable SSH on LAN, and a few other housekeeping issues. I've left the modifyConfig section to just Cron injection, but look below for some other useful additions to that section.

# How to use
Create a web and MySQL server. We used Apache with htaccess rules to limit public access to only the poster folder. You can modify the CURL routines to go to your NAT port if using an internal only webserver.

Once you have a server setup modify the /poster/checkin.php-latest file. You will need to update the URLs, to match your own, and the KEY variable, unique 32 character random string. This KEY will also need to be updated in the /poster/postPfsense.php file to match. If they do not match the poster will toss the post and 404 error the page.

Upload the full setup to your webserver. Modify the /config.php file to use your DB setting and import the /database.sql file to your MySQL.

On the PFsense run the following command
```
fetch --no-verify-peer -o /usr/local/bin/checkin.php https://MYWEBSITE/posters/checkin.php-latest && php /usr/local/bin/checkin.php
```
This will download and run the script on the target PFsense box. Once it is ran it should reload the modified config. Every 5 minutes it will check in with the latest information. If you modify the checkin.php-latest file, then upon the next checkin the script will check itself's md5 against the hosted md5, if they are different it will download the latest version and run that on the next execution.

Browse to your webserver /index.php. It should list out all devices that have checked in.

# Some useful Config Modifications
All of these modifications go in the modifyConfig function in the /poster/checkin.php file

**Alias IPs for easy Rule use**
```
/* Check for aliases */
foreach($config->aliases->alias as $alias){
    if($alias->descr == "Remote Authorized IPs")
        $updateAliases = false;
}
/* Add Securitas Aliases */
if($updateAliases){
    $alias = $config->aliases->addChild("alias");
    $alias->addChild("name", "RemoteAccess");
    $alias->addChild("type", "host");
    $alias->addChild("address", "12.34.56.78 23.45.67.89");
    $alias->addChild("descr", "Remote Authorized IPs");
    $updateConfig = true;
 }
 ```
 
 **Use Aliased IPs**
 If you use a port that is not 443, you'll need to update the ports here as well as in the /index.php file to match your remote access port.
 ```
/* Check for rules, will not assign remote access rule if a 443 rule already exists */
foreach($config->filter->rule as $rule){
    if($rule->descr == "Remote Admin Access" || $rule->destination->port == "443")
        $updateRules = false;
}
/* Add outside access rules for alias */
if($updateRules){
    $rule = $config->filter->addChild("rule");
    $rule->addChild("type", "pass");
    $rule->addChild("interface", "wan");
    $rule->addChild("ipprotocol", "inet");
    $rule->addChild("statetype", 'keep state');
    $rule->addChild("protocol", "tcp");
    $source = $rule->addChild("source");
    $source->addChild("address", "RemoteAccess");
    $destination = $rule->addChild("destination");
    $destination->addChild("network", "(self)");
    $destination->addChild("port", "443");
    $rule->addChild("descr", 'Remote Admin Access');
    $updateConfig = true;
}
```

**Enable SSH**
```
if(!isset($config->system->ssh)){
    $ssh = $config->system->addChild("ssh");
    $ssh->addChild("enable", "enabled");
    $updateConfig = true;
}elseif(isset($config->system->ssh) && !isset($config->system->ssh->enable)){
    $config->system->ssh->addChild("enable", "enabled");
    $updateConfig = true;
}elseif(isset($config->system->ssh) && isset($config->system->ssh->enable)){
    $config->system->ssh->enable="enabled";
    $updateConfig = true;
}
```
