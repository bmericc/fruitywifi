<?php
$mod_name="tor";
$mod_version="1.1";
$mod_path="/usr/share/fruitywifi/www/modules/$mod_name";
$mod_logs="$log_path/$mod_name.log"; 
$mod_logs_history="$mod_path/includes/logs/";
$mod_panel="show";
$mod_isup="ps auxww | grep 'tor ' | grep -v -e 'grep'";
$mod_alias="Tor";
# EXEC
$bin_danger = "/usr/share/fruitywifi/bin/danger";
$bin_tor = "/usr/bin/tor";
$bin_iptables = "/sbin/iptables";
$bin_sudo = "/usr/bin/sudo";
$bin_sh = "/bin/sh";
$bin_echo = "/bin/echo";
$bin_killall = "/usr/bin/killall";
$bin_cp = "/bin/cp";
$bin_sed = "/bin/sed";
$bin_rm = "/bin/rm";
$bin_route = "/sbin/route";
?>
